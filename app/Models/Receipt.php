<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Receipt extends Model
{
    public $incrementing = false;

    protected $fillable = [
        'id',
        'receipt_number',
        'date',
        'status',
        'price_received',
        'price_owed',
        'users_id',
        'orders_id',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'orders_id');
    }

    public function accus(): BelongsToMany
    {
        return $this->belongsToMany(Accu::class, 'accus_has_receipts', 'receipts_id', 'accus_id')
            ->withPivot('amount')
            ->withTimestamps();
    }

    public function shipment(): HasOne
    {
        return $this->hasOne(Shipment::class, 'receipts_id');
    }

    public function transfer(): HasOne
    {
        return $this->hasOne(Transfer::class, 'receipts_id');
    }
}
