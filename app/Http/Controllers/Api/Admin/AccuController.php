<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAccuRequest;
use App\Http\Requests\Admin\UpdateAccuRequest;
use App\Models\Accu;
use App\Models\Brand;
use App\Models\City;
use Illuminate\Http\JsonResponse;

class AccuController extends Controller
{
    public function index(): JsonResponse
    {
        $accus = Accu::with('brandRelation')->get();

        return response()->json([
            'message' => 'Daftar accu berhasil diambil',
            'data' => $accus,
        ]);
    }

    public function store(StoreAccuRequest $request): JsonResponse
    {
        $validated = $request->validated();
        
        $brandName = $request->input('brand');
        $brand = Brand::firstOrCreate(['name' => $brandName]);

        $trashed = Accu::onlyTrashed()
            ->where('brands_id', $brand->id)
            ->where('name', $validated['name'])
            ->first();

        if ($trashed) {
            $trashed->restore();
            $trashed->update(['berat_kering' => $validated['berat_kering']]);
            $trashed->load('brandRelation');

            // Ensure attached to all cities
            $cityIds = City::pluck('id')->toArray();
            $trashed->cities()->syncWithoutDetaching($cityIds);

            return response()->json([
                'message' => 'Aki berhasil dipulihkan dari data terhapus',
                'data' => $trashed,
            ], 200);
        }

        $data = [
            'id' => (Accu::withTrashed()->max('id') ?? 0) + 1,
            'brands_id' => $brand->id,
            'name' => $validated['name'],
            'berat_kering' => $validated['berat_kering'],
        ];

        $accu = Accu::create($data);
        $accu->load('brandRelation');

        // Automatically attach to ALL existing cities
        $cityIds = City::pluck('id')->toArray();
        if (!empty($cityIds)) {
            $accu->cities()->syncWithoutDetaching($cityIds);
        }

        return response()->json([
            'message' => 'Accu berhasil ditambahkan dan diterapkan ke semua kota',
            'data' => $accu,
        ], 201);
    }

    public function trashed(): JsonResponse
    {
        $accus = Accu::onlyTrashed()->with('brandRelation')->get();

        return response()->json([
            'message' => 'Daftar accu terhapus berhasil diambil',
            'data' => $accus,
        ]);
    }

    public function restore(int $id): JsonResponse
    {
        $accu = Accu::onlyTrashed()->findOrFail($id);
        $accu->restore();
        $accu->load('brandRelation');

        return response()->json([
            'message' => 'Accu berhasil dipulihkan',
            'data' => $accu,
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $accu = Accu::with(['cities', 'brandRelation'])->findOrFail($id);

        return response()->json([
            'message' => 'Detail accu berhasil diambil',
            'data' => $accu,
        ]);
    }

    public function update(UpdateAccuRequest $request, int $id): JsonResponse
    {
        $accu = Accu::findOrFail($id);
        $validated = $request->validated();

        $data = [];
        if (!empty($validated['brand'])) {
            $brand = Brand::firstOrCreate(['name' => $validated['brand']]);
            $data['brands_id'] = $brand->id;
        }
        if (!empty($validated['name'])) {
            $data['name'] = $validated['name'];
        }
        if (isset($validated['berat_kering'])) {
            $data['berat_kering'] = $validated['berat_kering'];
        }

        $accu->update($data);
        $accu->load('brandRelation');

        return response()->json([
            'message' => 'Accu berhasil diperbarui',
            'data' => $accu,
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $accu = Accu::findOrFail($id);
        $accu->delete();

        return response()->json([
            'message' => 'Accu berhasil dihapus',
        ]);
    }
}
