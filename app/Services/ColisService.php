<?php

namespace App\Services;

use App\Models\Colis;
use App\Models\ColisStatusLog;
use App\Models\Client;
use App\Models\PointRelais;
use App\Models\LogSysteme;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Exception;

class ColisService
{
    /**
     * Créer un nouveau colis
     */
    public function createColis(array $data): Colis
    {
        return DB::transaction(function () use ($data) {
            // Générer un tracking code unique
            $trackingCode = $this->generateUniqueTrackingCode();

            $colis = Colis::create([
                'uuid' => (string) Str::uuid(),
                'tracking_code' => $trackingCode,
                'vendor_id' => $data['vendor_id'],
                'point_relais_id' => $data['point_relais_id'],
                'product_name' => $data['product_name'],
                'description' => $data['description'] ?? null,
                'price' => $data['price'],
                'shipping_fee' => $data['shipping_fee'] ?? 0,
                'total_amount' => $data['price'] + ($data['shipping_fee'] ?? 0),
                'product_photo' => $data['product_photo'] ?? null,
                'fitting_option' => $data['fitting_option'] ?? false,
                'fitting_fee' => $data['fitting_option'] ? 500 : 0,
                'client_phone' => $data['client_phone'] ?? null,
                'client_email' => $data['client_email'] ?? null,
                'metadata' => $data['metadata'] ?? [],
            ]);

            // Log de création
            LogSysteme::logColis('Colis créé', [
                'action' => 'colis_created',
                'colis_id' => $colis->id,
                'tracking_code' => $colis->tracking_code,
                'vendor_id' => $colis->vendor_id,
            ]);

            return $colis;
        });
    }

    /**
     * Déposer un colis au point relais
     */
    public function depositColis(int $colisId, int $userId): Colis
    {
        return DB::transaction(function () use ($colisId, $userId) {
            $colis = Colis::findOrFail($colisId);

            if (!$colis->canBeDeposited()) {
                throw new Exception('Ce colis ne peut pas être déposé');
            }

            $oldStatus = $colis->status;

            // Mettre à jour le statut et la date de dépôt
            $colis->update([
                'status' => 'deposited',
                'deposited_at' => now(),
                'storage_deadline' => now()->addDays(7), // 7 jours pour retrait
            ]);

            // Mettre à jour la capacité du point relais
            $colis->pointRelais->incrementStorage();

            // Créer le log de changement de statut
            $this->createStatusLog($colis, $oldStatus, 'deposited', 'Colis déposé au point relais', $userId);

            // Log système
            LogSysteme::logColis('Colis déposé', [
                'action' => 'colis_deposited',
                'colis_id' => $colis->id,
                'old_status' => $oldStatus,
                'new_status' => 'deposited',
                'point_relais_id' => $colis->point_relais_id,
            ]);

            return $colis;
        });
    }

    /**
     * Retirer un colis par le client
     */
    public function withdrawColis(int $colisId, ?int $clientId = null, int $userId = null): Colis
    {
        return DB::transaction(function () use ($colisId, $clientId, $userId) {
            $colis = Colis::findOrFail($colisId);

            if (!$colis->canBeWithdrawn()) {
                throw new Exception('Ce colis ne peut pas être retiré');
            }

            $oldStatus = $colis->status;

            // Si option essayage, passer en statut d'essayage
            $newStatus = $colis->fitting_option ? 'in_fitting' : 'pending_withdrawal';

            $colis->update([
                'status' => $newStatus,
                'client_id' => $clientId,
                'withdrawn_at' => now(),
            ]);

            // Créer le log de changement de statut
            $this->createStatusLog($colis, $oldStatus, $newStatus, 'Colis retiré par le client', $userId);

            // Log système
            LogSysteme::logColis('Colis retiré', [
                'action' => 'colis_withdrawn',
                'colis_id' => $colis->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'client_id' => $clientId,
            ]);

            return $colis;
        });
    }

    /**
     * Mettre à jour le statut d'un colis
     */
    public function updateStatus(int $colisId, string $newStatus, ?string $reason = null, ?int $userId = null): Colis
    {
        return DB::transaction(function () use ($colisId, $newStatus, $reason, $userId) {
            $colis = Colis::findOrFail($colisId);

            $oldStatus = $colis->status;

            // Validation des transitions de statut
            if (!$this->isValidStatusTransition($oldStatus, $newStatus)) {
                throw new Exception("Transition de statut invalide: {$oldStatus} -> {$newStatus}");
            }

            // Mise à jour du colis selon le nouveau statut
            $updateData = ['status' => $newStatus];

            switch ($newStatus) {
                case 'paid':
                    $updateData['paid_at'] = now();
                    break;
                case 'reversed':
                    $updateData['reversed_at'] = now();
                    break;
                case 'refused':
                    $updateData['rejection_reason'] = $reason;
                    break;
            }

            $colis->update($updateData);

            // Créer le log de changement de statut
            $this->createStatusLog($colis, $oldStatus, $newStatus, $reason, $userId);

            // Log système
            LogSysteme::logColis('Statut colis mis à jour', [
                'action' => 'colis_status_updated',
                'colis_id' => $colis->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'reason' => $reason,
            ]);

            return $colis;
        });
    }

    /**
     * Calculer les frais de stockage pour un colis
     */
    public function calculateStorageFees(int $colisId): float
    {
        $colis = Colis::findOrFail($colisId);

        if (!$colis->storage_deadline || !$colis->isOverdue()) {
            return 0;
        }

        $daysOverdue = now()->diffInDays($colis->storage_deadline);
        return $daysOverdue * 100; // 100 FCFA par jour de retard
    }

    /**
     * Traiter les colis en retard de stockage
     */
    public function processOverdueColis(): array
    {
        $overdueColis = Colis::inStorage()->get();
        $processed = [];

        foreach ($overdueColis as $colis) {
            $storageFee = $this->calculateStorageFees($colis->id);

            if ($storageFee > 0) {
                $colis->storage_fee = $storageFee;
                $colis->save();

                $processed[] = [
                    'colis_id' => $colis->id,
                    'tracking_code' => $colis->tracking_code,
                    'storage_fee' => $storageFee,
                    'days_overdue' => now()->diffInDays($colis->storage_deadline),
                ];

                LogSysteme::logColis('Frais de stockage appliqués', [
                    'action' => 'storage_fee_applied',
                    'colis_id' => $colis->id,
                    'storage_fee' => $storageFee,
                ]);
            }
        }

        return $processed;
    }

    /**
     * Obtenir les statistiques des colis pour un vendeur
     */
    public function getVendorStats(int $vendorId, ?string $period = null): array
    {
        $query = Colis::where('vendor_id', $vendorId);

        if ($period) {
            switch ($period) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('created_at', now()->month);
                    break;
            }
        }

        $colis = $query->get();

        return [
            'total' => $colis->count(),
            'created' => $colis->where('status', 'created')->count(),
            'deposited' => $colis->where('status', 'deposited')->count(),
            'paid' => $colis->where('status', 'paid')->count(),
            'reversed' => $colis->where('status', 'reversed')->count(),
            'total_amount' => $colis->sum('total_amount'),
            'pending_amount' => $colis->whereIn('status', ['deposited', 'pending_withdrawal', 'in_fitting'])->sum('total_amount'),
        ];
    }

    // Méthodes privées

    private function generateUniqueTrackingCode(): string
    {
        do {
            $code = 'ECM' . str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT) . strtoupper(substr(uniqid(), -4));
        } while (Colis::where('tracking_code', $code)->exists());

        return $code;
    }

    private function createStatusLog(Colis $colis, ?string $oldStatus, string $newStatus, ?string $reason, ?int $userId): void
    {
        ColisStatusLog::create([
            'uuid' => (string) Str::uuid(),
            'colis_id' => $colis->id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'changed_by_user_id' => $userId,
            'change_reason' => $reason,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    private function isValidStatusTransition(string $from, string $to): bool
    {
        $validTransitions = [
            'created' => ['deposited'],
            'deposited' => ['pending_withdrawal', 'in_fitting', 'returned'],
            'pending_withdrawal' => ['in_fitting', 'paid', 'refused', 'returned'],
            'in_fitting' => ['paid', 'refused', 'returned'],
            'paid' => ['reversed'],
            'refused' => ['returned'],
            'reversed' => [],
            'in_storage' => ['returned'],
            'returned' => [],
        ];

        return in_array($to, $validTransitions[$from] ?? []);
    }
}
