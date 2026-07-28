<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

//OCR.space API integration
class OCRSpaceService
{
    public function extractText($image)
    {
        $bestResult = null;
        
        for ($pass = 1; $pass <= 4; $pass++) {
            if ($pass === 1) {
                $filePath = $image->getRealPath();
            } else {
                $filePath = $this->preprocessImage($image, $pass);
            }
            
            $result = $this->callApi($filePath, $image->getClientOriginalName());
            
            if ($pass > 1 && $filePath !== $image->getRealPath() && file_exists($filePath)) {
                @unlink($filePath);
            }
            
            // Evaluasi keberhasilan OCR
            if (!isset($result['IsErroredOnProcessing']) || !$result['IsErroredOnProcessing']) {
                $text = $result['ParsedResults'][0]['ParsedText'] ?? '';
                $textUpper = strtoupper($text);
                $isKtp = preg_match('/(\bKTP\b|\bPROVINSI\b|\bNIK\b|KARTU\s+TANDA\s+PENDUDUK|\b\d{16}\b)/i', $textUpper);
                $isSim = preg_match('/(SURAT\s+IZIN\s+MENGEMUDI|DRIVING\s+LICENSE|\bPOLRI\b|\bKORLANTAS\b)/i', $textUpper);
                
                if ($isKtp || $isSim) {
                    try {
                        // Jika nama berhasil ditarik, ini adalah foto yang terbaca dengan sempurna!
                        $name = $this->extractName($text);
                        if (!empty($name)) {
                            return $result; 
                        }
                    } catch (\Exception $e) {}
                    
                    // KTP terdeteksi tapi nama belum terbaca utuh. Simpan sebagai cadangan terbaik.
                    $bestResult = $result;
                }
            }
        }
        
        // Jika 3x percobaan gagal mendapatkan nama, kembalikan hasil terbaik (atau hasil terakhir)
        return $bestResult ?? $result;
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

    private function preprocessImage($image, $pass)
    {
        $path = $image->getRealPath();
        $mime = mime_content_type($path);
        
        switch ($mime) {
            case 'image/jpeg': $img = @imagecreatefromjpeg($path); break;
            case 'image/png': $img = @imagecreatefrompng($path); break;
            default: return $path;
        }

        if (!$img) return $path;
        
        $width = imagesx($img);
        $height = imagesy($img);

        if ($pass === 2) {
            // PASS 2: Grayscale + Contrast -25 + Gentle Sharpen (Untuk foto gelap/sedikit blur)
            imagefilter($img, IMG_FILTER_GRAYSCALE);
            imagefilter($img, IMG_FILTER_CONTRAST, -25); 
            $sharpenMatrix = [ [0, -1, 0], [-1, 12, -1], [0, -1, 0] ];
            imageconvolution($img, $sharpenMatrix, 8, 0);
        } else if ($pass === 3) {
            // PASS 3: Crop Kiri 75% + Kurangi Brightness + Kontras Tinggi + Upscale (Untuk foto over-expose / terlalu jauh)
            // Teks KTP mayoritas berada di 75% area kiri. Ini membuang foto wajah agar OCR lebih fokus ke teks.
            $cropWidth = (int)($width * 0.75);
            $cropped = imagecrop($img, ['x' => 0, 'y' => 0, 'width' => $cropWidth, 'height' => $height]);
            if ($cropped !== false) {
                imagedestroy($img);
                $img = $cropped;
                $width = $cropWidth;
            }
            
            // Upscale untuk memperjelas teks (jika angle terlalu jauh)
            $newWidth = 1500;
            if ($width < $newWidth) {
                $newHeight = (int)($height * ($newWidth / $width));
                $resized = imagecreatetruecolor($newWidth, $newHeight);
                imagecopyresampled($resized, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                imagedestroy($img);
                $img = $resized;
            }
            
            imagefilter($img, IMG_FILTER_GRAYSCALE);
            imagefilter($img, IMG_FILTER_BRIGHTNESS, -20); // Gelapkan area putih yang silau (over-exposed)
            imagefilter($img, IMG_FILTER_CONTRAST, -40); // Tarik warna abu-abu agar hitam kembali
            $sharpenMatrix = [ [0, -1, 0], [-1, 8, -1], [0, -1, 0] ]; // Heavy Sharpen
            imageconvolution($img, $sharpenMatrix, 4, 0);
        } else if ($pass === 4) {
            // PASS 4: Brightness Up + Contrast Down (Untuk foto sangat gelap/suram seperti Dian Yulia)
            imagefilter($img, IMG_FILTER_GRAYSCALE);
            imagefilter($img, IMG_FILTER_BRIGHTNESS, 20); // Terangkan gambar gelap
            imagefilter($img, IMG_FILTER_CONTRAST, -30); // Tarik kontras teks
            $sharpenMatrix = [ [0, -1, 0], [-1, 8, -1], [0, -1, 0] ];
            imageconvolution($img, $sharpenMatrix, 4, 0);
        }

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
            // Jika dokumen sama sekali bukan KTP/SIM (tidak ada satupun kata kunci)
            throw new \Exception("pastikan file yang diupload berupa sim/ktp");
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
                        if (strlen($next2) > 2 && !preg_match('/^(tempat|empat|ttl|jenis|alamat|agama|status|pekerjaan|nik|no\b|provinsi|kabupaten|kota|paltgi|peltgl|lahir|lah\b|tgl\b|gol\b|darah|rt|rw|desa|kel|kec|kewarganegaraan|berlaku)/i', $next2) && !preg_match('/,$/', $next2)) {
                            return $this->cleanName($next2);
                        }
                    }
                    
                    // Bila label "Nama" HILANG dan OCR langsung lompat membaca nilai namanya
                    if (strlen($next) > 2 && !preg_match('/^(nama|tempat|empat|ttl|jenis|alamat|agama|status|pekerjaan|nik|no\b|provinsi|kabupaten|kota|paltgi|peltgl|lahir|lah\b|tgl\b|gol\b|darah|rt|rw|desa|kel|kec|kewarganegaraan|berlaku)/i', $next) && !preg_match('/,$/', $next)) {
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
                    if (strlen($next) > 2 && !preg_match('/^(tempat|empat|ttl|jenis|alamat|agama|status|pekerjaan|nik|no\b|provinsi|kabupaten|kota|paltgi|peltgl|lahir|lah\b|tgl\b|gol\b|darah|rt|rw|desa|kel|kec|kewarganegaraan|berlaku)/i', $next) && !preg_match('/,$/', $next)) {
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

        // Strategy 4: Extreme fallback - Cari baris pertama yang murni UPPERCASE (selain elemen KTP standar)
        foreach ($lines as $line) {
            $line = trim($line);
            // Karakter yang diizinkan untuk nama: Huruf besar, spasi, titik, koma, tanda petik tunggal.
            if (strlen($line) > 3 && preg_match('/^[A-Z\s\.,\']+$/', $line)) {
                $skipWords = ['PROVINSI', 'KABUPATEN', 'KOTA', 'ISLAM', 'KRISTEN', 'KATHOLIK', 'KATOLIK', 'HINDU', 'BUDHA', 'KONGHUCU', 'KAWIN', 'BELUM KAWIN', 'CERAI', 'WNI', 'WNA', 'LAKI-LAKI', 'PEREMPUAN', 'KARTU TANDA PENDUDUK', 'GOLONGAN DARAH', 'SEUMUR HIDUP', 'PALTGI', 'PELTGL', 'GOL', 'TGL', 'LAHIR', 'LAH '];
                $isSkip = false;
                foreach ($skipWords as $word) {
                    if (strpos($line, $word) !== false) {
                        $isSkip = true;
                        break;
                    }
                }
                if (!$isSkip) {
                    return $this->cleanName($line);
                }
            }
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
