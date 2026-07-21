<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['pickup_order_id', 'cost_type', 'description', 'amount'])]
class PickupOrderCost extends Model
{
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }

    /**
     * Order terkait
     */
    public function pickupOrder(): BelongsTo
    {
        return $this->belongsTo(PickupOrder::class);
    }
}
