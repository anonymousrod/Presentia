<?php

namespace App\Notifications;

use App\Models\PasswordResetRequest;
use App\Jobs\SendWhatsAppNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class AdminPasswordResetAlert extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public PasswordResetRequest $request
    ) {
    }

    public function via(object $notifiable): array
    {
        // Notification in-app (database) + WhatsApp
        return ['database'];
    }

    /**
     * Envoie également une notification WhatsApp manuellement ou via un canal personnalisé.
     * Pour rester simple et conforme aux jobs existants, on dispatch le job ici si l'admin a un tel.
     */
    public function toArray(object $notifiable): array
    {
        $user = $this->request->user;
        $message = "Nouvelle demande de réinitialisation de mot de passe WhatsApp de {$user->name} ({$user->phone}).";

        if ($notifiable->phone) {
            dispatch(new SendWhatsAppNotification($notifiable, $message));
        }

        return [
            'request_id' => $this->request->id,
            'user_name' => $user->name,
            'user_phone' => $user->phone,
            'message' => $message,
        ];
    }
}
