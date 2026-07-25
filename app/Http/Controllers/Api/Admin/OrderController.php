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

        $query = Order::with(['city', 'customer.bank']);

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
        $order = Order::findOrFail($id);

        $updateData = ['status' => $request->status];

        if ($request->status === 'cancelled' && $request->filled('cancel_reason')) {
            $updateData['cancel_reason'] = $request->cancel_reason;
        }

        if ($request->status === 'completed' && $request->filled('proof_base64')) {
            $base64 = $request->proof_base64;
            $extension = explode('/', explode(':', substr($base64, 0, strpos($base64, ';')))[1])[1];
            $replace = substr($base64, 0, strpos($base64, ',')+1);
            $image = str_replace($replace, '', $base64);
            $image = str_replace(' ', '+', $image);
            $imageName = \Illuminate\Support\Str::random(10).'.'.$extension;
            \Illuminate\Support\Facades\Storage::disk('public')->put('transfers/'.$imageName, base64_decode($image));
            $proofPath = 'transfers/'.$imageName;

            $receipt = $order->receipt;
            if ($receipt) {
                $receipt->update([
                    'status' => 'paid',
                    'date' => now(),
                ]);
                
                \Illuminate\Support\Facades\DB::table('transfers')->updateOrInsert(
                    ['receipts_id' => $receipt->id],
                    [
                        'users_id' => auth()->id() ?? 1,
                        'amount' => $receipt->price_owed ?? 0,
                        'transfer_date' => now(),
                        'status' => 'verified',
                        'proof_image' => $proofPath,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }

        $order->update($updateData);

        return response()->json([
            'message' => 'Status order berhasil diperbarui',
            'data' => $order,
        ]);
    }
}
