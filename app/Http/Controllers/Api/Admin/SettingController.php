<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\PriceHistory;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index(): JsonResponse
    {
        $lme = (float) Setting::getValue('lme', 2100);
        $kurs = (float) Setting::getValue('kurs', 16000);

        return response()->json([
            'message' => 'Konfigurasi berhasil diambil',
            'data' => [
                'lme' => $lme,
                'kurs' => $kurs,
            ],
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'lme' => 'required|numeric|min:0',
            'kurs' => 'required|numeric|min:0',
        ]);

        $oldLme = (float) Setting::getValue('lme', 2100);
        $oldKurs = (float) Setting::getValue('kurs', 16000);

        $newLme = (float) $validated['lme'];
        $newKurs = (float) $validated['kurs'];

        if ($newLme == $oldLme && $newKurs == $oldKurs) {
            return response()->json([
                'message' => 'Tidak bisa perubahan harga LME dan Kurs. Coba dengan harga berbeda (Cannot change LME and Exchange Rate. Try a different price)',
            ], 422);
        }

        PriceHistory::create([
            'type' => 'lme',
            'label' => 'Global LME & Kurs',
            'old_value' => $oldLme,
            'new_value' => $newKurs,
            'lme' => $newLme,
        ]);

        Setting::setValue('lme', $newLme);
        Setting::setValue('kurs', $newKurs);

        return response()->json([
            'message' => 'LME dan Kurs berhasil diperbarui',
            'data' => [
                'lme' => $newLme,
                'kurs' => $newKurs,
            ],
        ]);
    }

    public function history(Request $request): JsonResponse
    {
        $query = PriceHistory::orderBy('created_at', 'desc');

        if ($request->filled('per_page')) {
            $perPage = (int) $request->input('per_page', 20);
            $paginated = $query->paginate($perPage);
            return response()->json([
                'message' => 'Riwayat perubahan harga berhasil diambil',
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

        $history = $query->take(50)->get();

        return response()->json([
            'message' => 'Riwayat perubahan harga berhasil diambil',
            'data' => $history,
        ]);
    }
}
