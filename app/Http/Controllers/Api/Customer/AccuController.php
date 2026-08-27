<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AccuController extends Controller
{
    public function getByCity(Request $request, int $cityId): JsonResponse
    {
        $city = City::with('accus')->find($cityId);

        if (!$city) {
            return response()->json([
                'message' => 'Kota tidak ditemukan',
                'data' => [],
            ], 404);
        }

        $search = trim($request->query('search', ''));

        // 1. Jika query search kosong, kembalikan array accus kosong
        if (empty($search)) {
            return response()->json([
                'message' => 'Silakan ketik nama aki untuk mencari',
                'data' => [
                    'city' => $city->only(['id', 'name', 'percentage']),
                    'accus' => [],
                ],
            ]);
        }

        $lme = (float) Setting::getValue('lme', 2100);
        $kurs = (float) Setting::getValue('kurs', 16000);
        $cityPercentage = (float) ($city->percentage ?? 80.00);

        // 2. Filter aki berdasarkan pencarian (search key) dan berat_kering > 0
        $accus = $city->accus
            ->filter(function ($accu) use ($search) {
                $hasWeight = (float) ($accu->berat_kering ?? 0) > 0;
                $matchesSearch = str_contains(strtolower($accu->name), strtolower($search)) || 
                                 str_contains(strtolower($accu->brand ?? ''), strtolower($search));
                return $hasWeight && $matchesSearch;
            })
            ->values()
            ->map(function ($accu) use ($lme, $kurs, $cityPercentage) {
                $beratKering = (float) ($accu->berat_kering ?? 0);
                $pricePerKg = ($lme * $kurs * ($cityPercentage / 100)) / 1000.0;
                $calculatedPrice = (int) round($pricePerKg * $beratKering);

                return [
                    'id' => $accu->id,
                    'brand' => $accu->brand ?? '-',
                    'name' => $accu->name,
                    'berat_kering' => $beratKering,
                    'percentage' => $cityPercentage,
                    'price' => $calculatedPrice,
                ];
            });

        return response()->json([
            'message' => 'Daftar aki dan harga di kota ' . $city->name,
            'data' => [
                'city' => $city->only(['id', 'name', 'percentage']),
                'accus' => $accus,
            ],
        ]);
    }
}
