<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAccuRequest;
use App\Http\Requests\Admin\UpdateAccuRequest;
use App\Models\Accu;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class AccuController extends Controller
{
    public function index(): JsonResponse
    {
        $accus = Accu::all();

        return response()->json([
            'message' => 'Daftar accu berhasil diambil',
            'data' => $accus,
        ]);
    }

    public function store(StoreAccuRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['id'] = (Accu::max('id') ?? 0) + 1;
        
        if ($request->hasFile('img')) {
            $data['img'] = $request->file('img')->store('accus', 'public');
        } else {
            $data['img'] = 'default/accu-default.png';
        }

        $accu = Accu::create($data);

        return response()->json([
            'message' => 'Accu berhasil ditambahkan',
            'data' => $accu,
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $accu = Accu::with('cities')->findOrFail($id);

        return response()->json([
            'message' => 'Detail accu berhasil diambil',
            'data' => $accu,
        ]);
    }

    public function update(UpdateAccuRequest $request, int $id): JsonResponse
    {
        $accu = Accu::findOrFail($id);
        $data = $request->validated();

        if ($request->hasFile('img')) {
            if ($accu->img && $accu->img !== 'default/accu-default.png' && Storage::disk('public')->exists($accu->img)) {
                Storage::disk('public')->delete($accu->img);
            }
            $data['img'] = $request->file('img')->store('accus', 'public');
        }

        $accu->update($data);

        return response()->json([
            'message' => 'Accu berhasil diperbarui',
            'data' => $accu,
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $accu = Accu::findOrFail($id);
        
        if ($accu->img && $accu->img !== 'default/accu-default.png' && Storage::disk('public')->exists($accu->img)) {
            Storage::disk('public')->delete($accu->img);
        }

        $accu->delete();

        return response()->json([
            'message' => 'Accu berhasil dihapus',
        ]);
    }
}
