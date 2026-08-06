<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    public $incrementing = false;

    protected $fillable = [
        'id',
        'cities_id',
        'storages_id',
        'pickup_address',
        'pickup_address_note',
        'pickup_lat',
        'pickup_long',
        'status',
        'delivery_method',
        'cancel_reason',
        'customers_id',
        'order_type',
        'new_accus_id',
        'payment_method',
        'accu_ktp',
        'warehouse_proof',
    ];

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'storages_id');
    }

    public function newAccu(): BelongsTo
    {
        return $this->belongsTo(NewAccu::class, 'new_accus_id');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'cities_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customers_id');
    }

    public function receipt(): HasOne
    {
        return $this->hasOne(Receipt::class, 'orders_id');
    }

    public function newAccusItems()
    {
        return $this->belongsToMany(NewAccu::class, 'new_accus_orders', 'orders_id', 'new_accus_id')
            ->withPivot(['quantity', 'price'])
            ->withTimestamps();
    }

    public function pickupPricing(): HasOne
    {
        return $this->hasOne(OrderPickupPricing::class, 'orders_id');
    }
}
