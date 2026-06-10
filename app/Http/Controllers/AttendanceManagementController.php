<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\User;
use App\Models\Attendance;
use App\Http\Requests\UpdateAttendanceRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceManagementController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of group members and their attendance status.
     */
    public function index(Activity $activity)
    {
        $this->authorize('manage', [$activity]);
        $user = Auth::user();

        if ($activity->visibility_group_id) {
            $group = $activity->group;
            $membersQuery = $group->members()->wherePivotNull('left_at');
        } else {
            // Global activity: retrieve registered users
            $membersQuery = User::whereIn('id', function ($query) use ($activity) {
                $query->select('user_id')
                      ->from('registrations')
                      ->where('activity_id', $activity->id)
                      ->where('is_waitlisted', false)
                      ->where('status', '!=', \App\Enums\RegistrationStatus::ABSENT_JUSTIFIED->value);
            })->whereNull('deleted_at');

            // If the user is not an admin, filter to members of groups led by this user
            if (!$user->hasRole('Administrateur')) {
                $ledGroupIds = $user->ledGroups()->pluck('groups.id')->toArray();
                $membersQuery->whereHas('groups', function ($q) use ($ledGroupIds) {
                    $q->whereIn('groups.id', $ledGroupIds)
                      ->whereNull('group_members.left_at');
                });
            }
        }

        $members = $membersQuery
            ->with(['attendances' => function ($query) use ($activity) {
                $query->where('activity_id', $activity->id);
            }])
            ->get();

        $isClosed = $activity->end_time->addHour()->lt(now());

        // Retrieve all other eligible users to be added to the list
        $allEligibleQuery = User::whereNull('deleted_at');
        if ($activity->visibility_group_id) {
            $allEligibleQuery->whereIn('id', function ($q) use ($activity) {
                $q->select('user_id')
                  ->from('group_members')
                  ->where('group_id', $activity->visibility_group_id)
                  ->whereNull('left_at');
            });
        } else {
            if (!$user->hasRole('Administrateur')) {
                $ledGroupIds = $user->ledGroups()->pluck('groups.id')->toArray();
                $allEligibleQuery->whereHas('groups', function ($q) use ($ledGroupIds) {
                    $q->whereIn('groups.id', $ledGroupIds)
                      ->whereNull('group_members.left_at');
                });
            }
        }

        // Exclude users who are already in the list
        $existingMemberIds = $members->pluck('id')->toArray();
        $otherEligibleUsers = $allEligibleQuery->whereNotIn('id', $existingMemberIds)->get();

        return view('activities.attendance', compact('activity', 'members', 'isClosed', 'otherEligibleUsers'));
    }

    /**
     * Update/validate a member's attendance.
     */
    public function update(UpdateAttendanceRequest $request, Activity $activity)
    {
        $this->authorize('manage', [$activity]);

        // Clôture check: 1 hour after end_time
        if ($activity->end_time->addHour()->lt(now())) {
            abort(403, "La modification des présences est bloquée 1h après la fin de l'activité.");
        }

        $userId = $request->input('user_id');
        $status = $request->input('status');
        $note = $request->input('note');

        // Check if the user is eligible for this activity
        $belongs = false;
        if ($activity->visibility_group_id) {
            $belongs = $activity->group->members()
                ->wherePivotNull('left_at')
                ->where('users.id', $userId)
                ->exists();
        } else {
            // Global or Role activity
            if (Auth::user()->hasRole('Administrateur')) {
                // Admin can manage any active user
                $belongs = User::where('id', $userId)->whereNull('deleted_at')->exists();
            } else {
                // Chef de groupe can manage members of their led groups
                $ledGroupIds = Auth::user()->ledGroups()->pluck('groups.id')->toArray();
                $belongs = User::where('id', $userId)
                    ->whereHas('groups', function ($q) use ($ledGroupIds) {
                        $q->whereIn('groups.id', $ledGroupIds)
                          ->whereNull('group_members.left_at');
                    })->whereNull('deleted_at')->exists();
            }
        }

        if (!$belongs) {
            return response()->json(['error' => "Ce membre n'est pas éligible ou n'appartient pas à vos groupes pour cette activité."], 403);
        }

        // Automatically register the user if not already registered (or register them as PRESENT on the fly)
        \App\Models\Registration::updateOrCreate(
            [
                'user_id' => $userId,
                'activity_id' => $activity->id,
            ],
            [
                'status' => \App\Enums\RegistrationStatus::PRESENT,
                'is_waitlisted' => false,
            ]
        );

        // update or create attendance
        $attendance = Attendance::updateOrCreate(
            [
                'user_id' => $userId,
                'activity_id' => $activity->id,
            ],
            [
                'status' => $status,
                'note' => $note,
                'scan_source' => 'manual',
                'scanned_at' => now(),
                'ip_address' => $request->ip() ?? '127.0.0.1',
            ]
        );

        return response()->json([
            'success' => true,
            'attendance' => [
                'user_id' => $attendance->user_id,
                'status' => $attendance->status->value,
                'note' => $attendance->note,
                'scan_source' => $attendance->scan_source,
            ]
        ]);
    }

    /**
     * Get real-time updates for polling.
     */
    public function getUpdates(Activity $activity)
    {
        $this->authorize('manage', [$activity]);
        $user = Auth::user();

        if ($activity->visibility_group_id) {
            $group = $activity->group;
            $membersQuery = $group->members()->wherePivotNull('left_at');
        } else {
            // Global activity: retrieve registered users
            $membersQuery = User::whereIn('id', function ($query) use ($activity) {
                $query->select('user_id')
                      ->from('registrations')
                      ->where('activity_id', $activity->id)
                      ->where('is_waitlisted', false)
                      ->where('status', '!=', \App\Enums\RegistrationStatus::ABSENT_JUSTIFIED->value);
            })->whereNull('deleted_at');

            // If the user is not an admin, filter to members of groups led by this user
            if (!$user->hasRole('Administrateur')) {
                $ledGroupIds = $user->ledGroups()->pluck('groups.id')->toArray();
                $membersQuery->whereHas('groups', function ($q) use ($ledGroupIds) {
                    $q->whereIn('groups.id', $ledGroupIds)
                      ->whereNull('group_members.left_at');
                });
            }
        }

        $members = $membersQuery
            ->with(['attendances' => function ($query) use ($activity) {
                $query->where('activity_id', $activity->id);
            }])
            ->get();

        $data = $members->map(function ($member) {
            $attendance = $member->attendances->first();
            return [
                'user_id' => $member->id,
                'full_name' => $member->full_name,
                'email' => $member->email,
                'status' => $attendance?->status?->value ?? null,
                'note' => $attendance?->note ?? null,
                'scan_source' => $attendance?->scan_source ?? null,
            ];
        });

        return response()->json($data);
    }

    /**
     * Remove a member's check-in and registration for the activity.
     */
    public function destroy(Request $request, Activity $activity)
    {
        $this->authorize('manage', [$activity]);

        // Clôture check: 1 hour after end_time
        if ($activity->end_time->addHour()->lt(now())) {
            return response()->json(['error' => "La modification des présences est bloquée 1h après la fin de l'activité."], 403);
        }

        $userId = $request->input('user_id');

        // Check if the user is eligible for this activity
        $belongs = false;
        if ($activity->visibility_group_id) {
            $belongs = $activity->group->members()
                ->wherePivotNull('left_at')
                ->where('users.id', $userId)
                ->exists();
        } else {
            // Global or Role activity
            if (Auth::user()->hasRole('Administrateur')) {
                $belongs = User::where('id', $userId)->whereNull('deleted_at')->exists();
            } else {
                $ledGroupIds = Auth::user()->ledGroups()->pluck('groups.id')->toArray();
                $belongs = User::where('id', $userId)
                    ->whereHas('groups', function ($q) use ($ledGroupIds) {
                        $q->whereIn('groups.id', $ledGroupIds)
                          ->whereNull('group_members.left_at');
                    })->whereNull('deleted_at')->exists();
            }
        }

        if (!$belongs) {
            return response()->json(['error' => "Ce membre n'est pas éligible ou n'appartient pas à vos groupes."], 403);
        }

        // Delete Registration
        \App\Models\Registration::where('activity_id', $activity->id)
            ->where('user_id', $userId)
            ->delete();

        // Delete Attendance
        Attendance::where('activity_id', $activity->id)
            ->where('user_id', $userId)
            ->delete();

        return response()->json(['success' => true]);
    }
}
