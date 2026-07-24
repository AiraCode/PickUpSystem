<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceHistory extends Model
{
    protected $fillable = ['type', 'label', 'old_value', 'new_value'];
}
