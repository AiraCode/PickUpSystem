<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Activity;
use App\Models\Order;
use Illuminate\Http\JsonResponse;

class ActivityController extends Controller
{
    public function index(): JsonResponse
    {
        $query = Activity::orderBy('created_at', 'desc');
        
        $user = auth()->user();
        if ($user && $user->role === 'warehouse_admin' && $user->warehouse_id) {
            $warehouseId = $user->warehouse_id;
            // Get all order IDs for this warehouse
            $orderIds = Order::where('storages_id', $warehouseId)->pluck('id');
            
            // Filter activities related to these orders
            $query->where(function ($q) use ($orderIds) {
                $q->where('related_type', Order::class)
                  ->whereIn('related_id', $orderIds);
            });
        }

        $activities = $query->limit(50)->get();

        return response()->json([
            'message' => 'Berhasil mengambil data aktivitas',
            'data' => $activities,
        ]);
    }

    public function destroyAll(): JsonResponse
    {
        $query = Activity::query();
        
        $user = auth()->user();
        if ($user && $user->role === 'warehouse_admin' && $user->warehouse_id) {
            $warehouseId = $user->warehouse_id;
            $orderIds = Order::where('storages_id', $warehouseId)->pluck('id');
            $query->where(function ($q) use ($orderIds) {
                $q->where('related_type', Order::class)
                  ->whereIn('related_id', $orderIds);
            });
        }

        $query->delete();

        return response()->json([
            'message' => 'Semua notifikasi aktivitas berhasil dihapus',
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $activity = Activity::findOrFail($id);
        $activity->delete();

        return response()->json([
            'message' => 'Notifikasi aktivitas berhasil dihapus',
        ]);
    }
}
