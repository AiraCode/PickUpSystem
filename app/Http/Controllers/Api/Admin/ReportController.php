<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Receipt;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $availableYears = Order::selectRaw('YEAR(created_at) as yr')
            ->groupBy('yr')
            ->orderBy('yr', 'desc')
            ->pluck('yr')
            ->toArray();

        if (empty($availableYears)) {
            $availableYears = [(int)date('Y')];
        }

        $selectedYear = (int)$request->input('year', $availableYears[0]);

        // Monthly revenue for the selected year (Jan - Dec)
        $monthlyRaw = Receipt::selectRaw('MONTH(created_at) as mth, SUM(price_received) as total_revenue, COUNT(id) as total_receipts')
            ->whereYear('created_at', $selectedYear)
            ->groupBy('mth')
            ->get()
            ->keyBy('mth');

        $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $monthlyData = [];

        for ($m = 1; $m <= 12; $m++) {
            $row = $monthlyRaw->get($m);
            $monthlyData[] = [
                'month_num' => $m,
                'month_name' => $monthNames[$m - 1],
                'revenue' => $row ? (float)$row->total_revenue : 0,
                'receipts_count' => $row ? (int)$row->total_receipts : 0,
            ];
        }

        // Summary Statistics for selected year
        $ordersQuery = Order::whereYear('created_at', $selectedYear);
        $totalOrdersCount = (clone $ordersQuery)->count();
        $completedOrdersCount = (clone $ordersQuery)->where('status', 'completed')->count();
        $pendingOrdersCount = (clone $ordersQuery)->where('status', 'pending')->count();
        $processingOrdersCount = (clone $ordersQuery)->where('status', 'processing')->count();
        $cancelledOrdersCount = (clone $ordersQuery)->where('status', 'cancelled')->count();

        $cancellationRate = $totalOrdersCount > 0 
            ? round(($cancelledOrdersCount / $totalOrdersCount) * 100, 1) 
            : 0;

        $totalSalesSum = (float)Receipt::whereYear('created_at', $selectedYear)->sum('price_received');
        $avgTransactionValue = $completedOrdersCount > 0 
            ? round($totalSalesSum / $completedOrdersCount) 
            : 0;

        // Top 5 Accus Sold
        $topAccus = DB::table('accus_has_receipts')
            ->join('receipts', 'accus_has_receipts.receipts_id', '=', 'receipts.id')
            ->join('accus', 'accus_has_receipts.accus_id', '=', 'accus.id')
            ->leftJoin('brands', 'accus.brands_id', '=', 'brands.id')
            ->whereYear('receipts.created_at', $selectedYear)
            ->selectRaw('accus.id, accus.name, brands.name as brand, SUM(accus_has_receipts.amount) as total_sold')
            ->groupBy('accus.id', 'accus.name', 'brands.name')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();

        // Top 5 Cities by Volume
        $topCities = DB::table('orders')
            ->join('cities', 'orders.cities_id', '=', 'cities.id')
            ->whereYear('orders.created_at', $selectedYear)
            ->selectRaw('cities.id, cities.name, COUNT(orders.id) as total_orders')
            ->groupBy('cities.id', 'cities.name')
            ->orderByDesc('total_orders')
            ->take(5)
            ->get();

        return response()->json([
            'message' => 'Data laporan analitik berhasil diambil',
            'data' => [
                'selected_year' => $selectedYear,
                'available_years' => $availableYears,
                'summary' => [
                    'total_sales' => $totalSalesSum,
                    'total_orders' => $totalOrdersCount,
                    'completed_orders' => $completedOrdersCount,
                    'processing_orders' => $processingOrdersCount,
                    'pending_orders' => $pendingOrdersCount,
                    'cancelled_orders' => $cancelledOrdersCount,
                    'cancellation_rate' => $cancellationRate,
                    'avg_transaction_value' => $avgTransactionValue,
                ],
                'monthly_chart' => $monthlyData,
                'top_accus' => $topAccus,
                'top_cities' => $topCities,
            ]
        ]);
    }
}
