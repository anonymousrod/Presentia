<?php

namespace App\Notifications\Activity;

use App\Models\Activity;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RegistrationConfirmedNotification extends Notification
{
    use Queueable;

    public function __construct(public readonly Activity $activity)
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
            'icon'    => 'mdi mdi-calendar-check',
            'color'   => 'success',
            'title'   => 'Inscription confirmée',
            'message' => "✅ Votre inscription à « {$this->activity->title} » le " . $this->activity->start_time->format('d/m/Y') . " est confirmée.",
            'url'     => route('activities.show', $this->activity),
        ];
    }
}
