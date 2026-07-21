<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['driver_id', 'plate_number', 'type', 'capacity_kg', 'status'])]
class Vehicle extends Model
{
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'capacity_kg' => 'decimal:2',
        ];
    }

    /**
     * Scope: kendaraan yang tersedia
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    /**
     * Driver yang ditugaskan ke kendaraan ini
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    /**
     * Order-order yang menggunakan kendaraan ini
     */
    public function pickupOrders(): HasMany
    {
        return $this->hasMany(PickupOrder::class);
    }
}
