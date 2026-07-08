<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use App\Services\WhatsAppServiceInterface;
use App\Models\WhatsappLog;

class WhatsAppChannel
{
    protected WhatsAppServiceInterface $whatsAppService;

    public function __construct(WhatsAppServiceInterface $whatsAppService)
    {
        $this->whatsAppService = $whatsAppService;
    }

    /**
     * Envoyer la notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        if (! method_exists($notification, 'toWhatsApp')) {
            return;
        }

        if (! method_exists($notifiable, 'routeNotificationForWhatsApp')) {
            return;
        }

        $phone = $notifiable->routeNotificationForWhatsApp();

        if (empty($phone)) {
            return;
        }

        $message = $notification->toWhatsApp($notifiable);

        // Si le message est vide, on ne fait rien
        if (empty($message) || !is_string($message)) {
            return;
        }

        try {
            $response = $this->whatsAppService->send($phone, $message);

            WhatsappLog::create([
                'user_id'           => $notifiable->id ?? null,
                'message_type'      => 'custom_notification',
                'status'            => 'sent',
                'provider_response' => $response,
            ]);
        } catch (\Exception $e) {
            WhatsappLog::create([
                'user_id'           => $notifiable->id ?? null,
                'message_type'      => 'custom_notification',
                'status'            => 'failed',
                'provider_response' => ['error' => $e->getMessage()],
            ]);
        }
    }
}
