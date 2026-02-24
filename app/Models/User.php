<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'email_verified_at',
        'is_active',
        'last_login_at',
        'last_login_ip',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
        'password' => 'hashed',
    ];

    // Relations
    public function vendor()
    {
        return $this->hasOne(Vendor::class);
    }

    public function managedPointRelais()
    {
        return $this->hasOne(PointRelais::class, 'staff_user_id');
    }

    public function colisStatusLogs()
    {
        return $this->hasMany(ColisStatusLog::class, 'changed_by_user_id');
    }

    public function processedReversements()
    {
        return $this->hasMany(Reversement::class, 'processed_by');
    }

    public function essais()
    {
        return $this->hasMany(Essai::class, 'staff_user_id');
    }

    public function logs()
    {
        return $this->hasMany(LogSysteme::class);
    }

    public function notifications()
    {
        return $this->morphMany(Notification::class, 'notifiable');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeVendors($query)
    {
        return $query->whereHas('roles', function ($q) {
            $q->where('name', 'vendor');
        });
    }

    public function scopeStaff($query)
    {
        return $query->whereHas('roles', function ($q) {
            $q->whereIn('name', ['staff', 'admin']);
        });
    }
}
