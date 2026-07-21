<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'order_number',
    'driver_id',
    'vehicle_id',
    'date',
    'status',
    'total_distance_km',
    'route_data',
    'started_at',
    'completed_at',
    'notes',
])]
class PickupOrder extends Model
{
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date' => 'date',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'total_distance_km' => 'decimal:2',
            'route_data' => 'array',
        ];
    }

    /**
     * Boot model — auto-generate order_number
     */
    protected static function booted(): void
    {
        static::creating(function (PickupOrder $order) {
            if (empty($order->order_number)) {
                $date = now()->format('Ymd');
                $lastOrder = static::where('order_number', 'like', "PO-{$date}-%")
                    ->orderByDesc('order_number')
                    ->first();

                $sequence = 1;
                if ($lastOrder) {
                    $lastSequence = (int) substr($lastOrder->order_number, -3);
                    $sequence = $lastSequence + 1;
                }

                $order->order_number = sprintf('PO-%s-%03d', $date, $sequence);
            }
        });
    }

    /**
     * Driver yang menjalankan order
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    /**
     * Kendaraan yang digunakan
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Detail stop-stop dalam order ini
     */
    public function details(): HasMany
    {
        return $this->hasMany(PickupOrderDetail::class)->orderBy('sequence_order');
    }

    /**
     * Biaya-biaya perjalanan
     */
    public function costs(): HasMany
    {
        return $this->hasMany(PickupOrderCost::class);
    }

    /**
     * Total biaya perjalanan
     */
    public function getTotalCostAttribute(): float
    {
        return (float) $this->costs()->sum('amount');
    }
}
