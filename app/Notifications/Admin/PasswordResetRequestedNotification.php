<?php

namespace App\Notifications\Admin;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PasswordResetRequestedNotification extends Notification
{
    use Queueable;

    public function __construct(public readonly User $member)
    {
    }

    public function via($notifiable): array
    {
        $channels = ['database'];
        if (method_exists($notifiable, 'hasPhone') && $notifiable->hasPhone()) {
            $channels[] = \App\Channels\WhatsAppChannel::class;
        }
        return $channels;
    }

    public function toWhatsApp($notifiable): string
    {
        $data = $this->toArray($notifiable);
        $title = $data['title'] ?? 'Notification Presentia';
        $message = $data['message'] ?? '';
        return "👋 Bonjour,

*{$title}*
{$message}";
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
