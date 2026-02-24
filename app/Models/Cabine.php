<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cabine extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'point_relais_id',
        'name',
        'status',
        'fee',
        'max_capacity',
        'current_occupancy',
        'equipment',
        'description',
    ];

    protected $casts = [
        'fee' => 'decimal:2',
        'max_capacity' => 'integer',
        'current_occupancy' => 'integer',
        'equipment' => 'array',
    ];

    // Relations
    public function pointRelais(): BelongsTo
    {
        return $this->belongsTo(PointRelais::class);
    }

    public function essais(): HasMany
    {
        return $this->hasMany(Essai::class);
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available')
                    ->whereRaw('current_occupancy < max_capacity');
    }

    public function scopeByPointRelais($query, $pointRelaisId)
    {
        return $query->where('point_relais_id', $pointRelaisId);
    }

    // Methods
    public function isAvailable(): bool
    {
        return $this->status === 'available' && $this->current_occupancy < $this->max_capacity;
    }

    public function isOccupied(): bool
    {
        return $this->status === 'occupied' || $this->current_occupancy >= $this->max_capacity;
    }

    public function getOccupancyPercentage(): float
    {
        return $this->max_capacity > 0
            ? ($this->current_occupancy / $this->max_capacity) * 100
            : 0;
    }

    public function incrementOccupancy(): void
    {
        $this->current_occupancy += 1;
        if ($this->current_occupancy >= $this->max_capacity) {
            $this->status = 'occupied';
        }
        $this->save();
    }

    public function decrementOccupancy(): void
    {
        $this->current_occupancy = max(0, $this->current_occupancy - 1);
        if ($this->current_occupancy < $this->max_capacity) {
            $this->status = 'available';
        }
        $this->save();
    }

    public function markAsMaintenance(): void
    {
        $this->update(['status' => 'maintenance']);
    }

    public function markAsAvailable(): void
    {
        $this->update(['status' => 'available']);
    }

    public function getFormattedFeeAttribute(): string
    {
        return number_format((float) $this->fee, 0, ',', ' ') . ' FCFA';
    }
}
