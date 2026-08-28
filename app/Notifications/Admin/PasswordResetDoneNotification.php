<?php

namespace App\Notifications\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PasswordResetDoneNotification extends Notification
{
    use Queueable;

    /**
     * Canal de notification : uniquement en base de données pour la cloche de notifications en interne.
     * Le message WhatsApp est déjà expédié directement avec le mot de passe temporaire.
     */
    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $appName = config('app.name', 'MeVoici');
        return [
            'icon'    => 'mdi mdi-lock-check-outline',
            'color'   => 'success',
            'title'   => 'Mot de passe réinitialisé',
            'message' => "Votre demande de réinitialisation de mot de passe a été traitée avec succès sur {$appName}. Connectez-vous avec votre nouveau mot de passe.",
            'url'     => route('login'),
        ];
    }
}
