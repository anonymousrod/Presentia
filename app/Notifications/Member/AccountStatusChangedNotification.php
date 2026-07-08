<?php

namespace App\Notifications\Member;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AccountStatusChangedNotification extends Notification
{
    use Queueable;

    public function __construct(public readonly string $newStatus)
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
        $statusLabels = [
            'ACTIVE'    => ['label' => 'Actif',        'color' => 'success', 'icon' => 'mdi mdi-check-circle-outline'],
            'INACTIVE'  => ['label' => 'Inactif',      'color' => 'secondary', 'icon' => 'mdi mdi-account-off-outline'],
            'SUSPENDED' => ['label' => 'Suspendu',     'color' => 'danger', 'icon' => 'mdi mdi-cancel'],
            'PENDING'   => ['label' => 'En attente',   'color' => 'warning', 'icon' => 'mdi mdi-clock-outline'],
        ];

        $info = $statusLabels[$this->newStatus] ?? ['label' => $this->newStatus, 'color' => 'secondary', 'icon' => 'mdi mdi-information-outline'];

        return [
            'icon'    => $info['icon'],
            'color'   => $info['color'],
            'title'   => 'Statut de compte mis à jour',
            'message' => "Votre statut de compte a été mis à jour : {$info['label']}.",
            'url'     => route('profile.edit'),
        ];
    }
}
