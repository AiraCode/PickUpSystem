<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Receipt;
use App\Models\Shipment;
use Illuminate\Http\JsonResponse;

class DashboardStatsController extends Controller
{
    public function index(): JsonResponse
    {
        $totalTransactions = Order::count();
        $pendingVerifications = Order::where('status', 'pending')->count();
        $totalSales = Receipt::sum('price_received');
        $averageProcessingTime = '1-2 Hari';
        $attentionOrders = Order::with(['customer', 'city'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $recentShipments = Shipment::with('warehouse')->orderBy('created_at', 'desc')->take(3)->get();
        $recentReceipts = Receipt::with('user')->orderBy('created_at', 'desc')->take(3)->get();

        $recentActivities = [
            'shipments' => $recentShipments,
            'receipts' => $recentReceipts,
        ];

        return response()->json([
            'message' => 'Statistik dashboard berhasil diambil',
            'data' => [
                'overview' => [
                    'total_transactions' => $totalTransactions,
                    'pending_verifications' => $pendingVerifications,
                    'total_sales' => $totalSales,
                    'avg_processing_time' => $averageProcessingTime,
                ],
                'attention_orders' => $attentionOrders,
                'recent_activities' => $recentActivities,
            ],
        ]);
    }
}
