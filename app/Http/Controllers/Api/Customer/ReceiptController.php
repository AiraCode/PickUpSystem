<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Receipt;
use Illuminate\Http\JsonResponse;

class ReceiptController extends Controller
{
    public function show(int $orderId): JsonResponse
    {
        $order = \App\Models\Order::with(['city', 'customer.bank', 'receipt.accus', 'newAccusItems', 'newAccu'])->find($orderId);

        if (! $order) {
            return response()->json([
                'message' => 'Struk / pesanan tidak ditemukan',
                'data' => null,
            ], 404);
        }

        $receiptData = null;
        if ($order->receipt) {
            // Calculate price dynamically using LME formula
            $lme = (float) \App\Models\Setting::getValue('lme', 2100);
            $kurs = (float) \App\Models\Setting::getValue('kurs', 16000);
            $city = $order->city;
            $cityPercentage = (float) ($city->percentage ?? 80.00);
            $pricePerKg = ($lme * $kurs * ($cityPercentage / 100)) / 1000.0;

            $rejectSubtotal = 0;
            $formattedAccus = [];
            foreach ($order->receipt->accus as $accu) {
                $beratKering = (float) ($accu->berat_kering ?? 0);
                $calculatedPrice = (int) round($pricePerKg * $beratKering);

                $brandName = '-';

                $formattedAccus[] = [
                    'id' => $accu->id,
                    'name' => $accu->name,
                    'brand' => $brandName,
                    'amount' => $accu->pivot->amount,
                    'price' => $calculatedPrice,
                    'subtotal' => $calculatedPrice * $accu->pivot->amount,
                ];
                $rejectSubtotal += ($calculatedPrice * $accu->pivot->amount);
            }

            $transfer = \Illuminate\Support\Facades\DB::table('transfers')->where('receipts_id', $order->receipt->id)->first();

            $receiptData = [
                'id' => $order->receipt->id,
                'receipt_number' => $order->receipt->receipt_number,
                'date' => $order->receipt->date,
                'status' => $order->receipt->status,
                'price_received' => $order->receipt->price_received,
                'price_owed' => $order->receipt->price_owed,
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
             // Fallback for older single-item data
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
            // price_owed = rejectSubtotal - pickupFee - newAccusSubtotal
            // pickupFee = rejectSubtotal - newAccusSubtotal - price_owed
            $calculatedPickupFee = $rejectSubtotal - $newAccusSubtotal - $order->receipt->price_owed;
            $pickupFee = max(0, $calculatedPickupFee);
        }

        return response()->json([
            'message' => 'Struk transaksi berhasil diambil',
            'data' => [
                'order_id' => $order->id,
                'order_type' => $order->order_type ?? 'sell',
                'new_accus_items' => $newAccusFormatted,
                'payment_method' => $order->payment_method,
                'status' => $order->status,
                'delivery_method' => $order->delivery_method ?? 'warehouse',
                'created_at' => $order->created_at,
                'updated_at' => $order->updated_at,
                'customer' => $order->customer,
                'city' => $order->city,
                'pickup_address' => $order->pickup_address,
                'pickup_address_note' => $order->pickup_address_note,
                'cancel_reason' => $order->cancel_reason,
                'pickup_fee' => $pickupFee,
                'receipt' => $receiptData,
            ],
        ]);
    }
}
