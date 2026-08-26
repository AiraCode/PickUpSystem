<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderHistory;
use App\Models\PriceHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuditHistoryController extends Controller
{
    /**
     * Get combined audit log history for users with 'central' role.
     */
    public function index(Request $request): JsonResponse
    {
        $user = auth()->user();

        // Enforce access control for role 'central' (or admin roles if central)
        if ($user && $user->role !== 'central' && $user->role !== 'admin' && $user->role !== 'superadmin') {
            return response()->json([
                'message' => 'Akses khusus role central.',
            ], 403);
        }

        // Fetch LME & Exchange Rate price history
        $priceHistories = PriceHistory::with('user:id,name')
            ->latest()
            ->take(50)
            ->get()
            ->map(function ($ph) {
                return [
                    'id' => $ph->id,
                    'category' => 'price_history',
                    'type' => $ph->type,
                    'label' => $ph->label,
                    'old_value' => $ph->old_value,
                    'new_value' => $ph->new_value,
                    'lme' => $ph->lme,
                    'admin_name' => $ph->user->name ?? 'System',
                    'user_id' => $ph->user_id,
                    'created_at' => $ph->created_at,
                ];
            });

        // Fetch Order history (status changes, item modifications)
        $orderHistories = OrderHistory::with(['user:id,name', 'order:id,uuid'])
            ->latest()
            ->take(100)
            ->get()
            ->map(function ($oh) {
                return [
                    'id' => $oh->id,
                    'category' => 'order_history',
                    'order_id' => $oh->order_id,
                    'order_uuid' => $oh->order->uuid ?? null,
                    'actor_type' => $oh->actor_type,
                    'action_type' => $oh->action_type,
                    'old_values' => $oh->old_values,
                    'new_values' => $oh->new_values,
                    'description' => $oh->description,
                    'admin_name' => $oh->user->name ?? 'System/Customer',
                    'user_id' => $oh->user_id,
                    'created_at' => $oh->created_at,
                ];
            });

        return response()->json([
            'message' => 'Riwayat perubahan berhasil diambil untuk role central',
            'data' => [
                'price_histories' => $priceHistories,
                'order_histories' => $orderHistories,
            ],
        ]);
    }
}
