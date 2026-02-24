<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Paiement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'transaction_id',
        'idempotency_key',
        'colis_id',
        'client_id',
        'amount',
        'currency',
        'provider',
        'provider_transaction_id',
        'status',
        'payment_method',
        'phone_number',
        'provider_response',
        'webhook_signature',
        'webhook_received_at',
        'completed_at',
        'fees',
        'net_amount',
        'failure_reason',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'fees' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'webhook_received_at' => 'datetime',
        'completed_at' => 'datetime',
        'provider_response' => 'array',
        'metadata' => 'array',
    ];

    // Relations
    public function colis(): BelongsTo
    {
        return $this->belongsTo(Colis::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByProvider($query, $provider)
    {
        return $query->where('provider', $provider);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    // Methods
    public function isSuccessful(): bool
    {
        return $this->status === 'completed';
    }

    public function isPending(): bool
    {
        return in_array($this->status, ['pending', 'processing']);
    }

    public function isFailed(): bool
    {
        return in_array($this->status, ['failed', 'cancelled']);
    }

    public function canBeRetried(): bool
    {
        return $this->isFailed() && $this->retry_count < 3;
    }

    public function generateIdempotencyKey(): string
    {
        return 'pay_' . $this->colis_id . '_' . uniqid();
    }

    public function verifyWebhookSignature(string $signature): bool
    {
        return hash_equals($this->webhook_signature, $signature);
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
}
