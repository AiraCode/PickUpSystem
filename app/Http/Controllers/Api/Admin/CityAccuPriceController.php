<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CityAccuPriceController extends Controller
{
    public function index(int $cityId): JsonResponse
    {
        $city = City::with('accus')->findOrFail($cityId);
        $lme = (float) Setting::getValue('lme', 2100);
        $kurs = (float) Setting::getValue('kurs', 16000);
        $cityPercentage = (float) ($city->percentage ?? 80.00);

        $accusList = $city->accus->map(function ($accu) use ($lme, $kurs, $cityPercentage) {
            $beratKering = (float) ($accu->berat_kering ?? 0);
            $pricePerKg = ($lme * $kurs * ($cityPercentage / 100)) / 1000.0;
            $calculatedPrice = (int) round($pricePerKg * $beratKering);

            return [
                'id' => $accu->id,
                'brand' => $accu->brand,
                'name' => $accu->name,
                'berat_kering' => $beratKering,
                'price' => $calculatedPrice,
            ];
        });

        return response()->json([
            'message' => 'Daftar harga accu di kota ' . $city->name,
            'data' => [
                'city' => $city->only(['id', 'name', 'percentage']),
                'lme' => $lme,
                'kurs' => $kurs,
                'accus' => $accusList,
            ],
        ]);
    }

    public function store(Request $request, int $cityId): JsonResponse
    {
        $city = City::findOrFail($cityId);
        $validated = $request->validate([
            'accus_id' => 'required|exists:accus,id',
        ]);

        $city->accus()->syncWithoutDetaching([
            $validated['accus_id'] => ['deleted_at' => null],
        ]);

        return $this->index($cityId);
    }

    public function update(Request $request, int $cityId, int $accuId): JsonResponse
    {
        $city = City::findOrFail($cityId);

        if (!$city->accus()->where('accus_id', $accuId)->exists()) {
            return response()->json([
                'message' => 'Aki tidak ditemukan di kota ini',
            ], 404);
        }

        return response()->json([
            'message' => 'Detail harga aki dihitung dari rumus',
        ]);
    }

    public function destroy(int $cityId, int $accuId): JsonResponse
    {
        $city = City::findOrFail($cityId);
        $city->accus()->updateExistingPivot($accuId, [
            'deleted_at' => now(),
        ]);

        return response()->json([
            'message' => 'Aki berhasil dihapus dari kota ini',
        ]);
    }
}
