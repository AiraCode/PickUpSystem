<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Bank;
use App\Models\City;

class DummyDataSeeder extends Seeder
{
    public function run()
    {
        if (City::count() == 0) City::create(['id' => 1, 'name' => 'Surabaya']);
        if (Bank::count() == 0) Bank::create(['id' => 1, 'name' => 'BCA']);
        
        if (Customer::count() == 0) {
            Customer::create([
                'id' => 1,
                'name' => 'Budi Santoso',
                'address' => 'Jl. Pahlawan No 1',
                'lat' => -7.25, 
                'long' => 112.75,
                'ktp' => '1234567890',
                'account_name' => 'Budi', 
                'account_number' => '123123',
                'phone_number' => '08123', 
                'flag' => 1, 
                'banks_id' => 1
            ]);
        }
        
        $startId = Order::max('id') + 1;
        for ($i=0; $i<5; $i++) {
            $id = $startId + $i;
            Order::create([
                'id' => $id,
                'cities_id' => 1,
                'pickup_address' => 'Jl. Pahlawan No ' . $id,
                'pickup_address_note' => 'Rumah merah',
                'pickup_lat' => -7.25, 
                'pickup_long' => 112.75,
                'status' => ($i % 2 == 0 ? 'processing' : 'pending'),
                'customers_id' => 1
            ]);
        }
    }
}
