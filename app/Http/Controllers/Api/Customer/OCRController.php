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

    public function verifyProof(Request $request): JsonResponse
    {
        $request->validate([
            'image' => 'required|file|mimes:png,jpg,jpeg|max:10240',
            'target_amount' => 'required|numeric',
        ]);

        $result = $this->ocr->extractText($request->file('image'));
        $targetAmount = (float) $request->input('target_amount');

        $extractedText = '';
        if (!empty($result['ParsedResults'])) {
            foreach ($result['ParsedResults'] as $res) {
                $extractedText .= "\n" . ($res['ParsedText'] ?? '');
            }
        }

        $cleanText = str_replace(["\r", "\n"], " ", $extractedText);
        $cleanText = strtoupper($cleanText);
        $cleanText = preg_replace('/(\d)[.,]00(?!\d)/', '$1', $cleanText);
        preg_match_all('/(?:IDR|RP|TOTAL|BAYAR|NOMINAL)?\s*[:\.]?\s*(?:RP\.?\s*)?([0-9]{1,3}(?:[\.,][0-9]{3})+|[0-9]{3,})/i', $cleanText, $matches);

        $foundNominal = null;
        $isMatch = false;

        if (!empty($matches[1])) {
            foreach ($matches[1] as $rawNum) {
                $digits = preg_replace('/[^0-9]/', '', $rawNum);
                if (empty($digits)) continue;
                $val = (float) $digits;

                if (abs($val - $targetAmount) < 1.0) {
                    $isMatch = true;
                    $foundNominal = (int) $val;
                    break;
                }

                if ($val >= 1000 && (!$foundNominal || abs($val - $targetAmount) < abs($foundNominal - $targetAmount))) {
                    $foundNominal = (int) $val;
                }
            }
        }

        if (!$isMatch) {
            preg_match_all('/\b\d{4,10}\b/', $cleanText, $allDigits);
            if (!empty($allDigits[0])) {
                foreach ($allDigits[0] as $dStr) {
                    $val = (float) $dStr;
                    if (abs($val - $targetAmount) < 1.0) {
                        $isMatch = true;
                        $foundNominal = (int) $val;
                        break;
                    }
                }
            }
        }

        return response()->json([
            'message' => $isMatch ? 'Nominal terdeteksi otomatis (Sesuai)' : 'Nominal bukti transfer tidak terdeteksi otomatis',
            'is_match' => $isMatch,
            'target_amount' => $targetAmount,
            'detected_amount' => $foundNominal,
            'raw_text' => substr($extractedText, 0, 300),
        ]);
    }
}
