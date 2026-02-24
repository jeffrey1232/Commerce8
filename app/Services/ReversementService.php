<?php

namespace App\Services;

use App\Models\Vendor;
use App\Models\Reversement;
use App\Models\Paiement;
use App\Models\LogSysteme;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Exception;

class ReversementService
{
    /**
     * Créer un reversement pour un paiement
     */
    public function createReversement(int $paymentId): Reversement
    {
        return DB::transaction(function () use ($paymentId) {
            $paiement = Paiement::findOrFail($paymentId);

            if (!$paiement->isSuccessful()) {
                throw new Exception('Seuls les paiements réussis peuvent être reversés');
            }

            // Vérifier si un reversement existe déjà
            $existingReversement = Reversement::where('payment_id', $paymentId)->first();
            if ($existingReversement) {
                throw new Exception('Un reversement existe déjà pour ce paiement');
            }

            $vendor = $paiement->colis->vendor;
            $commissionRate = $vendor->commission_rate;
            $grossAmount = $paiement->net_amount;
            $commissionAmount = $vendor->calculateCommission($grossAmount);
            $netAmount = $grossAmount - $commissionAmount;

            $reversement = Reversement::create([
                'uuid' => (string) Str::uuid(),
                'reference' => $this->generateReference(),
                'vendor_id' => $vendor->id,
                'payment_id' => $paymentId,
                'gross_amount' => $grossAmount,
                'commission_rate' => $commissionRate,
                'commission_amount' => $commissionAmount,
                'net_amount' => $netAmount,
                'status' => 'pending',
                'metadata' => [
                    'payment_transaction_id' => $paiement->transaction_id,
                    'colis_tracking_code' => $paiement->colis->tracking_code,
                ],
            ]);

            // Mettre à jour le solde du vendeur
            $vendor->updateBalance($netAmount);

            LogSysteme::logPayment('Reversement créé', [
                'action' => 'reversement_created',
                'reversement_id' => $reversement->id,
                'payment_id' => $paymentId,
                'vendor_id' => $vendor->id,
                'net_amount' => $netAmount,
            ]);

            return $reversement;
        });
    }

    /**
     * Traiter les reversements automatiques J+1
     */
    public function processDailyReversements(): array
    {
        $processed = [];
        $errors = [];

        // Récupérer les paiements d'hier qui n'ont pas encore été reversés
        $yesterdayPayments = Paiement::where('status', 'completed')
            ->whereDate('completed_at', now()->subDay())
            ->whereDoesntHave('reversement')
            ->get();

        foreach ($yesterdayPayments as $paiement) {
            try {
                $reversement = $this->createReversement($paiement->id);
                $processed[] = $reversement;
            } catch (Exception $e) {
                $errors[] = [
                    'payment_id' => $paiement->id,
                    'error' => $e->getMessage(),
                ];
            }
        }

        LogSysteme::logPayment('Reversements automatiques traités', [
            'action' => 'daily_reversements_processed',
            'processed_count' => count($processed),
            'error_count' => count($errors),
            'errors' => $errors,
        ]);

        return [
            'processed' => $processed,
            'errors' => $errors,
            'summary' => [
                'total' => $yesterdayPayments->count(),
                'success' => count($processed),
                'failed' => count($errors),
            ],
        ];
    }

    /**
     * Traiter un reversement individuel
     */
    public function processReversement(int $reversementId, int $processedBy): Reversement
    {
        return DB::transaction(function () use ($reversementId, $processedBy) {
            $reversement = Reversement::findOrFail($reversementId);

            if (!$reversement->isPending()) {
                throw new Exception('Ce reversement ne peut pas être traité');
            }

            $vendor = $reversement->vendor;

            // Marquer comme en cours de traitement
            $reversement->markAsProcessed($processedBy);

            // Effectuer le paiement au vendeur
            $paymentResponse = $this->sendPaymentToVendor($reversement);

            if ($paymentResponse['success']) {
                $reversement->markAsCompleted($paymentResponse['transaction_id'], $paymentResponse);

                // Mettre à jour le solde du vendeur
                $vendor->updateBalance(-$reversement->net_amount);

                LogSysteme::logPayment('Reversement effectué', [
                    'action' => 'reversement_completed',
                    'reversement_id' => $reversementId,
                    'vendor_id' => $vendor->id,
                    'amount' => $reversement->net_amount,
                    'provider_transaction_id' => $paymentResponse['transaction_id'],
                ]);

                return $reversement;
            } else {
                $reversement->markAsFailed($paymentResponse['error']);

                LogSysteme::logPayment('Reversement échoué', [
                    'action' => 'reversement_failed',
                    'reversement_id' => $reversementId,
                    'vendor_id' => $vendor->id,
                    'error' => $paymentResponse['error'],
                ]);

                throw new Exception('Échec du reversement: ' . $paymentResponse['error']);
            }
        });
    }

    /**
     * Traiter les reversements en lot (batch)
     */
    public function processBatchReversements(array $reversementIds, int $processedBy): array
    {
        $results = [
            'successful' => [],
            'failed' => [],
        ];

        foreach ($reversementIds as $reversementId) {
            try {
                $reversement = $this->processReversement($reversementId, $processedBy);
                $results['successful'][] = $reversement;
            } catch (Exception $e) {
                $results['failed'][] = [
                    'reversement_id' => $reversementId,
                    'error' => $e->getMessage(),
                ];
            }
        }

        LogSysteme::logPayment('Reversements en lot traités', [
            'action' => 'batch_reversements_processed',
            'processed_by' => $processedBy,
            'successful_count' => count($results['successful']),
            'failed_count' => count($results['failed']),
        ]);

        return $results;
    }

    /**
     * Obtenir les statistiques de reversement
     */
    public function getReversementStats(?string $period = null): array
    {
        $query = Reversement::query();

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

        $reversements = $query->get();

        return [
            'total' => $reversements->count(),
            'pending' => $reversements->where('status', 'pending')->count(),
            'processing' => $reversements->where('status', 'processing')->count(),
            'completed' => $reversements->where('status', 'completed')->count(),
            'failed' => $reversements->where('status', 'failed')->count(),
            'total_gross_amount' => $reversements->sum('gross_amount'),
            'total_commission' => $reversements->sum('commission_amount'),
            'total_net_amount' => $reversements->sum('net_amount'),
            'by_provider' => $reversements->whereNotNull('provider')->groupBy('provider')->map(function ($group) {
                return [
                    'count' => $group->count(),
                    'amount' => $group->sum('net_amount'),
                ];
            }),
        ];
    }

    /**
     * Obtenir le solde disponible pour reversement d'un vendeur
     */
    public function getVendorAvailableBalance(int $vendorId): float
    {
        $vendor = Vendor::findOrFail($vendorId);
        
        // Solde total moins les reversements en attente
        $pendingReversements = Reversement::where('vendor_id', $vendorId)
            ->whereIn('status', ['pending', 'processing'])
            ->sum('net_amount');

        return max(0, $vendor->balance - $pendingReversements);
    }

    /**
     * Créer un reversement manuel pour un vendeur
     */
    public function createManualReversement(int $vendorId, float $amount, string $reason): Reversement
    {
        return DB::transaction(function () use ($vendorId, $amount, $reason) {
            $vendor = Vendor::findOrFail($vendorId);
            $availableBalance = $this->getVendorAvailableBalance($vendorId);

            if ($amount > $availableBalance) {
                throw new Exception('Solde insuffisant pour ce reversement');
            }

            $commissionAmount = $vendor->calculateCommission($amount);
            $netAmount = $amount - $commissionAmount;

            $reversement = Reversement::create([
                'uuid' => (string) Str::uuid(),
                'reference' => $this->generateReference(),
                'vendor_id' => $vendorId,
                'gross_amount' => $amount,
                'commission_rate' => $vendor->commission_rate,
                'commission_amount' => $commissionAmount,
                'net_amount' => $netAmount,
                'status' => 'pending',
                'metadata' => [
                    'manual' => true,
                    'reason' => $reason,
                ],
            ]);

            // Mettre à jour le solde du vendeur
            $vendor->updateBalance(-$amount);

            LogSysteme::logPayment('Reversement manuel créé', [
                'action' => 'manual_reversement_created',
                'reversement_id' => $reversement->id,
                'vendor_id' => $vendorId,
                'amount' => $amount,
                'reason' => $reason,
            ]);

            return $reversement;
        });
    }

    // Méthodes privées

    private function generateReference(): string
    {
        return 'REV' . date('Ym') . str_pad(random_int(1, 999999), 6, '0', STR_PAD_LEFT);
    }

    private function sendPaymentToVendor(Reversement $reversement): array
    {
        // Simulation d'envoi d'argent au vendeur
        // À remplacer avec vraie intégration Mobile Money
        
        $vendor = $reversement->vendor;
        $provider = $vendor->contact_phone; // Utiliser le téléphone comme provider

        return [
            'success' => true,
            'transaction_id' => 'REV_' . uniqid(),
            'provider' => $provider,
            'response' => ['status' => 'success'],
        ];
    }
}
