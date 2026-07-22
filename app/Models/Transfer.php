<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transfer extends Model
{
    public $incrementing = false;

    protected $fillable = [
        'id',
        'receipts_id',
        'users_id',
        'amount',
        'transfer_date',
        'status',
        'proof_image',
    ];

    protected function casts(): array
    {
        return [
            'transfer_date' => 'datetime',
            'amount' => 'decimal:2',
        ];
    }

    public function receipt(): BelongsTo
    {
        return $this->belongsTo(Receipt::class, 'receipts_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'users_id');
    }
}
