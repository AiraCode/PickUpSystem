<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PickupPricingSetting extends Model
{
    protected $table = 'pickup_pricing_settings';

    protected $fillable = [
        'initial_fee',
        'distance_rate',
        'time_rate',
        'demand_multiplier',
        'weather_multiplier',
        'traffic_multiplier',
        'event_multiplier',
        'total_multiplier',
        'is_active',
        'updated_by',
    ];

    protected $casts = [
        'initial_fee'        => 'float',
        'distance_rate'      => 'float',
        'time_rate'          => 'float',
        'demand_multiplier'  => 'float',
        'weather_multiplier' => 'float',
        'traffic_multiplier' => 'float',
        'event_multiplier'   => 'float',
        'total_multiplier'   => 'float',
        'is_active'          => 'boolean',
    ];

    public static function getActive(): self
    {
        $active = static::where('is_active', true)->latest()->first();

        if ($active) {
            return $active;
        }

        return new static([
            'initial_fee'        => config('pickup_pricing.initial_fee', 5000),
            'distance_rate'      => config('pickup_pricing.distance_rate', 2300),
            'time_rate'          => config('pickup_pricing.time_rate', 25),
            'demand_multiplier'  => 1.0000,
            'weather_multiplier' => 1.0000,
            'traffic_multiplier' => 1.0000,
            'event_multiplier'   => 1.0000,
            'total_multiplier'   => 1.0000,
            'is_active'          => true,
        ]);
    }
}
