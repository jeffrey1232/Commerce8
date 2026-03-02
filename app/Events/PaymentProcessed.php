<?php

namespace App\Events;

use App\Models\Payment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentProcessed implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Payment $payment
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('vendor.' . $this->payment->vendor_id),
            new Channel('payments'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'payment.processed';
    }

    public function broadcastWith(): array
    {
        return [
            'payment_id' => $this->payment->id,
            'vendor_id' => $this->payment->vendor_id,
            'package_id' => $this->payment->package_id,
            'amount' => $this->payment->amount,
            'net_amount' => $this->payment->net_amount,
            'payment_method' => $this->payment->payment_method,
            'payment_status' => $this->payment->payment_status,
            'transaction_reference' => $this->payment->transaction_reference,
            'processed_at' => $this->payment->processed_at,
        ];
    }
}
