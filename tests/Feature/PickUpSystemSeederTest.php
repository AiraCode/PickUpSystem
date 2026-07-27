<?php

namespace Tests\Feature;

use Database\Seeders\PickUpSystemSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class PickUpSystemSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_pickup_system_seeder_can_run_on_sqlite(): void
    {
        $exitCode = Artisan::call('db:seed', ['--class' => PickUpSystemSeeder::class]);

        $this->assertSame(0, $exitCode);
        $this->assertDatabaseCount('cities', 9);
        $this->assertDatabaseHas('settings', ['key' => 'lme', 'value' => '2100']);
    }
}
