<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Threshold extends Model
{
    protected $fillable = [
        'market_id',
        'device_id',
        'parameter',
        'minimum_value',
        'maximum_value',
        'unit',
        'is_active',
    ];

    protected $casts = [
        'minimum_value' => 'decimal:2',
        'maximum_value' => 'decimal:2',
        'is_active'     => 'boolean',
    ];

    public function market()
    {
        return $this->belongsTo(Market::class);
    }

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function alerts()
    {
        return $this->hasMany(Alert::class);
    }
}
