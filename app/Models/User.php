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

    /**
     * Roles: admin, inspector, vendor
     * (Nyarugenge Mini Market — single-market system)
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'is_locked',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_locked'         => 'boolean',
        ];
    }

    public function vendor()
    {
        return $this->hasOne(Vendor::class);
    }

    public function inspections()
    {
        return $this->hasMany(Inspection::class, 'inspector_id');
    }

    /** Stalls this inspector is assigned to monitor */
    public function assignedStalls()
    {
        return $this->belongsToMany(Stall::class, 'inspector_stall', 'user_id', 'stall_id')
                    ->withTimestamps();
    }

    public function acknowledgedAlerts()
    {
        return $this->hasMany(Alert::class, 'acknowledged_by');
    }

    public function resolvedAlerts()
    {
        return $this->hasMany(Alert::class, 'resolved_by');
    }

    /* ── Role helpers ─────────────────────────────────────────── */

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isInspector(): bool
    {
        return $this->role === 'inspector';
    }

    public function isVendor(): bool
    {
        return $this->role === 'vendor';
    }

    /** Admin can do everything market_admin could */
    public function isMarketAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
