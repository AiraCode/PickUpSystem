<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use App\Http\Requests\Admin\StoreWarehouseRequest;
use App\Http\Requests\Admin\UpdateWarehouseRequest;
use Illuminate\Http\JsonResponse;

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
        $data['id'] = (Warehouse::max('id') ?? 0) + 1;
        $warehouse = Warehouse::create($data);

        return response()->json([
            'message' => 'Gudang berhasil ditambahkan',
            'data' => $warehouse,
        ], 201);
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

    public function destroy(int $id): JsonResponse
    {
        $warehouse = Warehouse::findOrFail($id);
        $warehouse->delete();

        return response()->json([
            'message' => 'Gudang berhasil dihapus',
        ]);
    }
}
