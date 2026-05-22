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
     * Register the authenticated user to the activity.
     */
    public function register(Activity $activity)
    {
        $user = Auth::user();

        // Check if the activity is PUBLISHED and visible
        if ($activity->status !== ActivityStatus::PUBLISHED) {
            abort(403, "Cette activité n'est pas disponible.");
        }

        // Check visibility access
        $this->authorizeVisibility($activity, $user);

        // Check if the activity has already started
        if ($activity->start_time->lte(now())) {
            return redirect()->back()->with('error', "Cette activité a déjà commencé. L'inscription n'est plus possible.");
        }

        // Check if already registered (not cancelled)
        $registration = Registration::where('user_id', $user->id)
            ->where('activity_id', $activity->id)
            ->first();

        if ($registration && $registration->status !== 'ABSENT_JUSTIFIED') {
            return redirect()->back()->with('error', 'Vous êtes déjà inscrit à cette activité.');
        }

        // Check capacity & waitlist
        $registeredCount = Registration::where('activity_id', $activity->id)
            ->where('is_waitlisted', false)
            ->where('status', '!=', 'ABSENT_JUSTIFIED')
            ->count();

        $isWaitlisted = false;
        if ($activity->capacity && $registeredCount >= $activity->capacity) {
            $isWaitlisted = true;
        }

        if ($registration) {
            // Reactivate registration
            $registration->update([
                'status' => 'PRESENT',
                'is_waitlisted' => $isWaitlisted,
                'justification' => null,
                'registered_at' => now(),
            ]);
        } else {
            Registration::create([
                'user_id' => $user->id,
                'activity_id' => $activity->id,
                'status' => 'PRESENT',
                'is_waitlisted' => $isWaitlisted,
                'registered_at' => now(),
            ]);
        }

        $msg = $isWaitlisted
            ? 'Vous avez été inscrit sur la liste d\'attente de cette activité.'
            : 'Inscription enregistrée avec succès.';

        return redirect()->back()->with('success', $msg);
    }

    /**
     * Cancel/Unregister the authenticated user from the activity.
     */
    public function unregister(Activity $activity)
    {
        $user = Auth::user();

        // Check if the activity has already started
        if ($activity->start_time->lte(now())) {
            return redirect()->back()->with('error', "Cette activité a déjà commencé. La désinscription n'est plus possible.");
        }

        $registration = Registration::where('user_id', $user->id)
            ->where('activity_id', $activity->id)
            ->first();

        if (!$registration || $registration->status === 'ABSENT_JUSTIFIED') {
            return redirect()->back()->with('error', 'Vous n\'êtes pas inscrit à cette activité.');
        }

        // Mark as ABSENT_JUSTIFIED (cancellation status)
        $registration->update([
            'status' => 'ABSENT_JUSTIFIED',
            'justification' => 'Annulation par le membre',
            'is_waitlisted' => false,
        ]);

        // If waitlist exists, promote the first person on waitlist
        $this->promoteFromWaitlist($activity);

        return redirect()->back()->with('success', 'Votre inscription a été annulée.');
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

    /**
     * Promote the first waitlisted registration for the activity.
     */
    protected function promoteFromWaitlist(Activity $activity)
    {
        if (!$activity->capacity) {
            return;
        }

        // Count active non-waitlisted registrations
        $registeredCount = Registration::where('activity_id', $activity->id)
            ->where('is_waitlisted', false)
            ->where('status', '!=', 'ABSENT_JUSTIFIED')
            ->count();

        if ($registeredCount < $activity->capacity) {
            // Find the oldest waitlisted registration
            $next = Registration::where('activity_id', $activity->id)
                ->where('is_waitlisted', true)
                ->orderBy('registered_at', 'asc')
                ->first();

            if ($next) {
                $next->update(['is_waitlisted' => false]);
            }
        }
    }
}
