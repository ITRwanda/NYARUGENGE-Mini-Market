<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    protected $fillable = [
        'device_id',
        'stall_id',
        'sensor_reading_id',
        'threshold_id',
        'parameter',
        'measured_value',
        'threshold_value',
        'severity',
        'message',
        'status',
        'acknowledged_by',
        'acknowledged_at',
        'resolved_by',
        'resolved_at',
    ];

    protected $casts = [
        'measured_value'   => 'decimal:2',
        'threshold_value'  => 'decimal:2',
        'acknowledged_at'  => 'datetime',
        'resolved_at'      => 'datetime',
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function stall()
    {
        return $this->belongsTo(Stall::class);
    }

    public function sensorReading()
    {
        return $this->belongsTo(SensorReading::class);
    }

    public function threshold()
    {
        return $this->belongsTo(Threshold::class);
    }

    public function acknowledgedBy()
    {
        return $this->belongsTo(User::class, 'acknowledged_by');
    }

    public function resolvedBy()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }
}
