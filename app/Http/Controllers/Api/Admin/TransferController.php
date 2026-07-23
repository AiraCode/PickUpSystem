<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transfer;
use App\Http\Requests\Admin\StoreTransferRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class TransferController extends Controller
{
    public function index(): JsonResponse
    {
        $transfers = Transfer::with(['receipt', 'user'])->get();

        return response()->json([
            'message' => 'Daftar transfer berhasil diambil',
            'data' => $transfers,
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $transfer = Transfer::with(['receipt', 'user'])->findOrFail($id);

        return response()->json([
            'message' => 'Detail transfer berhasil diambil',
            'data' => $transfer,
        ]);
    }

    public function update(StoreTransferRequest $request, int $id): JsonResponse
    {
        $transfer = Transfer::findOrFail($id);
        $path = $request->file('proof_image')->store('transfers', 'public');

        if ($transfer->proof_image && Storage::disk('public')->exists($transfer->proof_image)) {
            Storage::disk('public')->delete($transfer->proof_image);
        }

        $transfer->update([
            'proof_image' => $path,
            'status' => 'success',
            'transfer_date' => Carbon::now(),
        ]);

        return response()->json([
            'message' => 'Bukti transfer berhasil diunggah',
            'data' => $transfer,
        ]);
    }
}
