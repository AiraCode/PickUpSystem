<?php

namespace Tests\Feature;

use App\Models\PickupPricingHistory;
use App\Models\PickupPricingSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPickupPricingTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        // User model uses $incrementing = true — provide explicit id
        $this->admin = User::create([
            'id'       => 1,
            'name'     => 'Test Admin',
            'email'    => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);
        $this->token = $this->admin->createToken('test')->plainTextToken;
    }


    private function authHeaders(): array
    {
        return ['Authorization' => 'Bearer ' . $this->token];
    }

    // -------------------------------------------------------------------------
    // GET /api/admin/pengiriman
    // -------------------------------------------------------------------------

    public function test_index_returns_current_setting_and_history(): void
    {
        $response = $this->getJson('/api/admin/pengiriman', $this->authHeaders());

        $response->assertOk()
                 ->assertJsonStructure([
                     'setting' => ['initial_fee', 'distance_rate', 'time_rate', 'total_multiplier', 'is_active'],
                     'history' => ['data', 'total', 'current_page'],
                 ]);

        $this->assertEquals(5000, $response->json('setting.initial_fee'));
    }


    // -------------------------------------------------------------------------
    // PUT /api/admin/pengiriman — success path
    // -------------------------------------------------------------------------

    public function test_update_creates_new_version_and_history_record(): void
    {
        $payload = [
            'initial_fee'        => 7000,
            'distance_rate'      => 2500,
            'time_rate'          => 30,
            'demand_multiplier'  => 1.10,
            'weather_multiplier' => 1.00,
            'traffic_multiplier' => 1.00,
            'event_multiplier'   => 1.00,
        ];

        $response = $this->putJson('/api/admin/pengiriman', $payload, $this->authHeaders());

        $response->assertOk()
                 ->assertJsonPath('setting.initial_fee', 7000);

        // Verify new active setting saved
        $active = PickupPricingSetting::where('is_active', true)->first();
        $this->assertEquals(7000, $active->initial_fee);
        $this->assertEquals(1.10, $active->demand_multiplier);
        $this->assertEquals(1.10 * 1.00 * 1.00 * 1.00, round($active->total_multiplier, 4));

        // Verify history record was created
        $history = PickupPricingHistory::where('initial_fee', 7000)->first();
        $this->assertNotNull($history);
        $this->assertEquals(7000, $history->initial_fee);
        $this->assertEquals(2, PickupPricingHistory::count());
    }

    public function test_update_deactivates_previous_setting(): void
    {
        $payload = [
            'initial_fee'        => 6000,
            'distance_rate'      => 2300,
            'time_rate'          => 25,
            'demand_multiplier'  => 1.00,
            'weather_multiplier' => 1.00,
            'traffic_multiplier' => 1.00,
            'event_multiplier'   => 1.00,
        ];

        $this->putJson('/api/admin/pengiriman', $payload, $this->authHeaders())->assertOk();

        // Only 1 active setting should exist
        $this->assertEquals(1, PickupPricingSetting::where('is_active', true)->count());
    }

    public function test_update_adds_history_record_on_each_save(): void
    {
        $payload = [
            'initial_fee'        => 5000,
            'distance_rate'      => 2300,
            'time_rate'          => 25,
            'demand_multiplier'  => 1.00,
            'weather_multiplier' => 1.00,
            'traffic_multiplier' => 1.00,
            'event_multiplier'   => 1.00,
        ];

        $this->putJson('/api/admin/pengiriman', $payload, $this->authHeaders())->assertOk();
        $this->putJson('/api/admin/pengiriman', $payload, $this->authHeaders())->assertOk();

        $this->assertEquals(3, PickupPricingHistory::count());
    }


    // -------------------------------------------------------------------------
    // PUT /api/admin/pengiriman — validation failures
    // -------------------------------------------------------------------------

    public function test_update_rejects_negative_initial_fee(): void
    {
        $payload = [
            'initial_fee'        => -100,
            'distance_rate'      => 2300,
            'time_rate'          => 25,
            'demand_multiplier'  => 1.00,
            'weather_multiplier' => 1.00,
            'traffic_multiplier' => 1.00,
            'event_multiplier'   => 1.00,
        ];

        $this->putJson('/api/admin/pengiriman', $payload, $this->authHeaders())
             ->assertUnprocessable()
             ->assertJsonValidationErrors(['initial_fee']);
    }

    public function test_update_rejects_zero_distance_rate(): void
    {
        $payload = [
            'initial_fee'        => 5000,
            'distance_rate'      => 0,
            'time_rate'          => 25,
            'demand_multiplier'  => 1.00,
            'weather_multiplier' => 1.00,
            'traffic_multiplier' => 1.00,
            'event_multiplier'   => 1.00,
        ];

        $this->putJson('/api/admin/pengiriman', $payload, $this->authHeaders())
             ->assertUnprocessable()
             ->assertJsonValidationErrors(['distance_rate']);
    }

    public function test_update_rejects_multiplier_below_0_10(): void
    {
        $payload = [
            'initial_fee'        => 5000,
            'distance_rate'      => 2300,
            'time_rate'          => 25,
            'demand_multiplier'  => 0.05,   // below 0.10
            'weather_multiplier' => 1.00,
            'traffic_multiplier' => 1.00,
            'event_multiplier'   => 1.00,
        ];

        $this->putJson('/api/admin/pengiriman', $payload, $this->authHeaders())
             ->assertUnprocessable()
             ->assertJsonValidationErrors(['demand_multiplier']);
    }

    public function test_update_rejects_multiplier_above_5_00(): void
    {
        $payload = [
            'initial_fee'        => 5000,
            'distance_rate'      => 2300,
            'time_rate'          => 25,
            'demand_multiplier'  => 1.00,
            'weather_multiplier' => 6.00,   // above 5.00
            'traffic_multiplier' => 1.00,
            'event_multiplier'   => 1.00,
        ];

        $this->putJson('/api/admin/pengiriman', $payload, $this->authHeaders())
             ->assertUnprocessable()
             ->assertJsonValidationErrors(['weather_multiplier']);
    }

    // -------------------------------------------------------------------------
    // GET /api/admin/pengiriman/history
    // -------------------------------------------------------------------------

    public function test_history_endpoint_returns_paginated_records(): void
    {
        $response = $this->getJson('/api/admin/pengiriman/history', $this->authHeaders());

        $response->assertOk()
                 ->assertJsonStructure(['data', 'total', 'per_page', 'current_page']);

        $this->assertEquals(1, $response->json('total'));
    }

    // -------------------------------------------------------------------------
    // Unauthenticated access
    // -------------------------------------------------------------------------

    public function test_unauthenticated_cannot_access_pricing(): void
    {
        $this->getJson('/api/admin/pengiriman')->assertUnauthorized();
        $this->putJson('/api/admin/pengiriman', [])->assertUnauthorized();
    }
}
