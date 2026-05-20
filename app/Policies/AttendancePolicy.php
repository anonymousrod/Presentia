<?php

namespace App\Policies;

use App\Enums\PermissionEnum;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AttendancePolicy
{
    /**
     * Bypass global : l'Administrateur passe toutes les vérifications.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('Administrateur')) {
            return true;
        }

        return null;
    }

    /**
     * Voir toutes les présences.
     */
    public function viewAny(User $user): Response
    {
        if ($user->can(PermissionEnum::ATTENDANCE_VIEW->value)) {
            return Response::allow();
        }

        if ($user->can(PermissionEnum::ATTENDANCE_VIEW_OWN->value)) {
            return Response::allow();
        }

        return Response::deny("Vous n'avez pas la permission de voir les présences.");
    }

    /**
     * Voir une présence précise.
     * Règle contextuelle : le chef ne peut consulter QUE les présences de son groupe.
     */
    public function view(User $user, Attendance $attendance): Response
    {
        if ($user->can(PermissionEnum::ATTENDANCE_VIEW->value)) {
            return Response::allow();
        }

        // Règle contextuelle : le chef de groupe vérifie si l'activité appartient à son groupe
        if ($user->can(PermissionEnum::ATTENDANCE_VIEW_OWN->value)) {
            $activity = $attendance->activity;
            $leadsGroupForActivity = $user->ledGroups()
                ->whereHas('members', fn ($q) => $q->where('users.id', $attendance->user_id))
                ->exists();

            if ($leadsGroupForActivity) {
                return Response::allow();
            }
        }

        return Response::deny("Vous n'avez pas la permission de voir cette présence.");
    }

    /**
     * Scanner un QR code pour enregistrer une présence.
     */
    public function scanQr(User $user): Response
    {
        return $user->can(PermissionEnum::ATTENDANCE_SCAN_QR->value)
            ? Response::allow()
            : Response::deny("Vous n'avez pas la permission de scanner un QR code de présence.");
    }

    /**
     * Valider manuellement une présence.
     * Règle contextuelle : le chef ne peut valider QUE pour les membres de son groupe.
     */
    public function validateManual(User $user, Attendance $attendance): Response
    {
        if ($user->can(PermissionEnum::ATTENDANCE_VIEW->value) && $user->can(PermissionEnum::ATTENDANCE_VALIDATE_MANUAL->value)) {
            return Response::allow();
        }

        // Règle contextuelle : le chef de groupe valide uniquement pour son groupe
        if ($user->can(PermissionEnum::ATTENDANCE_VALIDATE_MANUAL->value)) {
            $memberBelongsToLeadersGroup = $user->ledGroups()
                ->whereHas('members', fn ($q) => $q->where('users.id', $attendance->user_id))
                ->exists();

            if ($memberBelongsToLeadersGroup) {
                return Response::allow();
            }

            return Response::deny("Vous ne pouvez valider manuellement que les présences des membres de votre propre groupe.");
        }

        return Response::deny("Vous n'avez pas la permission de valider manuellement des présences.");
    }
}
