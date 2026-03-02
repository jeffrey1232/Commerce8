<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Package extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tracking_code',
        'vendor_id',
        'client_name',
        'client_phone',
        'product_name',
        'product_description',
        'product_images',
        'total_amount',
        'commission_amount',
        'net_amount',
        'status',
        'deposited_at',
        'sold_at',
        'returned_at',
    ];

    protected $casts = [
        'product_images' => 'array',
        'total_amount' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'deposited_at' => 'datetime',
        'sold_at' => 'datetime',
        'returned_at' => 'datetime',
    ];

    // Relations
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function studioSession()
    {
        return $this->hasOne(StudioSession::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeDeposited($query)
    {
        return $query->where('status', 'deposited');
    }

    public function scopeSold($query)
    {
        return $query->where('status', 'sold');
    }

    public function scopeReturned($query)
    {
        return $query->where('status', 'returned');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue');
    }

    public function scopeByVendor($query, $vendorId)
    {
        return $query->where('vendor_id', $vendorId);
    }

    // Methods
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isDeposited(): bool
    {
        return $this->status === 'deposited';
    }

    public function isSold(): bool
    {
        return $this->status === 'sold';
    }

    public function isReturned(): bool
    {
        return $this->status === 'returned';
    }

    public function isOverdue(): bool
    {
        return $this->status === 'overdue';
    }

    public function canBeDeposited(): bool
    {
        return $this->isPending();
    }

    public function canBeSold(): bool
    {
        return $this->isDeposited();
    }

    public function canBeReturned(): bool
    {
        return $this->isSold();
    }

    public function markAsDeposited(): bool
    {
        $this->status = 'deposited';
        $this->deposited_at = now();
        return $this->save();
    }

    public function markAsSold(): bool
    {
        $this->status = 'sold';
        $this->sold_at = now();
        return $this->save();
    }

    public function markAsReturned(): bool
    {
        $this->status = 'returned';
        $this->returned_at = now();
        return $this->save();
    }

    public function markAsOverdue(): bool
    {
        $this->status = 'overdue';
        return $this->save();
    }
}
