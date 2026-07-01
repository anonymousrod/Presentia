<?php

namespace App\Notifications\Admin;

use App\Models\Group;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class GroupLeaderAssignedNotification extends Notification
{
    use Queueable;

    public function __construct(public readonly Group $group) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'icon'    => 'mdi mdi-crown-outline',
            'color'   => 'warning',
            'title'   => 'Nomination : Chef de groupe',
            'message' => "🎉 Vous avez été nommé Chef du groupe « {$this->group->name} ».",
            'url'     => route('admin.groups.show', $this->group),
        ];
    }
}
