<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Accu extends Model
{
    public $incrementing = false;

    protected $fillable = ['id', 'brand', 'name', 'img'];

    protected $appends = ['img_url'];

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
