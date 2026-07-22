<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Warehouse extends Model
{
    protected $table = 'storages';

    public $incrementing = false;

    protected $fillable = ['id', 'name', 'address', 'lat', 'long'];

    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class, 'storages_id');
    }
}
