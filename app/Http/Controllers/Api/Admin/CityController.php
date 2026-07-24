<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCityRequest;
use App\Models\City;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function index(): JsonResponse
    {
        $cities = City::all();

        return response()->json([
            'message' => 'Daftar kota berhasil diambil',
            'data' => $cities,
        ]);
    }

    public function store(StoreCityRequest $request): JsonResponse
    {
        $data = $request->validated();
        $name = ucwords(strtolower(trim($data['name'])));

        $trashed = City::onlyTrashed()->where('name', $name)->first();
        if ($trashed) {
            $trashed->restore();
            return response()->json([
                'message' => 'Kota berhasil dipulihkan dari data terhapus',
                'data' => $trashed,
            ], 200);
        }

        $data['name'] = $name;
        $data['id'] = (City::withTrashed()->max('id') ?? 0) + 1;
        $city = City::create($data);

        return response()->json([
            'message' => 'Kota berhasil ditambahkan',
            'data' => $city,
        ], 201);
    }

    public function trashed(): JsonResponse
    {
        $cities = City::onlyTrashed()->get();

        return response()->json([
            'message' => 'Daftar kota terhapus berhasil diambil',
            'data' => $cities,
        ]);
    }

    public function restore(int $id): JsonResponse
    {
        $city = City::onlyTrashed()->findOrFail($id);
        $city->restore();

        return response()->json([
            'message' => 'Kota berhasil dipulihkan',
            'data' => $city,
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $city = City::findOrFail($id);

        return response()->json([
            'message' => 'Detail kota berhasil diambil',
            'data' => $city,
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $city = City::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:45',
        ]);

        $city->update($validated);

        return response()->json([
            'message' => 'Kota berhasil diperbarui',
            'data' => $city,
        ]);
    }
    public function destroy(int $id): JsonResponse
    {
        $city = City::findOrFail($id);
        $city->delete();

        return response()->json([
            'message' => 'Kota berhasil dihapus',
        ]);
    }
}
