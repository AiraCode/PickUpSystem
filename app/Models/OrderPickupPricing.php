<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderPickupPricing extends Model
{
    protected $table = 'order_pickup_pricings';

    public $incrementing = false;

    protected $keyType = 'integer';

    protected $fillable = [
        'id',
        'orders_id',
        'storages_id',
        // Pricing configuration snapshot
        'initial_fee',
        'distance_rate',
        'time_rate',
        'demand_multiplier',
        'weather_multiplier',
        'traffic_multiplier',
        'event_multiplier',
        // Route & calculation results
        'route_distance_km',
        'travel_time_seconds',
        'base_price',
        'multiplier',
        'final_pickup_fee',
    ];

    protected $casts = [
        'initial_fee'         => 'float',
        'distance_rate'       => 'float',
        'time_rate'           => 'float',
        'demand_multiplier'   => 'float',
        'weather_multiplier'  => 'float',
        'traffic_multiplier'  => 'float',
        'event_multiplier'    => 'float',
        'route_distance_km'   => 'float',
        'travel_time_seconds' => 'integer',
        'base_price'          => 'float',
        'multiplier'          => 'float',
        'final_pickup_fee'    => 'integer',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'orders_id');
    }

    public function storage(): BelongsTo
    {
        return $this->belongsTo(Storage::class, 'storages_id');
    }
}
