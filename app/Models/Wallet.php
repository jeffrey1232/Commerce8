<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Wallet extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'vendor_id',
        'balance',
        'pending_balance',
        'total_earned',
        'total_withdrawn',
        'last_updated',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'pending_balance' => 'decimal:2',
        'total_earned' => 'decimal:2',
        'total_withdrawn' => 'decimal:2',
        'last_updated' => 'datetime',
    ];

    // Relations
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    // Methods
    public function addPending(float $amount): bool
    {
        $this->pending_balance += $amount;
        $this->total_earned += $amount;
        $this->last_updated = now();
        return $this->save();
    }

    public function confirmPending(float $amount): bool
    {
        if ($this->pending_balance >= $amount) {
            $this->pending_balance -= $amount;
            $this->balance += $amount;
            $this->last_updated = now();
            return $this->save();
        }
        return false;
    }

    public function withdraw(float $amount): bool
    {
        if ($this->balance >= $amount) {
            $this->balance -= $amount;
            $this->total_withdrawn += $amount;
            $this->last_updated = now();
            return $this->save();
        }
        return false;
    }

    public function getTotalBalance(): float
    {
        return $this->balance + $this->pending_balance;
    }

    public function canWithdraw(float $amount): bool
    {
        return $this->balance >= $amount;
    }
}
