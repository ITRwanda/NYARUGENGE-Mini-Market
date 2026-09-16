<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $fillable = [
        'market_id',
        'stall_id',
        'device_uid',
        'device_name',
        'microcontroller',
        'communication_module',
        'temperature_sensor',
        'humidity_sensor',
        'gas_sensor',
        'firmware_version',
        'last_seen_at',
        'status',
        'is_active',
    ];

    protected $casts = [
        'last_seen_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function market()
    {
        return $this->belongsTo(Market::class);
    }

    public function stall()
    {
        return $this->belongsTo(Stall::class);
    }

    public function readings()
    {
        return $this->hasMany(SensorReading::class);
    }

    public function thresholds()
    {
        return $this->hasMany(Threshold::class);
    }
}
