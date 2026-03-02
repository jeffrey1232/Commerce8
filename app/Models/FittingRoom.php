<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FittingRoom extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'status',
        'current_client',
        'guarantee_type',
        'guarantee_details',
        'check_in_time',
        'check_out_time',
    ];

    protected $casts = [
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime',
    ];

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function scopeOccupied($query)
    {
        return $query->where('status', 'occupied');
    }

    public function scopeMaintenance($query)
    {
        return $query->where('status', 'maintenance');
    }

    // Methods
    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }

    public function isOccupied(): bool
    {
        return $this->status === 'occupied';
    }

    public function isMaintenance(): bool
    {
        return $this->status === 'maintenance';
    }

    public function checkIn(string $clientName, string $guaranteeType, string $guaranteeDetails): bool
    {
        if ($this->isAvailable()) {
            $this->status = 'occupied';
            $this->current_client = $clientName;
            $this->guarantee_type = $guaranteeType;
            $this->guarantee_details = $guaranteeDetails;
            $this->check_in_time = now();
            return $this->save();
        }
        return false;
    }

    public function checkOut(): bool
    {
        if ($this->isOccupied()) {
            $this->status = 'available';
            $this->current_client = null;
            $this->guarantee_type = null;
            $this->guarantee_details = null;
            $this->check_out_time = now();
            return $this->save();
        }
        return false;
    }

    public function getOccupancyDuration(): ?string
    {
        if ($this->isOccupied() && $this->check_in_time) {
            return $this->check_in_time->diffForHumans(now(), true);
        }
        return null;
    }
}
