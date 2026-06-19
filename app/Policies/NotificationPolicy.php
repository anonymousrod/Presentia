<?php

namespace App\Policies;

use App\Enums\PermissionEnum;
use App\Models\Group;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class NotificationPolicy
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
     * Envoyer une notification à tous les membres.
     */
    public function sendToAll(User $user): Response
    {
        return $user->can(PermissionEnum::NOTIFICATION_SEND_ALL->value)
            ? Response::allow()
            : Response::deny("Vous n'avez pas la permission d'envoyer une notification globale à tous les membres.");
    }

    /**
     * Envoyer une notification à un groupe.
     * Règle contextuelle : le chef de groupe ne peut envoyer qu'à SON propre groupe.
     */
    public function sendToGroup(User $user, Group $group): Response
    {
        if ($user->can(PermissionEnum::NOTIFICATION_SEND_ALL->value)) {
            return Response::allow();
        }

        $groupLeaderRole = \Spatie\Permission\Models\Role::where('code', 'group_leader')->first();
        if ($user->can(PermissionEnum::NOTIFICATION_SEND_GROUP->value)) {
            if ($groupLeaderRole && $user->hasRole($groupLeaderRole->name) && $group->leader_id !== $user->id) {
                return Response::deny("Vous ne pouvez envoyer une notification qu'aux membres de votre propre groupe.");
            }

            return Response::allow();
        }

        return Response::deny("Vous n'avez pas la permission d'envoyer une notification à un groupe.");
    }

    /**
     * Envoyer une notification à un rôle (tous les Jeunes, tous les Chefs de groupe, etc.).
     */
    public function sendToRole(User $user): Response
    {
        return $user->can(PermissionEnum::NOTIFICATION_SEND_ROLE->value)
            ? Response::allow()
            : Response::deny("Vous n'avez pas la permission d'envoyer une notification à un rôle.");
    }

    /**
     * Envoyer une notification individuelle à un utilisateur précis.
     */
    public function sendToIndividual(User $user): Response
    {
        return $user->can(PermissionEnum::NOTIFICATION_SEND_INDIVIDUAL->value)
            ? Response::allow()
            : Response::deny("Vous n'avez pas la permission d'envoyer une notification individuelle.");
    }
}
