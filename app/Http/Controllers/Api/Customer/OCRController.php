<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Services\OCRSpaceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

//OCR ekstraksi nama dari KTP/SIM
class OCRController extends Controller
{
    protected OCRSpaceService $ocr;

    public function __construct(OCRSpaceService $ocr)
    {
        $this->ocr = $ocr;
    }

    public function extractName(Request $request): JsonResponse
    {
        $request->validate([
            'image' => 'required|file|mimes:png,jpg,jpeg|max:10240',
        ]);

        $result = $this->ocr->extractText($request->file('image'));

        if (!empty($result['IsErroredOnProcessing'])) {
            $errorMsg = is_array($result['ErrorMessage']) ? implode(', ', $result['ErrorMessage']) : ($result['ErrorMessage'] ?? 'Unknown error');
            return response()->json([
                'message' => 'OCR gagal memproses gambar: ' . $errorMsg,
                'name' => null,
                'debug' => $result
            ], 422);
        }

        $text = $result['ParsedResults'][0]['ParsedText'] ?? null;

        if (!$text) {
            return response()->json([
                'message' => 'Tidak dapat membaca teks dari gambar.',
                'name' => null,
            ], 422);
        }

        try {
            $name = $this->ocr->extractName($text);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'name' => null,
                'raw_text' => $text,
            ], 422);
        }

        if (!$name) {
            return response()->json([
                'message' => 'Silakan foto ulang. Coba ganti posisi foto KTP dengan posisi tulisan terbaca dengan baik.',
                'name' => null,
                'raw_text' => $text,
            ], 422);
        }

        return response()->json([
            'message' => 'Nama berhasil diekstrak.',
            'name' => $name,
        ]);
    }
}
