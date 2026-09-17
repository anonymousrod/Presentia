<?php

namespace App\Policies;

use App\Enums\PermissionEnum;
use App\Models\Group;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class GroupPolicy
{
    public function before(User $user, string $ability, ...$args): ?bool
    {
        // F-4 : Interdire les actions cross-tenant même pour un Administrateur
        if (!empty($args) && $args[0] instanceof Group) {
            $targetGroup = $args[0];
            $activeChurchId = session('tenant_church_id') ?? $user->church_id;
            if (!$user->isSuperAdmin() || session()->has('tenant_church_id')) {
                if ($activeChurchId && $targetGroup->church_id && (int) $targetGroup->church_id !== (int) $activeChurchId) {
                    return false;
                }
            }
        }

        if ($user->hasRole('Administrateur')) {
            return true;
        }

        return null;
    }

    /**
     * Voir la liste des groupes.
     */
    public function viewAny(User $user): Response
    {
        if ($user->can(PermissionEnum::GROUP_VIEW->value)) {
            return Response::allow();
        }

        // Le chef de groupe ou membre d'un groupe peut accéder à la liste (filtrée à son groupe dans le contrôleur)
        if ($user->ledGroups()->exists() || $user->groups()->wherePivotNull('left_at')->exists() || $user->can(PermissionEnum::GROUP_ASSIGN_MEMBER_OWN->value)) {
            return Response::allow();
        }

        return Response::deny("Vous n'avez pas la permission de voir les groupes.");
    }

    /**
     * Voir un groupe précis.
     * Règle contextuelle : permission globale group.view OU être chef / membre du groupe concerné.
     */
    public function view(User $user, Group $group): Response
    {
        if ($user->can(PermissionEnum::GROUP_VIEW->value)) {
            return Response::allow();
        }

        $isLeader = $group->leader_id === $user->id;
        $isMember = $group->members()->wherePivotNull('left_at')->where('users.id', $user->id)->exists();

        if ($isLeader || $isMember) {
            return Response::allow();
        }

        return Response::deny("Vous n'avez pas la permission de voir ce groupe.");
    }

    /**
     * Créer un groupe.
     */
    public function create(User $user): Response
    {
        return $user->can(PermissionEnum::GROUP_CREATE->value)
            ? Response::allow()
            : Response::deny("Vous n'avez pas la permission de créer un groupe.");
    }

    /**
     * Modifier un groupe.
     * - group.edit : modifier n'importe quel groupe de son église (Administrateurs / Gestionnaires).
     * - group.edit_own : modifier uniquement son propre groupe (Chef de groupe).
     */
    public function update(User $user, Group $group): Response
    {
        // 1. Permission globale pour tous les groupes
        if ($user->can(PermissionEnum::GROUP_EDIT->value)) {
            return Response::allow();
        }

        // 2. Permission pour son propre groupe uniquement (Chef de groupe)
        if ($user->can(PermissionEnum::GROUP_EDIT_OWN->value)) {
            if ($group->leader_id === $user->id) {
                return Response::allow();
            }

            return Response::deny("Vous ne pouvez modifier que votre propre groupe.");
        }

        return Response::deny("Vous n'avez pas la permission de modifier ce groupe.");
    }

    /**
     * Supprimer un groupe.
     */
    public function delete(User $user, Group $group): Response
    {
        return $user->can(PermissionEnum::GROUP_DELETE->value)
            ? Response::allow()
            : Response::deny("Vous n'avez pas la permission de supprimer ce groupe.");
    }

    /**
     * Assigner un membre à un groupe.
     * - group.assign_member : assigner dans n'importe quel groupe de son église (Administrateurs / Gestionnaires).
     * - group.assign_member_own : assigner uniquement dans son propre groupe (Chef de groupe / Membre).
     */
    public function assignMember(User $user, Group $group): Response
    {
        // 1. Permission globale pour tous les groupes
        if ($user->can(PermissionEnum::GROUP_ASSIGN_MEMBER->value)) {
            return Response::allow();
        }

        // 2. Permission pour son propre groupe
        if ($user->can(PermissionEnum::GROUP_ASSIGN_MEMBER_OWN->value)) {
            $isLeader = $group->leader_id === $user->id;
            $isMember = $group->members()->wherePivotNull('left_at')->where('users.id', $user->id)->exists();

            if ($isLeader || $isMember) {
                return Response::allow();
            }

            return Response::deny("Vous ne pouvez assigner des membres que dans votre propre groupe.");
        }

        return Response::deny("Vous n'avez pas la permission d'assigner un membre à ce groupe.");
    }
}
