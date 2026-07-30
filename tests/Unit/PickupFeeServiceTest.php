<?php

namespace Tests\Unit;

use App\Services\DynamicPricingService;
use App\Services\PickupFeeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PickupFeeServiceTest extends TestCase
{
    use RefreshDatabase;

    private PickupFeeService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $dynamicPricing = new DynamicPricingService();
        $this->service  = new PickupFeeService($dynamicPricing);
    }

    // -------------------------------------------------------------------------
    // Distance Rounding (conservative upward to 1 decimal)
    // -------------------------------------------------------------------------

    public function test_rounds_up_distance_5_61_to_5_7(): void
    {
        $this->assertEquals(5.7, $this->service->roundUpDistance(5.61));
    }

    public function test_rounds_up_distance_8_02_to_8_1(): void
    {
        $this->assertEquals(8.1, $this->service->roundUpDistance(8.02));
    }

    public function test_rounds_up_distance_10_00_stays_10_0(): void
    {
        $this->assertEquals(10.0, $this->service->roundUpDistance(10.00));
    }

    public function test_rounds_up_distance_7_01_to_7_1(): void
    {
        $this->assertEquals(7.1, $this->service->roundUpDistance(7.01));
    }

    public function test_rounds_up_distance_7_00_stays_7_0(): void
    {
        $this->assertEquals(7.0, $this->service->roundUpDistance(7.00));
    }

    // -------------------------------------------------------------------------
    // Final Price Rounding (upward to nearest Rp1.000)
    // -------------------------------------------------------------------------

    public function test_rounds_up_price_50991_to_51000(): void
    {
        $this->assertEquals(51000, $this->service->roundUpToThousand(50991));
    }

    public function test_rounds_up_price_38001_to_39000(): void
    {
        $this->assertEquals(39000, $this->service->roundUpToThousand(38001));
    }

    public function test_does_not_round_up_price_already_multiple_of_1000(): void
    {
        $this->assertEquals(38000, $this->service->roundUpToThousand(38000));
    }

    public function test_rounds_up_price_1_to_1000(): void
    {
        $this->assertEquals(1000, $this->service->roundUpToThousand(1));
    }

    // -------------------------------------------------------------------------
    // DynamicPricingService default multiplier
    // -------------------------------------------------------------------------

    public function test_dynamic_pricing_returns_1_00_by_default(): void
    {
        $pricing = new DynamicPricingService();
        $this->assertEquals(1.00, $pricing->getMultiplier());
    }

    public function test_dynamic_pricing_returns_1_00_with_empty_context(): void
    {
        $pricing = new DynamicPricingService();
        $this->assertEquals(1.00, $pricing->getMultiplier([]));
    }

    public function test_dynamic_pricing_returns_1_00_with_full_context(): void
    {
        $pricing = new DynamicPricingService();
        $multiplier = $pricing->getMultiplier([
            'pickup_lat'    => -7.2575,
            'pickup_lng'    => 112.7521,
            'warehouse_lat' => -7.3105,
            'warehouse_lng' => 112.7800,
        ]);
        $this->assertEquals(1.00, $multiplier);
    }

    // -------------------------------------------------------------------------
    // Price formula pipeline verification
    // -------------------------------------------------------------------------

    public function test_base_price_formula_8km_600s_rounded(): void
    {
        // Plan: 5000 + (8 * 2300) + (600 * 25) = 38400 -> roundUp to 1000 = 39000
        $this->assertEquals(39000, $this->service->roundUpToThousand(38400));
    }

    public function test_multiplier_1_15_applied_to_38400(): void
    {
        // 38400 * 1.15 = 44160 -> roundUp to nearest 1000 = 45000
        $this->assertEquals(45000, $this->service->roundUpToThousand(38400 * 1.15));
    }

    public function test_multiplier_0_95_applied_to_38400(): void
    {
        // 38400 * 0.95 = 36480 -> roundUp to nearest 1000 = 37000
        $this->assertEquals(37000, $this->service->roundUpToThousand(38400 * 0.95));
    }
}
