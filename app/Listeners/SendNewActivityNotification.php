<?php

namespace App\Listeners;

use App\Events\ActivityCreated;
use App\Notifications\Activity\NewActivityPublishedNotification;
use App\Models\User;
use App\Enums\ActivityVisibility;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class SendNewActivityNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(ActivityCreated $event): void
    {
        $activity = $event->activity;

        if ($activity->visibility === ActivityVisibility::ALL) {
            // Envoyer à tous les utilisateurs
            $users = User::all();
            Notification::send($users, new NewActivityPublishedNotification($activity));
        } elseif ($activity->visibility === ActivityVisibility::GROUP && $activity->visibility_group_id) {
            // Envoyer au groupe spécifique
            $group = \App\Models\Group::find($activity->visibility_group_id);
            if ($group) {
                $users = $group->members()->wherePivotNull('left_at')->get();
                Notification::send($users, new NewActivityPublishedNotification($activity));
            }
        } elseif ($activity->visibility === ActivityVisibility::ROLE && $activity->visibility_role_id) {
            // Envoyer au rôle spécifique
            $role = \Spatie\Permission\Models\Role::find($activity->visibility_role_id);
            if ($role) {
                $users = User::role($role->name)->get();
                Notification::send($users, new NewActivityPublishedNotification($activity));
            }
        }
    }
}
