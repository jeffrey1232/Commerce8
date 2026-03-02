<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'address',
        'status',
        'last_login',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login' => 'datetime',
        'password' => 'hashed',
    ];

    // Relations
    public function vendor()
    {
        return $this->hasOne(Vendor::class);
    }

    public function packages()
    {
        return $this->hasManyThrough(Package::class, Vendor::class);
    }

    public function payments()
    {
        return $this->hasManyThrough(Payment::class, Vendor::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    public function scopeVendors($query)
    {
        return $query->where('role', 'vendor');
    }

    public function scopeClients($query)
    {
        return $query->where('role', 'client');
    }

    public function scopeCommunityManagers($query)
    {
        return $query->where('role', 'community_manager');
    }

    public function scopeStaff($query)
    {
        return $query->where('role', 'staff');
    }

    // Methods
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isVendor(): bool
    {
        return $this->role === 'vendor';
    }

    public function isClient(): bool
    {
        return $this->role === 'client';
    }

    public function isCommunityManager(): bool
    {
        return $this->role === 'community_manager';
    }

    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }
}
