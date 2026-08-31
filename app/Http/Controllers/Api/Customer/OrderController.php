<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\Accu;
use App\Models\City;
use App\Models\Customer;
use App\Models\Order;
//use App\Mail\NotifMail;
use App\Models\OrderPickupPricing;
use App\Models\Receipt;
use App\Models\Setting;
use App\Services\PickupFeeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
//use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    public function __construct(protected PickupFeeService $pickupFeeService) {}

    /**
     * Calculate pickup fee dynamically from customer coordinates.
     * Called by the frontend when the customer selects courier delivery.
     */
    public function calculatePickupFee(Request $request): JsonResponse
    {
        $request->validate([
            'pickup_lat'  => 'required|numeric',
            'pickup_long' => 'required|numeric',
        ]);

        session([
            'pickup_cart' => $request->input('cart'),
            'pickup_address' => $request->input('address'),
            'pickup_order_type' => $request->input('order_type'),
            'pickup_trade_in_cart' => $request->input('trade_in_cart', []),
        ]);

        $lat = (float) $request->input('pickup_lat');
        $lng = (float) $request->input('pickup_long');

        // Find nearest warehouse
        $storages = DB::table('storages')->get();
        if ($storages->isEmpty()) {
            return response()->json(['final_pickup_fee' => 0, 'route_distance_km' => 0], 200);
        }

        $nearestStorage = null;
        $minDistance = INF;
        foreach ($storages as $s) {
            $d = $this->haversineKm($lat, $lng, (float)$s->lat, (float)$s->long);
            if ($d < $minDistance) {
                $minDistance = $d;
                $nearestStorage = $s;
            }
        }

        $breakdown = $this->pickupFeeService->calculate(
            $lat,
            $lng,
            (float) $nearestStorage->lat,
            (float) $nearestStorage->long
        );

        return response()->json(array_merge($breakdown, [
            'storage_id'   => $nearestStorage->id,
            'storage_name' => $nearestStorage->name,
        ]));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order_type' => 'nullable|string|in:sell,trade_in',
            'new_accus_id' => 'nullable|integer|exists:new_accus,id',
            'payment_method' => 'nullable|string',
            'name' => 'required|string|max:100',
            'phone_number' => 'required|string|max:45',
            'address' => 'required|string|max:500',
            'address_note' => 'nullable|string|max:500',
            'ktp' => 'nullable|string|max:45',
            'ktp_base64' => 'nullable|string',
            'accu_ktp_base64' => 'nullable|string',
            'transfer_proof_base64' => 'nullable|string',
            'flag' => 'nullable|integer|in:0,1',
            'flag_reason' => 'nullable|string|max:500',
            'banks_id' => 'required|integer|exists:banks,id',
            'account_name' => 'required|string|max:100',
            'account_number' => 'required|string|max:45',
            'cities_id' => 'required|integer|exists:cities,id',
            'pickup_address' => 'required|string|max:200',
            'pickup_address_note' => 'nullable|string|max:200',
            'pickup_lat' => 'nullable|numeric',
            'pickup_long' => 'nullable|numeric',
            'storages_id' => 'nullable|integer|exists:storages,id',
            'delivery_method' => 'nullable|string|in:courier,warehouse',
            'items' => 'required|array',
            'items.*.id' => 'required|integer|exists:accus,id',
            'items.*.quantity' => 'required|integer|min:1',
            'new_accus_items' => 'nullable|array',
            'new_accus_items.*.id' => 'required_with:new_accus_items|integer|exists:new_accus,id',
            'new_accus_items.*.quantity' => 'required_with:new_accus_items|integer|min:1',
        ]);

        try {
            $result = DB::transaction(function () use ($validated, $request) {
                $ktpPath = $validated['ktp'] ?? '3578' . rand(1000000000, 9999999999);
                if (! empty($validated['ktp_base64'])) {
                    if (preg_match('/^data:image\/(\w+);base64,/', $validated['ktp_base64'], $type)) {
                        $data = substr($validated['ktp_base64'], strpos($validated['ktp_base64'], ',') + 1);
                        $type = strtolower($type[1]);
                        if (in_array($type, ['jpg', 'jpeg', 'png'])) {
                            $data = base64_decode($data);
                            $filename = 'ktp/' . uniqid() . '.' . $type;
                            Storage::disk('public')->put($filename, $data);
                            $ktpPath = substr($filename, 0, 45);
                        }
                    }
                }

                $accuKtpPath = null;
                if (! empty($validated['accu_ktp_base64'])) {
                    if (preg_match('/^data:image\/(\w+);base64,/', $validated['accu_ktp_base64'], $type)) {
                        $data = substr($validated['accu_ktp_base64'], strpos($validated['accu_ktp_base64'], ',') + 1);
                        $type = strtolower($type[1]);
                        if (in_array($type, ['jpg', 'jpeg', 'png'])) {
                            $data = base64_decode($data);
                            $filename = 'accu_ktp/' . uniqid() . '.' . $type;
                            Storage::disk('public')->put($filename, $data);
                            $accuKtpPath = substr($filename, 0, 45);
                        }
                    }
                }

                $customerFlag = isset($validated['flag']) ? (int) $validated['flag'] : 1;
                $customer = Customer::create([
                    'name' => $validated['name'],
                    'phone_number' => $validated['phone_number'],
                    'address' => $validated['address'],
                    'address_note' => $validated['address_note'] ?? null,
                    'lat' => $validated['pickup_lat'] ?? -7.2575,
                    'long' => $validated['pickup_long'] ?? 112.7521,
                    'ktp' => $ktpPath,
                    'account_name' => $validated['account_name'],
                    'account_number' => $validated['account_number'],
                    'flag' => $customerFlag,
                    'flag_reason' => $customerFlag === 0 ? ($validated['flag_reason'] ?? null) : null,
                    'banks_id' => $validated['banks_id'],
                ]);

                $orderId = (Order::max('id') ?? 0) + 1;
                $deliveryMethod = $validated['delivery_method'] ?? 'warehouse';
                $orderType = $validated['order_type'] ?? 'sell';

                $selectedStorageId = $validated['storages_id'] ?? null;
                if (! $selectedStorageId) {
                    $allStorages = DB::table('storages')->get();
                    if ($allStorages->isNotEmpty()) {
                        $pLat = (float) ($validated['pickup_lat'] ?? -7.2575);
                        $pLng = (float) ($validated['pickup_long'] ?? 112.7521);
                        $minD = INF;
                        foreach ($allStorages as $st) {
                            $d = $this->haversineKm($pLat, $pLng, (float) $st->lat, (float) $st->long);
                            if ($d < $minD) {
                                $minD = $d;
                                $selectedStorageId = $st->id;
                            }
                        }
                    }
                }

                $order = Order::create([
                    'id' => $orderId,
                    'cities_id' => $validated['cities_id'],
                    'storages_id' => $selectedStorageId,
                    'pickup_address' => substr($validated['pickup_address'], 0, 200),
                    'pickup_address_note' => substr($validated['pickup_address_note'] ?? '-', 0, 200),
                    'pickup_lat' => $validated['pickup_lat'] ?? -7.2575,
                    'pickup_long' => $validated['pickup_long'] ?? 112.7521,
                    'status' => 'pending',
                    'delivery_method' => $deliveryMethod,
                    'customers_id' => $customer->id,
                    'order_type' => $orderType,
                    'new_accus_id' => $orderType === 'trade_in' ? ($validated['new_accus_id'] ?? null) : null,
                    'payment_method' => $orderType === 'trade_in' ? ($validated['payment_method'] ?? null) : null,
                    'accu_ktp' => $accuKtpPath,
                ]);

                $lme = (float) Setting::getValue('lme', 2100);
                $kurs = (float) Setting::getValue('kurs', 16000);
                $city = City::find($validated['cities_id']);
                $cityPercentage = (float) ($city->percentage ?? 80.00);
                $pricePerKg = ($lme * $kurs * ($cityPercentage / 100)) / 1000.0;

                $subtotal = 0;
                $accusPivot = [];
                foreach ($validated['items'] as $item) {
                    $accu = Accu::find($item['id']);
                    $beratKering = (float) ($accu->berat_kering ?? 0);
                    if ($beratKering <= 0) {
                        throw new \Exception("Aki {$accu->name} tidak dapat dipilih karena berat kering bernilai 0.");
                    }
                    $price = (int) round($pricePerKg * $beratKering);
                    $subtotal += $price * $item['quantity'];
                    $accusPivot[$item['id']] = ['amount' => $item['quantity']];
                }

                $pickupFee      = 0;
                $pricingBreakdown = null;
                $lat = (float)($validated['pickup_lat']  ?? -7.2575);
                $lng = (float)($validated['pickup_long'] ?? 112.7521);

                if ($deliveryMethod === 'courier') {
                    $storages = DB::table('storages')->get();
                    if ($storages->isNotEmpty()) {
                        // Find nearest warehouse (Haversine for selection only)
                        $nearestStorage = null;
                        $minDist = INF;
                        foreach ($storages as $s) {
                            $d = $this->haversineKm($lat, $lng, (float)$s->lat, (float)$s->long);
                            if ($d < $minDist) {
                                $minDist = $d;
                                $nearestStorage = $s;
                            }
                        }

                        // PickupFeeService: OSRM route distance + dynamic pricing
                        $pricingBreakdown = $this->pickupFeeService->calculate(
                            $lat,
                            $lng,
                            (float) $nearestStorage->lat,
                            (float) $nearestStorage->long
                        );
                        $pickupFee = $pricingBreakdown['final_pickup_fee'];
                    }
                }

                $newAccuPrice = 0;
                $newAccusPivotData = [];
                if ($orderType === 'trade_in' && !empty($validated['new_accus_items'])) {
                    foreach ($validated['new_accus_items'] as $newAccuItem) {
                        $newAccu = \App\Models\NewAccu::find($newAccuItem['id']);
                        if ($newAccu) {
                            $newAccuPrice += $newAccu->price * $newAccuItem['quantity'];
                            $newAccusPivotData[] = [
                                'new_accus_id' => $newAccu->id,
                                'quantity' => $newAccuItem['quantity'],
                                'price' => $newAccu->price,
                            ];
                        }
                    }
                }

                $priceOwed = $subtotal - $pickupFee - $newAccuPrice;

                $receiptId = (DB::table('receipts')->max('id') ?? 0) + 1;
                $receipt = Receipt::create([
                    'id' => $receiptId,
                    'receipt_number' => 'REC-' . date('Ymd') . '-' . str_pad($orderId, 4, '0', STR_PAD_LEFT),
                    'date' => now(),
                    'status' => 'UNPAID',
                    'price_received' => 0,
                    'price_owed' => $priceOwed,
                    'users_id' => 1,
                    'orders_id' => $orderId,
                ]);

                $receipt->accus()->sync($accusPivot);
                if (!empty($newAccusPivotData)) {
                    foreach ($newAccusPivotData as $pivotRow) {
                        DB::table('new_accus_orders')->insert([
                            'orders_id' => $orderId,
                            'new_accus_id' => $pivotRow['new_accus_id'],
                            'quantity' => $pivotRow['quantity'],
                            'price' => $pivotRow['price'],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }

                if (! empty($validated['transfer_proof_base64'])) {
                    if (preg_match('/^data:image\/(\w+);base64,/', $validated['transfer_proof_base64'], $type)) {
                        $tData = substr($validated['transfer_proof_base64'], strpos($validated['transfer_proof_base64'], ',') + 1);
                        $tType = strtolower($type[1]);
                        if (in_array($tType, ['jpg', 'jpeg', 'png'])) {
                            $tData = base64_decode($tData);
                            $tFilename = 'transfers/' . uniqid() . '.' . $tType;
                            Storage::disk('public')->put($tFilename, $tData);

                            $transferId = (DB::table('transfers')->max('id') ?? 0) + 1;
                            DB::table('transfers')->insert([
                                'id' => $transferId,
                                'receipts_id' => $receipt->id,
                                'users_id' => 1,
                                'amount' => abs($priceOwed),
                                'transfer_date' => now(),
                                'status' => 'success',
                                'proof_image' => $tFilename,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }
                }

                // Persist full pricing snapshot (locked permanently with this transaction)
                if ($pricingBreakdown && isset($nearestStorage)) {
                    $pricingId = (DB::table('order_pickup_pricings')->max('id') ?? 0) + 1;
                    OrderPickupPricing::create([
                        'id'                  => $pricingId,
                        'orders_id'           => $orderId,
                        'storages_id'         => $nearestStorage->id,
                        // Configuration snapshot (never recalculated after save)
                        'initial_fee'         => $pricingBreakdown['initial_fee'] ?? null,
                        'distance_rate'       => $pricingBreakdown['distance_rate'] ?? null,
                        'time_rate'           => $pricingBreakdown['time_rate'] ?? null,
                        'demand_multiplier'   => $pricingBreakdown['demand_multiplier'] ?? 1.0,
                        'weather_multiplier'  => $pricingBreakdown['weather_multiplier'] ?? 1.0,
                        'traffic_multiplier'  => $pricingBreakdown['traffic_multiplier'] ?? 1.0,
                        'event_multiplier'    => $pricingBreakdown['event_multiplier'] ?? 1.0,
                        // Calculation results
                        'route_distance_km'   => $pricingBreakdown['route_distance_km'],
                        'travel_time_seconds' => $pricingBreakdown['travel_time_seconds'],
                        'base_price'          => $pricingBreakdown['base_price'],
                        'multiplier'          => $pricingBreakdown['multiplier'],
                        'final_pickup_fee'    => $pricingBreakdown['final_pickup_fee'],
                    ]);
                }


                return [
                    'order'         => $order,
                    'customer'      => $customer,
                    'city'          => $city,
                    'total_cost'    => $priceOwed,
                    'new_accu_price' => $newAccuPrice,
                ];
            });

            $token = config('services.fonnte.token') ?? env('FONNTE_TOKEN');
            $order = $result['order'];
            $customer = $result['customer'];
            $city = $result['city'];
            $totalCost = $result['total_cost'];
            $cityName = strtolower($city->name ?? '');

            $witaCities = ['denpasar', 'badung', 'gianyar', 'singaraja', 'mataram', 'kupang', 'banjarmasin', 'balikpapan', 'samarinda', 'tarakan', 'makassar', 'manado', 'palu', 'kendari', 'gorontalo', 'mamuju', 'bali', 'lombok'];
            $witCities = ['ambon', 'ternate', 'jayapura', 'sorong', 'manokwari', 'merauke', 'timika', 'papua', 'maluku'];

            $timezone = 'Asia/Jakarta';

            foreach ($witaCities as $wita) {
                if (str_contains($cityName, $wita)) {
                    $timezone = 'Asia/Makassar';
                    break;
                }
            }

            foreach ($witCities as $wit) {
                if (str_contains($cityName, $wit)) {
                    $timezone = 'Asia/Jayapura';
                    break;
                }
            }

            $date = new \DateTime('now', new \DateTimeZone($timezone));
            $hour = (int) $date->format('H');

            if ($hour >= 4 && $hour < 11) {
                $greeting = 'Selamat pagi';
            } elseif ($hour >= 11 && $hour < 15) {
                $greeting = 'Selamat siang';
            } elseif ($hour >= 15 && $hour < 18) {
                $greeting = 'Selamat sore';
            } else {
                $greeting = 'Selamat malam';
            }

            $customerName = $customer->name ?? 'Kak';

            $isCustomerPaying = $totalCost < 0;
            $formattedTotal = number_format(abs($totalCost), 0, ',', '.');

            $orderTypeMsg = $order->order_type === 'trade_in' ? 'Tukar Tambah Aki' : 'Penjualan Aki Bekas';

            $message = "Halo {$customerName}, {$greeting}! 😊\n\n"
                . "Pesanan *{$orderTypeMsg}* Anda telah berhasil kami terima dengan rincian sebagai berikut:\n\n"
                . "🔹 *ID Pesanan*: #{$order->id}\n";

            if ($order->order_type === 'trade_in') {
                if ($isCustomerPaying) {
                    $message .= "🔹 *Total Tagihan Anda*: Rp {$formattedTotal} (" . strtoupper($order->payment_method) . ")\n\n";
                } else {
                    $message .= "🔹 *Total Uang Diterima*: Rp {$formattedTotal}\n\n";
                }
            } else {
                $message .= "🔹 *Total Uang Diterima*: Rp {$formattedTotal}\n\n";
            }

            $message .= "Untuk melihat rincian pesanan dan bukti transaksi, silakan klik tautan di bawah ini:\n"
                . "🔗 https://www.onestopsolution.my.id/receipt?order_id={$order->uuid}\n\n"
                . 'Jika ada pertanyaan lebih lanjut, dapat menghubungi admin di nomor berikut 0812-3456-7891.';

            Http::withHeaders([
                'Authorization' => $token,
                ])->post('https://api.fonnte.com/send', [
                    'target' => $validated['phone_number'],
                    'message' => $message,
                ]);


            try {
                \App\Models\Activity::create([
                    'type' => 'order_created',
                    'title' => 'Pesanan Baru #' . $order->id,
                    'description' => 'Pelanggan ' . $customer->name . ' membuat pesanan baru dari ' . ($city->name ?? '-'),
                    'related_id' => $order->id,
                    'related_type' => \App\Models\Order::class,
                ]);
            } catch (\Exception $e) {
                logger()->error('Failed to log activity: ' . $e->getMessage());
            }

            return response()->json([
                'message' => 'Pesanan penjualan aki berhasil dibuat',
                'data' => [
                    'order_id' => $order->id,
                    'uuid'     => $order->uuid,
                    'customer' => $customer,
                    'status' => $order->status,
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat membuat pesanan: ' . $e->getMessage(),
            ], 500);
        }
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
    /**
     * Haversine formula: straight-line distance in km.
     * Used only for warehouse selection (nearest warehouse identification).
     * Actual route distance for pricing is handled by PickupFeeService via OSRM.
     */
    private function haversineKm(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $R    = 6371.0;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a    = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;
        return $R * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }
}
