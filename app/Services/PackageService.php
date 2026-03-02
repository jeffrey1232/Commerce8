<?php

namespace App\Services;

use App\Models\Package;
use App\Models\Vendor;
use App\Jobs\SendNotificationJob;
use App\Events\PackageCreated;
use App\Events\PackageDelivered;
use Illuminate\Support\Str;

class PackageService
{
    public function createPackage(array $data, Vendor $vendor): Package
    {
        $commission = $data['total_amount'] * ($vendor->commission_rate / 100);
        $netAmount = $data['total_amount'] - $commission;

        $package = Package::create([
            'tracking_code' => $this->generateTrackingCode(),
            'vendor_id' => $vendor->id,
            'client_name' => $data['client_name'],
            'client_phone' => $data['client_phone'],
            'product_name' => $data['product_name'],
            'product_description' => $data['product_description'],
            'product_images' => $data['product_images'] ?? [],
            'total_amount' => $data['total_amount'],
            'commission_amount' => $commission,
            'net_amount' => $netAmount,
            'status' => 'pending'
        ]);

        // Mettre à jour les stats du vendeur
        $vendor->updateStats();

        // Envoyer notification au vendeur
        SendNotificationJob::dispatch($vendor->user, 'Nouveau colis créé: ' . $package->tracking_code);

        // Déclencher l'événement
        event(new PackageCreated($package));

        return $package;
    }

    public function updateStatus(Package $package, string $status): bool
    {
        $oldStatus = $package->status;
        $package->status = $status;
        
        switch ($status) {
            case 'deposited':
                $package->deposited_at = now();
                break;
            case 'sold':
                $package->sold_at = now();
                event(new PackageDelivered($package));
                break;
            case 'returned':
                $package->returned_at = now();
                break;
            case 'overdue':
                // Marquer comme en retard si créé il y a plus de 2 jours
                if ($package->created_at->diffInDays(now()) > 2) {
                    $package->status = 'overdue';
                }
                break;
        }

        $result = $package->save();

        if ($result && $oldStatus !== $status) {
            // Envoyer notification de changement de statut
            $this->notifyStatusChange($package, $oldStatus, $status);
        }

        return $result;
    }

    public function getPackageByTrackingCode(string $trackingCode): ?Package
    {
        return Package::where('tracking_code', $trackingCode)->first();
    }

    public function getOverduePackages(): \Illuminate\Database\Eloquent\Collection
    {
        return Package::where('status', 'overdue')
            ->where('created_at', '<', now()->subDays(2))
            ->with('vendor.user')
            ->get();
    }

    public function markOverduePackages(): int
    {
        $count = Package::where('status', 'pending')
            ->where('created_at', '<', now()->subDays(2))
            ->update(['status' => 'overdue']);

        return $count;
    }

    private function generateTrackingCode(): string
    {
        do {
            $code = 'ECM' . date('Y') . str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
        } while (Package::where('tracking_code', $code)->exists());

        return $code;
    }

    private function notifyStatusChange(Package $package, string $oldStatus, string $newStatus): void
    {
        $messages = [
            'pending_to_deposited' => 'Votre colis ' . $package->tracking_code . ' a été déposé au point relais',
            'deposited_to_sold' => 'Votre colis ' . $package->tracking_code . ' a été vendu',
            'sold_to_returned' => 'Votre colis ' . $package->tracking_code . ' a été retourné',
            'pending_to_overdue' => 'Votre colis ' . $package->tracking_code . ' est en retard',
        ];

        $key = $oldStatus . '_to_' . $newStatus;
        $message = $messages[$key] ?? 'Statut du colis ' . $package->tracking_code . ' mis à jour';

        // Notifier le vendeur
        if ($package->vendor && $package->vendor->user) {
            SendNotificationJob::dispatch($package->vendor->user, $message);
        }
    }

    public function getPackageStats(array $filters = []): array
    {
        $query = Package::query();

        if (isset($filters['vendor_id'])) {
            $query->where('vendor_id', $filters['vendor_id']);
        }

        if (isset($filters['date_range'])) {
            $dates = explode(',', $filters['date_range']);
            $query->whereBetween('created_at', [$dates[0], $dates[1]]);
        }

        return [
            'total' => $query->count(),
            'pending' => $query->clone()->where('status', 'pending')->count(),
            'deposited' => $query->clone()->where('status', 'deposited')->count(),
            'sold' => $query->clone()->where('status', 'sold')->count(),
            'returned' => $query->clone()->where('status', 'returned')->count(),
            'overdue' => $query->clone()->where('status', 'overdue')->count(),
        ];
    }
}
