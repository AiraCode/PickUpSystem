<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    use SoftDeletes;

    public $incrementing = false;

    protected $fillable = ['id', 'name', 'percentage'];

    public function accus(): BelongsToMany
    {
        return $this->belongsToMany(Accu::class, 'cities_has_accus', 'cities_id', 'accus_id')
            ->wherePivotNull('deleted_at')
            ->withPivot('deleted_at')
            ->withTimestamps();
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'cities_id');
    }
}
