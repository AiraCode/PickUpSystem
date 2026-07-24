<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use Illuminate\Http\JsonResponse;

class BankController extends Controller
{
    public function index(): JsonResponse
    {
        $banks = Bank::all(['id', 'name']);

        return response()->json([
            'message' => 'Daftar bank berhasil diambil',
            'data' => $banks,
        ]);
    }
}
