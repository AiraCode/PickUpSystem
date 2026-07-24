<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCityAccuPriceRequest;
use App\Models\City;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CityAccuPriceController extends Controller
{
    public function index(int $cityId): JsonResponse
    {
        $city = City::with('accus')->findOrFail($cityId);

        return response()->json([
            'message' => 'Daftar harga accu di kota ' . $city->name,
            'data' => [
                'city' => $city->only(['id', 'name']),
                'accus' => $city->accus->map(function ($accu) {
                    return [
                        'id' => $accu->id,
                        'brand' => $accu->brand,
                        'name' => $accu->name,
                        'price' => $accu->pivot->price,
                    ];
                }),
            ],
        ]);
    }

    public function store(StoreCityAccuPriceRequest $request, int $cityId): JsonResponse
    {
        $city = City::findOrFail($cityId);

        $city->accus()->syncWithoutDetaching([
            $request->accus_id => ['price' => $request->price, 'deleted_at' => null],
        ]);

        $city->load('accus');

        return response()->json([
            'message' => 'Harga accu berhasil ditambahkan untuk kota ' . $city->name,
            'data' => [
                'city' => $city->only(['id', 'name']),
                'accus' => $city->accus->map(function ($accu) {
                    return [
                        'id' => $accu->id,
                        'brand' => $accu->brand,
                        'name' => $accu->name,
                        'price' => $accu->pivot->price,
                    ];
                }),
            ],
        ], 201);
    }

    public function update(Request $request, int $cityId, int $accuId): JsonResponse
    {
        $city = City::findOrFail($cityId);

        $validated = $request->validate([
            'price' => 'required|integer|min:0',
        ]);

        if (! $city->accus()->where('accus_id', $accuId)->exists()) {
            return response()->json([
                'message' => 'Harga accu tidak ditemukan di kota ini',
            ], 404);
        }

        $city->accus()->updateExistingPivot($accuId, [
            'price' => $validated['price'],
        ]);

        return response()->json([
            'message' => 'Harga accu berhasil diperbarui',
            'data' => [
                'city_id' => $cityId,
                'accus_id' => $accuId,
                'price' => $validated['price'],
            ],
        ]);
    }

    public function destroy(int $cityId, int $accuId): JsonResponse
    {
        $city = City::findOrFail($cityId);
        $city->accus()->updateExistingPivot($accuId, [
            'deleted_at' => now(),
        ]);

        return response()->json([
            'message' => 'Harga accu berhasil dihapus (soft delete) dari kota ini',
        ]);
    }
}
