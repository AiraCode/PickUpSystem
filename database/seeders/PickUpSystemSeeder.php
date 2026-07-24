<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class PickUpSystemSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('accus_has_receipts')->truncate();
        DB::table('cities_has_accus')->truncate();
        DB::table('transfers')->truncate();
        DB::table('shipments')->truncate();
        DB::table('receipts')->truncate();
        DB::table('orders')->truncate();
        DB::table('customers')->truncate();
        DB::table('storages')->truncate();
        DB::table('banks')->truncate();
        DB::table('users')->truncate();
        DB::table('accus')->truncate();
        DB::table('brands')->truncate();
        DB::table('cities')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $now = Carbon::now();

        // 1. Kota (9 Area Service)
        $citiesData = [
            ['id' => 1, 'name' => 'Jakarta', 'lat' => -6.2088, 'long' => 106.8456, 'percentage' => 82.5],
            ['id' => 2, 'name' => 'Surabaya', 'lat' => -7.2575, 'long' => 112.7521, 'percentage' => 85.0],
            ['id' => 3, 'name' => 'Bandung', 'lat' => -6.9175, 'long' => 107.6191, 'percentage' => 87.5],
            ['id' => 4, 'name' => 'Semarang', 'lat' => -6.9667, 'long' => 110.4167, 'percentage' => 90.0],
            ['id' => 5, 'name' => 'Medan', 'lat' => 3.5952, 'long' => 98.6722, 'percentage' => 80.0],
            ['id' => 6, 'name' => 'Makassar', 'lat' => -5.1477, 'long' => 119.4327, 'percentage' => 82.5],
            ['id' => 7, 'name' => 'Bali', 'lat' => -8.6705, 'long' => 115.2126, 'percentage' => 85.0],
            ['id' => 8, 'name' => 'Malang', 'lat' => -7.9666, 'long' => 112.6326, 'percentage' => 87.5],
            ['id' => 9, 'name' => 'Yogyakarta', 'lat' => -7.7956, 'long' => 110.3695, 'percentage' => 90.0],
        ];
        foreach ($citiesData as $c) {
            DB::table('cities')->insert([
                'id' => $c['id'],
                'name' => $c['name'],
                'percentage' => $c['percentage'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // 2. Brands (8 Brands)
        $brandsData = ['GS Astra', 'Yuasa', 'Incoe', 'Delkor', 'Amaron', 'Bosch', 'Rocket', 'Massiv'];
        foreach ($brandsData as $idx => $bName) {
            DB::table('brands')->insert([
                'id' => $idx + 1,
                'name' => $bName,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // 0. Settings
        DB::table('settings')->truncate();
        DB::table('settings')->insert([
            ['key' => 'lme', 'value' => '2100', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'kurs', 'value' => '16000', 'created_at' => $now, 'updated_at' => $now],
        ]);

        // 3. Accus (12 Tipe Aki dengan berat_kering dalam kg)
        $accusData = [
            ['id' => 1, 'name' => 'NS40Z', 'brands_id' => 1, 'berat_kering' => 5.20],
            ['id' => 2, 'name' => 'NS60', 'brands_id' => 2, 'berat_kering' => 6.10],
            ['id' => 3, 'name' => 'N50Z', 'brands_id' => 3, 'berat_kering' => 7.00],
            ['id' => 4, 'name' => 'NS70', 'brands_id' => 1, 'berat_kering' => 8.20],
            ['id' => 5, 'name' => 'N70Z', 'brands_id' => 4, 'berat_kering' => 9.50],
            ['id' => 6, 'name' => 'DIN 55559', 'brands_id' => 5, 'berat_kering' => 11.80],
            ['id' => 7, 'name' => 'YTX9-BS', 'brands_id' => 6, 'berat_kering' => 4.50],
            ['id' => 8, 'name' => 'GTZ5S', 'brands_id' => 7, 'berat_kering' => 3.80],
            ['id' => 9, 'name' => 'MF 55D23L', 'brands_id' => 8, 'berat_kering' => 10.20],
            ['id' => 10, 'name' => 'NS40ZL', 'brands_id' => 1, 'berat_kering' => 5.40],
            ['id' => 11, 'name' => 'N100', 'brands_id' => 2, 'berat_kering' => 15.50],
            ['id' => 12, 'name' => 'N150', 'brands_id' => 3, 'berat_kering' => 22.00],
        ];
        foreach ($accusData as $a) {
            DB::table('accus')->insert([
                'id' => $a['id'],
                'name' => $a['name'],
                'brands_id' => $a['brands_id'],
                'berat_kering' => $a['berat_kering'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // 4. Hubungan Aki per Kota (cities_has_accus)
        $cityAccuRows = [];
        foreach ($citiesData as $c) {
            foreach ($accusData as $a) {
                $cityAccuRows[] = [
                    'cities_id' => $c['id'],
                    'accus_id' => $a['id'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }
        DB::table('cities_has_accus')->insert($cityAccuRows);

        // 5. Admin Users
        $usersData = [
            ['id' => 1, 'name' => 'Admin Test', 'password' => Hash::make('password123')],
            ['id' => 2, 'name' => 'Admin Utama', 'password' => Hash::make('password123')],
            ['id' => 3, 'name' => 'Budi Staf Admin', 'password' => Hash::make('password123')],
            ['id' => 4, 'name' => 'Siti Ops Surabaya', 'password' => Hash::make('password123')],
            ['id' => 5, 'name' => 'Dedi Ops Bandung', 'password' => Hash::make('password123')],
        ];
        foreach ($usersData as $u) {
            DB::table('users')->insert([
                'id' => $u['id'],
                'name' => $u['name'],
                'password' => $u['password'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // 6. Banks
        $banksData = ['BCA', 'Mandiri', 'BNI', 'BRI', 'CIMB Niaga'];
        foreach ($banksData as $idx => $bName) {
            DB::table('banks')->insert([
                'id' => $idx + 1,
                'name' => $bName,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // 7. Gudang / Storages
        $storagesData = [
            ['id' => 1, 'name' => 'Gudang Pusat Jakarta', 'address' => 'Jl. Merdeka Raya No. 45, Jakarta', 'lat' => -6.1751, 'long' => 106.8272],
            ['id' => 2, 'name' => 'Gudang Surabaya Rungkut', 'address' => 'Kawasan Industri Rungkut, Surabaya', 'lat' => -7.3294, 'long' => 112.7661],
            ['id' => 3, 'name' => 'Gudang Bandung Soekarno-Hatta', 'address' => 'Jl. Soekarno-Hatta No. 120, Bandung', 'lat' => -6.9382, 'long' => 107.6432],
            ['id' => 4, 'name' => 'Gudang Medan Amplas', 'address' => 'Jl. Sisingamangaraja, Medan', 'lat' => 3.5412, 'long' => 98.7012],
        ];
        foreach ($storagesData as $s) {
            DB::table('storages')->insert([
                'id' => $s['id'],
                'name' => $s['name'],
                'address' => $s['address'],
                'lat' => $s['lat'],
                'long' => $s['long'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // 8. Generate 1,250 Dummy Transactions (Spread across 2024, 2025, 2026)
        $totalTransactions = 1250;
        
        $firstNames = ['Agus', 'Budi', 'Candra', 'Dewi', 'Eko', 'Fajar', 'Gita', 'Hendra', 'Irfan', 'Joko', 'Kartika', 'Lestari', 'Mega', 'Novi', 'Oscar', 'Pratama', 'Rian', 'Sari', 'Taufik', 'Utami', 'Vina', 'Wawan', 'Yudi', 'Zainal'];
        $lastNames = ['Santoso', 'Wijaya', 'Pratama', 'Hidayat', 'Kusuma', 'Saputra', 'Laksana', 'Nugroho', 'Wibowo', 'Firmansyah', 'Suryono', 'Utomo', 'Subagyo', 'Gunawan', 'Setiawan'];
        $streets = ['Jl. Sudirman', 'Jl. Ahmad Yani', 'Jl. Diponegoro', 'Jl. Gajah Mada', 'Jl. Pahlawan', 'Jl. Gatot Subroto', 'Jl. Pemuda', 'Jl. Basuki Rahmat', 'Jl. Veteran', 'Jl. Raya Darmo'];

        $statuses = ['completed', 'completed', 'completed', 'completed', 'completed', 'completed', 'completed', 'processing', 'pending', 'cancelled'];

        $customersBatch = [];
        $ordersBatch = [];
        $receiptsBatch = [];
        $accusHasReceiptsBatch = [];
        $shipmentsBatch = [];
        $transfersBatch = [];
        $shipmentIdCounter = 1;
        $transferIdCounter = 1;

        for ($i = 1; $i <= $totalTransactions; $i++) {
            $fn = $firstNames[array_rand($firstNames)];
            $ln = $lastNames[array_rand($lastNames)];
            $custName = "$fn $ln";
            
            $cityObj = $citiesData[array_rand($citiesData)];
            $street = $streets[array_rand($streets)];
            $streetNum = rand(1, 199);
            $pickupAddr = "$street No. $streetNum, {$cityObj['name']}";
            
            // Random Date Generation across 2024 (35%), 2025 (45%), 2026 (20%)
            $yearRoll = rand(1, 100);
            if ($yearRoll <= 35) {
                $year = 2024;
                $month = rand(1, 12);
            } elseif ($yearRoll <= 80) {
                $year = 2025;
                $month = rand(1, 12);
            } else {
                $year = 2026;
                $month = rand(1, 7); // Jan - Jul 2026
            }
            $day = rand(1, 28);
            $hour = rand(8, 20);
            $minute = rand(0, 59);

            $createdAt = Carbon::create($year, $month, $day, $hour, $minute);
            $updatedAt = (clone $createdAt)->addHours(rand(1, 48));

            $bankId = rand(1, count($banksData));
            $accountNum = rand(1000000000, 9999999999);
            $ktpNum = '3578' . rand(1000000000, 9999999999);
            $phone = '08' . rand(111111111, 999999999);

            $customersBatch[] = [
                'id' => $i,
                'name' => $custName,
                'address' => $pickupAddr,
                'address_note' => rand(0, 1) ? 'Dekat masjid / patung' : null,
                'lat' => $cityObj['lat'] + (rand(-50, 50) / 10000),
                'long' => $cityObj['long'] + (rand(-50, 50) / 10000),
                'ktp' => $ktpNum,
                'account_name' => $custName,
                'account_number' => (string)$accountNum,
                'phone_number' => $phone,
                'flag' => 1,
                'banks_id' => $bankId,
                'created_at' => $createdAt,
                'updated_at' => $updatedAt,
            ];

            $status = $statuses[array_rand($statuses)];
            $cancelReason = ($status === 'cancelled') ? 'Customer membatalkan pesanan (ganti pikiran)' : null;

            $ordersBatch[] = [
                'id' => $i,
                'cities_id' => $cityObj['id'],
                'pickup_address' => $pickupAddr,
                'pickup_address_note' => 'Lokasi penyerahan barang',
                'pickup_lat' => $cityObj['lat'] + (rand(-50, 50) / 10000),
                'pickup_long' => $cityObj['long'] + (rand(-50, 50) / 10000),
                'status' => $status,
                'cancel_reason' => $cancelReason,
                'customers_id' => $i,
                'created_at' => $createdAt,
                'updated_at' => $updatedAt,
            ];

            // Pick 1 to 2 random distinct accus
            $accuKeys = array_rand($accusData, rand(1, 2));
            if (!is_array($accuKeys)) {
                $accuKeys = [$accuKeys];
            }
            $accuItem1 = $accusData[$accuKeys[0]];
            $qty1 = rand(1, 3);
            $pricePerKg = (2100 * 16000 * 80.0) / 1000.0;
            $price1 = (int) round($pricePerKg * $accuItem1['berat_kering']);
            $totalAmount = $price1 * $qty1;

            if (isset($accuKeys[1])) {
                $accuItem2 = $accusData[$accuKeys[1]];
                $qty2 = rand(1, 2);
                $price2 = (int) round($pricePerKg * $accuItem2['berat_kering']);
                $totalAmount += $price2 * $qty2;
            } else {
                $accuItem2 = null;
            }

            $receiptStatus = ($status === 'completed') ? 'PAID' : (($status === 'cancelled') ? 'CANCELLED' : 'UNPAID');
            $priceReceived = ($status === 'completed') ? $totalAmount : (($status === 'processing') ? rand(0, $totalAmount) : 0);
            $priceOwed = max(0, $totalAmount - $priceReceived);

            $receiptNumber = sprintf('RCP-%04d-%05d', $year, $i);

            $receiptsBatch[] = [
                'id' => $i,
                'receipt_number' => $receiptNumber,
                'date' => $createdAt->toDateString(),
                'status' => $receiptStatus,
                'price_received' => $priceReceived,
                'price_owed' => $priceOwed,
                'users_id' => rand(1, count($usersData)),
                'orders_id' => $i,
                'created_at' => $createdAt,
                'updated_at' => $updatedAt,
            ];

            $accusHasReceiptsBatch[] = [
                'accus_id' => $accuItem1['id'],
                'receipts_id' => $i,
                'amount' => $qty1,
                'created_at' => $createdAt,
                'updated_at' => $updatedAt,
            ];

            if ($accuItem2 && isset($qty2)) {
                $accusHasReceiptsBatch[] = [
                    'accus_id' => $accuItem2['id'],
                    'receipts_id' => $i,
                    'amount' => $qty2,
                    'created_at' => $createdAt,
                    'updated_at' => $updatedAt,
                ];
            }

            if ($status === 'completed' || $status === 'processing') {
                $shipmentStatus = ($status === 'completed') ? 'DELIVERED' : 'IN_TRANSIT';
                $pickupDate = (clone $createdAt)->addHours(rand(2, 8));
                $receivedDate = (clone $pickupDate)->addHours(rand(12, 36));

                $shipmentsBatch[] = [
                    'id' => $shipmentIdCounter++,
                    'storages_id' => rand(1, count($storagesData)),
                    'status' => $shipmentStatus,
                    'pickup_date' => $pickupDate,
                    'received_date' => $receivedDate,
                    'receipts_id' => $i,
                    'created_at' => $createdAt,
                    'updated_at' => $updatedAt,
                ];

                if ($status === 'completed') {
                    $transfersBatch[] = [
                        'id' => $transferIdCounter++,
                        'receipts_id' => $i,
                        'users_id' => rand(1, count($usersData)),
                        'amount' => (float)$totalAmount,
                        'transfer_date' => $receivedDate ?? $updatedAt,
                        'status' => 'COMPLETED',
                        'proof_image' => 'img/default-accu.png',
                        'created_at' => $createdAt,
                        'updated_at' => $updatedAt,
                    ];
                }
            }

            // Insert in chunks of 250 records to keep memory clean and fast
            if (count($customersBatch) >= 250) {
                DB::table('customers')->insert($customersBatch);
                DB::table('orders')->insert($ordersBatch);
                DB::table('receipts')->insert($receiptsBatch);
                DB::table('accus_has_receipts')->insert($accusHasReceiptsBatch);
                if (!empty($shipmentsBatch)) DB::table('shipments')->insert($shipmentsBatch);
                if (!empty($transfersBatch)) DB::table('transfers')->insert($transfersBatch);

                $customersBatch = [];
                $ordersBatch = [];
                $receiptsBatch = [];
                $accusHasReceiptsBatch = [];
                $shipmentsBatch = [];
                $transfersBatch = [];
            }
        }

        // Insert remaining rows
        if (!empty($customersBatch)) {
            DB::table('customers')->insert($customersBatch);
            DB::table('orders')->insert($ordersBatch);
            DB::table('receipts')->insert($receiptsBatch);
            DB::table('accus_has_receipts')->insert($accusHasReceiptsBatch);
            if (!empty($shipmentsBatch)) DB::table('shipments')->insert($shipmentsBatch);
            if (!empty($transfersBatch)) DB::table('transfers')->insert($transfersBatch);
        }
    }
}
