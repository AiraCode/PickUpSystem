<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    public $incrementing = false;

    protected $fillable = [
        'id',
        'name',
        'address',
        'address_note',
        'lat',
        'long',
        'ktp',
        'account_name',
        'account_number',
        'phone_number',
        'flag',
        'banks_id',
    ];

    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class, 'banks_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'customers_id');
    }
}
