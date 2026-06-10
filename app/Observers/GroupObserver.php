<?php

namespace App\Observers;

use App\Models\Group;
use App\Models\User;

class GroupObserver
{
    /**
     * Cache for tracking the original leader_id before update.
     */
    protected static array $previousLeaders = [];

    /**
     * Handle the Group "saving" event.
     */
    public function saving(Group $group): void
    {
        if ($group->exists) {
            self::$previousLeaders[$group->id] = $group->getOriginal('leader_id');
        }
    }

    /**
     * Handle the Group "saved" event.
     */
    public function saved(Group $group): void
    {
        $newLeaderId = $group->leader_id;
        // Retrieve and clean up cached previous leader ID
        $oldLeaderId = self::$previousLeaders[$group->id] ?? null;
        if (array_key_exists($group->id, self::$previousLeaders)) {
            unset(self::$previousLeaders[$group->id]);
        }

        if ($newLeaderId !== $oldLeaderId) {
            // 1. Assign "Chef de groupe" role to the new leader if set
            if ($newLeaderId) {
                $newLeader = User::find($newLeaderId);
                if ($newLeader && \Spatie\Permission\Models\Role::where('name', 'Chef de groupe')->exists()) {
                    if (!$newLeader->hasRole('Chef de groupe')) {
                        $newLeader->assignRole('Chef de groupe');
                    }
                }

                // Automatically add the new leader as an active member of the group
                $pivot = $group->members()->wherePivot('user_id', $newLeaderId)->first();
                if ($pivot) {
                    if ($pivot->pivot->left_at !== null) {
                        $group->members()->updateExistingPivot($newLeaderId, [
                            'joined_at' => now(),
                            'left_at'   => null,
                        ]);
                    }
                } else {
                    $group->members()->attach($newLeaderId, [
                        'joined_at' => now(),
                        'left_at'   => null,
                    ]);
                }
            }

            // 2. Remove "Chef de groupe" role from old leader if they no longer lead any group
            if ($oldLeaderId) {
                $oldLeaderStillLeads = Group::where('leader_id', $oldLeaderId)
                    ->where('id', '!=', $group->id)
                    ->exists();

                if (!$oldLeaderStillLeads) {
                    $oldLeader = User::find($oldLeaderId);
                    if ($oldLeader && $oldLeader->hasRole('Chef de groupe')) {
                        $oldLeader->removeRole('Chef de groupe');
                        // Ensure they still have the default 'Jeune' role
                        if (!$oldLeader->hasRole('Jeune') && \Spatie\Permission\Models\Role::where('name', 'Jeune')->exists()) {
                            $oldLeader->assignRole('Jeune');
                        }
                    }
                }
            }
        }
    }

    /**
     * Handle the Group "deleted" event.
     */
    public function deleted(Group $group): void
    {
        // If group is deleted, check if the leader still leads any other group
        if ($group->leader_id) {
            $leaderStillLeads = Group::where('leader_id', $group->leader_id)
                ->where('id', '!=', $group->id)
                ->exists();

            if (!$leaderStillLeads) {
                $leader = User::find($group->leader_id);
                if ($leader && $leader->hasRole('Chef de groupe')) {
                    $leader->removeRole('Chef de groupe');
                    // Ensure they still have the default 'Jeune' role
                    if (!$leader->hasRole('Jeune') && \Spatie\Permission\Models\Role::where('name', 'Jeune')->exists()) {
                        $leader->assignRole('Jeune');
                    }
                }
            }
        }
    }
}
