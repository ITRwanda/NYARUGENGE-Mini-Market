<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InspectionItem extends Model
{
    protected $fillable = [
        'inspection_id',
        'item',
        'status',
        'notes',
    ];

    public function inspection()
    {
        return $this->belongsTo(Inspection::class);
    }
}
