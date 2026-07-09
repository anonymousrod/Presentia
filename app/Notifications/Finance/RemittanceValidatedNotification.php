<?php

namespace App\Notifications\Finance;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RemittanceValidatedNotification extends Notification
{
    use Queueable;

    public function __construct(public readonly int $amount)
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
            'icon'    => 'mdi mdi-bank-check',
            'color'   => 'success',
            'title'   => 'Versement validé',
            'message' => "✅ Votre versement de " . number_format($this->amount, 0, ',', ' ') . " FCFA a été validé par le trésorier.",
            'url'     => route('admin.finance.contributions.index'),
        ];
    }
}
