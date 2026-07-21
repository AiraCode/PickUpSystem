<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'pickup_order_id',
    'pickup_request_id',
    'sequence_order',
    'status',
    'picked_up_at',
    'notes',
])]
class PickupOrderDetail extends Model
{
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sequence_order' => 'integer',
            'picked_up_at' => 'datetime',
        ];
    }

    /**
     * Order induk
     */
    public function pickupOrder(): BelongsTo
    {
        return $this->belongsTo(PickupOrder::class);
    }

    /**
     * Request yang di-pickup di stop ini
     */
    public function pickupRequest(): BelongsTo
    {
        return $this->belongsTo(PickupRequest::class);
    }
}
