<?php

namespace App\Policies;

use App\Enums\PermissionEnum;
use App\Models\Activity;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ActivityPolicy
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
     * Voir la liste des activités.
     */
    public function viewAny(User $user): Response
    {
        return $user->can(PermissionEnum::ACTIVITY_VIEW->value)
            ? Response::allow()
            : Response::deny("Vous n'avez pas la permission de voir les activités.");
    }

    /**
     * Voir une activité précise.
     */
    public function view(User $user, Activity $activity): Response
    {
        return $user->can(PermissionEnum::ACTIVITY_VIEW->value)
            ? Response::allow()
            : Response::deny("Vous n'avez pas la permission de voir cette activité.");
    }

    /**
     * Créer une activité.
     */
    public function create(User $user): Response
    {
        return $user->can(PermissionEnum::ACTIVITY_CREATE->value)
            ? Response::allow()
            : Response::deny("Vous n'avez pas la permission de créer une activité.");
    }

    /**
     * Modifier une activité.
     */
    public function update(User $user, Activity $activity): Response
    {
        return $user->can(PermissionEnum::ACTIVITY_EDIT->value)
            ? Response::allow()
            : Response::deny("Vous n'avez pas la permission de modifier cette activité.");
    }



    /**
     * Supprimer une activité.
     */
    public function delete(User $user, Activity $activity): Response
    {
        // Seul l'Administrateur peut supprimer définitivement une activité via le bypass before()
        return Response::deny("Seul un Administrateur peut supprimer définitivement une activité.");
    }

    /**
     * Gérer la présence pour cette activité.
     */
    public function manage(User $user, Activity $activity): Response
    {
        // Les utilisateurs ayant la permission explicite globale peuvent toujours gérer l'émargement
        if ($user->can(PermissionEnum::ATTENDANCE_VALIDATE_MANUAL_ALL->value)) {
            return Response::allow();
        }

        // Sinon, l'utilisateur doit avoir la permission de gérer SON groupe
        if (!$user->can(PermissionEnum::ATTENDANCE_VALIDATE_MANUAL_OWN->value)) {
            return Response::deny("Vous n'avez pas la permission de gérer l'émargement.");
        }

        // Si c'est une activité de groupe, vérifier s'il est chef du groupe en question
        if ($activity->visibility === \App\Enums\ActivityVisibility::GROUP) {
            if (!$activity->visibility_group_id) {
                return Response::deny("Aucun groupe n'est associé à cette activité.");
            }

            $isLeader = $user->ledGroups()->where('groups.id', $activity->visibility_group_id)->exists();

            return $isLeader
                ? Response::allow()
                : Response::deny("Vous n'êtes pas le chef du groupe associé à cette activité.");
        }

        // Pour les activités globales (ALL, ROLE), tout chef de groupe actif peut y accéder pour émarger son propre groupe
        $hasLedGroups = $user->ledGroups()->exists();

        return $hasLedGroups
            ? Response::allow()
            : Response::deny("Vous n'avez aucun groupe à gérer pour cette activité.");
    }
}
