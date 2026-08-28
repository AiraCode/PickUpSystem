<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SessionController extends Controller
{
    public function storeCheckoutSession(Request $request): JsonResponse
    {
        $request->validate([
            'cart' => 'required|array|min:1',
            'address' => 'required|string',
            'order_type' => 'required|string',
        ]);

        session([
            'pickup_cart' => $request->input('cart'),
            'pickup_address' => $request->input('address'),
            'pickup_order_type' => $request->input('order_type'),
            'pickup_trade_in_cart' => $request->input('trade_in_cart', []),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Session checkout berhasil disimpan',
        ]);
    }
}