<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    protected $fillable = [
        'user_id',
        'market_id',
        'vendor_code',
        'business_name',
        'phone',
        'food_category',
        'is_active',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function market()
    {
        return $this->belongsTo(Market::class);
    }

    public function stalls()
    {
        return $this->hasMany(Stall::class);
    }
}