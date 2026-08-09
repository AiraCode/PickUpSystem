<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Activity;
use App\Models\Order;
use App\Models\Warehouse;
use Carbon\Carbon;

class ActivitySeeder extends Seeder
{
    public function run(): void
    {
        // Clear old dummy activities
        Activity::truncate();

        $orders = Order::with(['customer', 'city', 'warehouse'])->latest()->take(10)->get();
        $warehouses = Warehouse::take(5)->get();

        $now = Carbon::now();
        $activities = [];

        // 1. Order created activities
        if ($orders->isNotEmpty()) {
            $o1 = $orders->first();
            $customerName1 = $o1->customer->name ?? 'Budi Santoso';
            $cityName1 = $o1->city->name ?? 'Surabaya';
            $activities[] = [
                'type' => 'order_created',
                'title' => 'Pesanan Baru #' . $o1->id,
                'description' => 'Pelanggan ' . $customerName1 . ' membuat pesanan baru dari ' . $cityName1,
                'related_id' => $o1->id,
                'related_type' => Order::class,
                'created_at' => $now->copy()->subMinutes(6),
                'updated_at' => $now->copy()->subMinutes(6),
            ];
        }

        // 2. Status updated to processing
        if ($orders->count() >= 2) {
            $o2 = $orders->get(1);
            $activities[] = [
                'type' => 'order_status_updated',
                'title' => 'Update Status Pesanan #' . $o2->id,
                'description' => 'Admin memperbarui status pesanan menjadi Sedang Dijemput / Processing',
                'related_id' => $o2->id,
                'related_type' => Order::class,
                'created_at' => $now->copy()->subMinutes(22),
                'updated_at' => $now->copy()->subMinutes(22),
            ];
        }

        // 3. Stock threshold reached in warehouse
        if ($warehouses->isNotEmpty()) {
            $w1 = $warehouses->first();
            $wName1 = preg_match('/^gudang/i', $w1->name) ? $w1->name : 'Gudang ' . $w1->name;
            $activities[] = [
                'type' => 'stock_threshold_reached',
                'title' => 'Stok Gudang Siap Diambil',
                'description' => $wName1 . ' telah mencapai akumulasi 24 unit aki (siap penjemputan pusat)',
                'related_id' => $w1->id,
                'related_type' => Warehouse::class,
                'created_at' => $now->copy()->subMinutes(55),
                'updated_at' => $now->copy()->subMinutes(55),
            ];
        }

        // 4. Order item edit accepted by customer
        if ($orders->count() >= 3) {
            $o3 = $orders->get(2);
            $customerName3 = $o3->customer->name ?? 'Siti Aminah';
            $activities[] = [
                'type' => 'order_edit_accepted',
                'title' => 'Perubahan Pesanan Disetujui #' . $o3->id,
                'description' => 'Pelanggan ' . $customerName3 . ' menyetujui penyesuaian rincian item aki oleh admin',
                'related_id' => $o3->id,
                'related_type' => Order::class,
                'created_at' => $now->copy()->subHours(2),
                'updated_at' => $now->copy()->subHours(2),
            ];
        }

        // 5. Order arrived at warehouse
        if ($orders->count() >= 4) {
            $o4 = $orders->get(3);
            $warehouseName4 = $o4->warehouse->name ?? 'Gudang Utama';
            $wName4 = preg_match('/^gudang/i', $warehouseName4) ? $warehouseName4 : 'Gudang ' . $warehouseName4;
            $activities[] = [
                'type' => 'order_status_updated',
                'title' => 'Aki Tiba di Gudang #' . $o4->id,
                'description' => 'Kurir telah mengantarkan aki pesanan ke ' . $wName4,
                'related_id' => $o4->id,
                'related_type' => Order::class,
                'created_at' => $now->copy()->subHours(4),
                'updated_at' => $now->copy()->subHours(4),
            ];
        }

        // 6. Central pickup completed
        if ($warehouses->count() >= 2) {
            $w2 = $warehouses->get(1);
            $wName2 = preg_match('/^gudang/i', $w2->name) ? $w2->name : 'Gudang ' . $w2->name;
            $activities[] = [
                'type' => 'warehouse_pickup_completed',
                'title' => 'Pengambilan Gudang Selesai',
                'description' => 'Pusat telah mengonfirmasi pengambilan 32 unit aki dari ' . $wName2,
                'related_id' => $w2->id,
                'related_type' => Warehouse::class,
                'created_at' => $now->copy()->subHours(7),
                'updated_at' => $now->copy()->subHours(7),
            ];
        }

        // 7. Order completed
        if ($orders->count() >= 5) {
            $o5 = $orders->get(4);
            $activities[] = [
                'type' => 'order_status_updated',
                'title' => 'Pesanan Selesai #' . $o5->id,
                'description' => 'Pembayaran transfer telah diverifikasi dan pesanan selesai',
                'related_id' => $o5->id,
                'related_type' => Order::class,
                'created_at' => $now->copy()->subDay(),
                'updated_at' => $now->copy()->subDay(),
            ];
        }

        // 8. Order items updated by admin
        if ($orders->count() >= 6) {
            $o6 = $orders->get(5);
            $activities[] = [
                'type' => 'order_items_updated',
                'title' => 'Penyesuaian Item Pesanan #' . $o6->id,
                'description' => 'Admin memperbarui estimasi berat kering dan tipe aki pada pesanan',
                'related_id' => $o6->id,
                'related_type' => Order::class,
                'created_at' => $now->copy()->subDays(2),
                'updated_at' => $now->copy()->subDays(2),
            ];
        }

        // 9. Order created from another customer
        if ($orders->count() >= 7) {
            $o7 = $orders->get(6);
            $customerName7 = $o7->customer->name ?? 'Agus Pratama';
            $cityName7 = $o7->city->name ?? 'Jakarta';
            $activities[] = [
                'type' => 'order_created',
                'title' => 'Pesanan Baru #' . $o7->id,
                'description' => 'Pelanggan ' . $customerName7 . ' membuat pesanan baru dari ' . $cityName7,
                'related_id' => $o7->id,
                'related_type' => Order::class,
                'created_at' => $now->copy()->subDays(3),
                'updated_at' => $now->copy()->subDays(3),
            ];
        }

        // Default fallbacks if there are no existing orders in DB yet
        if (empty($activities)) {
            $activities = [
                [
                    'type' => 'order_created',
                    'title' => 'Pesanan Baru #1024',
                    'description' => 'Pelanggan Hendra Gunawan membuat pesanan baru dari Surabaya',
                    'related_id' => 1,
                    'related_type' => Order::class,
                    'created_at' => $now->copy()->subMinutes(5),
                    'updated_at' => $now->copy()->subMinutes(5),
                ],
                [
                    'type' => 'order_status_updated',
                    'title' => 'Update Status Pesanan #1023',
                    'description' => 'Admin memperbarui status pesanan menjadi Sedang Dijemput / Processing',
                    'related_id' => 2,
                    'related_type' => Order::class,
                    'created_at' => $now->copy()->subMinutes(25),
                    'updated_at' => $now->copy()->subMinutes(25),
                ],
                [
                    'type' => 'stock_threshold_reached',
                    'title' => 'Stok Gudang Siap Diambil',
                    'description' => 'Gudang Surabaya Rungkut telah mencapai akumulasi 24 unit aki (siap penjemputan pusat)',
                    'related_id' => 1,
                    'related_type' => Warehouse::class,
                    'created_at' => $now->copy()->subHours(1),
                    'updated_at' => $now->copy()->subHours(1),
                ],
                [
                    'type' => 'order_edit_accepted',
                    'title' => 'Perubahan Pesanan Disetujui #1021',
                    'description' => 'Pelanggan Siti Rahayu menyetujui penyesuaian rincian item aki oleh admin',
                    'related_id' => 3,
                    'related_type' => Order::class,
                    'created_at' => $now->copy()->subHours(3),
                    'updated_at' => $now->copy()->subHours(3),
                ],
                [
                    'type' => 'order_status_updated',
                    'title' => 'Aki Tiba di Gudang #1020',
                    'description' => 'Kurir telah mengantarkan aki pesanan ke Gudang Pusat Jakarta',
                    'related_id' => 4,
                    'related_type' => Order::class,
                    'created_at' => $now->copy()->subHours(5),
                    'updated_at' => $now->copy()->subHours(5),
                ],
                [
                    'type' => 'warehouse_pickup_completed',
                    'title' => 'Pengambilan Gudang Selesai',
                    'description' => 'Pusat telah mengonfirmasi pengambilan 30 unit aki dari Gudang Bandung Soekarno-Hatta',
                    'related_id' => 2,
                    'related_type' => Warehouse::class,
                    'created_at' => $now->copy()->subHours(8),
                    'updated_at' => $now->copy()->subHours(8),
                ],
                [
                    'type' => 'order_status_updated',
                    'title' => 'Pesanan Selesai #1018',
                    'description' => 'Pembayaran transfer telah diverifikasi dan pesanan selesai',
                    'related_id' => 5,
                    'related_type' => Order::class,
                    'created_at' => $now->copy()->subDay(),
                    'updated_at' => $now->copy()->subDay(),
                ],
            ];
        }

        foreach ($activities as $act) {
            Activity::create($act);
        }
    }
}
