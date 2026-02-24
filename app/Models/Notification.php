<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Notification extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'notifiable_type',
        'notifiable_id',
        'type',
        'channel',
        'recipient',
        'subject',
        'content',
        'status',
        'provider_response',
        'provider_message_id',
        'sent_at',
        'delivered_at',
        'failed_at',
        'failure_reason',
        'retry_count',
        'next_retry_at',
        'metadata',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'failed_at' => 'datetime',
        'next_retry_at' => 'datetime',
        'provider_response' => 'array',
        'metadata' => 'array',
    ];

    // Relations
    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByChannel($query, $channel)
    {
        return $query->where('channel', $channel);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeReadyForRetry($query)
    {
        return $query->where('status', 'failed')
                    ->where('retry_count', '<', 3)
                    ->where('next_retry_at', '<=', now());
    }

    // Methods
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isSent(): bool
    {
        return $this->status === 'sent';
    }

    public function isDelivered(): bool
    {
        return $this->status === 'delivered';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function canBeRetried(): bool
    {
        return $this->isFailed() && $this->retry_count < 3;
    }

    public function markAsSent(array $response = []): void
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
            'provider_response' => $response,
        ]);
    }

    public function markAsDelivered(string $messageId): void
    {
        $this->update([
            'status' => 'delivered',
            'delivered_at' => now(),
            'provider_message_id' => $messageId,
        ]);
    }

    public function markAsFailed(string $reason): void
    {
        $this->update([
            'status' => 'failed',
            'failed_at' => now(),
            'failure_reason' => $reason,
            'retry_count' => $this->retry_count + 1,
            'next_retry_at' => now()->addMinutes(15 * ($this->retry_count + 1)),
        ]);
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'colis_deposited' => 'Colis déposé',
            'colis_ready_withdrawal' => 'Colis prêt pour retrait',
            'payment_completed' => 'Paiement effectué',
            'reversement_processed' => 'Reversement effectué',
            'fitting_reminder' => 'Rappel essayage',
            'storage_overdue' => 'Stockage en retard',
            default => ucfirst($this->type),
        };
    }

    public function getChannelLabelAttribute(): string
    {
        return match($this->channel) {
            'sms' => 'SMS',
            'email' => 'Email',
            'whatsapp' => 'WhatsApp',
            'push' => 'Push',
            'database' => 'Base de données',
            default => ucfirst($this->channel),
        };
    }
}
