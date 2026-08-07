<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use App\Http\Requests\Admin\StoreWarehouseRequest;
use App\Http\Requests\Admin\UpdateWarehouseRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class WarehouseController extends Controller
{
    public function index(): JsonResponse
    {
        $warehouses = Warehouse::all();

        return response()->json([
            'message' => 'Daftar gudang berhasil diambil',
            'data' => $warehouses,
        ]);
    }

    public function store(StoreWarehouseRequest $request): JsonResponse
    {
        $data = $request->validated();

        $trashed = Warehouse::onlyTrashed()->where('name', $data['name'])->first();
        if ($trashed) {
            $trashed->restore();
            $trashed->update($data);
            return response()->json([
                'message' => 'Gudang berhasil dipulihkan dari data terhapus',
                'data' => $trashed,
            ], 200);
        }

        $data['id'] = (Warehouse::withTrashed()->max('id') ?? 0) + 1;
        $warehouse = Warehouse::create($data);

        return response()->json([
            'message' => 'Gudang berhasil ditambahkan',
            'data' => $warehouse,
        ], 201);
    }

    public function trashed(): JsonResponse
    {
        $warehouses = Warehouse::onlyTrashed()->get();

        return response()->json([
            'message' => 'Daftar gudang terhapus berhasil diambil',
            'data' => $warehouses,
        ]);
    }

    public function restore(int $id): JsonResponse
    {
        $warehouse = Warehouse::onlyTrashed()->findOrFail($id);
        $warehouse->restore();

        return response()->json([
            'message' => 'Gudang berhasil dipulihkan',
            'data' => $warehouse,
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $warehouse = Warehouse::findOrFail($id);

        return response()->json([
            'message' => 'Detail gudang berhasil diambil',
            'data' => $warehouse,
        ]);
    }

    public function update(UpdateWarehouseRequest $request, int $id): JsonResponse
    {
        $warehouse = Warehouse::findOrFail($id);
        $warehouse->update($request->validated());

        return response()->json([
            'message' => 'Gudang berhasil diperbarui',
            'data' => $warehouse,
        ]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'password' => 'required|string',
        ], [
            'password.required' => 'Wajib memasukkan password admin untuk menghapus gudang.',
        ]);

        if (! Hash::check($request->password, $request->user()->password)) {
            return response()->json([
                'message' => 'Password admin salah! Penghapusan gudang dibatalkan.',
            ], 422);
        }

        $warehouse = Warehouse::findOrFail($id);
        $warehouse->delete();

        return response()->json([
            'message' => 'Gudang berhasil dihapus (soft delete)',
        ]);
    }

    public function stockSummary(int $id): JsonResponse
    {
        $warehouse = Warehouse::find($id);
        if (! $warehouse) {
            return response()->json([
                'message' => 'Gudang tidak ditemukan',
                'warehouse' => null,
                'stocks' => [],
                'total_items' => 0,
            ], 404);
        }

        $existingStocks = \Illuminate\Support\Facades\DB::table('orders')
            ->join('receipts', 'orders.id', '=', 'receipts.orders_id')
            ->join('accus_has_receipts', 'receipts.id', '=', 'accus_has_receipts.receipts_id')
            ->join('accus', 'accus_has_receipts.accus_id', '=', 'accus.id')
            ->whereIn('orders.status', ['arrived_at_warehouse', 'completed'])
            ->where('orders.storages_id', $id)
            ->where('orders.taken_by_central', false)
            ->select(
                'accus.id as accu_id',
                \Illuminate\Support\Facades\DB::raw('SUM(accus_has_receipts.amount) as total_quantity')
            )
            ->groupBy('accus.id')
            ->pluck('total_quantity', 'accu_id');

        $takenStocksRaw = \Illuminate\Support\Facades\DB::table('orders')
            ->join('receipts', 'orders.id', '=', 'receipts.orders_id')
            ->join('accus_has_receipts', 'receipts.id', '=', 'accus_has_receipts.receipts_id')
            ->join('accus', 'accus_has_receipts.accus_id', '=', 'accus.id')
            ->whereIn('orders.status', ['arrived_at_warehouse', 'completed'])
            ->where('orders.storages_id', $id)
            ->where('orders.taken_by_central', true)
            ->select(
                'accus.id as accu_id',
                \Illuminate\Support\Facades\DB::raw('SUM(accus_has_receipts.amount) as total_quantity')
            )
            ->groupBy('accus.id')
            ->pluck('total_quantity', 'accu_id');

        $allAccus = \App\Models\Accu::orderBy('name', 'asc')->get();

        $stocks = $allAccus->map(function ($accu) use ($existingStocks) {
            $qty = (int) ($existingStocks[$accu->id] ?? 0);
            return [
                'accu_id' => $accu->id,
                'accu_name' => $accu->name,
                'accu_brand' => '-',
                'total_quantity' => $qty,
            ];
        })->sort(function ($a, $b) {
            if ($a['total_quantity'] !== $b['total_quantity']) {
                return $b['total_quantity'] <=> $a['total_quantity'];
            }
            return strnatcasecmp($a['accu_name'], $b['accu_name']);
        })->values();

        $takenStocks = $allAccus->map(function ($accu) use ($takenStocksRaw) {
            $qty = (int) ($takenStocksRaw[$accu->id] ?? 0);
            return [
                'accu_id' => $accu->id,
                'accu_name' => $accu->name,
                'accu_brand' => '-',
                'total_quantity' => $qty,
            ];
        })->sort(function ($a, $b) {
            if ($a['total_quantity'] !== $b['total_quantity']) {
                return $b['total_quantity'] <=> $a['total_quantity'];
            }
            return strnatcasecmp($a['accu_name'], $b['accu_name']);
        })->values();

        $totalItems = $stocks->sum('total_quantity');
        $totalTakenItems = $takenStocks->sum('total_quantity');

        return response()->json([
            'message' => 'Detail stok gudang berhasil diambil',
            'warehouse' => $warehouse,
            'stocks' => $stocks,
            'taken_stocks' => $takenStocks,
            'total_items' => $totalItems,
            'total_taken_items' => $totalTakenItems,
        ]);
    }

    public function readyToPickup(): JsonResponse
    {
        // Calculate total untaken items per warehouse
        $untakenCounts = \Illuminate\Support\Facades\DB::table('orders')
            ->join('receipts', 'orders.id', '=', 'receipts.orders_id')
            ->join('accus_has_receipts', 'receipts.id', '=', 'accus_has_receipts.receipts_id')
            ->whereIn('orders.status', ['arrived_at_warehouse', 'completed'])
            ->where('orders.taken_by_central', false)
            ->select('orders.storages_id', \Illuminate\Support\Facades\DB::raw('SUM(accus_has_receipts.amount) as total_untaken'))
            ->groupBy('orders.storages_id')
            ->pluck('total_untaken', 'storages_id');

        // Total taken across all warehouses
        $totalTaken = \Illuminate\Support\Facades\DB::table('orders')
            ->join('receipts', 'orders.id', '=', 'receipts.orders_id')
            ->join('accus_has_receipts', 'receipts.id', '=', 'accus_has_receipts.receipts_id')
            ->whereIn('orders.status', ['arrived_at_warehouse', 'completed'])
            ->where('orders.taken_by_central', true)
            ->sum('accus_has_receipts.amount');

        $readyWarehouses = [];
        $totalUntakenAll = 0;

        $warehouses = Warehouse::all();
        foreach ($warehouses as $w) {
            $untaken = (int) ($untakenCounts[$w->id] ?? 0);
            $totalUntakenAll += $untaken;
            
            if ($untaken >= 20) {
                $w->total_untaken = $untaken;
                $readyWarehouses[] = $w;
            }
        }

        return response()->json([
            'message' => 'Data gudang siap diambil',
            'ready_warehouses' => $readyWarehouses,
            'total_taken_all' => (int) $totalTaken,
            'total_untaken_all' => $totalUntakenAll,
        ]);
    }

    public function pickup(int $id): JsonResponse
    {
        $warehouse = Warehouse::findOrFail($id);

        $updatedCount = \App\Models\Order::where('storages_id', $id)
            ->whereIn('status', ['arrived_at_warehouse', 'completed'])
            ->where('taken_by_central', false)
            ->update(['taken_by_central' => true]);

        if ($updatedCount > 0) {
            \App\Models\Activity::create([
                'type' => 'warehouse_pickup',
                'title' => 'Pengambilan Aki ' . $warehouse->name,
                'description' => 'Admin Pusat mengambil barang yang sudah siap dari gudang cabang.',
                'related_id' => $id,
                'related_type' => Warehouse::class,
            ]);
        }

        return response()->json([
            'message' => 'Barang di ' . $warehouse->name . ' berhasil ditandai sebagai sudah diambil pusat.',
        ]);
    }
}
