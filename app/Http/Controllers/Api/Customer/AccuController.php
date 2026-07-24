<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;

class AccuController extends Controller
{
    public function getByCity(int $cityId): JsonResponse
    {
        $city = City::with('accus')->find($cityId);

        if (!$city) {
            return response()->json([
                'message' => 'Kota tidak ditemukan',
                'data' => [],
            ], 404);
        }

        $lme = (float) Setting::getValue('lme', 2100);
        $kurs = (float) Setting::getValue('kurs', 16000);
        $cityPercentage = (float) ($city->percentage ?? 80.00);

        $accus = $city->accus->map(function ($accu) use ($lme, $kurs, $cityPercentage) {
            $beratKering = (float) ($accu->berat_kering ?? 0);
            $pricePerKg = ($lme * $kurs * ($cityPercentage / 100)) / 1000.0;
            $calculatedPrice = (int) round($pricePerKg * $beratKering);

            return [
                'id' => $accu->id,
                'brand' => $accu->brand,
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
