<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Accu;
use App\Models\City;
use App\Models\NewAccu;
use Illuminate\Support\Facades\DB;

class NewAccuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $surabaya = City::where('name', 'LIKE', '%Surabaya%')->first() ?? City::first();
        $accus = Accu::all();

        foreach ($accus as $accu) {
            $price = 500000;
            if ($surabaya) {
                $cityAccu = DB::table('cities_has_accus')
                    ->where('cities_id', $surabaya->id)
                    ->where('accus_id', $accu->id)
                    ->first();
                if ($cityAccu && !empty($cityAccu->price) && $cityAccu->price > 0) {
                    $price = $cityAccu->price;
                }
            }

            $newPrice = round($price * 2, -3);

            NewAccu::updateOrCreate(
                ['name' => $accu->name],
                [
                    'brands_id' => $accu->brands_id ?? 1,
                    'price' => $newPrice,
                ]
            );
        }
    }
}
