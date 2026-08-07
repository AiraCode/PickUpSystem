<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $centralAdmin = \App\Models\User::first();
        if ($centralAdmin) {
            $centralAdmin->role = 'central';
            $centralAdmin->warehouse_id = null;
            $centralAdmin->save();
        }

        $warehouses = \App\Models\Warehouse::all();
        foreach ($warehouses as $w) {
            $cleanName = trim(str_replace('Gudang', '', $w->name));
            $username = 'admin.' . strtolower(str_replace([' ', '-'], '', $cleanName));
            
            $existing = \App\Models\User::where('name', $username)->first();
            if (!$existing) {
                \App\Models\User::create([
                    'id' => (\App\Models\User::max('id') ?? 0) + 1,
                    'name' => $username,
                    'password' => bcrypt('password123'),
                    'role' => 'warehouse',
                    'warehouse_id' => $w->id,
                ]);
            } else {
                $existing->role = 'warehouse';
                $existing->warehouse_id = $w->id;
                $existing->save();
            }
        }
    }
}
