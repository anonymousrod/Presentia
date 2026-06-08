<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Registration;
use App\Enums\ActivityStatus;
use App\Enums\ActivityVisibility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityController extends Controller
{
    /**
     * Display a listing of visible published activities.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Retrieve user's active groups
        $groupIds = $user->groups()
            ->wherePivotNull('left_at')
            ->pluck('groups.id')
            ->toArray();

        // Retrieve user's roles
        $roleIds = $user->roles()->pluck('id')->toArray();

        // Build query for published activities matching visibility criteria
        $query = Activity::where('status', ActivityStatus::PUBLISHED)
            ->where(function ($q) use ($groupIds, $roleIds) {
                $q->where('visibility', ActivityVisibility::ALL)
                  ->orWhere(function ($sub) use ($groupIds) {
                      $sub->where('visibility', ActivityVisibility::GROUP)
                          ->whereIn('visibility_group_id', $groupIds);
                  })
                  ->orWhere(function ($sub) use ($roleIds) {
                      $sub->where('visibility', ActivityVisibility::ROLE)
                          ->whereIn('visibility_role_id', $roleIds);
                  });
            });

        // Optional search/filter by type if needed
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $activities = $query->orderBy('start_time', 'asc')->paginate(10);

        // Get all user's registrations to show registration status
        $myRegistrations = $user->registrations()
            ->pluck('status', 'activity_id')
            ->toArray();

        // Also get waitlisted status
        $myWaitlists = $user->registrations()
            ->pluck('is_waitlisted', 'activity_id')
            ->toArray();

        return view('activities.index', compact('activities', 'myRegistrations', 'myWaitlists'));
    }

    /**
     * Display the specified activity details.
     */
    public function show(Activity $activity)
    {
        $user = Auth::user();

        // Check if the activity is PUBLISHED
        if ($activity->status !== ActivityStatus::PUBLISHED) {
            abort(403, "Cette activité n'est pas disponible.");
        }

        $this->authorizeVisibility($activity, $user);

        $myRegistration = Registration::where('user_id', $user->id)
            ->where('activity_id', $activity->id)
            ->first();

        $activeRegistrationsCount = Registration::where('activity_id', $activity->id)
            ->where('is_waitlisted', false)
            ->where('status', '!=', \App\Enums\RegistrationStatus::ABSENT_JUSTIFIED)
            ->count();

        return view('activities.show', compact('activity', 'myRegistration', 'activeRegistrationsCount'));
    }

    /**
     * Helper: check if user has access to see/register for this activity.
     */
    protected function authorizeVisibility(Activity $activity, $user)
    {
        if ($activity->visibility === ActivityVisibility::ALL) {
            return;
        }

        if ($activity->visibility === ActivityVisibility::GROUP) {
            $isMember = $user->groups()
                ->where('groups.id', $activity->visibility_group_id)
                ->wherePivotNull('left_at')
                ->exists();
            if ($isMember) {
                return;
            }
        }

        if ($activity->visibility === ActivityVisibility::ROLE) {
            if ($user->roles()->where('roles.id', $activity->visibility_role_id)->exists()) {
                return;
            }
        }

        abort(403, "Vous n'avez pas accès à cette activité.");
    }
}
