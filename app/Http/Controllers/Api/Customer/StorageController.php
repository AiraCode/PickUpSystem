<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use Illuminate\Http\JsonResponse;

class StorageController extends Controller
{
    public function index(): JsonResponse
    {
        $storages = Warehouse::all(['id', 'name', 'address', 'lat', 'long']);

        return response()->json([
            'message' => 'Daftar lokasi gudang berhasil diambil',
            'data' => $storages,
        ]);
    }
}
