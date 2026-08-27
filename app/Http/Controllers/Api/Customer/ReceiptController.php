<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerResource;
use App\Models\Activity;
use App\Models\Order;
use App\Models\Setting;
use App\Models\Warehouse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReceiptController extends Controller
{
    public function show(string $orderUuid, Request $request): JsonResponse
    {
        // Kunci query hanya mencari berdasarkan kolom UUID acak
        $order = Order::with(['city', 'warehouse', 'customer.bank', 'receipt.accus', 'newAccusItems', 'newAccu'])
            ->where('uuid', $orderUuid)
            ->first();

        if (! $order) {
            return response()->json([
                'message' => 'Struk / pesanan tidak ditemukan',
                'data' => null,
            ], 404);
        }

        $warehouse = $order->warehouse;
        if (! $warehouse && ($order->delivery_method ?? 'warehouse') !== 'courier') {
            if ($order->city) {
                $warehouse = Warehouse::where('name', 'LIKE', '%'.$order->city->name.'%')
                    ->orWhere('address', 'LIKE', '%'.$order->city->name.'%')
                    ->first();
            }
            if (! $warehouse) {
                $warehouse = Warehouse::first();
            }
        }

        $receiptData = null;
        $rejectSubtotal = 0;
        if ($order->receipt) {
            $lme = (float) Setting::getValue('lme', 2100);
            $kurs = (float) Setting::getValue('kurs', 16000);
            $city = $order->city;
            $cityPercentage = (float) ($city->percentage ?? 80.00);
            $pricePerKg = ($lme * $kurs * ($cityPercentage / 100)) / 1000.0;

            $formattedAccus = [];
            foreach ($order->receipt->accus as $accu) {
                $beratKering = (float) ($accu->berat_kering ?? 0);
                $calculatedPrice = (int) round($pricePerKg * $beratKering);

                $formattedAccus[] = [
                    'id' => $accu->id,
                    'name' => $accu->name,
                    'brand' => '-',
                    'amount' => $accu->pivot->amount,
                    'price' => $calculatedPrice,
                    'subtotal' => $calculatedPrice * $accu->pivot->amount,
                ];
                $rejectSubtotal += ($calculatedPrice * $accu->pivot->amount);
            }

            $transfer = DB::table('transfers')->where('receipts_id', $order->receipt->id)->first();

            $receiptData = [
                'id' => $order->receipt->id,
                'receipt_number' => $order->receipt->receipt_number,
                'date' => $order->receipt->date,
                'status' => $order->receipt->status,
                'price_received' => $order->receipt->price_received,
                'price_owed' => $order->receipt->price_owed,
                'edit_confirmed_by_user' => is_null($order->receipt->edit_confirmed_by_user) ? 0 : (int) $order->receipt->edit_confirmed_by_user,
                'accus' => $formattedAccus,
                'transfer' => $transfer,
            ];
        }

        $newAccusFormatted = [];
        $newAccusSubtotal = 0;
        if ($order->newAccusItems && $order->newAccusItems->count() > 0) {
            foreach ($order->newAccusItems as $item) {
                $sub = $item->pivot->price * $item->pivot->quantity;
                $newAccusFormatted[] = [
                    'id' => $item->id,
                    'name' => $item->name,
                    'amount' => $item->pivot->quantity,
                    'price' => $item->pivot->price,
                    'subtotal' => $sub,
                ];
                $newAccusSubtotal += $sub;
            }
        } elseif ($order->newAccu) {
            $sub = $order->newAccu->price;
            $newAccusFormatted[] = [
                'id' => $order->newAccu->id,
                'name' => $order->newAccu->name,
                'amount' => 1,
                'price' => $order->newAccu->price,
                'subtotal' => $sub,
            ];
            $newAccusSubtotal += $sub;
        }

        $pickupFee = 0;
        if ($receiptData) {
            $calculatedPickupFee = $rejectSubtotal - $newAccusSubtotal - $order->receipt->price_owed;
            $pickupFee = max(0, $calculatedPickupFee);
        }

        return response()->json([
            'message' => 'Struk transaksi berhasil diambil',
            'data' => [
                'order_id' => $order->id,
                'uuid' => $order->uuid,
                'order_type' => $order->order_type ?? 'sell',
                'new_accus_items' => $newAccusFormatted,
                'payment_method' => $order->payment_method,
                'status' => $order->status,
                'delivery_method' => $order->delivery_method ?? 'warehouse',
                'created_at' => $order->created_at,
                'updated_at' => $order->updated_at,
                'customer' => $order->customer ? (new CustomerResource($order->customer))->toArray($request) : null,
                'city' => $order->city,
                'warehouse' => $warehouse,
                'pickup_address' => $order->pickup_address,
                'pickup_address_note' => $order->pickup_address_note,
                'cancel_reason' => $order->cancel_reason,
                'warehouse_proof' => $order->warehouse_proof,
                'pickup_fee' => $pickupFee,
                'receipt' => $receiptData,
            ],
        ]);
    }

    public function confirmEdit(Request $request, string $orderUuid): JsonResponse
{
    $cleanUuid = trim(urldecode($orderUuid));

    $request->validate([
        'action' => 'required|string|in:accept,reject',
    ]);

    // 1. CARI ORDER SECARA EKSPLISIT BERDASARKAN UUID ATAU ID
    $order = Order::where('uuid', $cleanUuid)->first();

    // Fallback jika input berupa Integer ID murni
    if (! $order && is_numeric($cleanUuid)) {
        $order = Order::find((int) $cleanUuid);
    }

    if (! $order) {
        return response()->json(['message' => 'Pesanan tidak ditemukan.'], 404);
    }

    // 2. AMBIL RECEIPT DENGAN ELOQUENT QUERY KETAT (BUKAN RELASI BINDING)
    $receipt = DB::table('receipts')->where('orders_id', $order->id)->first();

    if (! $receipt) {
        return response()->json(['message' => 'Receipt tidak ditemukan.'], 404);
    }

    $newStatus = ($request->action === 'accept') ? 1 : 2;

    // 3. PAKAI DB DIRECT UPDATE (BYPASS SEMUA CACHE ELOQUENT & EVENT)
    DB::table('receipts')
        ->where('id', $receipt->id)
        ->update([
            'edit_confirmed_by_user' => $newStatus,
            'updated_at' => now(),
        ]);

    if ($request->action === 'accept') {
        Activity::create([
            'type' => 'order_edit_accepted',
            'title' => 'Perubahan Disetujui #' . $order->id,
            'description' => 'Customer menyetujui perubahan rincian aki dari Admin.',
            'related_id' => $order->id,
            'related_type' => Order::class,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Perubahan pesanan berhasil disetujui!'
        ]);
    } else {
        Order::where('id', $order->id)->update([
            'status' => 'cancelled',
            'cancel_reason' => 'Perubahan item pesanan ditolak oleh pelanggan.',
            'updated_at' => now(),
        ]);

        Activity::create([
            'type' => 'order_edit_rejected',
            'title' => 'Perubahan Ditolak #' . $order->id,
            'description' => 'Customer menolak perubahan rincian aki.',
            'related_id' => $order->id,
            'related_type' => Order::class,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Perubahan pesanan ditolak.'
        ]);
    }
}
}