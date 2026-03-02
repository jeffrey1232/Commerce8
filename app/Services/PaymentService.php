<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Package;
use App\Models\Wallet;
use App\Models\Vendor;
use App\Jobs\ProcessPaymentJob;
use App\Events\PaymentProcessed;
use Illuminate\Support\Str;

class PaymentService
{
    public function processClientPayment(array $data): array
    {
        $package = Package::where('tracking_code', $data['tracking_code'])->first();
        
        if (!$package || $package->status !== 'deposited') {
            return ['success' => false, 'message' => 'Colis non disponible'];
        }

        $payment = Payment::create([
            'package_id' => $package->id,
            'vendor_id' => $package->vendor_id,
            'amount' => $package->total_amount,
            'commission' => $package->commission_amount,
            'net_amount' => $package->net_amount,
            'payment_method' => $data['payment_method'],
            'payment_status' => 'processing',
            'transaction_reference' => $this->generateTransactionReference()
        ]);

        // Mettre à jour le statut du colis
        $package->status = 'sold';
        $package->sold_at = now();
        $package->save();

        // Ajouter au wallet du vendeur (en attente)
        $this->addToWallet($package->vendor_id, $package->net_amount, 'pending');

        // Mettre à jour les stats du vendeur
        $vendor = Vendor::find($package->vendor_id);
        if ($vendor) {
            $vendor->updateStats();
        }

        // Traiter le paiement en arrière-plan
        ProcessPaymentJob::dispatch($payment);

        return [
            'success' => true,
            'transaction_id' => $payment->transaction_reference,
            'payment' => $payment
        ];
    }

    public function processDisbursement(Payment $payment): array
    {
        try {
            $payment->payment_status = 'completed';
            $payment->processed_at = now();
            $payment->save();

            // Transférer du pending balance au available balance
            $this->addToWallet($payment->vendor_id, $payment->net_amount, 'available');

            // Déclencher l'événement
            event(new PaymentProcessed($payment));

            return ['success' => true];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function refundPayment(Payment $payment, string $reason): array
    {
        try {
            if ($payment->payment_status !== 'completed') {
                return ['success' => false, 'message' => 'Seuls les paiements complétés peuvent être remboursés'];
            }

            $payment->payment_status = 'refunded';
            $payment->save();

            // Retirer du wallet du vendeur
            $this->removeFromWallet($payment->vendor_id, $payment->net_amount);

            // Mettre à jour le statut du colis
            $package = $payment->package;
            if ($package) {
                $package->status = 'returned';
                $package->returned_at = now();
                $package->save();
            }

            return ['success' => true];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    private function addToWallet(int $vendorId, float $amount, string $type): void
    {
        $wallet = Wallet::firstOrCreate(['vendor_id' => $vendorId], [
            'balance' => 0,
            'pending_balance' => 0,
            'total_earned' => 0,
            'total_withdrawn' => 0
        ]);

        if ($type === 'pending') {
            $wallet->pending_balance += $amount;
            $wallet->total_earned += $amount;
        } elseif ($type === 'available') {
            $wallet->pending_balance -= $amount;
            $wallet->balance += $amount;
        }

        $wallet->last_updated = now();
        $wallet->save();
    }

    private function removeFromWallet(int $vendorId, float $amount): void
    {
        $wallet = Wallet::where('vendor_id', $vendorId)->first();
        
        if ($wallet && $wallet->balance >= $amount) {
            $wallet->balance -= $amount;
            $wallet->total_withdrawn += $amount;
            $wallet->last_updated = now();
            $wallet->save();
        }
    }

    private function generateTransactionReference(): string
    {
        return 'TXN' . date('YmdHis') . str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT);
    }

    public function getPaymentStats(array $filters = []): array
    {
        $query = Payment::query();

        if (isset($filters['vendor_id'])) {
            $query->where('vendor_id', $filters['vendor_id']);
        }

        if (isset($filters['payment_method'])) {
            $query->where('payment_method', $filters['payment_method']);
        }

        if (isset($filters['date_range'])) {
            $dates = explode(',', $filters['date_range']);
            $query->whereBetween('created_at', [$dates[0], $dates[1]]);
        }

        return [
            'total' => $query->count(),
            'pending' => $query->clone()->where('payment_status', 'pending')->sum('amount'),
            'processing' => $query->clone()->where('payment_status', 'processing')->sum('amount'),
            'completed' => $query->clone()->where('payment_status', 'completed')->sum('amount'),
            'failed' => $query->clone()->where('payment_status', 'failed')->sum('amount'),
            'total_revenue' => $query->clone()->where('payment_status', 'completed')->sum('net_amount'),
            'total_commission' => $query->clone()->where('payment_status', 'completed')->sum('commission'),
        ];
    }

    public function getPendingDisbursements(): \Illuminate\Database\Eloquent\Collection
    {
        return Payment::with('vendor.user')
            ->where('payment_status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function processBatchDisbursements(array $paymentIds): array
    {
        $results = ['success' => 0, 'failed' => 0, 'errors' => []];

        foreach ($paymentIds as $paymentId) {
            $payment = Payment::find($paymentId);
            if ($payment) {
                $result = $this->processDisbursement($payment);
                if ($result['success']) {
                    $results['success']++;
                } else {
                    $results['failed']++;
                    $results['errors'][] = "Payment {$paymentId}: " . $result['message'];
                }
            } else {
                $results['failed']++;
                $results['errors'][] = "Payment {$paymentId} not found";
            }
        }

        return $results;
    }
}
