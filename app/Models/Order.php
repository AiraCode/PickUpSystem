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
        'pickup_address',
        'pickup_address_note',
        'pickup_lat',
        'pickup_long',
        'status',
        'customers_id',
    ];

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
}
