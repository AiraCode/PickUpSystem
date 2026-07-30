<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceHistory extends Model
{
    protected $fillable = ['type', 'label', 'old_value', 'new_value', 'lme', 'LME'];
    protected $appends = ['lme'];

    public function getLmeAttribute()
    {
        // prefer numeric return
        if (array_key_exists('lme', $this->attributes) && $this->attributes['lme'] !== null) {
            return is_numeric($this->attributes['lme']) ? (float) $this->attributes['lme'] : $this->attributes['lme'];
        }
        if (array_key_exists('LME', $this->attributes) && $this->attributes['LME'] !== null) {
            return is_numeric($this->attributes['LME']) ? (float) $this->attributes['LME'] : $this->attributes['LME'];
        }
        return null;
    }

    public function setLmeAttribute($value)
    {
        if (array_key_exists('LME', $this->attributes)) {
            $this->attributes['LME'] = $value;
        } else {
            $this->attributes['lme'] = $value;
        }
    }
}
