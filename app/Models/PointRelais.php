<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PointRelais extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'points_relais';

    protected $fillable = [
        'uuid',
        'name',
        'address',
        'city',
        'country',
        'latitude',
        'longitude',
        'manager_name',
        'manager_phone',
        'manager_email',
        'status',
        'storage_capacity',
        'current_storage',
        'operating_hours',
        'staff_user_id',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'storage_capacity' => 'integer',
        'current_storage' => 'integer',
        'operating_hours' => 'array',
    ];

    // Relations
    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_user_id');
    }

    public function colis(): HasMany
    {
        return $this->hasMany(Colis::class);
    }

    public function cabines(): HasMany
    {
        return $this->hasMany(Cabine::class);
    }

    public function essais(): HasMany
    {
        return $this->hasMany(Essai::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByCity($query, $city)
    {
        return $query->where('city', $city);
    }

    public function scopeWithAvailableStorage($query)
    {
        return $query->whereRaw('current_storage < storage_capacity');
    }

    // Methods
    public function isAvailable(): bool
    {
        return $this->status === 'active' && $this->current_storage < $this->storage_capacity;
    }

    public function getStoragePercentage(): float
    {
        return $this->storage_capacity > 0
            ? ($this->current_storage / $this->storage_capacity) * 100
            : 0;
    }

    public function incrementStorage(): void
    {
        $this->current_storage += 1;
        $this->save();
    }

    public function decrementStorage(): void
    {
        $this->current_storage = max(0, $this->current_storage - 1);
        $this->save();
    }

    public function getFullAddressAttribute(): string
    {
        return "{$this->address}, {$this->city}, {$this->country}";
    }
}
