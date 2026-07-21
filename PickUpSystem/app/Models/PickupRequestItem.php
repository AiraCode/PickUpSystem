<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'pickup_request_id',
    'category_id',
    'item_name',
    'description',
    'quantity',
    'weight_kg',
    'photo',
    'estimated_price',
    'final_price',
])]
class PickupRequestItem extends Model
{
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'weight_kg' => 'decimal:2',
            'estimated_price' => 'decimal:2',
            'final_price' => 'decimal:2',
        ];
    }

    /**
     * Boot model — auto-calculate estimated_price dari kategori
     */
    protected static function booted(): void
    {
        static::creating(function (PickupRequestItem $item) {
            if ($item->estimated_price == 0 && $item->category_id) {
                $category = Category::find($item->category_id);
                if ($category) {
                    $item->estimated_price = $category->price_per_unit * $item->quantity;
                }
            }
        });
    }

    /**
     * Request induk
     */
    public function pickupRequest(): BelongsTo
    {
        return $this->belongsTo(PickupRequest::class);
    }

    /**
     * Kategori barang
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
