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

        $groupLeaderRole = \Spatie\Permission\Models\Role::where('code', 'group_leader')->first();
        if ($groupLeaderRole && ! $leader->hasRole($groupLeaderRole->name)) {
            $leader->assignRole($groupLeaderRole->name);
        }
    }
}
