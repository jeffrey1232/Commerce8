<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $message
    ) {}

    public function handle(): void
    {
        try {
            // Pour l'instant, on log la notification
            // Dans un vrai projet, on enverrait un email, SMS, push notification
            Log::info('Notification sent', [
                'user_id' => $this->user->id,
                'user_email' => $this->user->email,
                'message' => $this->message,
                'sent_at' => now()
            ]);

            // Ici on pourrait intégrer un service d'envoi d'emails
            // Mail::to($this->user->email)->send(new NotificationMail($this->message));

        } catch (\Exception $e) {
            Log::error('Failed to send notification', [
                'user_id' => $this->user->id,
                'message' => $this->message,
                'error' => $e->getMessage()
            ]);
        }
    }
}
