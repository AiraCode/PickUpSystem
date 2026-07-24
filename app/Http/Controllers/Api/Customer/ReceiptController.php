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
        $order = \App\Models\Order::with(['city', 'customer.bank', 'receipt.accus'])->find($orderId);

        if (! $order) {
            return response()->json([
                'message' => 'Struk / pesanan tidak ditemukan',
                'data' => null,
            ], 404);
        }

        $receiptData = null;
        if ($order->receipt) {
            $formattedAccus = [];
            foreach ($order->receipt->accus as $accu) {
                $cityPrice = \Illuminate\Support\Facades\DB::table('cities_has_accus')
                    ->where('cities_id', $order->cities_id)
                    ->where('accus_id', $accu->id)
                    ->value('price') ?? 0;

                $brandName = \Illuminate\Support\Facades\DB::table('brands')->where('id', $accu->brands_id)->value('name') ?? 'Indoprima';

                $formattedAccus[] = [
                    'id' => $accu->id,
                    'name' => $accu->name,
                    'brand' => $brandName,
                    'amount' => $accu->pivot->amount,
                    'price' => $cityPrice,
                    'subtotal' => $cityPrice * $accu->pivot->amount,
                ];
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

        return response()->json([
            'message' => 'Struk transaksi berhasil diambil',
            'data' => [
                'order_id' => $order->id,
                'status' => $order->status,
                'created_at' => $order->created_at,
                'customer' => $order->customer,
                'city' => $order->city,
                'pickup_address' => $order->pickup_address,
                'pickup_address_note' => $order->pickup_address_note,
                'receipt' => $receiptData,
            ],
        ]);
    }
}
