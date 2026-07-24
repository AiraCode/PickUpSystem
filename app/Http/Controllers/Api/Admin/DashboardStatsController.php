<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Receipt;
use App\Models\Shipment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardStatsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $totalTransactions = Order::count();
        $pendingVerifications = Order::where('status', 'pending')->count();
        $totalSales = (float)Receipt::sum('price_received');
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

        $period = $request->input('period', '7days');
        $activityChart = [];

        if ($period === '30days') {
            $startDate = Carbon::now()->subDays(30)->startOfDay();
            $rawChart = Order::selectRaw('DATE(created_at) as dt, COUNT(id) as total_orders')
                ->where('created_at', '>=', $startDate)
                ->groupBy('dt')
                ->orderBy('dt', 'asc')
                ->get()
                ->keyBy('dt');

            for ($i = 29; $i >= 0; $i--) {
                $dayStr = Carbon::now()->subDays($i)->format('Y-m-d');
                $labelStr = Carbon::now()->subDays($i)->format('d/m');
                $row = $rawChart->get($dayStr);
                $activityChart[] = [
                    'label' => $labelStr,
                    'count' => $row ? (int)$row->total_orders : 0,
                ];
            }
        } elseif ($period === 'year') {
            $year = Carbon::now()->year;
            $rawChart = Order::selectRaw('MONTH(created_at) as mth, COUNT(id) as total_orders')
                ->whereYear('created_at', $year)
                ->groupBy('mth')
                ->get()
                ->keyBy('mth');

            $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            for ($m = 1; $m <= 12; $m++) {
                $row = $rawChart->get($m);
                $activityChart[] = [
                    'label' => $monthNames[$m - 1],
                    'count' => $row ? (int)$row->total_orders : 0,
                ];
            }
        } else {
            $rawChart = Order::selectRaw('DATE(created_at) as dt, COUNT(id) as total_orders')
                ->where('created_at', '>=', Carbon::now()->subDays(6)->startOfDay())
                ->groupBy('dt')
                ->orderBy('dt', 'asc')
                ->get()
                ->keyBy('dt');

            $dayNames = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
            for ($i = 6; $i >= 0; $i--) {
                $dtObj = Carbon::now()->subDays($i);
                $dayStr = $dtObj->format('Y-m-d');
                $labelStr = $dayNames[$dtObj->dayOfWeek];
                $row = $rawChart->get($dayStr);
                $activityChart[] = [
                    'label' => $labelStr,
                    'count' => $row ? (int)$row->total_orders : 0,
                ];
            }
        }

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
                'activity_chart' => [
                    'period' => $period,
                    'data' => $activityChart,
                ],
            ],
        ]);
    }
}
