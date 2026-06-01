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

        if (! $leader->hasRole('Chef de groupe')) {
            $leader->assignRole('Chef de groupe');
        }
    }
}
