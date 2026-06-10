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
     * Publier une activité (rendre visible aux membres).
     */
    public function publish(User $user, Activity $activity): Response
    {
        return $user->can(PermissionEnum::ACTIVITY_PUBLISH->value)
            ? Response::allow()
            : Response::deny("Vous n'avez pas la permission de publier cette activité.");
    }

    /**
     * Annuler une activité.
     */
    public function cancel(User $user, Activity $activity): Response
    {
        return $user->can(PermissionEnum::ACTIVITY_CANCEL->value)
            ? Response::allow()
            : Response::deny("Vous n'avez pas la permission d'annuler cette activité.");
    }

    /**
     * Archiver une activité.
     */
    public function archive(User $user, Activity $activity): Response
    {
        return $user->can(PermissionEnum::ACTIVITY_ARCHIVE->value)
            ? Response::allow()
            : Response::deny("Vous n'avez pas la permission d'archiver cette activité.");
    }

    /**
     * Gérer la présence pour cette activité.
     */
    public function manage(User $user, Activity $activity): Response
    {
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
            : Response::deny("Vous n'avez pas la permission de gérer l'émargement de cette activité.");
    }
}
