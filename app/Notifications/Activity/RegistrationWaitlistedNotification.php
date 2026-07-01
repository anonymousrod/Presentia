<?php

namespace App\Notifications\Activity;

use App\Models\Activity;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RegistrationWaitlistedNotification extends Notification
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
            'icon'    => 'mdi mdi-calendar-clock',
            'color'   => 'warning',
            'title'   => 'Liste d\'attente',
            'message' => "Vous êtes en liste d'attente pour « {$this->activity->title} ». Vous serez notifié si une place se libère.",
            'url'     => route('activities.show', $this->activity),
        ];
    }
}
