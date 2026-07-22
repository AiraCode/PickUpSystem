<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAccuRequest;
use App\Http\Requests\Admin\UpdateAccuRequest;
use App\Models\Accu;
use Illuminate\Http\JsonResponse;

class AccuController extends Controller
{
    public function index(): JsonResponse
    {
        $accus = Accu::all();

        return response()->json([
            'message' => 'Daftar accu berhasil diambil',
            'data' => $accus,
        ]);
    }

    public function store(StoreAccuRequest $request): JsonResponse
    {
        $accu = Accu::create($request->validated());

        return response()->json([
            'message' => 'Accu berhasil ditambahkan',
            'data' => $accu,
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $accu = Accu::with('cities')->findOrFail($id);

        return response()->json([
            'message' => 'Detail accu berhasil diambil',
            'data' => $accu,
        ]);
    }

    public function update(UpdateAccuRequest $request, int $id): JsonResponse
    {
        $accu = Accu::findOrFail($id);
        $accu->update($request->validated());

        return response()->json([
            'message' => 'Accu berhasil diperbarui',
            'data' => $accu,
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $accu = Accu::findOrFail($id);
        $accu->delete();

        return response()->json([
            'message' => 'Accu berhasil dihapus',
        ]);
    }
}
