<?php

namespace App\Services;

use App\Models\User;
use App\Models\Warehouse;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class WarehousePickupReceiptPdfService
{
    /**
     * Generate PDF binary content for the pickup receipt of a warehouse.
     */
    public function generateReceiptPdf(Warehouse $warehouse): string
    {
        // Query untaken battery items currently sitting in this warehouse (before marking as taken, or recently taken)
        $items = DB::table('orders')
            ->join('receipts', 'orders.id', '=', 'receipts.orders_id')
            ->join('accus_has_receipts', 'receipts.id', '=', 'accus_has_receipts.receipts_id')
            ->join('accus', 'accus_has_receipts.accus_id', '=', 'accus.id')
            ->whereIn('orders.status', ['arrived_at_warehouse', 'completed'])
            ->where('orders.storages_id', $warehouse->id)
            ->where('orders.taken_by_central', false)
            ->select(
                'accus.id',
                'accus.name',
                'accus.berat_kering',
                DB::raw('SUM(accus_has_receipts.amount) as total_qty')
            )
            ->groupBy('accus.id', 'accus.name', 'accus.berat_kering')
            ->get();

        $totalQty = 0;
        $totalWeight = 0.0;

        foreach ($items as $item) {
            $qty = (int) ($item->total_qty ?? 0);
            $weight = (float) ($item->berat_kering ?? 0);
            $totalQty += $qty;
            $totalWeight += ($qty * $weight);
        }

        // Get warehouse admin info
        $admin = User::where('warehouse_id', $warehouse->id)
            ->where('role', 'warehouse')
            ->first();

        $adminName = $admin ? $admin->name : 'Admin Gudang ' . $warehouse->name;
        $adminEmail = $admin ? $admin->email : '-';

        // Logo Base64 encoding for Dompdf
        $logoBase64 = '';
        $logoPath = public_path('img/logo_admin1-removebg-preview.png');
        if (file_exists($logoPath)) {
            $logoBase64 = base64_encode(file_get_contents($logoPath));
        }

        $receiptDate = \Carbon\Carbon::now('Asia/Jakarta')->format('d F Y, H:i') . ' WIB';

        $data = [
            'warehouse' => $warehouse,
            'items' => $items,
            'totalQty' => $totalQty,
            'totalWeight' => $totalWeight,
            'adminName' => $adminName,
            'adminEmail' => $adminEmail,
            'logoBase64' => $logoBase64,
            'receiptDate' => $receiptDate,
        ];

        return Pdf::loadView('pdf.warehouse_pickup_receipt', $data)
            ->setPaper('a4', 'portrait')
            ->output();
    }
}
