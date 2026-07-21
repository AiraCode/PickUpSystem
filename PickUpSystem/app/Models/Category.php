<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'description', 'price_per_unit', 'unit', 'is_active'])]
class Category extends Model
{
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price_per_unit' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Scope: hanya kategori aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Item-item yang menggunakan kategori ini
     */
    public function pickupRequestItems(): HasMany
    {
        return $this->hasMany(PickupRequestItem::class);
    }
}
