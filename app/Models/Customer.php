<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

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
        'flag_reason',
        'banks_id',
    ];

    /**
     * Safe Encryption & Decryption Helper
     */
    protected function safeEncryptedAttribute(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if ($value === null || $value === '') {
                    return $value;
                }
                try {
                    return Crypt::decryptString($value);
                } catch (DecryptException $e) {
                    // Fallback if data in DB is legacy plain text
                    return $value;
                }
            },
            set: function ($value) {
                if ($value === null || $value === '') {
                    return $value;
                }
                // Avoid double encryption if already encrypted
                try {
                    Crypt::decryptString($value);
                    return $value;
                } catch (DecryptException $e) {
                    return Crypt::encryptString($value);
                }
            }
        );
    }

    protected function accountName(): Attribute
    {
        return $this->safeEncryptedAttribute();
    }

    protected function accountNumber(): Attribute
    {
        return $this->safeEncryptedAttribute();
    }

    protected function phoneNumber(): Attribute
    {
        return $this->safeEncryptedAttribute();
    }

    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class, 'banks_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'customers_id');
    }
}
