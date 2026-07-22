<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;

    public $incrementing = false;

    protected $fillable = ['id', 'name', 'password'];

    protected $hidden = ['password'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function receipts(): HasMany
    {
        return $this->hasMany(Receipt::class, 'users_id');
    }

    public function transfers(): HasMany
    {
        return $this->hasMany(Transfer::class, 'users_id');
    }
}
