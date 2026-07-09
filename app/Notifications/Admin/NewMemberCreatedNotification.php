<?php

namespace App\Notifications\Admin;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewMemberCreatedNotification extends Notification
{
    use Queueable;

    public function __construct(public readonly User $newMember)
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
            'icon'    => 'mdi mdi-account-plus-outline',
            'color'   => 'primary',
            'title'   => 'Nouveau membre créé',
            'message' => "Un nouveau compte a été créé pour {$this->newMember->first_name} {$this->newMember->name}.",
            'url'     => route('admin.users.show', $this->newMember),
        ];
    }
}
