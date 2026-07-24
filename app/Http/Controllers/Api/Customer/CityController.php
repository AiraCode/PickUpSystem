<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\JsonResponse;

class CityController extends Controller
{
    public function index(): JsonResponse
    {
        $cities = City::all(['id', 'name']);

        return response()->json([
            'message' => 'Daftar kota berhasil diambil',
            'data' => $cities,
        ]);
    }
}
