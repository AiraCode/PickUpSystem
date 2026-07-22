<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Receipt;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReceiptController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Receipt::with(['user', 'order.customer', 'order.city', 'accus']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $receipts = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'message' => 'Daftar struk berhasil diambil',
            'data' => $receipts,
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $receipt = Receipt::with([
            'user',
            'order.customer.bank',
            'order.city',
            'accus',
            'shipment.warehouse',
            'transfer',
        ])->findOrFail($id);

        return response()->json([
            'message' => 'Detail struk berhasil diambil',
            'data' => $receipt,
        ]);
    }
}
