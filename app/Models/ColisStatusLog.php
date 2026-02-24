<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ColisStatusLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'colis_id',
        'old_status',
        'new_status',
        'changed_by_user_id',
        'change_reason',
        'metadata',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    // Relations
    public function colis(): BelongsTo
    {
        return $this->belongsTo(Colis::class);
    }

    public function changedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by_user_id');
    }

    // Scopes
    public function scopeByColis($query, $colisId)
    {
        return $query->where('colis_id', $colisId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('new_status', $status);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('changed_by_user_id', $userId);
    }

    // Methods
    public function getFormattedChangeAttribute(): string
    {
        $oldStatus = $this->old_status ? "de {$this->old_status}" : '';
        return "Changement{$oldStatus} vers {$this->new_status}";
    }

    public function isSystemGenerated(): bool
    {
        return is_null($this->changed_by_user_id);
    }
}
