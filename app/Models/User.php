<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function vendor()
    {
        return $this->hasOne(Vendor::class);
    }

    public function inspections()
    {
        return $this->hasMany(
            Inspection::class,
            'inspector_id'
        );
    }

    public function acknowledgedAlerts()
    {
        return $this->hasMany(
            Alert::class,
            'acknowledged_by'
        );
    }

    public function resolvedAlerts()
    {
        return $this->hasMany(
            Alert::class,
            'resolved_by'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Role Helpers
    |--------------------------------------------------------------------------
    */

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isMarketAdmin(): bool
    {
        return $this->role === 'market_admin';
    }

    public function isInspector(): bool
    {
        return $this->role === 'inspector';
    }

    public function isVendor(): bool
    {
        return $this->role === 'vendor';
    }
}