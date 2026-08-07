<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class WarehouseStockNotificationService
{
    public function notifyWarehouseReady(Warehouse $warehouse): void
    {
        $untaken = DB::table('orders')
            ->join('receipts', 'orders.id', '=', 'receipts.orders_id')
            ->join('accus_has_receipts', 'receipts.id', '=', 'accus_has_receipts.receipts_id')
            ->whereIn('orders.status', ['arrived_at_warehouse', 'completed'])
            ->where('orders.storages_id', $warehouse->id)
            ->where('orders.taken_by_central', false)
            ->select(DB::raw('SUM(accus_has_receipts.amount) as total_untaken'))
            ->value('total_untaken');

        $untaken = (int) $untaken;
        if ($untaken < 20) {
            return;
        }

        $alreadyNotified = Activity::where('type', 'warehouse_ready')
            ->where('related_type', Warehouse::class)
            ->where('related_id', $warehouse->id)
            ->exists();

        if ($alreadyNotified) {
            return;
        }

        $warehouseAdmin = User::where('warehouse_id', $warehouse->id)
            ->where('role', 'warehouse')
            ->first();

        $centralEmails = User::where('role', 'central')
            ->pluck('email')
            ->filter(function ($email) {
                return filter_var($email, FILTER_VALIDATE_EMAIL);
            })
            ->values()
            ->all();

        if (! $warehouseAdmin || ! filter_var($warehouseAdmin->email, FILTER_VALIDATE_EMAIL) || empty($centralEmails)) {
            return;
        }

        try {
            Mail::raw(
                "Gudang cabang {$warehouse->name} memiliki stok aki \u2265 20 unit.\n\nSilakan cek daftar gudang cabang yang siap diambil dan tekan OK untuk mengurangi stok aki pada gudang.\n\nLink: " . url('/admin/gudang'),
                function ($message) use ($warehouseAdmin, $centralEmails, $warehouse, $untaken) {
                    $message->from($warehouseAdmin->email, $warehouseAdmin->name ?: 'Admin Gudang');
                    $message->to($centralEmails);
                    $message->subject("Gudang {$warehouse->name} Siap Diambil - Stok {$untaken} Aki");
                },
            );
        } catch (\Exception $e) {
            Log::error('Gagal mengirim email notifikasi stok gudang: ' . $e->getMessage());
            return;
        }

        try {
            Activity::create([
                'type' => 'warehouse_ready',
                'title' => 'Daftar gudang cabang dengan jumlah stok ≥ 20 unit.',
                'description' => "Gudang {$warehouse->name} memiliki {$untaken} aki yang siap diambil.",
                'related_id' => $warehouse->id,
                'related_type' => Warehouse::class,
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal mencatat aktivitas notifikasi stok gudang: ' . $e->getMessage());
        }
    }
}
