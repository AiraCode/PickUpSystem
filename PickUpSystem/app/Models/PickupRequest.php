<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'user_id',
    'pickup_address',
    'latitude',
    'longitude',
    'preferred_date',
    'preferred_time_start',
    'preferred_time_end',
    'notes',
    'status',
    'total_estimated_price',
    'total_final_price',
    'scheduled_date',
    'picked_up_at',
])]
class PickupRequest extends Model
{
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'preferred_date' => 'date',
            'scheduled_date' => 'date',
            'picked_up_at' => 'datetime',
            'total_estimated_price' => 'decimal:2',
            'total_final_price' => 'decimal:2',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    /**
     * Customer yang membuat request ini
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Item-item barang dalam request ini
     */
    public function items(): HasMany
    {
        return $this->hasMany(PickupRequestItem::class);
    }

    /**
     * Detail order pickup (stop dalam rute)
     */
    public function orderDetail(): HasOne
    {
        return $this->hasOne(PickupOrderDetail::class);
    }

    /**
     * Pembayaran untuk request ini
     */
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    /**
     * Hitung ulang total estimated price dari semua items
     */
    public function recalculateEstimatedPrice(): void
    {
        $this->update([
            'total_estimated_price' => $this->items()->sum('estimated_price'),
        ]);
    }
}
