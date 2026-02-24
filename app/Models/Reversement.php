<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reversement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'reference',
        'vendor_id',
        'payment_id',
        'gross_amount',
        'commission_rate',
        'commission_amount',
        'net_amount',
        'status',
        'provider',
        'provider_transaction_id',
        'recipient_phone',
        'recipient_account',
        'provider_response',
        'processed_at',
        'completed_at',
        'processed_by',
        'failure_reason',
        'batch_details',
        'metadata',
    ];

    protected $casts = [
        'gross_amount' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'processed_at' => 'datetime',
        'completed_at' => 'datetime',
        'provider_response' => 'array',
        'batch_details' => 'array',
        'metadata' => 'array',
    ];

    // Relations
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Paiement::class, 'payment_id');
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeByVendor($query, $vendorId)
    {
        return $query->where('vendor_id', $vendorId);
    }

    // Methods
    public function generateReference(): string
    {
        return 'REV' . date('Ym') . str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isFailed(): bool
    {
        return in_array($this->status, ['failed', 'cancelled']);
    }

    public function calculateCommission(float $amount, float $rate): float
    {
        return $amount * ($rate / 100);
    }

    public function markAsProcessed(int $processedBy): void
    {
        $this->update([
            'status' => 'processing',
            'processed_at' => now(),
            'processed_by' => $processedBy,
        ]);
    }

    public function markAsCompleted(string $providerTransactionId, array $response = []): void
    {
        $this->update([
            'status' => 'completed',
            'provider_transaction_id' => $providerTransactionId,
            'provider_response' => $response,
            'completed_at' => now(),
        ]);
    }

    public function markAsFailed(string $reason): void
    {
        $this->update([
            'status' => 'failed',
            'failure_reason' => $reason,
        ]);
    }

    public function getFormattedAmountAttribute(): string
    {
        return number_format((float) $this->net_amount, 0, ',', ' ') . ' FCFA';
    }
}
