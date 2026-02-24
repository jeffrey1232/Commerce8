<?php

namespace App\Services;

use App\Models\Colis;
use App\Models\Paiement;
use App\Models\Client;
use App\Models\LogSysteme;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Exception;

class PaiementService
{
    /**
     * Initialiser un paiement
     */
    public function initiatePayment(int $colisId, array $paymentData): Paiement
    {
        return DB::transaction(function () use ($colisId, $paymentData) {
            $colis = Colis::findOrFail($colisId);

            if (!$colis->canBePaid()) {
                throw new Exception('Ce colis ne peut pas être payé');
            }

            // Vérifier si un paiement existe déjà
            $existingPayment = Paiement::where('colis_id', $colisId)->first();
            if ($existingPayment && $existingPayment->isSuccessful()) {
                throw new Exception('Un paiement a déjà été effectué pour ce colis');
            }

            $amount = $colis->getTotalWithFees();

            $paiement = Paiement::create([
                'uuid' => (string) Str::uuid(),
                'transaction_id' => $this->generateTransactionId(),
                'idempotency_key' => $paymentData['idempotency_key'] ?? $this->generateIdempotencyKey($colisId),
                'colis_id' => $colisId,
                'client_id' => $colis->client_id,
                'amount' => $amount,
                'currency' => 'XOF',
                'provider' => $paymentData['provider'] ?? 'cash',
                'payment_method' => $paymentData['payment_method'] ?? null,
                'phone_number' => $paymentData['phone_number'] ?? null,
                'fees' => $this->calculateProviderFees($amount, $paymentData['provider'] ?? 'cash'),
                'net_amount' => $amount,
                'status' => 'pending',
                'metadata' => $paymentData['metadata'] ?? [],
            ]);

            // Log de création du paiement
            LogSysteme::logPayment('Paiement initié', [
                'action' => 'payment_initiated',
                'payment_id' => $paiement->id,
                'colis_id' => $colisId,
                'amount' => $amount,
                'provider' => $paiement->provider,
            ]);

            return $paiement;
        });
    }

    /**
     * Traiter un paiement Mobile Money
     */
    public function processMobileMoneyPayment(int $paymentId, array $providerData): array
    {
        return DB::transaction(function () use ($paymentId, $providerData) {
            $paiement = Paiement::findOrFail($paymentId);

            if ($paiement->status !== 'pending') {
                throw new Exception('Ce paiement ne peut pas être traité');
            }

            // Simulation d'appel API Mobile Money
            $response = $this->callMobileMoneyAPI($paiement, $providerData);

            if ($response['success']) {
                $paiement->markAsCompleted($response['transaction_id'], $response);
                
                // Mettre à jour le statut du colis
                $paiement->colis->update(['status' => 'paid']);

                LogSysteme::logPayment('Paiement Mobile Money réussi', [
                    'action' => 'payment_completed',
                    'payment_id' => $paymentId,
                    'provider_transaction_id' => $response['transaction_id'],
                ]);

                return [
                    'success' => true,
                    'payment' => $paiement,
                    'message' => 'Paiement effectué avec succès',
                ];
            } else {
                $paiement->markAsFailed($response['error']);

                LogSysteme::logPayment('Paiement Mobile Money échoué', [
                    'action' => 'payment_failed',
                    'payment_id' => $paymentId,
                    'error' => $response['error'],
                ]);

                return [
                    'success' => false,
                    'payment' => $paiement,
                    'message' => $response['error'],
                ];
            }
        });
    }

    /**
     * Traiter un webhook de paiement
     */
    public function processWebhook(string $provider, array $webhookData): bool
    {
        try {
            // Vérifier la signature du webhook
            if (!$this->verifyWebhookSignature($provider, $webhookData)) {
                LogSysteme::logSecurity('Signature webhook invalide', [
                    'action' => 'webhook_signature_invalid',
                    'provider' => $provider,
                    'webhook_data' => $webhookData,
                ]);
                return false;
            }

            $transactionId = $webhookData['transaction_id'];
            $status = $webhookData['status'];

            $paiement = Paiement::where('transaction_id', $transactionId)->first();
            if (!$paiement) {
                LogSysteme::logPayment('Transaction non trouvée dans webhook', [
                    'action' => 'webhook_transaction_not_found',
                    'transaction_id' => $transactionId,
                ]);
                return false;
            }

            // Mettre à jour le paiement selon le statut
            if ($status === 'success') {
                $paiement->markAsCompleted($webhookData['provider_transaction_id'], $webhookData);
                $paiement->colis->update(['status' => 'paid']);
            } elseif ($status === 'failed') {
                $paiement->markAsFailed($webhookData['error_message'] ?? 'Échec du paiement');
            }

            // Marquer le webhook comme reçu
            $paiement->update([
                'webhook_signature' => $webhookData['signature'] ?? null,
                'webhook_received_at' => now(),
            ]);

            LogSysteme::logPayment('Webhook traité avec succès', [
                'action' => 'webhook_processed',
                'payment_id' => $paiement->id,
                'status' => $status,
            ]);

            return true;
        } catch (Exception $e) {
            LogSysteme::logPayment('Erreur traitement webhook', [
                'action' => 'webhook_error',
                'error' => $e->getMessage(),
                'webhook_data' => $webhookData,
            ]);
            return false;
        }
    }

    /**
     * Rembourser un paiement
     */
    public function refundPayment(int $paymentId, string $reason): bool
    {
        return DB::transaction(function () use ($paymentId, $reason) {
            $paiement = Paiement::findOrFail($paymentId);

            if (!$paiement->isSuccessful()) {
                throw new Exception('Seuls les paiements réussis peuvent être remboursés');
            }

            // Simulation de remboursement
            $refundResponse = $this->processRefund($paiement);

            if ($refundResponse['success']) {
                $paiement->update([
                    'status' => 'refunded',
                    'failure_reason' => $reason,
                ]);

                // Mettre à jour le statut du colis
                $paiement->colis->update(['status' => 'refused']);

                LogSysteme::logPayment('Paiement remboursé', [
                    'action' => 'payment_refunded',
                    'payment_id' => $paymentId,
                    'reason' => $reason,
                ]);

                return true;
            }

            return false;
        });
    }

    /**
     * Obtenir les statistiques de paiement
     */
    public function getPaymentStats(?string $period = null): array
    {
        $query = Paiement::query();

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

        $payments = $query->get();

        return [
            'total' => $payments->count(),
            'successful' => $payments->where('status', 'completed')->count(),
            'failed' => $payments->where('status', 'failed')->count(),
            'pending' => $payments->where('status', 'pending')->count(),
            'total_amount' => $payments->sum('amount'),
            'successful_amount' => $payments->where('status', 'completed')->sum('amount'),
            'fees' => $payments->sum('fees'),
            'by_provider' => $payments->groupBy('provider')->map(function ($group) {
                return [
                    'count' => $group->count(),
                    'amount' => $group->sum('amount'),
                    'success_rate' => $group->where('status', 'completed')->count() / $group->count() * 100,
                ];
            }),
        ];
    }

    // Méthodes privées

    private function generateTransactionId(): string
    {
        return 'PAY_' . time() . '_' . strtoupper(substr(uniqid(), -6));
    }

    private function generateIdempotencyKey(int $colisId): string
    {
        return 'pay_' . $colisId . '_' . uniqid();
    }

    private function calculateProviderFees(float $amount, string $provider): float
    {
        switch ($provider) {
            case 'wave':
                return $amount * 0.02; // 2%
            case 'orange_money':
                return $amount * 0.015; // 1.5%
            case 'mtn':
                return $amount * 0.018; // 1.8%
            default:
                return 0;
        }
    }

    private function callMobileMoneyAPI(Paiement $paiement, array $providerData): array
    {
        // Simulation d'appel API - à remplacer avec vraie intégration
        $providers = [
            'wave' => [
                'success' => true,
                'transaction_id' => 'WAVE_' . uniqid(),
                'response' => ['status' => 'success'],
            ],
            'orange_money' => [
                'success' => true,
                'transaction_id' => 'OM_' . uniqid(),
                'response' => ['status' => 'success'],
            ],
            'mtn' => [
                'success' => true,
                'transaction_id' => 'MTN_' . uniqid(),
                'response' => ['status' => 'success'],
            ],
        ];

        return $providers[$paiement->provider] ?? [
            'success' => false,
            'error' => 'Provider non supporté',
        ];
    }

    private function verifyWebhookSignature(string $provider, array $webhookData): bool
    {
        // Simulation de vérification de signature
        // À implémenter selon la documentation du provider
        return isset($webhookData['signature']) && !empty($webhookData['signature']);
    }

    private function processRefund(Paiement $paiement): array
    {
        // Simulation de remboursement
        return [
            'success' => true,
            'refund_id' => 'REF_' . uniqid(),
        ];
    }
}
