<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CustomerController extends Controller
{
    public function index(): JsonResponse
    {
        $customers = Customer::with('bank')->get();

        return response()->json([
            'message' => 'Daftar customer berhasil diambil',
            'data' => $customers,
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $customer = Customer::with('bank')->findOrFail($id);

        return response()->json([
            'message' => 'Detail customer berhasil diambil',
            'data' => $customer,
        ]);
    }

    public function updateFlag(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'flag' => 'required|integer|in:0,1',
        ]);

        $customer = Customer::findOrFail($id);
        $customer->update(['flag' => $request->flag]);

        return response()->json([
            'message' => 'Status customer berhasil diperbarui',
            'data' => $customer,
        ]);
    }

    /*
    public function verifyKtp(Request $request): JsonResponse
    {
        $request->validate([
            'ktp_image' => 'required|image|mimes:jpeg,png,jpg|max:4096',
            'expected_name' => 'required|string',
        ]);

        $expectedName = strtoupper(trim($request->expected_name));
        $ktpFile = $request->file('ktp_image');

        // Kirim ke API OCR.space (gratis)
        $response = Http::attach(
            'file', file_get_contents($ktpFile), $ktpFile->getClientOriginalName()
        )->post('https://api.ocr.space/parse/image', [
            'apikey' => 'helloworld', // Ganti key gratis dari ocr.space jika error limit
            'language' => 'eng',
            'isOverlayRequired' => false,
        ]);

        $result = $response->json();
        $extractedText = '';

        if (!empty($result['ParsedResults'])) {
            $extractedText = strtoupper($result['ParsedResults'][0]['ParsedText']);
        }

        // Cek kesamaan nama (harus sama persis 100%)
        $isMatch = strpos($extractedText, $expectedName) !== false;

        // Catatan: Kalau mau  pakai sistem kemiripan teks (misal 80% mirip),
        // similar_text($expectedName, $extractedText, $percent);
        // $isMatch = $percent >= 80;

        return response()->json([
            'message' => 'Proses verifikasi KTP selesai',
            'is_match' => $isMatch,
            'expected_name' => $expectedName,
            'extracted_text' => $extractedText,
        ]);
    }
    */
}
