<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use App\Channels\WhatsAppChannel;

class CustomNotification extends Notification
{
    public function __construct(
        public string $title,
        public string $message,
    ) {
    }

    public function via($notifiable): array
    {
        $channels = ['database'];

        if (method_exists($notifiable, 'hasPhone') && $notifiable->hasPhone()) {
            $channels[] = WhatsAppChannel::class;
        }

        return $channels;
    }

    public function toWhatsApp($notifiable): string
    {
        return "*" . $this->title . "*\n\n" . $this->message;
    }

    public function toArray($notifiable): array
    {
        return [
            'title'   => $this->title,
            'message' => $this->message,
            'url'     => route('dashboard'),
        ];
    }
}
