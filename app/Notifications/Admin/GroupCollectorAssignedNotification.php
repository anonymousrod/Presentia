<?php

namespace App\Notifications\Admin;

use App\Models\Group;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class GroupCollectorAssignedNotification extends Notification
{
    use Queueable;

    public function __construct(public readonly Group $group)
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
        $title = $data['title'] ?? 'Notification ' . config('app.name');
        $message = $data['message'] ?? '';
        return "👋 Bonjour,

*{$title}*
{$message}";
    }

    public function toArray($notifiable): array
    {
        return [
            'icon'    => 'mdi mdi-cash-register',
            'color'   => 'info',
            'title'   => 'Nomination : Chargé de collecte',
            'message' => "Vous avez été désigné Chargé de collecte du groupe « {$this->group->name} ».",
            'url'     => route('admin.finance.contributions.index'),
        ];
    }
}
