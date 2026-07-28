<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceHistory extends Model
{
    // Keep both variants in fillable to be tolerant of existing DB column name (LME) vs code (lme)
    protected $fillable = ['type', 'label', 'old_value', 'new_value', 'lme', 'LME'];

    // Ensure the normalized accessor 'lme' is included when model is serialized to array/JSON
    protected $appends = ['lme'];

    // Provide a normalized attribute 'lme' for code/frontend convenience
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
        // Prefer writing to the existing DB column name if present
        if (array_key_exists('LME', $this->attributes)) {
            $this->attributes['LME'] = $value;
        } else {
            $this->attributes['lme'] = $value;
        }
    }
}
