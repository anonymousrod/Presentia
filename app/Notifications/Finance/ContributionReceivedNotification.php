<?php

namespace App\Notifications\Finance;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ContributionReceivedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly int $amount,
        public readonly string $date
    ) {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'icon'    => 'mdi mdi-cash-check',
            'color'   => 'success',
            'title'   => 'Cotisation enregistrée',
            'message' => "Votre cotisation de " . number_format($this->amount, 0, ',', ' ') . " FCFA du {$this->date} a été enregistrée.",
            'url'     => route('profile.edit'),
        ];
    }
}
