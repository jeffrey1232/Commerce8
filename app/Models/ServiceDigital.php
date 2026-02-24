<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceDigital extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'services_digitaux';

    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'description',
        'type',
        'price',
        'pricing_model',
        'is_active',
        'sort_order',
        'options',
        'pricing_tiers',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'options' => 'array',
        'pricing_tiers' => 'array',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // Methods
    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function getFormattedPriceAttribute(): string
    {
        return number_format((float) $this->price, 0, ',', ' ') . ' FCFA';
    }

    public function calculatePrice(float $baseAmount = 0): float
    {
        return match($this->pricing_model) {
            'fixed' => $this->price,
            'percentage' => $baseAmount * ($this->price / 100),
            'tiered' => $this->calculateTieredPrice($baseAmount),
            default => $this->price,
        };
    }

    private function calculateTieredPrice(float $amount): float
    {
        if (!$this->pricing_tiers) {
            return (float) $this->price;
        }

        foreach ($this->pricing_tiers as $tier) {
            if ($amount >= $tier['min'] && $amount <= $tier['max']) {
                return (float) $tier['price'];
            }
        }

        return (float) $this->price;
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'photo' => 'Photo professionnelle',
            'video' => 'Vidéo produit',
            'packaging' => 'Emballage premium',
            'insurance' => 'Assurance colis',
            'express_delivery' => 'Livraison express',
            'gift_wrap' => 'Emballage cadeau',
            default => ucfirst($this->type),
        };
    }

    public function getPricingModelLabelAttribute(): string
    {
        return match($this->pricing_model) {
            'fixed' => 'Fixe',
            'percentage' => 'Pourcentage',
            'tiered' => 'Paliers',
            default => ucfirst($this->pricing_model),
        };
    }
}
