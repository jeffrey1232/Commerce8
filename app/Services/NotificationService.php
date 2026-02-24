<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Colis;
use App\Models\Vendor;
use App\Models\Client;
use App\Models\LogSysteme;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NotificationService
{
    /**
     * Envoyer une notification multi-canaux
     */
    public function sendNotification($notifiable, string $type, string $channel, array $data): Notification
    {
        return DB::transaction(function () use ($notifiable, $type, $channel, $data) {
            $notification = Notification::create([
                'uuid' => (string) Str::uuid(),
                'notifiable_type' => get_class($notifiable),
                'notifiable_id' => $notifiable->id,
                'type' => $type,
                'channel' => $channel,
                'recipient' => $this->getRecipient($notifiable, $channel),
                'subject' => $data['subject'] ?? null,
                'content' => $this->generateContent($type, $data),
                'metadata' => $data,
            ]);

            // Envoyer selon le canal
            $this->dispatchNotification($notification);

            LogSysteme::log('info', 'Notification envoyée', [
                'context' => 'notification',
                'action' => 'notification_sent',
                'notification_id' => $notification->id,
                'type' => $type,
                'channel' => $channel,
            ]);

            return $notification;
        });
    }

    /**
     * Notifier le dépôt d'un colis
     */
    public function notifyColisDeposited(Colis $colis): void
    {
        // Notifier le client
        if ($colis->client_phone) {
            $this->sendNotification($colis, 'colis_deposited', 'sms', [
                'subject' => 'Votre colis est disponible',
                'colis_tracking_code' => $colis->tracking_code,
                'point_relais_name' => $colis->pointRelais->name,
                'withdrawal_deadline' => $colis->storage_deadline->format('d/m/Y'),
            ]);
        }

        // Notifier le vendeur
        $this->sendNotification($colis->vendor, 'colis_deposited', 'database', [
            'subject' => 'Colis déposé',
            'colis_tracking_code' => $colis->tracking_code,
            'point_relais_name' => $colis->pointRelais->name,
        ]);
    }

    /**
     * Notifier le retrait d'un colis
     */
    public function notifyColisWithdrawn(Colis $colis): void
    {
        // Notifier le vendeur
        $this->sendNotification($colis->vendor, 'colis_withdrawn', 'database', [
            'subject' => 'Colis retiré',
            'colis_tracking_code' => $colis->tracking_code,
            'client_name' => $colis->client ? $colis->client->getFullNameAttribute() : 'Client',
            'withdrawal_date' => $colis->withdrawn_at->format('d/m/Y H:i'),
        ]);
    }

    /**
     * Notifier le paiement effectué
     */
    public function notifyPaymentCompleted(Colis $colis): void
    {
        // Notifier le vendeur
        $this->sendNotification($colis->vendor, 'payment_completed', 'database', [
            'subject' => 'Paiement reçu',
            'colis_tracking_code' => $colis->tracking_code,
            'amount' => number_format((float) $colis->total_amount, 0, ',', ' ') . ' FCFA',
            'payment_date' => $colis->paid_at->format('d/m/Y H:i'),
        ]);

        // Notifier le client si possible
        if ($colis->client_phone) {
            $this->sendNotification($colis, 'payment_completed', 'sms', [
                'subject' => 'Paiement confirmé',
                'colis_tracking_code' => $colis->tracking_code,
                'amount' => number_format((float) $colis->total_amount, 0, ',', ' ') . ' FCFA',
            ]);
        }
    }

    /**
     * Notifier le reversement effectué
     */
    public function notifyReversementCompleted(Vendor $vendor, float $amount): void
    {
        $this->sendNotification($vendor, 'reversement_completed', 'database', [
            'subject' => 'Reversement effectué',
            'amount' => number_format((float) $amount, 0, ',', ' ') . ' FCFA',
            'new_balance' => number_format((float) $vendor->balance, 0, ',', ' ') . ' FCFA',
        ]);

        // SMS si disponible
        if ($vendor->contact_phone) {
            $this->sendNotification($vendor, 'reversement_completed', 'sms', [
                'subject' => 'Reversement reçu',
                'amount' => number_format((float) $amount, 0, ',', ' ') . ' FCFA',
            ]);
        }
    }

    /**
     * Envoyer un rappel d'essayage
     */
    public function sendFittingReminder(Colis $colis): void
    {
        if ($colis->client_phone && $colis->fitting_option) {
            $this->sendNotification($colis, 'fitting_reminder', 'sms', [
                'subject' => 'Rappel essayage',
                'colis_tracking_code' => $colis->tracking_code,
                'point_relais_name' => $colis->pointRelais->name,
                'fitting_fee' => '500 FCFA',
            ]);
        }
    }

    /**
     * Notifier le stockage en retard
     */
    public function notifyStorageOverdue(Colis $colis): void
    {
        // Notifier le client
        if ($colis->client_phone) {
            $this->sendNotification($colis, 'storage_overdue', 'sms', [
                'subject' => 'Frais de stockage',
                'colis_tracking_code' => $colis->tracking_code,
                'storage_fee' => number_format((float) $colis->storage_fee, 0, ',', ' ') . ' FCFA',
                'days_overdue' => now()->diffInDays($colis->storage_deadline),
            ]);
        }

        // Notifier le vendeur
        $this->sendNotification($colis->vendor, 'storage_overdue', 'database', [
            'subject' => 'Frais de stockage appliqués',
            'colis_tracking_code' => $colis->tracking_code,
            'storage_fee' => $colis->storage_fee,
        ]);
    }

    /**
     * Envoyer des notifications en lot
     */
    public function sendBatchNotifications(array $notifications): array
    {
        $results = [
            'sent' => [],
            'failed' => [],
        ];

        foreach ($notifications as $notificationData) {
            try {
                $notification = $this->sendNotification(
                    $notificationData['notifiable'],
                    $notificationData['type'],
                    $notificationData['channel'],
                    $notificationData['data']
                );
                $results['sent'][] = $notification;
            } catch (\Exception $e) {
                $results['failed'][] = [
                    'data' => $notificationData,
                    'error' => $e->getMessage(),
                ];
            }
        }

        LogSysteme::log('info', 'Notifications en lot envoyées', [
            'context' => 'notification',
            'action' => 'batch_notifications_sent',
            'sent_count' => count($results['sent']),
            'failed_count' => count($results['failed']),
        ]);

        return $results;
    }

    /**
     * Relancer les notifications échouées
     */
    public function retryFailedNotifications(): array
    {
        $failedNotifications = Notification::readyForRetry()->get();
        $retried = [];

        foreach ($failedNotifications as $notification) {
            try {
                $this->dispatchNotification($notification);
                $retried[] = $notification;
            } catch (\Exception $e) {
                $notification->markAsFailed($e->getMessage());
            }
        }

        LogSysteme::log('info', 'Notifications échouées relancées', [
            'context' => 'notification',
            'action' => 'failed_notifications_retried',
            'retried_count' => count($retried),
        ]);

        return $retried;
    }

    /**
     * Obtenir les statistiques de notification
     */
    public function getNotificationStats(?string $period = null): array
    {
        $query = Notification::query();

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

        $notifications = $query->get();

        return [
            'total' => $notifications->count(),
            'sent' => $notifications->where('status', 'sent')->count(),
            'delivered' => $notifications->where('status', 'delivered')->count(),
            'failed' => $notifications->where('status', 'failed')->count(),
            'by_channel' => $notifications->groupBy('channel')->map(function ($group) {
                return [
                    'count' => $group->count(),
                    'success_rate' => $group->whereIn('status', ['sent', 'delivered'])->count() / $group->count() * 100,
                ];
            }),
            'by_type' => $notifications->groupBy('type')->map(function ($group) {
                return [
                    'count' => $group->count(),
                ];
            }),
        ];
    }

    // Méthodes privées

    private function getRecipient($notifiable, string $channel): string
    {
        switch ($channel) {
            case 'sms':
                return $notifiable->phone ?? $notifiable->contact_phone ?? '';
            case 'email':
                return $notifiable->email ?? $notifiable->contact_email ?? '';
            case 'whatsapp':
                return $notifiable->phone ?? $notifiable->contact_phone ?? '';
            default:
                return '';
        }
    }

    private function generateContent(string $type, array $data): string
    {
        $templates = [
            'colis_deposited' => "Votre colis {$data['colis_tracking_code']} est disponible au point relais {$data['point_relais_name']}. Retrait avant le {$data['withdrawal_deadline']}.",
            'colis_withdrawn' => "Votre colis {$data['colis_tracking_code']} a été retiré par {$data['client_name']} le {$data['withdrawal_date']}.",
            'payment_completed' => "Paiement de {$data['amount']} reçu pour votre colis {$data['colis_tracking_code']} le {$data['payment_date']}.",
            'reversement_completed' => "Reversement de {$data['amount']} effectué. Nouveau solde: {$data['new_balance']}.",
            'fitting_reminder' => "Rappel: Votre colis {$data['colis_tracking_code']} est disponible pour essayage à {$data['point_relais_name']}. Frais: {$data['fitting_fee']}.",
            'storage_overdue' => "Frais de stockage de {$data['storage_fee']} appliqués à votre colis {$data['colis_tracking_code']}. Retard: {$data['days_overdue']} jours.",
        ];

        return $templates[$type] ?? $data['message'] ?? '';
    }

    private function dispatchNotification(Notification $notification): void
    {
        switch ($notification->channel) {
            case 'sms':
                $this->sendSMS($notification);
                break;
            case 'email':
                $this->sendEmail($notification);
                break;
            case 'whatsapp':
                $this->sendWhatsApp($notification);
                break;
            case 'database':
                $notification->markAsSent();
                break;
        }
    }

    private function sendSMS(Notification $notification): void
    {
        // Simulation d'envoi SMS
        // À remplacer avec vraie intégration SMS Gateway

        $success = true; // Simulation

        if ($success) {
            $notification->markAsSent();
            $notification->markAsDelivered('SMS_' . uniqid());
        } else {
            $notification->markAsFailed('Erreur envoi SMS');
        }
    }

    private function sendEmail(Notification $notification): void
    {
        // Simulation d'envoi email
        // À remplacer avec vraie intégration email

        $success = true; // Simulation

        if ($success) {
            $notification->markAsSent();
            $notification->markAsDelivered('EMAIL_' . uniqid());
        } else {
            $notification->markAsFailed('Erreur envoi email');
        }
    }

    private function sendWhatsApp(Notification $notification): void
    {
        // Simulation d'envoi WhatsApp
        // À remplacer avec vraie intégration WhatsApp Business API

        $success = true; // Simulation

        if ($success) {
            $notification->markAsSent();
            $notification->markAsDelivered('WA_' . uniqid());
        } else {
            $notification->markAsFailed('Erreur envoi WhatsApp');
        }
    }
}
