<?php

namespace App\Events;

use App\Models\Group;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GroupLeaderAssigned
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly Group $group,
        public readonly User $newLeader,
    ) {
    }
}
