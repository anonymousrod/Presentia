<?php

namespace App\Notifications\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PasswordResetDoneNotification extends Notification
{
    use Queueable;

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'icon'    => 'mdi mdi-lock-check-outline',
            'color'   => 'success',
            'title'   => 'Mot de passe réinitialisé',
            'message' => "Votre mot de passe a été réinitialisé par un administrateur. Connectez-vous avec votre nouveau mot de passe.",
            'url'     => route('login'),
        ];
    }
}
