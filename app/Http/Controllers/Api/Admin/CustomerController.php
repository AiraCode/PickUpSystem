<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CustomerController extends Controller
{
    public function index(): JsonResponse
    {
        $customers = Customer::with('bank')->get();

        return response()->json([
            'message' => 'Daftar customer berhasil diambil',
            'data' => $customers,
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $customer = Customer::with('bank')->findOrFail($id);

        return response()->json([
            'message' => 'Detail customer berhasil diambil',
            'data' => $customer,
        ]);
    }

    public function updateFlag(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'flag' => 'required|integer|in:0,1',
            'flag_reason' => 'nullable|string|max:500',
        ]);

        $customer = Customer::findOrFail($id);
        $customer->update([
            'flag' => $request->flag,
            'flag_reason' => $request->flag == 1 ? null : ($request->flag_reason ?? $customer->flag_reason),
        ]);

        return response()->json([
            'message' => 'Status customer berhasil diperbarui',
            'data' => $customer,
        ]);
    }
}
