<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Receipt;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\StoreReceiptRequest;
use App\Http\Requests\Admin\UpdateReceiptRequest;
use Illuminate\Support\Facades\DB;

class ReceiptController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Receipt::with(['user', 'order.customer', 'order.city', 'accus']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $receipts = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'message' => 'Daftar struk berhasil diambil',
            'data' => $receipts,
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $receipt = Receipt::with([
            'user',
            'order.customer.bank',
            'order.city',
            'accus',
            'shipment.warehouse',
            'transfer',
        ])->findOrFail($id);

        return response()->json([
           'message' => 'Detail struk berhasil diambil',
            'data' => $receipt,
        ]);
    }

    public function store(StoreReceiptRequest $request): JsonResponse
    {
        $data = $request->validated();
        
        DB::beginTransaction();
        try {
            $receipt = Receipt::create([
                'id' => $data['id'],
                'receipt_number' => $data['receipt_number'],
                'date' => $data['date'],
                'status' => $data['status'],
                'price_received' => $data['price_received'],
                'price_owed' => $data['price_owed'] ?? null,
                'users_id' => $data['users_id'],
                'orders_id' => $data['orders_id'],
            ]);

            $accusPivot = [];
            foreach ($data['accus'] as $accu) {
                $accusPivot[$accu['id']] = ['amount' => $accu['amount']];
            }

            $receipt->accus()->sync($accusPivot);
            DB::commit();

            return response()->json([
                'message' => 'Nota berhasil ditambahkan',
                'data' => $receipt->load('accus'),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal menambahkan nota: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(UpdateReceiptRequest $request, int $id): JsonResponse
    {
        $receipt = Receipt::findOrFail($id);
        $receipt->update($request->validated());

        return response()->json([
            'message' => 'Detail nota berhasil diperbarui',
            'data' => $receipt,
        ]);
    }
}
