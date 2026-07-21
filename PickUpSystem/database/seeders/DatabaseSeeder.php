<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Buat admin default
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@pickupsystem.com',
            'role' => 'admin',
        ]);

        // Buat customer test
        User::factory()->create([
            'name' => 'Test Customer',
            'email' => 'customer@example.com',
            'role' => 'customer',
        ]);

        // Buat driver test
        User::factory()->create([
            'name' => 'Test Driver',
            'email' => 'driver@example.com',
            'role' => 'driver',
        ]);

        // Seed kategori barang
        $this->call(CategorySeeder::class);
    }
}
