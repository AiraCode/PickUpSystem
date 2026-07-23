<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brand extends Model
{
    protected $fillable = ['id', 'name'];

    public function accus(): HasMany
    {
        return $this->hasMany(Accu::class, 'brands_id');
    }
}
