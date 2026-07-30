<?php

namespace App\Services;

use App\Models\PickupPricingSetting;

/**
 * DynamicPricingService
 *
 * Reads dynamic multiplier factors from the active DB pricing configuration
 * (PickupPricingSetting). Each multiplier factor (demand, weather, traffic,
 * event) defaults to 1.00 if no active configuration exists.
 *
 * Future integrations only need to update the factor methods; the
 * aggregate multiplier pipeline is defined here.
 */
class DynamicPricingService
{
    /**
     * Calculate the combined pricing multiplier.
     *
     * @param  array  $context  Optional context: ['lat', 'lng', 'warehouse_id', 'datetime', ...]
     * @param  PickupPricingSetting|null  $setting  Active setting override (avoids duplicate DB queries)
     * @return float  Final multiplier (e.g. 1.00, 1.15, 0.95)
     */
    public function getMultiplier(array $context = [], ?PickupPricingSetting $setting = null): float
    {
        $setting ??= PickupPricingSetting::getActive();

        $demand  = $this->getDemandFactor($context, $setting);
        $weather = $this->getWeatherFactor($context, $setting);
        $traffic = $this->getTrafficFactor($context, $setting);
        $event   = $this->getEventFactor($context, $setting);

        return round($demand * $weather * $traffic * $event, 4);
    }

    // -------------------------------------------------------------------------
    // Individual Factor Methods
    // -------------------------------------------------------------------------

    protected function getDemandFactor(array $context, PickupPricingSetting $setting): float
    {
        // Returns admin-configured demand multiplier.
        // Future: can apply additional real-time overrides on top of base admin value.
        return (float) ($setting->demand_multiplier ?? 1.00);
    }

    protected function getWeatherFactor(array $context, PickupPricingSetting $setting): float
    {
        // Returns admin-configured weather multiplier.
        // Future: override with live weather API data.
        return (float) ($setting->weather_multiplier ?? 1.00);
    }

    protected function getTrafficFactor(array $context, PickupPricingSetting $setting): float
    {
        // Returns admin-configured traffic multiplier.
        // Future: override with live traffic API data.
        return (float) ($setting->traffic_multiplier ?? 1.00);
    }

    protected function getEventFactor(array $context, PickupPricingSetting $setting): float
    {
        // Returns admin-configured event multiplier.
        // Future: detect local events near warehouse/pickup area.
        return (float) ($setting->event_multiplier ?? 1.00);
    }
}
