<?php

namespace App\Notifications\Admin;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewMemberCreatedNotification extends Notification
{
    use Queueable;

    public function __construct(public readonly User $newMember) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'icon'    => 'mdi mdi-account-plus-outline',
            'color'   => 'primary',
            'title'   => 'Nouveau membre créé',
            'message' => "Un nouveau compte a été créé pour {$this->newMember->first_name} {$this->newMember->name}.",
            'url'     => route('admin.users.show', $this->newMember),
        ];
    }
}
