<?php

namespace App\Services;

use App\Models\PickupPricingSetting;
use Illuminate\Support\Facades\Log;

/**
 * PickupFeeService
 *
 * Single source of truth for all pickup fee calculations.
 * Uses OSRM (OpenStreetMap Routing Machine) for route distance retrieval,
 * with a Haversine fallback when the network is unreachable.
 *
 * Pricing parameters are read from the active PickupPricingSetting DB record
 * (with fallback to config/pickup_pricing.php defaults for fresh installs).
 *
 * Inputs:
 *   - Customer pickup latitude/longitude
 *   - Warehouse latitude/longitude
 *   - Optional: pre-loaded PickupPricingSetting to avoid duplicate DB queries
 *
 * Outputs:
 *   - pricing_version         (int)   - configuration version at calculation time
 *   - initial_fee             (float) - snapshot
 *   - distance_rate           (float) - snapshot
 *   - time_rate               (float) - snapshot
 *   - demand_multiplier       (float) - snapshot
 *   - weather_multiplier      (float) - snapshot
 *   - traffic_multiplier      (float) - snapshot
 *   - event_multiplier        (float) - snapshot
 *   - route_distance_km       (float) - road distance, rounded up to 1 decimal
 *   - travel_time_seconds     (int)   - internal only, never displayed
 *   - base_price              (float) - before multiplier
 *   - multiplier              (float) - total combined multiplier
 *   - final_pickup_fee        (int)   - rounded up to nearest Rp1.000
 */
class PickupFeeService
{
    public function __construct(
        protected DynamicPricingService $dynamicPricing
    ) {}

    /**
     * Calculate the full pickup fee breakdown using the current active pricing config.
     */
    public function calculate(
        float $pickupLat,
        float $pickupLng,
        float $warehouseLat,
        float $warehouseLng,
        array $context = []
    ): array {
        // Load active configuration once (single DB read per calculation)
        $setting = PickupPricingSetting::getActive();

        return $this->calculateWithSetting(
            $pickupLat, $pickupLng, $warehouseLat, $warehouseLng, $setting, $context
        );
    }

    /**
     * Calculate the full pickup fee breakdown using an explicitly provided setting.
     * Used internally and by calculatePickupFee API endpoint.
     */
    public function calculateWithSetting(
        float $pickupLat,
        float $pickupLng,
        float $warehouseLat,
        float $warehouseLng,
        PickupPricingSetting $setting,
        array $context = []
    ): array {
        // 1. Get route distance from OSRM (road distance, not straight-line)
        $rawDistanceKm = $this->fetchRouteDistance($pickupLat, $pickupLng, $warehouseLat, $warehouseLng);

        // 2. Apply conservative upward rounding to 1 decimal place
        $distanceKm = $this->roundUpDistance($rawDistanceKm);

        // 3. Derive travel time internally from speed constant
        $travelTimeSeconds = $this->calculateTravelTime($distanceKm);

        // 4. Calculate base price using active rates
        $basePrice = $this->calculateBasePrice($distanceKm, $travelTimeSeconds, $setting);

        // 5. Get dynamic multiplier (reads from setting object, not DB again)
        $multiplier = $this->dynamicPricing->getMultiplier(
            array_merge($context, [
                'pickup_lat'    => $pickupLat,
                'pickup_lng'    => $pickupLng,
                'warehouse_lat' => $warehouseLat,
                'warehouse_lng' => $warehouseLng,
            ]),
            $setting
        );

        // 6. Calculate final fee (always through multiplier step)
        $rawFinalFee = $basePrice * $multiplier;

        // 7. Round up to nearest Rp1.000
        $finalPickupFee = $this->roundUpToThousand($rawFinalFee);

        return [
            // Configuration snapshot (stored permanently with the transaction)
            'initial_fee'        => $setting->initial_fee,
            'distance_rate'      => $setting->distance_rate,
            'time_rate'          => $setting->time_rate,
            'demand_multiplier'  => $setting->demand_multiplier,
            'weather_multiplier' => $setting->weather_multiplier,
            'traffic_multiplier' => $setting->traffic_multiplier,
            'event_multiplier'   => $setting->event_multiplier,
            // Calculation results
            'route_distance_km'   => $distanceKm,
            'travel_time_seconds' => $travelTimeSeconds,
            'base_price'          => round($basePrice, 2),
            'multiplier'          => $multiplier,
            'final_pickup_fee'    => $finalPickupFee,
        ];
    }

    // -------------------------------------------------------------------------
    // Route Distance (OSRM → Haversine fallback)
    // -------------------------------------------------------------------------

    protected function fetchRouteDistance(
        float $fromLat, float $fromLng,
        float $toLat,   float $toLng
    ): float {
        try {
            $url = sprintf(
                'http://router.project-osrm.org/route/v1/driving/%s,%s;%s,%s?overview=false',
                $fromLng, $fromLat,
                $toLng,   $toLat
            );

            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 8,
                CURLOPT_CONNECTTIMEOUT => 5,
                CURLOPT_USERAGENT      => 'PickUpSystem/1.0 (internal-routing)',
            ]);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($response && $httpCode === 200) {
                $data = json_decode($response, true);
                if (
                    isset($data['code']) && $data['code'] === 'Ok' &&
                    isset($data['routes'][0]['distance'])
                ) {
                    $meters = (float) $data['routes'][0]['distance'];
                    return $meters / 1000.0;
                }
            }
        } catch (\Throwable $e) {
            Log::warning('PickupFeeService: OSRM routing failed, using Haversine fallback.', [
                'error' => $e->getMessage(),
            ]);
        }

        Log::info('PickupFeeService: Using Haversine fallback with road factor 1.35.');
        return $this->haversineDistance($fromLat, $fromLng, $toLat, $toLng) * 1.35;
    }

    protected function haversineDistance(
        float $lat1, float $lng1,
        float $lat2, float $lng2
    ): float {
        $R    = 6371.0;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a    = sin($dLat / 2) ** 2
              + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $R * $c;
    }

    // -------------------------------------------------------------------------
    // Travel Time
    // -------------------------------------------------------------------------

    protected function calculateTravelTime(float $distanceKm): int
    {
        $speedKmh  = config('pickup_pricing.courier_speed_kmh', 40);
        $speedMs   = $speedKmh / 3.6;
        $distanceM = $distanceKm * 1000;
        return (int) ceil($distanceM / $speedMs);
    }

    // -------------------------------------------------------------------------
    // Base Price
    // -------------------------------------------------------------------------

    protected function calculateBasePrice(
        float $distanceKm,
        int $travelTimeSeconds,
        PickupPricingSetting $setting
    ): float {
        return $setting->initial_fee
            + ($distanceKm * $setting->distance_rate)
            + ($travelTimeSeconds * $setting->time_rate);
    }

    // -------------------------------------------------------------------------
    // Rounding Utilities (conservative upward — never round down)
    // -------------------------------------------------------------------------

    public function roundUpDistance(float $km): float
    {
        return ceil($km * 10) / 10;
    }

    public function roundUpToThousand(float $price): int
    {
        return (int) (ceil($price / 1000) * 1000);
    }
}
