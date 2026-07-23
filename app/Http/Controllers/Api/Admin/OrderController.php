<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Http\Requests\Admin\UpdateOrderStatusRequest;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    public function index(): JsonResponse
    {
        $orders = Order::with(['city', 'customer'])->get();

        return response()->json([
            'message' => 'Daftar order berhasil diambil',
            'data' => $orders,
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $order = Order::with(['city', 'customer.bank'])->findOrFail($id);

        return response()->json([
            'message' => 'Detail order berhasil diambil',
            'data' => $order,
        ]);
    }

    public function updateStatus(UpdateOrderStatusRequest $request, int $id): JsonResponse
    {
        $order = Order::findOrFail($id);
        
        $updateData = ['status' => $request->status];
        
        if ($request->status === 'cancelled' && $request->filled('cancel_reason')) {
            $updateData['pickup_address_note'] = 'Batal: ' . $request->cancel_reason;
        }

        $order->update($updateData);

        return response()->json([
            'message' => 'Status order berhasil diperbarui',
            'data' => $order,
        ]);
    }
}
