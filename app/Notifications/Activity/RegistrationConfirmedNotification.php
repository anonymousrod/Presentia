<?php

namespace App\Notifications\Activity;

use App\Models\Activity;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RegistrationConfirmedNotification extends Notification
{
    use Queueable;

    public function __construct(public readonly Activity $activity) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'icon'    => 'mdi mdi-calendar-check',
            'color'   => 'success',
            'title'   => 'Inscription confirmée',
            'message' => "✅ Votre inscription à « {$this->activity->title} » le " . $this->activity->start_time->format('d/m/Y') . " est confirmée.",
            'url'     => route('activities.show', $this->activity),
        ];
    }
}
