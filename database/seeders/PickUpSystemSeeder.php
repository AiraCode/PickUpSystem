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
        $now = Carbon::now();

        // Dummy Kota
        DB::table('cities')->insert([
            ['id' => 1, 'name' => 'Jakarta', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'name' => 'Surabaya', 'created_at' => $now, 'updated_at' => $now],
        ]);

        DB::table('brands')->insert([
            ['id' => 1, 'name' => 'GS', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'name' => 'Yuasa', 'created_at' => $now, 'updated_at' => $now],
        ]);

        // Dummy Accu
        DB::table('accus')->insert([
            ['id' => 1, 'name' => 'NS40Z', 'brands_id' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'name' => 'NS60', 'brands_id' => 2, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // Dummy User
        DB::table('users')->insert([
            ['id' => 1, 'name' => 'Admin Test', 'password' => Hash::make('password123'), 'created_at' => $now, 'updated_at' => $now],
        ]);

        // Dummy Banks
        DB::table('banks')->insert([
            ['id' => 1, 'name' => 'BCA', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'name' => 'Mandiri', 'created_at' => $now, 'updated_at' => $now],
        ]);

        // Dummy Customers
        DB::table('customers')->insert([
            [
                'id' => 1,
                'name' => 'Budi Santoso',
                'address' => 'Jl. Sudirman No. 1',
                'address_note' => 'Dekat patung',
                'lat' => -6.2088,
                'long' => 106.8456,
                'ktp' => '1234567890123456',
                'account_name' => 'Budi Santoso',
                'account_number' => '1234567890',
                'phone_number' => '08123456789',
                'flag' => 1,
                'banks_id' => 1,
                'created_at' => $now,
                'updated_at' => $now
            ]
        ]);

        // Dummy Orders
        DB::table('orders')->insert([
            [
                'id' => 1,
                'cities_id' => 1,
                'pickup_address' => 'Jl. Sudirman No. 1',
                'pickup_address_note' => 'Dekat patung',
                'pickup_lat' => -6.2088,
                'pickup_long' => 106.8456,
                'status' => 'PENDING',
                'customers_id' => 1,
                'created_at' => $now,
                'updated_at' => $now
            ]
        ]);

        // Dummy Receipts
        DB::table('receipts')->insert([
            [
                'id' => 1,
                'receipt_number' => 'RCP-2024-001',
                'date' => clone $now,
                'status' => 'UNPAID',
                'price_received' => 150000,
                'price_owed' => 0,
                'users_id' => 1,
                'orders_id' => 1,
                'created_at' => $now,
                'updated_at' => $now
            ]
        ]);

        // Dummy Accus Has Receipts
        DB::table('accus_has_receipts')->insert([
            ['accus_id' => 1, 'receipts_id' => 1, 'amount' => 1, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // Dummy Storages
        DB::table('storages')->insert([
            [
                'id' => 1,
                'name' => 'Gudang Pusat Jakarta',
                'address' => 'Jl. Merdeka Raya',
                'lat' => -6.1751,
                'long' => 106.8272,
                'created_at' => $now,
                'updated_at' => $now
            ]
        ]);

        // Dummy Shipments
        DB::table('shipments')->insert([
            [
                'id' => 1,
                'storages_id' => 1,
                'status' => 'IN_TRANSIT',
                'pickup_date' => clone $now,
                'received_date' => (clone $now)->addDays(1),
                'receipts_id' => 1,
                'created_at' => $now,
                'updated_at' => $now
            ]
        ]);

        // Dummy Cities Has Accus
        DB::table('cities_has_accus')->insert([
            ['cities_id' => 1, 'accus_id' => 1, 'price' => 150000, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // Dummy Transfers
        DB::table('transfers')->insert([
            [
                'id' => 1,
                'receipts_id' => 1,
                'users_id' => 1,
                'amount' => 150000.00,
                'transfer_date' => clone $now,
                'status' => 'COMPLETED',
                'proof_image' => 'proof1.jpg',
                'created_at' => $now,
                'updated_at' => $now
            ]
        ]);

        
    }
}
