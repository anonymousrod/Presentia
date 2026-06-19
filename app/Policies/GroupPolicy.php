<?php

namespace App\Policies;

use App\Enums\PermissionEnum;
use App\Models\Group;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class GroupPolicy
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
     * Voir la liste de tous les groupes.
     */
    public function viewAny(User $user): Response
    {
        if ($user->can(PermissionEnum::GROUP_VIEW->value)) {
            return Response::allow();
        }

        // Le chef de groupe peut voir mais uniquement son propre groupe (group.view_own)
        if ($user->can(PermissionEnum::GROUP_VIEW_OWN->value)) {
            return Response::allow();
        }

        return Response::deny("Vous n'avez pas la permission de voir les groupes.");
    }

    /**
     * Voir un groupe précis.
     * Règle contextuelle : le chef ne peut voir QUE son propre groupe.
     */
    public function view(User $user, Group $group): Response
    {
        if ($user->can(PermissionEnum::GROUP_VIEW->value)) {
            return Response::allow();
        }

        // Règle contextuelle chef de groupe ou membre du groupe
        if ($user->can(PermissionEnum::GROUP_VIEW_OWN->value)) {
            $isLeader = $group->leader_id === $user->id;
            $isMember = $group->members()->wherePivotNull('left_at')->where('users.id', $user->id)->exists();

            if ($isLeader || $isMember) {
                return Response::allow();
            }
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
     * Règle contextuelle : le chef ne peut modifier QUE son propre groupe.
     */
    public function update(User $user, Group $group): Response
    {
        if ($user->can(PermissionEnum::GROUP_EDIT->value)) {
            return Response::allow();
        }

        // 2. Si le rôle 'Chef de groupe' est assigné, il ne peut modifier que SON groupe
        $groupLeaderRole = \Spatie\Permission\Models\Role::where('code', 'group_leader')->first();
        if ($groupLeaderRole && $user->hasRole($groupLeaderRole->name) && $group->leader_id === $user->id) {
            return Response::allow();
        }

        return Response::deny("Vous n'avez pas la permission de modifier ce groupe. Seul le chef de ce groupe peut le modifier.");
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
     * Règle contextuelle : un chef de groupe peut uniquement assigner dans SON groupe.
     */
    public function assignMember(User $user, Group $group): Response
    {
        if ($user->can(PermissionEnum::GROUP_ASSIGN_MEMBER->value)) {
            return Response::allow();
        }

        $groupLeaderRole = \Spatie\Permission\Models\Role::where('code', 'group_leader')->first();
        if ($groupLeaderRole && $user->hasRole($groupLeaderRole->name) && $group->leader_id === $user->id) {
            return Response::allow();
        }

        return Response::deny("Vous n'avez pas la permission d'assigner un membre à ce groupe.");
    }
}
