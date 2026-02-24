<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vendor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'user_id',
        'business_name',
        'contact_phone',
        'contact_email',
        'address',
        'id_card_number',
        'id_card_photo',
        'status',
        'commission_rate',
        'balance',
        'business_documents',
        'approved_at',
        'approved_by',
        'rejection_reason',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'commission_rate' => 'decimal:2',
        'balance' => 'decimal:2',
        'business_documents' => 'array',
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function colis(): HasMany
    {
        return $this->hasMany(Colis::class);
    }

    public function reversements(): HasMany
    {
        return $this->hasMany(Reversement::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeByBalance($query, $minBalance = 0)
    {
        return $query->where('balance', '>=', $minBalance);
    }

    // Methods
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function canRequestReversement(): bool
    {
        return $this->isApproved() && $this->balance > 0;
    }

    public function calculateCommission(float $amount): float
    {
        return $amount * ($this->commission_rate / 100);
    }

    public function updateBalance(float $amount): void
    {
        $this->balance += $amount;
        $this->save();
    }
}
