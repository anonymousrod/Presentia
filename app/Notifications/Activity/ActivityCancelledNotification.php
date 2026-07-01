<?php

namespace App\Notifications\Activity;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ActivityCancelledNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly string $activityTitle,
        public readonly ?string $reason = null
    ) {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $message = "⚠️ L'activité « {$this->activityTitle} » a été annulée.";
        if ($this->reason) {
            $message .= " Raison : {$this->reason}";
        }

        return [
            'icon'    => 'mdi mdi-calendar-remove',
            'color'   => 'danger',
            'title'   => 'Activité annulée',
            'message' => $message,
            'url'     => route('activities.index'),
        ];
    }
}
