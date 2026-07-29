<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NewAccu extends Model
{
    protected $fillable = [
        'brands_id',
        'name',
        'price',
    ];

    protected $appends = ['brand'];

    public function brandRelation(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'brands_id');
    }

    public function getBrandAttribute(): string
    {
        if ($this->relationLoaded('brandRelation') && $this->brandRelation) {
            return $this->brandRelation->name;
        }
        if ($this->brands_id) {
            $b = Brand::find($this->brands_id);
            return $b ? $b->name : '-';
        }
        return '-';
    }
}
