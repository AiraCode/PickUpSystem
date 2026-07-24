<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\PriceHistory;
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

        if ($oldLme !== $newLme) {
            PriceHistory::create([
                'type' => 'lme',
                'label' => 'Global LME',
                'old_value' => $oldLme,
                'new_value' => $newLme,
            ]);
        }

        if ($oldKurs !== $newKurs) {
            PriceHistory::create([
                'type' => 'kurs',
                'label' => 'Global Kurs',
                'old_value' => $oldKurs,
                'new_value' => $newKurs,
            ]);
        }

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

    public function history(): JsonResponse
    {
        $history = PriceHistory::orderBy('created_at', 'desc')->take(50)->get();

        return response()->json([
            'message' => 'Riwayat perubahan harga berhasil diambil',
            'data' => $history,
        ]);
    }
}
