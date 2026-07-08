<?php

namespace App\Notifications\Member;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RemovedFromGroupNotification extends Notification
{
    use Queueable;

    public function __construct(public readonly string $groupName)
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
            'icon'    => 'mdi mdi-account-remove-outline',
            'color'   => 'warning',
            'title'   => 'Retrait d\'un groupe',
            'message' => "Vous avez été retiré du groupe « {$this->groupName} ».",
            'url'     => route('profile.edit') . '#groups',
        ];
    }
}
