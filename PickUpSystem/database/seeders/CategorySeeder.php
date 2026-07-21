<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Elektronik',
                'description' => 'Perangkat elektronik seperti TV, laptop, HP, dll',
                'price_per_unit' => 50000,
                'unit' => 'pcs',
            ],
            [
                'name' => 'Furniture',
                'description' => 'Perabotan rumah tangga seperti meja, kursi, lemari, dll',
                'price_per_unit' => 75000,
                'unit' => 'pcs',
            ],
            [
                'name' => 'Pakaian',
                'description' => 'Pakaian bekas layak pakai',
                'price_per_unit' => 5000,
                'unit' => 'kg',
            ],
            [
                'name' => 'Buku & Kertas',
                'description' => 'Buku bekas, koran, majalah, kertas',
                'price_per_unit' => 3000,
                'unit' => 'kg',
            ],
            [
                'name' => 'Logam & Besi',
                'description' => 'Barang-barang logam dan besi tua',
                'price_per_unit' => 8000,
                'unit' => 'kg',
            ],
            [
                'name' => 'Plastik',
                'description' => 'Barang-barang plastik bekas',
                'price_per_unit' => 2000,
                'unit' => 'kg',
            ],
            [
                'name' => 'Kaca & Botol',
                'description' => 'Botol kaca, pecahan kaca',
                'price_per_unit' => 1500,
                'unit' => 'kg',
            ],
            [
                'name' => 'Peralatan Rumah Tangga',
                'description' => 'Alat masak, alat kebersihan, dll',
                'price_per_unit' => 15000,
                'unit' => 'pcs',
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['name' => $category['name']],
                $category,
            );
        }
    }
}
