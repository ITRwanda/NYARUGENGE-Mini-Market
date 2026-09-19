<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stall extends Model
{
    protected $fillable = [
        'market_id',
        'vendor_id',
        'stall_number',
        'section',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function market()
    {
        return $this->belongsTo(Market::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function devices()
    {
        return $this->hasMany(Device::class);
    }

    public function sensorReadings()
    {
        return $this->hasMany(SensorReading::class);
    }

    public function alerts()
    {
        return $this->hasMany(Alert::class);
    }

    public function inspections()
    {
        return $this->hasMany(Inspection::class);
    }
}
