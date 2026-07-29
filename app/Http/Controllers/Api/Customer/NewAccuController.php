<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\NewAccu;
use Illuminate\Http\JsonResponse;

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
}
