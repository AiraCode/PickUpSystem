<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'phone_number' => 'required|string|max:45',
            'address' => 'required|string|max:500',
            'address_note' => 'nullable|string|max:500',
            'ktp' => 'nullable|string|max:45',
            'banks_id' => 'required|integer|exists:banks,id',
            'account_name' => 'required|string|max:100',
            'account_number' => 'required|string|max:45',
            'cities_id' => 'required|integer|exists:cities,id',
            'pickup_address' => 'required|string|max:45',
            'pickup_address_note' => 'nullable|string|max:45',
            'pickup_lat' => 'nullable|numeric',
            'pickup_long' => 'nullable|numeric',
            'delivery_method' => 'nullable|string|in:courier,warehouse',
            'items' => 'required|array',
            'items.*.id' => 'required|integer|exists:accus,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        return DB::transaction(function () use ($validated) {
            $customerId = (Customer::max('id') ?? 0) + 1;

            $customer = Customer::create([
                'id' => $customerId,
                'name' => $validated['name'],
                'phone_number' => $validated['phone_number'],
                'address' => $validated['address'],
                'address_note' => $validated['address_note'] ?? null,
                'lat' => $validated['pickup_lat'] ?? -7.2575,
                'long' => $validated['pickup_long'] ?? 112.7521,
                'ktp' => $validated['ktp'] ?? '3578' . rand(1000000000, 9999999999),
                'account_name' => $validated['account_name'],
                'account_number' => $validated['account_number'],
                'flag' => 0,
                'banks_id' => $validated['banks_id'],
            ]);

            $orderId = (Order::max('id') ?? 0) + 1;

            $deliveryMethod = $validated['delivery_method'] ?? 'warehouse';

            $order = Order::create([
                'id' => $orderId,
                'cities_id' => $validated['cities_id'],
                'pickup_address' => substr($validated['pickup_address'], 0, 45),
                'pickup_address_note' => substr($validated['pickup_address_note'] ?? '-', 0, 45),
                'pickup_lat' => $validated['pickup_lat'] ?? -7.2575,
                'pickup_long' => $validated['pickup_long'] ?? 112.7521,
                'status' => 'pending',
                'delivery_method' => $deliveryMethod,
                'customers_id' => $customer->id,
            ]);

            // Calculate subtotal using dynamic LME-based pricing
            $lme = (float) \App\Models\Setting::getValue('lme', 2100);
            $kurs = (float) \App\Models\Setting::getValue('kurs', 16000);
            $city = \App\Models\City::find($validated['cities_id']);
            $cityPercentage = (float) ($city->percentage ?? 80.00);
            $pricePerKg = ($lme * $kurs * ($cityPercentage / 100)) / 1000.0;

            $subtotal = 0;
            $accusPivot = [];
            foreach ($validated['items'] as $item) {
                $accu = \App\Models\Accu::find($item['id']);
                $beratKering = (float) ($accu->berat_kering ?? 0);
                $price = (int) round($pricePerKg * $beratKering);
                $subtotal += $price * $item['quantity'];
                $accusPivot[$item['id']] = ['amount' => $item['quantity']];
            }

            // Calculate pickup fee using Haversine
            $pickupFee = 0;
            $lat = $validated['pickup_lat'] ?? -7.2575;
            $lng = $validated['pickup_long'] ?? 112.7521;

            if ($deliveryMethod === 'courier') {
                $storages = DB::table('storages')->get();
                if ($storages->isNotEmpty()) {
                    $minDistance = INF;
                    foreach ($storages as $w) {
                        $lat1 = $lat;
                        $lon1 = $lng;
                        $lat2 = $w->lat;
                        $lon2 = $w->long;
                        $R = 6371;
                        $dLat = deg2rad($lat2 - $lat1);
                        $dLon = deg2rad($lon2 - $lon1);
                        $a = sin($dLat/2) * sin($dLat/2) +
                             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
                             sin($dLon/2) * sin($dLon/2);
                        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
                        $dist = $R * $c;
                        if ($dist < $minDistance) {
                            $minDistance = $dist;
                        }
                    }
                    $pickupFee = max(10000, round($minDistance * 2000));
                }
            }

            $receiptId = (DB::table('receipts')->max('id') ?? 0) + 1;
            $receipt = \App\Models\Receipt::create([
                'id' => $receiptId,
                'receipt_number' => 'REC-' . date('Ymd') . '-' . str_pad($orderId, 4, '0', STR_PAD_LEFT),
                'date' => now(),
                'status' => 'unpaid',
                'price_received' => 0,
                'price_owed' => $subtotal + $pickupFee,
                'users_id' => 1,
                'orders_id' => $orderId,
            ]);

            $receipt->accus()->sync($accusPivot);

            return response()->json([
                'message' => 'Pesanan penjualan aki berhasil dibuat',
                'data' => [
                    'order_id' => $order->id,
                    'customer' => $customer,
                    'status' => $order->status,
                ],
            ], 201);
        });
    }

    public function show(int $id): JsonResponse
    {
        $order = Order::with(['city', 'customer.bank'])->find($id);

        if (! $order) {
            return response()->json([
                'message' => 'Pesanan tidak ditemukan',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'message' => 'Detail pesanan berhasil diambil',
            'data' => $order,
        ]);
    }
}
