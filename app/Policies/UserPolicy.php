<?php

namespace App\Policies;

use App\Enums\PermissionEnum;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
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
     * Voir la liste des utilisateurs.
     */
    public function viewAny(User $user): Response
    {
        return $user->can(PermissionEnum::MEMBER_VIEW->value)
            ? Response::allow()
            : Response::deny("Vous n'avez pas la permission de voir la liste des membres.");
    }

    /**
     * Voir un utilisateur précis.
     */
    public function view(User $user, User $model): Response
    {
        return $user->can(PermissionEnum::MEMBER_VIEW->value)
            ? Response::allow()
            : Response::deny("Vous n'avez pas la permission de voir ce profil.");
    }

    /**
     * Créer un utilisateur — réservé à l'admin (AUTH-001).
     */
    public function create(User $user): Response
    {
        return $user->can(PermissionEnum::MEMBER_CREATE->value)
            ? Response::allow()
            : Response::deny("Seul l'administrateur peut créer des comptes.");
    }

    /**
     * Modifier un utilisateur.
     * Règle contextuelle : un Jeune peut modifier son propre profil, SAUF le champ "phone".
     * Le champ "phone" est réservé à ceux qui ont la permission member.edit (admin, bureau…).
     */
    public function update(User $user, User $model): Response
    {
        // Un utilisateur peut modifier son propre profil de base
        if ($user->id === $model->id) {
            return Response::allow();
        }

        return $user->can(PermissionEnum::MEMBER_EDIT->value)
            ? Response::allow()
            : Response::deny("Vous n'avez pas la permission de modifier le profil de cet utilisateur.");
    }

    /**
     * Modifier le téléphone d'un utilisateur — réservé à l'admin / bureau.
     */
    public function updatePhone(User $user, User $model): Response
    {
        return $user->can(PermissionEnum::MEMBER_EDIT->value)
            ? Response::allow()
            : Response::deny("Vous n'êtes pas autorisé à modifier le numéro de téléphone d'un autre membre.");
    }

    /**
     * Supprimer (soft delete) un utilisateur.
     */
    public function delete(User $user, User $model): Response
    {
        if ($user->id === $model->id) {
            return Response::deny("Vous ne pouvez pas supprimer votre propre compte.");
        }

        return $user->can(PermissionEnum::MEMBER_DELETE->value)
            ? Response::allow()
            : Response::deny("Vous n'avez pas la permission de supprimer cet utilisateur.");
    }

    /**
     * Restaurer un utilisateur supprimé.
     */
    public function restore(User $user, User $model): Response
    {
        return $user->can(PermissionEnum::MEMBER_RESTORE->value)
            ? Response::allow()
            : Response::deny("Vous n'avez pas la permission de restaurer cet utilisateur.");
    }

    /**
     * Exporter la liste des membres.
     */
    public function export(User $user): Response
    {
        return $user->can(PermissionEnum::MEMBER_EXPORT->value)
            ? Response::allow()
            : Response::deny("Vous n'avez pas la permission d'exporter la liste des membres.");
    }
}
