<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'first_name',
        'last_name',
        'phone',
        'email',
        'id_card_number',
        'address',
        'verification_status',
        'total_spent',
        'total_orders',
        'last_order_at',
    ];

    protected $casts = [
        'last_order_at' => 'datetime',
        'total_spent' => 'decimal:2',
        'total_orders' => 'integer',
    ];

    // Relations
    public function colis(): HasMany
    {
        return $this->hasMany(Colis::class);
    }

    public function essais(): HasMany
    {
        return $this->hasMany(Essai::class);
    }

    public function paiements(): HasMany
    {
        return $this->hasMany(Paiement::class);
    }

    // Scopes
    public function scopeVerified($query)
    {
        return $query->where('verification_status', 'verified');
    }

    public function scopeByPhone($query, $phone)
    {
        return $query->where('phone', $phone);
    }

    // Methods
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function isVerified(): bool
    {
        return $this->verification_status === 'verified';
    }

    public function updateOrderStats(float $amount): void
    {
        $this->total_orders += 1;
        $this->total_spent += $amount;
        $this->last_order_at = now();
        $this->save();
    }
}
