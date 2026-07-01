<?php

namespace App\Notifications\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RoleRevokedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly string $roleName,
        public readonly string $groupName
    ) {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'icon'    => 'mdi mdi-account-minus-outline',
            'color'   => 'secondary',
            'title'   => 'Rôle retiré',
            'message' => "Vous n'êtes plus {$this->roleName} du groupe « {$this->groupName} ».",
            'url'     => route('profile.edit'),
        ];
    }
}
