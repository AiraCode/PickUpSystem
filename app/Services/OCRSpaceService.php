<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

//OCR.space API integration
class OCRSpaceService
{
    public function extractText($image)
    {
        // First Pass: Original Image
        $result = $this->callApi($image->getRealPath(), $image->getClientOriginalName());
        
        // Cek apakah First Pass berhasil mendapatkan teks KTP yang valid
        $text = $result['ParsedResults'][0]['ParsedText'] ?? '';
        $textUpper = strtoupper($text);
        $isKtp = preg_match('/(\bKTP\b|\bPROVINSI\b|\bNIK\b|KARTU\s+TANDA\s+PENDUDUK|\b\d{16}\b)/i', $textUpper);
        
        // Coba ekstrak nama dari First Pass
        $name = null;
        if ($isKtp) {
            try {
                $name = $this->extractName($text);
            } catch (\Exception $e) {}
        }

        // Jika First Pass gagal (tidak terdeteksi KTP atau nama kosong), lakukan Second Pass dengan Image Processing
        if (!$isKtp || empty($name)) {
            $processedPath = $this->preprocessImage($image);
            $fallbackResult = $this->callApi($processedPath, $image->getClientOriginalName());
            
            if ($processedPath !== $image->getRealPath() && file_exists($processedPath)) {
                @unlink($processedPath);
            }

            // Gunakan hasil fallback jika teksnya lebih panjang atau lebih valid
            $fallbackText = $fallbackResult['ParsedResults'][0]['ParsedText'] ?? '';
            $fallbackUpper = strtoupper($fallbackText);
            $fallbackIsKtp = preg_match('/(\bKTP\b|\bPROVINSI\b|\bNIK\b|KARTU\s+TANDA\s+PENDUDUK|\b\d{16}\b)/i', $fallbackUpper);
            
            if ($fallbackIsKtp) {
                return $fallbackResult;
            }
        }

        return $result;
    }

    private function callApi($filePath, $fileName)
    {
        try {
            $response = Http::withoutVerifying()
                ->timeout(60)
                ->withHeaders([
                    'apikey' => config('services.ocr_space.key'),
                ])->attach(
                    'file',
                    file_get_contents($filePath),
                    $fileName
                )->post(config('services.ocr_space.url'), [
                    'apikey' => config('services.ocr_space.key'),
                    'language' => 'eng',
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

    private function preprocessImage($image)
    {
        $path = $image->getRealPath();
        $mime = mime_content_type($path);
        
        switch ($mime) {
            case 'image/jpeg':
                $img = @imagecreatefromjpeg($path);
                break;
            case 'image/png':
                $img = @imagecreatefrompng($path);
                break;
            default:
                return $path;
        }

        if (!$img) return $path;

        // Terapkan Grayscale
        imagefilter($img, IMG_FILTER_GRAYSCALE);
        
        // Tingkatkan Contrast (-25 terbukti optimal untuk KTP gelap seperti Wilson)
        imagefilter($img, IMG_FILTER_CONTRAST, -25); 
        
        // Gentle Sharpening (menajamkan teks yang blur)
        $sharpenMatrix = [
            [0, -1, 0],
            [-1, 12, -1],
            [0, -1, 0]
        ];
        imageconvolution($img, $sharpenMatrix, 8, 0);

        $tempPath = tempnam(sys_get_temp_dir(), 'ocr_') . '.jpg';
        imagejpeg($img, $tempPath, 100); 
        imagedestroy($img);

        return $tempPath;
    }

    //Ekstraksi nama dari teks KTP/SIM
    public function extractName(string $text): ?string
    {
        $textUpper = strtoupper($text);
        
        // Tambahan pola pengecekan 16 digit agar lebih toleran walau kata NIK tidak ada
        $isKtp = preg_match('/(\bKTP\b|\bPROVINSI\b|\bNIK\b|KARTU\s+TANDA\s+PENDUDUK|\b\d{16}\b)/i', $textUpper);
        $isSim = preg_match('/(SURAT\s+IZIN\s+MENGEMUDI|DRIVING\s+LICENSE|\bPOLRI\b|\bKORLANTAS\b)/i', $textUpper);
        
        if (!$isKtp && !$isSim) {
            throw new \Exception("Pastikan Anda mengupload file berupa KTP / SIM.");
        }

        $lines = preg_split('/[\r\n]+/', $text);

        // Strategy 1: Cari baris NIK (Bisa kata "NIK" atau langsung 16 digit angka)
        foreach ($lines as $i => $line) {
            if (preg_match('/\bNIK\b/i', $line) || preg_match('/\b\d{16}\b/', $line)) {
                if (isset($lines[$i+1])) {
                    $next = trim($lines[$i+1]);
                    
                    // Bila format masih terbaca normal "Nama : VALUE"
                    if (preg_match('/Nama\s*[:\/]?\s*([A-Z\s\.,\']+)/i', $next, $m)) {
                        $name = trim($m[1]);
                        if (strlen($name) > 2 && strtoupper($name) !== 'NAMA') return $this->cleanName($name);
                    }
                    
                    // Bila label terpisah "Nama" lalu nilai di bawahnya
                    if (preg_match('/^Nama\s*$/i', $next) && isset($lines[$i+2])) {
                        $next2 = trim($lines[$i+2]);
                        if (strlen($next2) > 2 && !preg_match('/^(tempat|ttl|jenis|alamat|agama|status|pekerjaan|nik|no|provinsi|kabupaten|kota)/i', $next2) && !preg_match('/,$/', $next2)) {
                            return $this->cleanName($next2);
                        }
                    }
                    
                    // Bila label "Nama" HILANG dan OCR langsung lompat membaca nilai namanya
                    if (strlen($next) > 2 && !preg_match('/^(nama|tempat|ttl|jenis|alamat|agama|status|pekerjaan|nik|no|provinsi|kabupaten|kota)/i', $next) && !preg_match('/,$/', $next)) {
                        // Kita asumsikan ini adalah nama yang valid
                        return $this->cleanName($next);
                    }
                }
            }
        }

        // Strategy 2: Standard fallback line-by-line
        foreach ($lines as $i => $line) {
            $line = trim($line);

            // KTP: cari baris yang mengandung Nama
            if (preg_match('/^:?\s*Nama\b/i', $line)) {
                if (preg_match('/Nama\s*[:\/]?\s*([A-Z\s\.,\']+)/i', $line, $m)) {
                    $name = trim($m[1]);
                    if (strlen($name) > 2 && strtoupper($name) !== 'NAMA') return $this->cleanName($name);
                }
                if (isset($lines[$i + 1])) {
                    $next = trim($lines[$i + 1]);
                    if (strlen($next) > 2 && !preg_match('/^(tempat|ttl|jenis|alamat|agama|status|pekerjaan|nik|no|provinsi|kabupaten|kota)/i', $next) && !preg_match('/,$/', $next)) {
                        return $this->cleanName($next);
                    }
                }
            }

            // SIM: cari "Nama/Name"
            if (preg_match('/Nama\s*\/\s*Name\s*[:\/]?\s*(.+)/i', $line, $m)) {
                $name = trim($m[1]);
                if (strlen($name) > 2) return $this->cleanName($name);
            }

            // SIM (Format Baru/Smart SIM): Angka 1. lalu Nama
            if ($isSim && preg_match('/^1[\.\-\:]?\s+([a-zA-Z\s\.\']+)/', $line, $m)) {
                $name = trim($m[1]);
                if (strlen($name) > 2) return $this->cleanName($name);
            }
        }

        // Strategy 3: Global fallback matching Nama ...
        if (preg_match('/\bNama\b\s*[:\/]?\s*([A-Z][A-Z\s\.,\']+)/i', $text, $m)) {
            $name = trim($m[1]);
            if (strlen($name) > 2 && strtoupper($name) !== 'NAMA') return $this->cleanName($name);
        }

        return null;
    }

    private function cleanName(string $name): string
    {
        $name = preg_replace('/[^a-zA-Z\s.,]/', '', $name);
        $name = preg_replace('/\s+/', ' ', $name);
        return strtoupper(trim($name));
    }
}
