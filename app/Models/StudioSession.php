<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudioSession extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'vendor_id',
        'package_id',
        'session_type',
        'scheduled_time',
        'duration',
        'status',
        'photos_count',
    ];

    protected $casts = [
        'scheduled_time' => 'datetime',
        'duration' => 'integer',
        'photos_count' => 'integer',
    ];

    // Relations
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    // Scopes
    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('session_type', $type);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('scheduled_time', '>', now());
    }

    public function scopePast($query)
    {
        return $query->where('scheduled_time', '<', now());
    }

    // Methods
    public function isScheduled(): bool
    {
        return $this->status === 'scheduled';
    }

    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function startSession(): bool
    {
        if ($this->isScheduled()) {
            $this->status = 'in_progress';
            return $this->save();
        }
        return false;
    }

    public function completeSession(int $photosCount = 0): bool
    {
        if ($this->isInProgress()) {
            $this->status = 'completed';
            $this->photos_count = $photosCount;
            return $this->save();
        }
        return false;
    }

    public function cancelSession(): bool
    {
        if ($this->isScheduled() || $this->isInProgress()) {
            $this->status = 'cancelled';
            return $this->save();
        }
        return false;
    }

    public function getRemainingTime(): ?string
    {
        if ($this->isScheduled() && $this->scheduled_time > now()) {
            return $this->scheduled_time->diffForHumans(now(), true);
        }
        return null;
    }
}
