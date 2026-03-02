<?php

namespace App\Jobs;

use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessPaymentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [5, 10, 30];

    public function __construct(
        public Payment $payment
    ) {}

    public function handle(PaymentService $paymentService): void
    {
        try {
            // Simuler le traitement du paiement avec le fournisseur
            $this->simulatePaymentProcessing();

            // Marquer le paiement comme complété
            $this->payment->payment_status = 'completed';
            $this->payment->processed_at = now();
            $this->payment->save();

            // Transférer les fonds au wallet du vendeur
            $paymentService->addToWallet(
                $this->payment->vendor_id, 
                $this->payment->net_amount, 
                'available'
            );

        } catch (\Exception $e) {
            // Marquer comme échoué si erreur
            $this->payment->payment_status = 'failed';
            $this->payment->save();
            
            throw $e;
        }
    }

    private function simulatePaymentProcessing(): void
    {
        // Simuler un délai de traitement
        sleep(2);
        
        // Simuler une validation (toujours réussir pour la démo)
        if (rand(1, 100) <= 95) { // 95% de succès
            return;
        }
        
        throw new \Exception('Échec de traitement du paiement');
    }

    public function failed(\Throwable $exception): void
    {
        \Log::error('Payment processing failed', [
            'payment_id' => $this->payment->id,
            'error' => $exception->getMessage()
        ]);
    }
}
