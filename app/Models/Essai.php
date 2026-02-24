<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Essai extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'colis_id',
        'client_id',
        'cabine_id',
        'point_relais_id',
        'status',
        'fee',
        'fee_paid',
        'payment_id',
        'id_card_number',
        'id_card_photo',
        'deposit_amount',
        'result',
        'rejection_reason',
        'started_at',
        'completed_at',
        'cancelled_at',
        'staff_user_id',
        'photos_taken',
        'notes',
    ];

    protected $casts = [
        'fee' => 'decimal:2',
        'fee_paid' => 'boolean',
        'deposit_amount' => 'decimal:2',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'photos_taken' => 'array',
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

    public function cabine(): BelongsTo
    {
        return $this->belongsTo(Cabine::class);
    }

    public function pointRelais(): BelongsTo
    {
        return $this->belongsTo(PointRelais::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Paiement::class, 'payment_id');
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_user_id');
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    public function scopeByCabine($query, $cabineId)
    {
        return $query->where('cabine_id', $cabineId);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['started', 'in_progress']);
    }

    // Methods
    public function isActive(): bool
    {
        return in_array($this->status, ['started', 'in_progress']);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function isApproved(): bool
    {
        return $this->result === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->result === 'rejected';
    }

    public function start(): void
    {
        $this->update([
            'status' => 'in_progress',
            'started_at' => now(),
        ]);
    }

    public function complete(string $result, ?string $rejectionReason = null): void
    {
        $this->update([
            'status' => 'completed',
            'result' => $result,
            'rejection_reason' => $rejectionReason,
            'completed_at' => now(),
        ]);

        // Libérer la cabine
        if ($this->cabine) {
            $this->cabine->decrementOccupancy();
        }
    }

    public function cancel(string $reason): void
    {
        $this->update([
            'status' => 'cancelled',
            'rejection_reason' => $reason,
            'cancelled_at' => now(),
        ]);

        // Libérer la cabine
        if ($this->cabine) {
            $this->cabine->decrementOccupancy();
        }
    }

    public function markFeeAsPaid(int $paymentId): void
    {
        $this->update([
            'fee_paid' => true,
            'payment_id' => $paymentId,
        ]);
    }

    public function getDurationAttribute(): ?string
    {
        if (!$this->started_at) {
            return null;
        }

        $endTime = $this->completed_at ?? $this->cancelled_at ?? now();
        return $this->started_at->diffForHumans($endTime, true);
    }

    public function getFormattedFeeAttribute(): string
    {
        return number_format((float) $this->fee, 0, ',', ' ') . ' FCFA';
    }
}
