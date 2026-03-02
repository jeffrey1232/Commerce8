<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VendorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
                'phone' => $this->user->phone,
                'status' => $this->user->status,
            ],
            'store' => [
                'name' => $this->store_name,
                'address' => $this->store_address,
                'phone' => $this->store_phone,
                'business_license' => $this->business_license,
            ],
            'business' => [
                'commission_rate' => $this->commission_rate,
                'rating' => $this->rating,
                'total_packages' => $this->total_packages,
                'total_revenue' => $this->total_revenue,
                'status' => $this->status,
                'status_label' => $this->getStatusLabel($this->status),
            ],
            'wallet' => $this->when($this->wallet, function () {
                return [
                    'balance' => $this->wallet->balance,
                    'pending_balance' => $this->wallet->pending_balance,
                    'total_earned' => $this->wallet->total_earned,
                    'total_withdrawn' => $this->wallet->total_withdrawn,
                    'last_updated' => $this->wallet->last_updated,
                ];
            }),
            'stats' => [
                'packages_by_status' => $this->getPackagesByStatus(),
                'revenue_this_month' => $this->getRevenueThisMonth(),
                'average_rating' => $this->rating,
                'completion_rate' => $this->getCompletionRate(),
            ],
            'timestamps' => [
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ],
        ];
    }

    private function getStatusLabel(string $status): string
    {
        $labels = [
            'active' => 'Actif',
            'inactive' => 'Inactif',
            'suspended' => 'Suspendu',
        ];

        return $labels[$status] ?? $status;
    }

    private function getPackagesByStatus(): array
    {
        $packages = $this->packages();
        
        return [
            'pending' => $packages->where('status', 'pending')->count(),
            'deposited' => $packages->where('status', 'deposited')->count(),
            'sold' => $packages->where('status', 'sold')->count(),
            'returned' => $packages->where('status', 'returned')->count(),
            'overdue' => $packages->where('status', 'overdue')->count(),
        ];
    }

    private function getRevenueThisMonth(): float
    {
        return $this->payments()
            ->where('payment_status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('net_amount');
    }

    private function getCompletionRate(): float
    {
        $totalPackages = $this->packages()->count();
        $soldPackages = $this->packages()->where('status', 'sold')->count();
        
        return $totalPackages > 0 ? round(($soldPackages / $totalPackages) * 100, 2) : 0;
    }
}
