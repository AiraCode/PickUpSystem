<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Warehouse extends Model
{
    use SoftDeletes;
    protected $table = 'storages';

    public $incrementing = true;

    protected $fillable = ['id', 'name', 'address', 'lat', 'long'];

    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class, 'storages_id');
    }
 
    public function admins(): HasMany
    {
        return $this->hasMany(User::class, 'warehouse_id');
    }
}
