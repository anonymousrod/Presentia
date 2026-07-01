<?php

namespace App\Notifications\Member;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AccountCreatedNotification extends Notification
{
    use Queueable;

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'icon'    => 'mdi mdi-account-check-outline',
            'color'   => 'success',
            'title'   => 'Bienvenue sur Presentia !',
            'message' => 'Votre compte a été créé avec succès.',
            'url'     => route('profile.edit'),
        ];
    }
}
