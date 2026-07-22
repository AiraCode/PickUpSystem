<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBankRequest;
use App\Models\Bank;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BankController extends Controller
{
    public function index(): JsonResponse
    {
        $banks = Bank::all();

        return response()->json([
            'message' => 'Daftar bank berhasil diambil',
            'data' => $banks,
        ]);
    }

    public function store(StoreBankRequest $request): JsonResponse
    {
        $bank = Bank::create($request->validated());

        return response()->json([
            'message' => 'Bank berhasil ditambahkan',
            'data' => $bank,
        ], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $bank = Bank::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:45',
        ]);

        $bank->update($validated);

        return response()->json([
            'message' => 'Bank berhasil diperbarui',
            'data' => $bank,
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $bank = Bank::findOrFail($id);
        $bank->delete();

        return response()->json([
            'message' => 'Bank berhasil dihapus',
        ]);
    }
}
