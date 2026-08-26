<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAccuRequest;
use App\Http\Requests\Admin\UpdateAccuRequest;
use App\Models\Accu;
use App\Models\Brand;
use App\Models\City;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AccuController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Accu::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->filled('per_page')) {
            $perPage = (int) $request->input('per_page', 20);
            $paginated = $query->paginate($perPage);
            return response()->json([
                'message' => 'Daftar accu berhasil diambil',
                'data' => $paginated->items(),
                'pagination' => [
                    'current_page' => $paginated->currentPage(),
                    'last_page' => $paginated->lastPage(),
                    'per_page' => $paginated->perPage(),
                    'total' => $paginated->total(),
                    'from' => $paginated->firstItem(),
                    'to' => $paginated->lastItem(),
                ],
            ]);
        }

        $accus = $query->get();

        return response()->json([
            'message' => 'Daftar accu berhasil diambil',
            'data' => $accus,
        ]);
    }

    public function store(StoreAccuRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $trashed = Accu::onlyTrashed()
            ->where('name', $validated['name'])
            ->first();

        if ($trashed) {
            $trashed->restore();
            $trashed->update(['berat_kering' => $validated['berat_kering']]);

            $cityIds = City::pluck('id')->toArray();
            $trashed->cities()->syncWithoutDetaching($cityIds);

            return response()->json([
                'message' => 'Aki berhasil dipulihkan dari data terhapus',
                'data' => $trashed,
            ], 200);
        }

        $data = [
            'id' => (Accu::withTrashed()->max('id') ?? 0) + 1,
            'name' => $validated['name'],
            'berat_kering' => $validated['berat_kering'],
        ];

        $accu = Accu::create($data);

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
        $accus = Accu::onlyTrashed()->get();

        return response()->json([
            'message' => 'Daftar accu terhapus berhasil diambil',
            'data' => $accus,
        ]);
    }

    public function restore(int $id): JsonResponse
    {
        $accu = Accu::onlyTrashed()->findOrFail($id);
        $accu->restore();

        return response()->json([
            'message' => 'Accu berhasil dipulihkan',
            'data' => $accu,
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $accu = Accu::with(['cities'])->findOrFail($id);

        return response()->json([
            'message' => 'Detail accu berhasil diambil',
            'data' => $accu,
        ]);
    }

    public function update(UpdateAccuRequest $request, int $id): JsonResponse
    {
        $accu = Accu::findOrFail($id);
    $validated = $request->validated();
    $userId = $request->user()?->id ?? auth('sanctum')->id() ?? auth()->id() ?? 1;

    $oldName = $accu->name;
    $oldBerat = (float) $accu->berat_kering;

    $data = [];
    if (!empty($validated['name'])) {
        $data['name'] = $validated['name'];
    }
    if (isset($validated['berat_kering'])) {
        $data['berat_kering'] = (float) str_replace(',', '.', (string)$validated['berat_kering']);
    }

    $newName = $data['name'] ?? $oldName;
    $newBerat = isset($data['berat_kering']) ? (float)$data['berat_kering'] : $oldBerat;

    $isNameChanged = isset($data['name']) && $oldName !== $newName;
    $isBeratChanged = isset($data['berat_kering']) && $oldBerat !== $newBerat;

    if ($isNameChanged && $isBeratChanged) {
        \App\Models\PriceHistory::create([
            'user_id'   => $userId,
            'type'      => 'accu_name_and_weight',
            'label'     => 'Aki ' . $oldName . ' Menjadi ' . $newName,
            'old_value' => $oldBerat,
            'new_value' => $newBerat,
        ]);
    } 
    elseif ($isNameChanged) {
        \App\Models\PriceHistory::create([
            'user_id'   => $userId,
            'type'      => 'accu_name',
            'label'     => 'Aki ' . $oldName . ' Menjadi ' . $newName,
            'old_value' => $oldBerat,
            'new_value' => $oldBerat,
        ]);
    } 
    elseif ($isBeratChanged) {
        \App\Models\PriceHistory::create([
            'user_id'   => $userId,
            'type'      => 'accu_weight',
            'label'     => 'Berat Aki ' . $oldName,
            'old_value' => $oldBerat,
            'new_value' => $newBerat,
        ]);
    }

    $accu->update($data);

    return response()->json([
        'message' => 'Accu berhasil diperbarui',
        'data'    => $accu,
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
