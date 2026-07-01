<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Registration;
use App\Enums\RegistrationStatus;
use App\Enums\ActivityStatus;
use App\Http\Requests\StoreRegistrationRequest;
use App\Jobs\SendRegistrationConfirmation;
use App\Notifications\Activity\RegistrationConfirmedNotification;
use App\Notifications\Activity\RegistrationWaitlistedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RegistrationController extends Controller
{
    /**
     * Store a new registration or reactivate a cancellation.
     */
    public function store(StoreRegistrationRequest $request, Activity $activity)
    {
        $user = Auth::user();

        // 1. Check if the activity is PUBLISHED
        if ($activity->status !== ActivityStatus::PUBLISHED) {
            abort(403, "Cette activité n'est pas disponible.");
        }

        // 2. Check visibility access
        $this->authorizeVisibility($activity, $user);

        // 3. Check if already started (specific error message for backward compatibility with tests)
        if ($activity->start_time->lte(now())) {
            return redirect()->back()->with('error', "Cette activité a déjà commencé. L'inscription n'est plus possible.");
        }

        $status = RegistrationStatus::from($request->input('status'));

        return DB::transaction(function () use ($request, $activity, $user, $status) {
            // Check double registration
            $registration = Registration::where('user_id', $user->id)
                ->where('activity_id', $activity->id)
                ->first();

            if ($registration && $registration->status !== RegistrationStatus::ABSENT_JUSTIFIED) {
                return redirect()->back()->with('error', 'Vous êtes déjà inscrit à cette activité.');
            }

            // Calculate active registered count
            $registeredCount = Registration::where('activity_id', $activity->id)
                ->where('is_waitlisted', false)
                ->where('status', '!=', RegistrationStatus::ABSENT_JUSTIFIED)
                ->count();

            // Waitlist check
            $isWaitlisted = false;
            if ($status !== RegistrationStatus::ABSENT_JUSTIFIED) {
                if ($activity->capacity && $registeredCount >= $activity->capacity) {
                    $isWaitlisted = true;
                }
            }

            if ($registration) {
                $registration->update([
                    'status' => $status,
                    'is_waitlisted' => $isWaitlisted,
                    'justification' => $status === RegistrationStatus::ABSENT_JUSTIFIED ? $request->input('justification') : null,
                    'registered_at' => now(),
                ]);
            } else {
                $registration = Registration::create([
                    'user_id' => $user->id,
                    'activity_id' => $activity->id,
                    'status' => $status,
                    'is_waitlisted' => $isWaitlisted,
                    'justification' => $status === RegistrationStatus::ABSENT_JUSTIFIED ? $request->input('justification') : null,
                    'registered_at' => now(),
                ]);
            }

            // Dispatch confirmation notification (WhatsApp)
            dispatch(new SendRegistrationConfirmation($registration));

            // Notification système
            if ($isWaitlisted) {
                $user->notify(new RegistrationWaitlistedNotification($activity));
            } else {
                $user->notify(new RegistrationConfirmedNotification($activity));
            }

            $msg = $isWaitlisted
                ? "Vous avez été inscrit sur la liste d'attente de cette activité."
                : "Inscription enregistrée avec succès.";

            return redirect()->route('activities.show', $activity)->with('success', $msg);
        });
    }

    /**
     * Update an existing registration status.
     */
    public function update(StoreRegistrationRequest $request, Activity $activity)
    {
        $user = Auth::user();

        // Check if already started
        if ($activity->start_time->lte(now())) {
            return redirect()->back()->with('error', "Cette activité a déjà commencé. Modification de l'inscription impossible.");
        }

        $newStatus = RegistrationStatus::from($request->input('status'));

        return DB::transaction(function () use ($request, $activity, $user, $newStatus) {
            $registration = Registration::where('user_id', $user->id)
                ->where('activity_id', $activity->id)
                ->firstOrFail();

            $oldStatus = $registration->status;
            $oldWaitlisted = $registration->is_waitlisted;

            // Recalculate waitlist and promotion
            $isWaitlisted = $registration->is_waitlisted;

            if ($newStatus === RegistrationStatus::ABSENT_JUSTIFIED) {
                // Changing to cancelled -> no longer waitlisted
                $isWaitlisted = false;
            } elseif ($oldStatus === RegistrationStatus::ABSENT_JUSTIFIED) {
                // Changing from cancelled to active -> check capacity
                $registeredCount = Registration::where('activity_id', $activity->id)
                    ->where('is_waitlisted', false)
                    ->where('status', '!=', RegistrationStatus::ABSENT_JUSTIFIED)
                    ->count();

                if ($activity->capacity && $registeredCount >= $activity->capacity) {
                    $isWaitlisted = true;
                } else {
                    $isWaitlisted = false;
                }
            }

            // Update registration
            $registration->update([
                'status' => $newStatus,
                'is_waitlisted' => $isWaitlisted,
                'justification' => $newStatus === RegistrationStatus::ABSENT_JUSTIFIED ? $request->input('justification') : null,
            ]);

            // Handle waitlist promotion if user becomes absent and they were NOT waitlisted
            if ($newStatus === RegistrationStatus::ABSENT_JUSTIFIED && !$oldWaitlisted) {
                $this->promoteFromWaitlist($activity);
            }

            // Dispatch confirmation notification
            dispatch(new SendRegistrationConfirmation($registration->fresh()));

            $msg = $isWaitlisted
                ? "Votre statut a été mis à jour et vous êtes sur la liste d'attente."
                : "Votre inscription a été mise à jour avec succès.";

            return redirect()->route('activities.show', $activity)->with('success', $msg);
        });
    }

    /**
     * Cancel/unregister the authenticated user from the activity (cancellation).
     */
    public function destroy(Request $request, Activity $activity)
    {
        $user = Auth::user();

        // Check if the activity has already started (specific error message for backward compatibility)
        if ($activity->start_time->lte(now())) {
            return redirect()->back()->with('error', "Cette activité a déjà commencé. La désinscription n'est plus possible.");
        }

        // Validate justification
        $request->validate([
            'justification' => 'required|string|min:5|max:255',
        ], [
            'justification.required' => 'Le motif est obligatoire lorsque vous indiquez être absent.',
            'justification.min' => 'Le motif doit faire au moins 5 caractères.',
            'justification.max' => 'Le motif ne peut pas dépasser 255 caractères.',
        ]);

        // 2-hour deadline check
        if ($activity->start_time->subHours(2)->lt(now())) {
            return redirect()->back()->with('error', "La désinscription n'est plus possible à moins de 2 heures du début de l'activité.");
        }

        return DB::transaction(function () use ($request, $activity, $user) {
            $registration = Registration::where('user_id', $user->id)
                ->where('activity_id', $activity->id)
                ->first();

            if (!$registration || $registration->status === RegistrationStatus::ABSENT_JUSTIFIED) {
                return redirect()->back()->with('error', "Vous n'êtes pas inscrit à cette activité.");
            }

            $wasWaitlisted = $registration->is_waitlisted;

            // Mark as ABSENT_JUSTIFIED (cancellation status)
            $registration->update([
                'status' => RegistrationStatus::ABSENT_JUSTIFIED,
                'justification' => $request->input('justification'),
                'is_waitlisted' => false,
            ]);

            // Promote oldest waitlisted person if this user was active
            if (!$wasWaitlisted) {
                $this->promoteFromWaitlist($activity);
            }

            // Dispatch confirmation notification
            dispatch(new SendRegistrationConfirmation($registration->fresh()));

            return redirect()->back()->with('success', 'Votre inscription a été annulée.');
        });
    }

    /**
     * Helper: check if user has access to see/register for this activity.
     */
    protected function authorizeVisibility(Activity $activity, $user)
    {
        if ($user && $user->hasRole('Administrateur')) {
            return;
        }

        if ($activity->visibility === \App\Enums\ActivityVisibility::ALL) {
            return;
        }

        if ($activity->visibility === \App\Enums\ActivityVisibility::GROUP) {
            $isMember = $user->groups()
                ->where('groups.id', $activity->visibility_group_id)
                ->wherePivotNull('left_at')
                ->exists();
            if ($isMember) {
                return;
            }
        }

        if ($activity->visibility === \App\Enums\ActivityVisibility::ROLE) {
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
            ->where('status', '!=', RegistrationStatus::ABSENT_JUSTIFIED)
            ->count();

        if ($registeredCount < $activity->capacity) {
            // Find the oldest waitlisted registration
            $next = Registration::where('activity_id', $activity->id)
                ->where('is_waitlisted', true)
                ->orderBy('registered_at', 'asc')
                ->first();

            if ($next) {
                $next->update(['is_waitlisted' => false]);

                // Dispatch confirmation notification for promoted user
                dispatch(new SendRegistrationConfirmation($next));
            }
        }
    }
}
