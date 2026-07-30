<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PickUpSystemSeeder extends Seeder
{
    protected function disableForeignKeyChecks(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');

            return;
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    }

    protected function enableForeignKeyChecks(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON;');

            return;
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    protected function truncateTables(array $tables): void
    {
        foreach ($tables as $table) {
            DB::table($table)->truncate();
        }
    }

    public function run(): void
    {
        $this->disableForeignKeyChecks();
        $this->truncateTables([
            'accus_has_receipts',
            'cities_has_accus',
            'transfers',
            'shipments',
            'receipts',
            'orders',
            'customers',
            'storages',
            'banks',
            'users',
            'accus',
            'brands',
            'cities',
        ]);
        $this->enableForeignKeyChecks();

        $now = Carbon::now();

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

        $brandsData = ['GS Astra', 'Yuasa', 'Incoe', 'Delkor', 'Amaron', 'Bosch', 'Rocket', 'Massiv'];
        foreach ($brandsData as $idx => $bName) {
            DB::table('brands')->insert([
                'id' => $idx + 1,
                'name' => $bName,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        DB::table('settings')->truncate();
        DB::table('settings')->insert([
            ['key' => 'lme', 'value' => '2100', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'kurs', 'value' => '16000', 'created_at' => $now, 'updated_at' => $now],
        ]);

        $accusData = [
            ['id' => 1, 'name' => 'N50', 'brands_id' => 1, 'berat_kering' => 11.80],
            ['id' => 2, 'name' => 'N50Z', 'brands_id' => 1, 'berat_kering' => 13.30],
            ['id' => 3, 'name' => 'NS60', 'brands_id' => 1, 'berat_kering' => 10.10],
            ['id' => 4, 'name' => 'NS70', 'brands_id' => 1, 'berat_kering' => 15.90],
            ['id' => 5, 'name' => 'N70', 'brands_id' => 1, 'berat_kering' => 15.60],
            ['id' => 6, 'name' => 'N70Z', 'brands_id' => 1, 'berat_kering' => 18.60],
            ['id' => 7, 'name' => 'N100', 'brands_id' => 1, 'berat_kering' => 23.60],
            ['id' => 8, 'name' => 'N120', 'brands_id' => 1, 'berat_kering' => 28.60],
            ['id' => 9, 'name' => 'N150', 'brands_id' => 1, 'berat_kering' => 31.60],
            ['id' => 10, 'name' => 'N200', 'brands_id' => 1, 'berat_kering' => 45.40],
            ['id' => 11, 'name' => 'NX100', 'brands_id' => 1, 'berat_kering' => 10.50],
            ['id' => 12, 'name' => 'NX110', 'brands_id' => 1, 'berat_kering' => 14.80],
            ['id' => 13, 'name' => 'NX120', 'brands_id' => 1, 'berat_kering' => 20.30],
            ['id' => 14, 'name' => 'GM-5Z', 'brands_id' => 1, 'berat_kering' => 1.90],
            ['id' => 15, 'name' => 'GTZ-5S', 'brands_id' => 1, 'berat_kering' => 1.40],
            ['id' => 16, 'name' => 'GTZ-6', 'brands_id' => 1, 'berat_kering' => 2.20],
            ['id' => 17, 'name' => 'GTZ-7', 'brands_id' => 1, 'berat_kering' => 2.30],
            ['id' => 18, 'name' => 'YTX-9', 'brands_id' => 1, 'berat_kering' => 3.10],
            ['id' => 19, 'name' => '12N10', 'brands_id' => 1, 'berat_kering' => 3.60],
            ['id' => 20, 'name' => 'NX400-20', 'brands_id' => 1, 'berat_kering' => 54.00],
            ['id' => 21, 'name' => '55530', 'brands_id' => 1, 'berat_kering' => 13.00],
            ['id' => 22, 'name' => '55559-MF', 'brands_id' => 1, 'berat_kering' => 11.10],
            ['id' => 23, 'name' => '58024', 'brands_id' => 1, 'berat_kering' => 15.40],
            ['id' => 24, 'name' => '58024-SMF', 'brands_id' => 1, 'berat_kering' => 18.00],
            ['id' => 25, 'name' => '60038', 'brands_id' => 1, 'berat_kering' => 19.80],
            ['id' => 26, 'name' => '85D23L', 'brands_id' => 1, 'berat_kering' => 17.00],
            ['id' => 27, 'name' => '34B19L', 'brands_id' => 1, 'berat_kering' => 7.80],
            ['id' => 28, 'name' => '32B20L', 'brands_id' => 1, 'berat_kering' => 7.50],
            ['id' => 29, 'name' => '36B20L', 'brands_id' => 1, 'berat_kering' => 9.30],
            ['id' => 30, 'name' => '46B24R', 'brands_id' => 1, 'berat_kering' => 10.10],
            ['id' => 31, 'name' => '55B23L', 'brands_id' => 1, 'berat_kering' => 11.00],
            ['id' => 32, 'name' => '55B24L', 'brands_id' => 1, 'berat_kering' => 10.00],
            ['id' => 33, 'name' => '65B24L', 'brands_id' => 1, 'berat_kering' => 15.90],
            ['id' => 34, 'name' => '55D23L', 'brands_id' => 1, 'berat_kering' => 13.10],
            ['id' => 35, 'name' => '80D26L', 'brands_id' => 1, 'berat_kering' => 14.80],
            ['id' => 36, 'name' => '95D31L', 'brands_id' => 1, 'berat_kering' => 20.30],
            ['id' => 37, 'name' => '105D13L-MF', 'brands_id' => 1, 'berat_kering' => 18.00],
            ['id' => 38, 'name' => '75D31L', 'brands_id' => 1, 'berat_kering' => 18.60],
            ['id' => 39, 'name' => 'DYNEX 105D31R-BH/105D31L', 'brands_id' => 1, 'berat_kering' => 23.60],
            ['id' => 40, 'name' => 'GM-3', 'brands_id' => 1, 'berat_kering' => 1.60],
            ['id' => 41, 'name' => 'GM-7', 'brands_id' => 1, 'berat_kering' => 2.30],
            ['id' => 42, 'name' => 'GTZ-16', 'brands_id' => 1, 'berat_kering' => 4.00],
            ['id' => 43, 'name' => 'YTZ-4V', 'brands_id' => 1, 'berat_kering' => 1.80],
            ['id' => 44, 'name' => 'YTZ-5S', 'brands_id' => 1, 'berat_kering' => 3.10],
            ['id' => 45, 'name' => 'YTZ-6', 'brands_id' => 1, 'berat_kering' => 3.10],
            ['id' => 46, 'name' => 'YB-5L', 'brands_id' => 1, 'berat_kering' => 1.80],
            ['id' => 47, 'name' => 'Y25L', 'brands_id' => 1, 'berat_kering' => 1.10],
            ['id' => 48, 'name' => '58010', 'brands_id' => 1, 'berat_kering' => 18.00],
            ['id' => 49, 'name' => '105D26L', 'brands_id' => 1, 'berat_kering' => 18.00],
            ['id' => 50, 'name' => 'Accu UPS Kecil', 'brands_id' => 1, 'berat_kering' => 2.20],
            ['id' => 51, 'name' => '6V7', 'brands_id' => 1, 'berat_kering' => 1.00],
            ['id' => 52, 'name' => 'NS40', 'brands_id' => 1, 'berat_kering' => 7.80],
            ['id' => 53, 'name' => 'N40Z', 'brands_id' => 1, 'berat_kering' => 9.00],
            ['id' => 54, 'name' => '38B20L', 'brands_id' => 1, 'berat_kering' => 9.30],
            ['id' => 55, 'name' => 'YB7-A', 'brands_id' => 1, 'berat_kering' => 2.8],
            ['id' => 56, 'name' => 'NS40Z', 'brands_id' => 1, 'berat_kering' => 9.30],
            ['id' => 57, 'name' => 'Battery Scraps', 'brands_id' => 1, 'berat_kering' => 1.00],
            ['id' => 58, 'name' => '12N9-4B1M', 'brands_id' => 1, 'berat_kering' => 0.00],
            ['id' => 59, 'name' => '44B19', 'brands_id' => 1, 'berat_kering' => 10.10],
            ['id' => 60, 'name' => '562-19', 'brands_id' => 1, 'berat_kering' => 0.00],
            ['id' => 61, 'name' => '355LN2', 'brands_id' => 1, 'berat_kering' => 13.40],
            ['id' => 62, 'name' => 'N40', 'brands_id' => 1, 'berat_kering' => 7.26],
            ['id' => 63, 'name' => '370LN3', 'brands_id' => 1, 'berat_kering' => 17.99],
            ['id' => 64, 'name' => '75D26L/R', 'brands_id' => 1, 'berat_kering' => 16.55],
            ['id' => 65, 'name' => 'VRLA / AGM 105Ah', 'brands_id' => 1, 'berat_kering' => 29.46],
            ['id' => 66, 'name' => 'VRLA / AGM 92Ah', 'brands_id' => 1, 'berat_kering' => 25.10],
            ['id' => 67, 'name' => 'VRLA / AGM 80Ah', 'brands_id' => 1, 'berat_kering' => 22.84],
            ['id' => 69, 'name' => 'GMZ7-4A', 'brands_id' => 1, 'berat_kering' => 2.04],
            ['id' => 70, 'name' => 'ITZ-5S-BS', 'brands_id' => 1, 'berat_kering' => 1.44],
            ['id' => 71, 'name' => '12N5-3B', 'brands_id' => 1, 'berat_kering' => 1.10],
            ['id' => 72, 'name' => 'IMF5Z-3S', 'brands_id' => 1, 'berat_kering' => 1.88],
            ['id' => 73, 'name' => 'SMF M55565', 'brands_id' => 1, 'berat_kering' => 10.64],
            ['id' => 74, 'name' => 'PMF M60044', 'brands_id' => 1, 'berat_kering' => 19.06],
            ['id' => 75, 'name' => 'SMF M55B24RS', 'brands_id' => 1, 'berat_kering' => 8.46],
            ['id' => 76, 'name' => 'SMF M80D23L', 'brands_id' => 1, 'berat_kering' => 12.10],
            ['id' => 77, 'name' => 'SMF M80D26R', 'brands_id' => 1, 'berat_kering' => 12.40],
            ['id' => 78, 'name' => 'EFB M56L', 'brands_id' => 1, 'berat_kering' => 9.84],
            ['id' => 79, 'name' => 'EFB M56R', 'brands_id' => 1, 'berat_kering' => 9.58],
            ['id' => 80, 'name' => 'EFB S120L', 'brands_id' => 1, 'berat_kering' => 17.06],
            ['id' => 81, 'name' => 'N100ZL-MF', 'brands_id' => 1, 'berat_kering' => 17.82],
            ['id' => 82, 'name' => 'PMF 56219', 'brands_id' => 1, 'berat_kering' => 12.10],
            ['id' => 83, 'name' => 'PMF 57220', 'brands_id' => 1, 'berat_kering' => 13.26],
            ['id' => 84, 'name' => 'PMF 58014', 'brands_id' => 1, 'berat_kering' => 15.82],
            ['id' => 85, 'name' => 'PMF 68032', 'brands_id' => 1, 'berat_kering' => 39.38],
            ['id' => 86, 'name' => 'PMF 73011SHD', 'brands_id' => 1, 'berat_kering' => 48.16],
            ['id' => 87, 'name' => 'SMF 57113', 'brands_id' => 1, 'berat_kering' => 13.48],
        ];
        foreach ($accusData as $a) {
            DB::table('accus')->insert([
                'id' => $a['id'],
                'name' => $a['name'],
                'berat_kering' => $a['berat_kering'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

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

        $banksData = ['BCA', 'Mandiri', 'BNI', 'BRI', 'CIMB Niaga'];
        foreach ($banksData as $idx => $bName) {
            DB::table('banks')->insert([
                'id' => $idx + 1,
                'name' => $bName,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

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

        $totalTransactions = 2537;

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

            $yearRoll = rand(1, 100);
            if ($yearRoll <= 35) {
                $year = 2024;
                $month = rand(1, 12);
            } elseif ($yearRoll <= 80) {
                $year = 2025;
                $month = rand(1, 12);
            } else {
                $year = 2026;
                $month = rand(1, 7);
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
                'account_number' => (string) $accountNum,
                'phone_number' => $phone,
                'flag' => 1,
                'banks_id' => $bankId,
                'created_at' => $createdAt,
                'updated_at' => $updatedAt,
            ];

            $status = $statuses[array_rand($statuses)];
            $cancelReason = ($status === 'cancelled') ? 'Customer membatalkan pesanan (ganti pikiran)' : null;

            $isCourierPickup = rand(1, 100) <= 80;
            $deliveryMethod = $isCourierPickup ? 'courier' : 'warehouse';
            $pickupFee = $isCourierPickup ? 10000 : 0;

            $ordersBatch[] = [
                'id' => $i,
                'cities_id' => $cityObj['id'],
                'delivery_method' => $deliveryMethod,
                'pickup_address' => $pickupAddr,
                'pickup_address_note' => $isCourierPickup ? 'Lokasi penjemputan kurir' : 'Diantar sendiri ke gudang',
                'pickup_lat' => $cityObj['lat'] + (rand(-50, 50) / 10000),
                'pickup_long' => $cityObj['long'] + (rand(-50, 50) / 10000),
                'status' => $status,
                'cancel_reason' => $cancelReason,
                'customers_id' => $i,
                'created_at' => $createdAt,
                'updated_at' => $updatedAt,
            ];

            $accuKeys = array_rand($accusData, rand(1, 2));
            if (! is_array($accuKeys)) {
                $accuKeys = [$accuKeys];
            }
            $accuItem1 = $accusData[$accuKeys[0]];
            $qty1 = rand(1, 3);
            $pricePerKg = (2100 * 16000 * 80.0) / 1000.0;
            $price1 = (int) round($pricePerKg * $accuItem1['berat_kering']);
            $totalAccuPrice = $price1 * $qty1;

            if (isset($accuKeys[1])) {
                $accuItem2 = $accusData[$accuKeys[1]];
                $qty2 = rand(1, 2);
                $price2 = (int) round($pricePerKg * $accuItem2['berat_kering']);
                $totalAccuPrice += $price2 * $qty2;
            } else {
                $accuItem2 = null;
            }

            $totalAmount = (int) max(0, $totalAccuPrice - $pickupFee);

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
                        'amount' => (float) $totalAmount,
                        'transfer_date' => $receivedDate ?? $updatedAt,
                        'status' => 'COMPLETED',
                        'proof_image' => 'img/default-accu.png',
                        'created_at' => $createdAt,
                        'updated_at' => $updatedAt,
                    ];
                }
            }

            if (count($customersBatch) >= 250) {
                DB::table('customers')->insert($customersBatch);
                DB::table('orders')->insert($ordersBatch);
                DB::table('receipts')->insert($receiptsBatch);
                DB::table('accus_has_receipts')->insert($accusHasReceiptsBatch);
                if (! empty($shipmentsBatch)) {
                    DB::table('shipments')->insert($shipmentsBatch);
                }
                if (! empty($transfersBatch)) {
                    DB::table('transfers')->insert($transfersBatch);
                }

                $customersBatch = [];
                $ordersBatch = [];
                $receiptsBatch = [];
                $accusHasReceiptsBatch = [];
                $shipmentsBatch = [];
                $transfersBatch = [];
            }
        }

        if (! empty($customersBatch)) {
            DB::table('customers')->insert($customersBatch);
            DB::table('orders')->insert($ordersBatch);
            DB::table('receipts')->insert($receiptsBatch);
            DB::table('accus_has_receipts')->insert($accusHasReceiptsBatch);
            if (! empty($shipmentsBatch)) {
                DB::table('shipments')->insert($shipmentsBatch);
            }
            if (! empty($transfersBatch)) {
                DB::table('transfers')->insert($transfersBatch);
            }
        }
    }
}
