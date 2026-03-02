<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'package_id',
        'vendor_id',
        'amount',
        'commission',
        'net_amount',
        'payment_method',
        'payment_status',
        'transaction_reference',
        'processed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'commission' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    // Relations
    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('payment_status', 'pending');
    }

    public function scopeProcessing($query)
    {
        return $query->where('payment_status', 'processing');
    }

    public function scopeCompleted($query)
    {
        return $query->where('payment_status', 'completed');
    }

    public function scopeFailed($query)
    {
        return $query->where('payment_status', 'failed');
    }

    public function scopeByMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }

    // Methods
    public function isPending(): bool
    {
        return $this->payment_status === 'pending';
    }

    public function isProcessing(): bool
    {
        return $this->payment_status === 'processing';
    }

    public function isCompleted(): bool
    {
        return $this->payment_status === 'completed';
    }

    public function isFailed(): bool
    {
        return $this->payment_status === 'failed';
    }

    public function markAsCompleted(): bool
    {
        $this->payment_status = 'completed';
        $this->processed_at = now();
        return $this->save();
    }

    public function markAsFailed(): bool
    {
        $this->payment_status = 'failed';
        return $this->save();
    }

    public function canBeProcessed(): bool
    {
        return $this->isPending();
    }
}
