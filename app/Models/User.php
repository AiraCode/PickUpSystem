<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;

    public $incrementing = false;

    protected $fillable = [
        'id',
        'name',
        'email',
        'smtp_email',
        'smtp_password',
        'smtp_host',
        'smtp_port',
        'smtp_encryption',
        'password',
        'role',
        'warehouse_id',
    ];

    protected $hidden = ['password', 'smtp_password'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'smtp_port' => 'integer',
        ];
    }

    public function receipts(): HasMany
    {
        return $this->hasMany(Receipt::class, 'users_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id', 'id');
    }

    public function transfers(): HasMany
    {
        return $this->hasMany(Transfer::class, 'users_id');
    }
}
