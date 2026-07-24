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
}
