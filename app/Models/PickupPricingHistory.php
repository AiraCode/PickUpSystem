<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PickupPricingHistory extends Model
{
    protected $table = 'pickup_pricing_histories';

    protected $fillable = [
        'initial_fee',
        'distance_rate',
        'time_rate',
        'demand_multiplier',
        'weather_multiplier',
        'traffic_multiplier',
        'event_multiplier',
        'total_multiplier',
        'created_by',
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
    ];
}
