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
        $statusCounts = [
            'pending' => Order::where('status', 'pending')->count(),
            'processing' => Order::where('status', 'processing')->count(),
            'completed' => Order::where('status', 'completed')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
            'all' => Order::count(),
        ];

        $query = Order::with(['city', 'customer']);

        $search = $request->input('search');
        $status = $request->input('status');

        // Global search across all statuses if search query is provided
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhere('pickup_address', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%")
                         ->orWhere('phone_number', 'like', "%{$search}%");
                  })
                  ->orWhereHas('city', function ($ciq) use ($search) {
                      $ciq->where('name', 'like', "%{$search}%");
                  });
            });
            // When searching, search across all statuses unless user explicitly clicked a specific status tab
            if (!empty($status) && $status !== 'all') {
                $query->where('status', $status);
            }
        } else {
            // Default status tab is 'pending' if no search or status filter specified
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

        $limit = ($status === 'all' || !empty($search)) ? 100 : 200;
        $orders = $query->orderBy('created_at', 'desc')->take($limit)->get();

        return response()->json([
            'message' => 'Daftar order berhasil diambil',
            'counts' => $statusCounts,
            'current_status' => $status ?? ($search ? 'all' : 'pending'),
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
            $updateData['cancel_reason'] = $request->cancel_reason;
        }

        $order->update($updateData);

        return response()->json([
            'message' => 'Status order berhasil diperbarui',
            'data' => $order,
        ]);
    }
}
