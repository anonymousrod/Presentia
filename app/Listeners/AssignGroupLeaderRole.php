<?php

namespace App\Listeners;

use App\Events\GroupLeaderAssigned;

class AssignGroupLeaderRole
{
    /**
     * Attribue automatiquement le rôle 'Chef de groupe' au nouveau leader.
     */
    public function handle(GroupLeaderAssigned $event): void
    {
        $leader = $event->newLeader;
        $churchId = $event->group->church_id ?? $leader->church_id;

        if (function_exists('setPermissionsTeamId') && $churchId) {
            setPermissionsTeamId($churchId);
        }

        $groupLeaderRole = \App\Models\Role::where('church_id', $churchId)
            ->where('code', 'group_leader')
            ->first();

        if ($groupLeaderRole && ! $leader->hasRole($groupLeaderRole->name)) {
            $leader->assignRole($groupLeaderRole);
        }
    }
}
