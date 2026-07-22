<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bank extends Model
{
    public $incrementing = false;

    protected $fillable = ['id', 'name'];

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class, 'banks_id');
    }
}
