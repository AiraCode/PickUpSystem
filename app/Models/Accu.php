<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Accu extends Model
{
    use SoftDeletes;
    public $incrementing = false;

    protected $fillable = ['id', 'brands_id', 'name', 'img'];

    protected $appends = ['img_url', 'brand'];

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

    public function getImgUrlAttribute()
    {
        if ($this->img) {
            return asset('storage/' . $this->img);
        }
        return null;
    }

    public function cities(): BelongsToMany
    {
        return $this->belongsToMany(City::class, 'cities_has_accus', 'accus_id', 'cities_id')
            ->withPivot('price')
            ->withTimestamps();
    }

    public function receipts(): BelongsToMany
    {
        return $this->belongsToMany(Receipt::class, 'accus_has_receipts', 'accus_id', 'receipts_id')
            ->withPivot('amount')
            ->withTimestamps();
    }
}
