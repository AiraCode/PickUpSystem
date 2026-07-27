<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

//OCR.space API integration
class OCRSpaceService
{
    public function extractText($image)
    {
        try {
            $response = Http::withoutVerifying()
                ->timeout(60)
                ->withHeaders([
                    'apikey' => config('services.ocr_space.key'),
                ])->attach(
                    'file',
                    file_get_contents($image->getRealPath()),
                    $image->getClientOriginalName()
                )->post(config('services.ocr_space.url'), [
                    'apikey' => config('services.ocr_space.key'),
                    'language' => 'auto',
                    'OCREngine' => 2,
                    'scale' => 'true',
                    'detectOrientation' => 'true',
                    'isOverlayRequired' => 'false',
                ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('OCR API Error: ' . $e->getMessage());
            return [
                'IsErroredOnProcessing' => true,
                'ErrorMessage' => ['Koneksi ke server OCR terputus atau terlalu lama (Timeout).']
            ];
        }
    }

    //Ekstraksi nama dari teks KTP/SIM
    public function extractName(string $text): ?string
    {
        $lines = preg_split('/[\r\n]+/', $text);

        foreach ($lines as $i => $line) {
            $line = trim($line);

            //KTP: cari baris setelah "Nama" / "NAMA"
            if (preg_match('/^:?\s*Nama\b/i', $line)) {
                //Nama mungkin di baris yang sama setelah ":"
                if (preg_match('/Nama\s*[:\/]\s*(.+)/i', $line, $m)) {
                    $name = trim($m[1]);
                    if (strlen($name) > 1) return $this->cleanName($name);
                }
                //Atau di baris berikutnya
                if (isset($lines[$i + 1])) {
                    $next = trim($lines[$i + 1]);
                    if (strlen($next) > 1 && !preg_match('/^(tempat|ttl|jenis|alamat|agama|status|pekerjaan|nik|no)/i', $next)) {
                        return $this->cleanName($next);
                    }
                }
            }

            //SIM: cari "Nama/Name"
            if (preg_match('/Nama\s*\/\s*Name\s*[:\/]?\s*(.+)/i', $line, $m)) {
                $name = trim($m[1]);
                if (strlen($name) > 1) return $this->cleanName($name);
            }
        }

        //Fallback: cari pola "Nama : VALUE" atau "NAMA VALUE"
        if (preg_match('/\bNama\b\s*[:\/]\s*([A-Z][A-Z\s\.]+)/i', $text, $m)) {
            return $this->cleanName($m[1]);
        }

        return null;
    }

    private function cleanName(string $name): string
    {
        $name = preg_replace('/[^a-zA-Z\s\.]/', '', $name);
        $name = preg_replace('/\s+/', ' ', $name);
        return strtoupper(trim($name));
    }
}
