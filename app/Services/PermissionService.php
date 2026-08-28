<?php

namespace App\Services;

use App\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Spatie\Permission\PermissionRegistrar;

class PermissionService
{
    protected PermissionRegistrar $registrar;

    public function __construct(PermissionRegistrar $registrar)
    {
        $this->registrar = $registrar;
    }

    /**
     * Crée un nouveau rôle Spatie rattaché à l'église courante et lui synchronise les permissions indiquées.
     */
    public function createRole(string $name, ?string $description, array $permissionNames, ?int $churchId = null): Role
    {
        $churchId = $churchId ?? session('tenant_church_id') ?? auth()->user()?->church_id ?? null;

        $role = Role::create([
            'church_id'   => $churchId,
            'name'        => $name,
            'description' => $description,
            'guard_name'  => 'web'
        ]);
        $role->syncPermissions($permissionNames);

        $this->clearCache();

        return $role;
    }

    /**
     * Met à jour le nom et les permissions d'un rôle Spatie.
     */
    public function updateRole(Role $role, string $name, ?string $description, array $permissionNames): Role
    {
        $role->update(['name' => $name, 'description' => $description]);
        $role->syncPermissions($permissionNames);

        $this->clearCache();

        return $role;
    }

    /**
     * Supprime un rôle Spatie en s'assurant que ce ne soit pas un rôle système.
     */
    public function deleteRole(Role $role): void
    {
        if ($role->is_system) {
            throw new \Exception("Le rôle système '{$role->name}' ne peut pas être supprimé.");
        }

        $role->delete();
        $this->clearCache();
    }

    /**
     * Synchronise les permissions directes de l'utilisateur.
     */
    public function syncUserDirectPermissions(User $user, array $permissionNames): void
    {
        $user->syncPermissions($permissionNames);
        $this->clearCache();
    }

    /**
     * Synchronise les rôles de l'utilisateur dans son église.
     */
    public function syncUserRoles(User $user, array $roleNames): void
    {
        $churchId = $user->church_id;
        $defaultRole = Role::where('church_id', $churchId)->where('code', 'default_user')->first();
        if ($defaultRole && !in_array($defaultRole->name, $roleNames)) {
            $roleNames[] = $defaultRole->name;
        }

        // Préserver le rôle 'Chef de groupe' s'il l'a déjà
        $groupLeaderRole = Role::where('church_id', $churchId)->where('code', 'group_leader')->first();
        if ($groupLeaderRole && $user->hasRole($groupLeaderRole->name) && !in_array($groupLeaderRole->name, $roleNames)) {
            $roleNames[] = $groupLeaderRole->name;
        }

        $user->syncRoles($roleNames);
        $this->clearCache();
    }

    /**
     * Invalide le cache Spatie Permission.
     */
    public function clearCache(): void
    {
        $this->registrar->forgetCachedPermissions();
    }
}
