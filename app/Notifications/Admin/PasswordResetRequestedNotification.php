<?php

namespace App\Notifications\Admin;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PasswordResetRequestedNotification extends Notification
{
    use Queueable;

    public function __construct(public readonly User $member) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'icon'    => 'mdi mdi-lock-reset',
            'color'   => 'warning',
            'title'   => 'Demande de réinitialisation',
            'message' => "{$this->member->first_name} {$this->member->name} a demandé une réinitialisation de mot de passe.",
            'url'     => route('admin.password-requests.index'),
        ];
    }
}
