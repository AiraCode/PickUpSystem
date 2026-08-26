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

    /**
     * Get paginated and filtered order history for 'central' role.
     */
    public function orderHistories(Request $request): JsonResponse
    {
        $user = auth()->user();

        if (!$user || $user->role !== 'central') {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $query = OrderHistory::with(['user:id,name', 'order:id,uuid']);

        // Search by description or order uuid
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhereHas('order', function ($o) use ($search) {
                      $o->where('uuid', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by action_type
        if ($request->has('action_type')) {
            $query->where('action_type', $request->input('action_type'));
        }

        // Filter by actor_type
        if ($request->has('actor_type')) {
            $query->where('actor_type', $request->input('actor_type'));
        }

        $histories = $query->latest()->paginate($request->input('per_page', 15));

        return response()->json([
            'message' => 'Order history berhasil diambil',
            'data' => $histories,
        ]);
    }
}
