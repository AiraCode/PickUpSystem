<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shipment extends Model
{
    public $incrementing = false;

    protected $fillable = [
        'id',
        'storages_id',
        'status',
        'pickup_date',
        'received_date',
        'receipts_id',
    ];

    protected function casts(): array
    {
        return [
            'pickup_date' => 'datetime',
            'received_date' => 'datetime',
        ];
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'storages_id');
    }

    public function receipt(): BelongsTo
    {
        return $this->belongsTo(Receipt::class, 'receipts_id');
    }
}
