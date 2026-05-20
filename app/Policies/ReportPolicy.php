<?php

namespace App\Policies;

use App\Enums\PermissionEnum;
use App\Models\Group;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ReportPolicy
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
     * Voir les statistiques globales de toute la plateforme.
     */
    public function viewGlobalStats(User $user): Response
    {
        return $user->can(PermissionEnum::STATS_VIEW_GLOBAL->value)
            ? Response::allow()
            : Response::deny("Vous n'avez pas la permission de voir les statistiques globales.");
    }

    /**
     * Voir les statistiques de son propre groupe.
     * Règle contextuelle : le chef de groupe peut uniquement voir les stats de son groupe.
     */
    public function viewGroupStats(User $user, Group $group): Response
    {
        if ($user->can(PermissionEnum::STATS_VIEW_GLOBAL->value)) {
            return Response::allow();
        }

        if ($user->can(PermissionEnum::STATS_VIEW_OWN_GROUP->value) && $group->leader_id === $user->id) {
            return Response::allow();
        }

        return Response::deny("Vous n'avez pas la permission de voir les statistiques de ce groupe.");
    }

    /**
     * Exporter le rapport global (tous les membres, toutes les activités).
     */
    public function exportGlobal(User $user): Response
    {
        return $user->can(PermissionEnum::REPORT_EXPORT_GLOBAL->value)
            ? Response::allow()
            : Response::deny("Vous n'avez pas la permission d'exporter le rapport global.");
    }

    /**
     * Exporter le rapport de son propre groupe.
     * Règle contextuelle : le chef ne peut exporter que pour SON groupe.
     */
    public function exportGroup(User $user, Group $group): Response
    {
        if ($user->can(PermissionEnum::REPORT_EXPORT_GLOBAL->value)) {
            return Response::allow();
        }

        if ($user->can(PermissionEnum::REPORT_EXPORT_OWN_GROUP->value) && $group->leader_id === $user->id) {
            return Response::allow();
        }

        return Response::deny("Vous n'avez pas la permission d'exporter le rapport de ce groupe. Seul le chef de ce groupe peut l'exporter.");
    }
}
