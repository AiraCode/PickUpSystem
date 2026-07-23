<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Http\Requests\Admin\StoreShipmentRequest;
use App\Http\Requests\Admin\UpdateShipmentRequest;
use Illuminate\Http\JsonResponse;

class ShipmentController extends Controller
{
    public function index(): JsonResponse
    {
        $shipments = Shipment::with(['warehouse', 'receipt'])->get();

        return response()->json([
            'message' => 'Daftar pengiriman berhasil diambil',
            'data' => $shipments,
        ]);
    }

    public function store(StoreShipmentRequest $request): JsonResponse
    {
        $shipment = Shipment::create($request->validated());

        return response()->json([
            'message' => 'Pengiriman berhasil ditambahkan',
            'data' => $shipment,
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $shipment = Shipment::with(['warehouse', 'receipt'])->findOrFail($id);

        return response()->json([
            'message' => 'Detail pengiriman berhasil diambil',
            'data' => $shipment,
        ]);
    }

    public function update(UpdateShipmentRequest $request, int $id): JsonResponse
    {
        $shipment = Shipment::findOrFail($id);
        $shipment->update($request->validated());

        return response()->json([
            'message' => 'Pengiriman berhasil diperbarui',
            'data' => $shipment,
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $shipment = Shipment::findOrFail($id);
        $shipment->delete();

        return response()->json([
            'message' => 'Pengiriman berhasil dihapus',
        ]);
    }
}
