<?php

namespace App\Events;

use App\Models\Activity;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ActivityCreated
{
    use Dispatchable;
    use SerializesModels;

    public Activity $activity;

    /**
     * Create a new event instance.
     */
    public function __construct(Activity $activity)
    {
        $this->activity = $activity;
    }
}
