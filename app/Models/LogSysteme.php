<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogSysteme extends Model
{
    use HasFactory;

    protected $table = 'logs_systeme';

    protected $fillable = [
        'uuid',
        'level',
        'message',
        'context',
        'user_id',
        'ip_address',
        'user_agent',
        'action',
        'resource_type',
        'resource_id',
        'old_values',
        'new_values',
        'metadata',
        'exception',
        'request_id',
        'session_id',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'metadata' => 'array',
    ];

    // Relations
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    public function scopeByContext($query, $context)
    {
        return $query->where('context', $context);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    public function scopeError($query)
    {
        return $query->whereIn('level', ['error', 'critical', 'emergency']);
    }

    public function scopeSecurity($query)
    {
        return $query->where('context', 'security');
    }

    // Methods
    public static function log(string $level, string $message, array $context = []): self
    {
        return self::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'level' => $level,
            'message' => $message,
            'context' => $context['context'] ?? 'system',
            'user_id' => \Illuminate\Support\Facades\Auth::check() ? \Illuminate\Support\Facades\Auth::id() : null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'action' => $context['action'] ?? null,
            'resource_type' => $context['resource_type'] ?? null,
            'resource_id' => $context['resource_id'] ?? null,
            'old_values' => $context['old_values'] ?? null,
            'new_values' => $context['new_values'] ?? null,
            'metadata' => $context['metadata'] ?? null,
            'exception' => $context['exception'] ?? null,
            'request_id' => $context['request_id'] ?? null,
            'session_id' => session()->getId(),
        ]);
    }

    public static function logPayment(string $message, array $data = []): self
    {
        return self::log('info', $message, [
            'context' => 'payment',
            'action' => $data['action'] ?? 'payment_processed',
            'resource_type' => 'paiement',
            'resource_id' => $data['payment_id'] ?? null,
            'metadata' => $data,
        ]);
    }

    public static function logSecurity(string $message, array $data = []): self
    {
        return self::log('warning', $message, [
            'context' => 'security',
            'action' => $data['action'] ?? 'security_event',
            'resource_type' => $data['resource_type'] ?? null,
            'resource_id' => $data['resource_id'] ?? null,
            'metadata' => $data,
        ]);
    }

    public static function logColis(string $message, array $data = []): self
    {
        return self::log('info', $message, [
            'context' => 'colis',
            'action' => $data['action'] ?? 'colis_updated',
            'resource_type' => 'colis',
            'resource_id' => $data['colis_id'] ?? null,
            'old_values' => $data['old_values'] ?? null,
            'new_values' => $data['new_values'] ?? null,
            'metadata' => $data,
        ]);
    }

    public function getLevelLabelAttribute(): string
    {
        return match($this->level) {
            'debug' => 'Débogage',
            'info' => 'Information',
            'notice' => 'Notice',
            'warning' => 'Avertissement',
            'error' => 'Erreur',
            'critical' => 'Critique',
            'emergency' => 'Urgence',
            default => ucfirst($this->level),
        };
    }

    public function getContextLabelAttribute(): string
    {
        return match($this->context) {
            'payment' => 'Paiement',
            'reversement' => 'Reversement',
            'colis' => 'Colis',
            'auth' => 'Authentification',
            'notification' => 'Notification',
            'system' => 'Système',
            'security' => 'Sécurité',
            default => ucfirst($this->context),
        };
    }

    public function isError(): bool
    {
        return in_array($this->level, ['error', 'critical', 'emergency']);
    }

    public function isSecurity(): bool
    {
        return $this->context === 'security';
    }

    public function getFormattedMetadataAttribute(): string
    {
        if (!$this->metadata) {
            return 'Aucune';
        }

        return json_encode($this->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
