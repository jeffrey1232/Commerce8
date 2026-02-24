<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Colis extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'tracking_code',
        'vendor_id',
        'client_id',
        'point_relais_id',
        'product_name',
        'description',
        'price',
        'shipping_fee',
        'total_amount',
        'product_photo',
        'status',
        'fitting_option',
        'fitting_fee',
        'deposited_at',
        'withdrawn_at',
        'paid_at',
        'reversed_at',
        'storage_deadline',
        'storage_fee',
        'rejection_reason',
        'client_phone',
        'client_email',
        'metadata',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'shipping_fee' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'fitting_option' => 'boolean',
        'fitting_fee' => 'decimal:2',
        'deposited_at' => 'datetime',
        'withdrawn_at' => 'datetime',
        'paid_at' => 'datetime',
        'reversed_at' => 'datetime',
        'storage_deadline' => 'datetime',
        'storage_fee' => 'decimal:2',
        'metadata' => 'array',
    ];

    // Relations
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function pointRelais(): BelongsTo
    {
        return $this->belongsTo(PointRelais::class);
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(ColisStatusLog::class);
    }

    public function paiement(): HasOne
    {
        return $this->hasOne(Paiement::class);
    }

    public function reversement(): HasOne
    {
        return $this->hasOne(Reversement::class);
    }

    public function essai(): HasOne
    {
        return $this->hasOne(Essai::class);
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByVendor($query, $vendorId)
    {
        return $query->where('vendor_id', $vendorId);
    }

    public function scopeByPointRelais($query, $pointRelaisId)
    {
        return $query->where('point_relais_id', $pointRelaisId);
    }

    public function scopePendingWithdrawal($query)
    {
        return $query->where('status', 'pending_withdrawal');
    }

    public function scopeInStorage($query)
    {
        return $query->where('status', 'in_storage')
                    ->where('storage_deadline', '<', now());
    }

    // Methods
    public function generateTrackingCode(): string
    {
        return 'ECM' . str_pad($this->id, 8, '0', STR_PAD_LEFT) . strtoupper(substr(uniqid(), -4));
    }

    public function canBeWithdrawn(): bool
    {
        return in_array($this->status, ['deposited', 'pending_withdrawal']);
    }

    public function canBePaid(): bool
    {
        return in_array($this->status, ['pending_withdrawal', 'in_fitting']);
    }

    public function isOverdue(): bool
    {
        return $this->storage_deadline && $this->storage_deadline->isPast();
    }

    public function calculateStorageFee(): float
    {
        if (!$this->storage_deadline) {
            return 0;
        }

        $daysOverdue = max(0, now()->diffInDays($this->storage_deadline));
        return $daysOverdue * 100; // 100 FCFA per day
    }

    public function updateStatus(string $newStatus, ?string $reason = null, ?int $userId = null): void
    {
        $oldStatus = $this->status;
        $this->status = $newStatus;
        $this->save();

        // Log the status change
        $this->statusLogs()->create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'changed_by_user_id' => $userId,
            'change_reason' => $reason,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public function getTotalWithFees(): float
    {
        return $this->total_amount + $this->fitting_fee + $this->storage_fee;
    }
}
