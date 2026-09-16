<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SensorReading extends Model
{
    protected $fillable = [
        'device_id',
        'stall_id',
        'temperature',
        'humidity',
        'gas_level',
        'recorded_at',
    ];

    protected $casts = [
        'temperature' => 'decimal:2',
        'humidity' => 'decimal:2',
        'gas_level' => 'decimal:2',
        'recorded_at' => 'datetime',
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function stall()
    {
        return $this->belongsTo(Stall::class);
    }
}