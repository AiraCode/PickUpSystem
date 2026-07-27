<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Http\Requests\Admin\UpdateOrderStatusRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $filterCountsQuery = Order::query();

        if ($request->filled('city_id')) {
            $filterCountsQuery->where('cities_id', $request->input('city_id'));
        }

        if ($request->filled('bank_id')) {
            $bankId = $request->input('bank_id');
            $filterCountsQuery->whereHas('customer', function ($cq) use ($bankId) {
                $cq->where('banks_id', $bankId);
            });
        }

        if ($request->filled('date_start')) {
            $filterCountsQuery->whereDate('created_at', '>=', $request->input('date_start'));
        }

        if ($request->filled('date_end')) {
            $filterCountsQuery->whereDate('created_at', '<=', $request->input('date_end'));
        }

        $statusCounts = [
            'pending' => (clone $filterCountsQuery)->where('status', 'pending')->count(),
            'processing' => (clone $filterCountsQuery)->where('status', 'processing')->count(),
            'completed' => (clone $filterCountsQuery)->where('status', 'completed')->count(),
            'cancelled' => (clone $filterCountsQuery)->where('status', 'cancelled')->count(),
            'all' => (clone $filterCountsQuery)->count(),
        ];

        $query = Order::with(['city', 'customer.bank', 'receipt.transfer']);

        $search = $request->input('search');
        $status = $request->input('status');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $cleanSearch = ltrim($search, '#');

                if (is_numeric($cleanSearch)) {
                    $q->where('id', $cleanSearch);
                }

                $q->orWhere('id', 'like', "{$cleanSearch}%");

                $q->orWhere('pickup_address', 'like', "{$search}%")
                    ->orWhere('pickup_address', 'like', "% {$search}%");

                $q->orWhereHas('customer', function ($cq) use ($search, $cleanSearch) {
                    $cq->where('name', 'like', "{$search}%")
                        ->orWhere('name', 'like', "% {$search}%")
                        ->orWhere('phone_number', 'like', "{$cleanSearch}%")
                        ->orWhere('phone_number', 'like', "% {$cleanSearch}%");
                });

                $q->orWhereHas('city', function ($ciq) use ($search) {
                    $ciq->where('name', 'like', "{$search}%")
                        ->orWhere('name', 'like', "% {$search}%");
                });
            });
            if (!empty($status) && $status !== 'all') {
                $query->where('status', $status);
            }
        } else {
            if (empty($status)) {
                $status = 'pending';
            }
            if ($status !== 'all') {
                $query->where('status', $status);
            }
        }

        if ($request->filled('city_id')) {
            $query->where('cities_id', $request->input('city_id'));
        }

        if ($request->filled('bank_id')) {
            $bankId = $request->input('bank_id');
            $query->whereHas('customer', function ($cq) use ($bankId) {
                $cq->where('banks_id', $bankId);
            });
        }

        if ($request->filled('date_start')) {
            $query->whereDate('created_at', '>=', $request->input('date_start'));
        }

        if ($request->filled('date_end')) {
            $query->whereDate('created_at', '<=', $request->input('date_end'));
        }

        $sort = $request->input('sort', 'desc');
        $sortDir = strtolower($sort) === 'asc' ? 'asc' : 'desc';

        $limit = ($status === 'all' || !empty($search) || $request->filled('bank_id') || $request->filled('date_start')) ? 150 : 200;
        $orders = $query->orderBy('created_at', $sortDir)->take($limit)->get();

        return response()->json([
            'message' => 'Daftar order berhasil diambil',
            'counts' => $statusCounts,
            'current_status' => $status ?? ($search ? 'all' : 'pending'),
            'data' => $orders,
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $order = Order::with(['city', 'customer.bank', 'receipt.accus'])->findOrFail($id);

        $orderData = $order->toArray();
        if ($order->receipt) {
            $lme = (float) \App\Models\Setting::getValue('lme', 2100);
            $kurs = (float) \App\Models\Setting::getValue('kurs', 16000);
            $cityPercentage = (float) ($order->city->percentage ?? 80.00);
            $pricePerKg = ($lme * $kurs * ($cityPercentage / 100)) / 1000.0;

            $formattedAccus = [];
            foreach ($order->receipt->accus as $accu) {
                $beratKering = (float) ($accu->berat_kering ?? 0);
                $calculatedPrice = (int) round($pricePerKg * $beratKering);
                $brandName = \Illuminate\Support\Facades\DB::table('brands')->where('id', $accu->brands_id)->value('name') ?? 'Indoprima';

                $formattedAccus[] = [
                    'id' => $accu->id,
                    'name' => $accu->name,
                    'brand' => $brandName,
                    'amount' => $accu->pivot->amount,
                    'price' => $calculatedPrice,
                    'subtotal' => $calculatedPrice * $accu->pivot->amount,
                ];
            }

            $orderData['receipt']['accus'] = $formattedAccus;
        }

        return response()->json([
            'message' => 'Detail order berhasil diambil',
            'data' => $orderData,
        ]);
    }

    public function updateStatus(UpdateOrderStatusRequest $request, int $id): JsonResponse
    {
        $order = Order::with(['customer', 'city'])->findOrFail($id);

        $updateData = ['status' => $request->status];
        $cancelReason = $request->cancel_reason;

        if ($request->status === 'cancelled' && $request->filled('cancel_reason')) {
            $updateData['cancel_reason'] = $cancelReason;
        }

        $proofPath = null;
        if ($request->status === 'completed' && $request->filled('proof_base64')) {
            $base64 = $request->proof_base64;
            $extension = explode('/', explode(':', substr($base64, 0, strpos($base64, ';')))[1])[1];
            $replace = substr($base64, 0, strpos($base64, ',') + 1);
            $image = str_replace($replace, '', $base64);
            $image = str_replace(' ', '+', $image);
            $imageName = \Illuminate\Support\Str::random(10) . '.' . $extension;
            \Illuminate\Support\Facades\Storage::disk('public')->put('transfers/' . $imageName, base64_decode($image));
            $proofPath = 'transfers/' . $imageName;

            $receipt = $order->receipt;
            if ($receipt) {
                $receipt->update([
                    'status' => 'paid',
                    'date' => now(),
                ]);

                $existingTransfer = \Illuminate\Support\Facades\DB::table('transfers')->where('receipts_id', $receipt->id)->first();
                if (!$existingTransfer) {
                    $newId = (\Illuminate\Support\Facades\DB::table('transfers')->max('id') ?? 0) + 1;
                    \Illuminate\Support\Facades\DB::table('transfers')->insert([
                        'id' => $newId,
                        'receipts_id' => $receipt->id,
                        'users_id' => auth()->id(),
                        'amount' => $receipt->price_owed ?? 0,
                        'transfer_date' => now(),
                        'status' => 'verified',
                        'proof_image' => $proofPath,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    \Illuminate\Support\Facades\DB::table('transfers')->where('receipts_id', $receipt->id)->update([
                        'users_id' => auth()->id(),
                        'amount' => $receipt->price_owed ?? 0,
                        'transfer_date' => now(),
                        'status' => 'verified',
                        'proof_image' => $proofPath,
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        $order->update($updateData);

        // Kirim WA
        try {
            if (in_array($request->status, ['processing', 'completed', 'cancelled'])) {
                $customer = $order->customer;
                $city = $order->city;

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
                $message = "Halo {$customerName}, {$greeting}! 😊\n\n";

                $fonnteData = [
                    'target' => $customer->phone_number,
                ];

                if ($request->status === 'processing') {
                    $message .= "Pesanan Anda (ID: #{$order->id}) saat ini sedang *DIPROSES* oleh tim kami.";
                } elseif ($request->status === 'completed') {
                    $message .= "Pesanan Anda (ID: #{$order->id}) telah *SELESAI*.\n\nPembayaran untuk aki Anda juga telah berhasil ditransfer. Terima kasih telah mempercayakan layanan tukar tambah aki kepada PickUpSystem.\n\nDitunggu pesanan selanjutnya!";
                } elseif ($request->status === 'cancelled') {
                    $reason = $cancelReason ?? 'Tidak ada alasan yang diberikan.';
                    $message .= "Mohon maaf, Pesanan Anda (ID: #{$order->id}) telah *DIBATALKAN*.\n\n*Alasan Pembatalan*:\n\"{$reason}\"\n\nJika ada pertanyaan lebih lanjut atau ingin memesan ulang, dapat menghubungi admin di nomor berikut 0812-3456-7891. Terima kasih! 🙏";
                }

                $fonnteData['message'] = $message;

                \Illuminate\Support\Facades\Http::withoutVerifying()
                    ->withHeaders([
                        'Authorization' => config('services.fonnte.token'),
                    ])->post('https://api.fonnte.com/send', $fonnteData);
            }
        } catch (\Exception $e) {
            // Log error WA send if needed, but do not interrupt the flow
            \Illuminate\Support\Facades\Log::error('Gagal mengirim WA update status: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'Status order berhasil diperbarui',
            'data' => $order,
        ]);
    }
}
