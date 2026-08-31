<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Accu extends Model
{
    use SoftDeletes;
    public $incrementing = true;

    protected $fillable = ['id', 'name', 'berat_kering'];



    public function cities(): BelongsToMany
    {
        return $this->belongsToMany(City::class, 'cities_has_accus', 'accus_id', 'cities_id')
            ->withTimestamps();
    }

    public function receipts(): BelongsToMany
    {
        return $this->belongsToMany(Receipt::class, 'accus_has_receipts', 'accus_id', 'receipts_id')
            ->withPivot('amount')
            ->withTimestamps();
    }
}
