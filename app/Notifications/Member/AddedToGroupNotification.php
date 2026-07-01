<?php

namespace App\Notifications\Member;

use App\Models\Group;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AddedToGroupNotification extends Notification
{
    use Queueable;

    public function __construct(public readonly Group $group) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'icon'    => 'mdi mdi-account-group-outline',
            'color'   => 'primary',
            'title'   => 'Ajout à un groupe',
            'message' => "Vous avez été ajouté au groupe « {$this->group->name} ».",
            'url'     => route('profile.edit') . '#groups',
        ];
    }
}
