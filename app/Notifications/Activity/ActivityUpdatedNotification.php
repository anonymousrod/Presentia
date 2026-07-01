<?php

namespace App\Notifications\Activity;

use App\Models\Activity;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ActivityUpdatedNotification extends Notification
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
            'icon'    => 'mdi mdi-calendar-edit',
            'color'   => 'warning',
            'title'   => 'Activité modifiée',
            'message' => "L'activité « {$this->activity->title} » a été mise à jour. Veuillez vérifier les nouvelles informations.",
            'url'     => route('activities.show', $this->activity),
        ];
    }
}
