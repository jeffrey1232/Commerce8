<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PackageResource extends JsonResource
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
            'tracking_code' => $this->tracking_code,
            'vendor' => [
                'id' => $this->vendor->id,
                'store_name' => $this->vendor->store_name,
                'store_phone' => $this->vendor->store_phone,
                'commission_rate' => $this->vendor->commission_rate,
            ],
            'client' => [
                'name' => $this->client_name,
                'phone' => $this->client_phone,
            ],
            'product' => [
                'name' => $this->product_name,
                'description' => $this->product_description,
                'images' => $this->product_images,
            ],
            'financials' => [
                'total_amount' => $this->total_amount,
                'commission_amount' => $this->commission_amount,
                'net_amount' => $this->net_amount,
            ],
            'status' => [
                'current' => $this->status,
                'label' => $this->getStatusLabel($this->status),
                'can_be_deposited' => $this->canBeDeposited(),
                'can_be_sold' => $this->canBeSold(),
                'can_be_returned' => $this->canBeReturned(),
            ],
            'timestamps' => [
                'created_at' => $this->created_at,
                'deposited_at' => $this->deposited_at,
                'sold_at' => $this->sold_at,
                'returned_at' => $this->returned_at,
                'updated_at' => $this->updated_at,
            ],
            'payment' => $this->when($this->payment, function () {
                return [
                    'id' => $this->payment->id,
                    'method' => $this->payment->payment_method,
                    'status' => $this->payment->payment_status,
                    'transaction_reference' => $this->payment->transaction_reference,
                    'processed_at' => $this->payment->processed_at,
                ];
            }),
            'studio_session' => $this->when($this->studioSession, function () {
                return [
                    'id' => $this->studioSession->id,
                    'type' => $this->studioSession->session_type,
                    'scheduled_time' => $this->studioSession->scheduled_time,
                    'duration' => $this->studioSession->duration,
                    'status' => $this->studioSession->status,
                    'photos_count' => $this->studioSession->photos_count,
                ];
            }),
        ];
    }

    private function getStatusLabel(string $status): string
    {
        $labels = [
            'pending' => 'En attente de dépôt',
            'deposited' => 'Disponible au point relais',
            'sold' => 'Vendu et payé',
            'returned' => 'Retourné',
            'overdue' => 'En retard',
        ];

        return $labels[$status] ?? $status;
    }
}
