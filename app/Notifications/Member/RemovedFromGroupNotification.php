<?php

namespace App\Notifications\Member;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RemovedFromGroupNotification extends Notification
{
    use Queueable;

    public function __construct(public readonly string $groupName) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'icon'    => 'mdi mdi-account-remove-outline',
            'color'   => 'warning',
            'title'   => 'Retrait d\'un groupe',
            'message' => "Vous avez été retiré du groupe « {$this->groupName} ».",
            'url'     => route('profile.edit') . '#groups',
        ];
    }
}
