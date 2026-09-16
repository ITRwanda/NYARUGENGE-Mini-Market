<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Market extends Model
{
    protected $fillable = [
        'name',
        'district',
        'city',
        'country',
        'description',
        'is_active',
    ];

    public function vendors()
    {
        return $this->hasMany(Vendor::class);
    }

    public function stalls()
    {
        return $this->hasMany(Stall::class);
    }

    public function devices()
    {
        return $this->hasMany(Device::class);
    }

    public function thresholds()
    {
        return $this->hasMany(Threshold::class);
    }
}
