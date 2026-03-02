<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'store_name',
        'store_address',
        'store_phone',
        'business_license',
        'commission_rate',
        'rating',
        'total_packages',
        'total_revenue',
        'status',
    ];

    protected $casts = [
        'commission_rate' => 'decimal:2',
        'rating' => 'decimal:2',
        'total_packages' => 'integer',
        'total_revenue' => 'decimal:2',
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function packages()
    {
        return $this->hasMany(Package::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function studioSessions()
    {
        return $this->hasMany(StudioSession::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function scopeSuspended($query)
    {
        return $query->where('status', 'suspended');
    }

    public function scopeByRating($query, $minRating = 0)
    {
        return $query->where('rating', '>=', $minRating);
    }

    // Methods
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isInactive(): bool
    {
        return $this->status === 'inactive';
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    public function calculateCommission(float $amount): float
    {
        return $amount * ($this->commission_rate / 100);
    }

    public function calculateNetAmount(float $amount): float
    {
        return $amount - $this->calculateCommission($amount);
    }

    public function updateStats(): void
    {
        $this->total_packages = $this->packages()->count();
        $this->total_revenue = $this->payments()->where('payment_status', 'completed')->sum('net_amount');
        $this->save();
    }

    public function getWalletBalance(): float
    {
        return $this->wallet ? $this->wallet->balance : 0;
    }

    public function getPendingBalance(): float
    {
        return $this->wallet ? $this->wallet->pending_balance : 0;
    }
}
