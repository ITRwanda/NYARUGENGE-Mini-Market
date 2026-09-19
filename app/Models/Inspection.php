<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inspection extends Model
{
    protected $fillable = [
        'market_id',
        'stall_id',
        'inspector_id',
        'inspection_date',
        'status',
        'general_notes',
    ];

    protected $casts = [
        'inspection_date' => 'datetime',
    ];

    public function market()
    {
        return $this->belongsTo(Market::class);
    }

    public function stall()
    {
        return $this->belongsTo(Stall::class);
    }

    public function inspector()
    {
        return $this->belongsTo(User::class, 'inspector_id');
    }

    public function items()
    {
        return $this->hasMany(InspectionItem::class);
    }
}
