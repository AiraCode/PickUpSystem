<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    public $incrementing = false;

    protected $fillable = ['id', 'name'];

    public function accus(): BelongsToMany
    {
        return $this->belongsToMany(Accu::class, 'cities_has_accus', 'cities_id', 'accus_id')
            ->withPivot('price')
            ->withTimestamps();
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'cities_id');
    }
}
