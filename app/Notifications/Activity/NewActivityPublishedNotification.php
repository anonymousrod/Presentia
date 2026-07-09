<?php

namespace App\Notifications\Activity;

use App\Models\Activity;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewActivityPublishedNotification extends Notification
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
        $title = $data['title'] ?? 'Notification ' . config('app.name');
        $message = $data['message'] ?? '';
        return "👋 Bonjour,

*{$title}*
{$message}";
    }

    public function toArray($notifiable): array
    {
        return [
            'icon'    => 'mdi mdi-calendar-plus',
            'color'   => 'info',
            'title'   => 'Nouvelle activité publiée',
            'message' => "Une nouvelle activité est disponible : « {$this->activity->title} » le " . $this->activity->start_time->format('d/m/Y à H:i') . '.',
            'url'     => route('activities.show', $this->activity),
        ];
    }
}
