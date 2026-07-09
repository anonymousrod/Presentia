<?php

namespace App\Notifications\Finance;

use App\Models\Group;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RemittanceSubmittedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly User $collector,
        public readonly Group $group,
        public readonly int $amount
    ) {
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
            'icon'    => 'mdi mdi-bank-transfer-in',
            'color'   => 'info',
            'title'   => 'Nouveau versement à valider',
            'message' => "{$this->collector->first_name} {$this->collector->name} (Groupe : {$this->group->name}) a soumis un versement de " . number_format($this->amount, 0, ',', ' ') . " FCFA.",
            'url'     => route('admin.finance.treasury.index'),
        ];
    }
}
