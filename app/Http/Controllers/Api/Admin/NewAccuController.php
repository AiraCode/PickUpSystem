<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewAccu;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NewAccuController extends Controller
{
    public function index(): JsonResponse
    {
        $newAccus = NewAccu::with('brandRelation')->get();
        return response()->json([
            'message' => 'Daftar aki baru berhasil diambil',
            'data' => $newAccus,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'brands_id' => 'required|integer|exists:brands,id',
            'name' => 'required|string|max:45|unique:new_accus,name',
            'price' => 'required|numeric|min:0',
        ], [
            'name.unique' => 'Nama aki ini sudah terdaftar. Silakan gunakan nama yang berbeda.',
        ]);

        $newAccu = NewAccu::create($validated);

        return response()->json([
            'message' => 'Data aki baru berhasil ditambahkan',
            'data' => $newAccu,
        ], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $newAccu = NewAccu::findOrFail($id);

        $validated = $request->validate([
            'brands_id' => 'sometimes|integer|exists:brands,id',
            'name' => 'sometimes|string|max:45|unique:new_accus,name,' . $id,
            'price' => 'sometimes|numeric|min:0',
        ], [
            'name.unique' => 'Nama aki ini sudah terdaftar. Silakan gunakan nama yang berbeda.',
        ]);

        $newAccu->update($validated);

        return response()->json([
            'message' => 'Data aki baru berhasil diperbarui',
            'data' => $newAccu,
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $newAccu = NewAccu::findOrFail($id);
        $newAccu->delete();

        return response()->json([
            'message' => 'Data aki baru berhasil dihapus',
        ]);
    }
}
