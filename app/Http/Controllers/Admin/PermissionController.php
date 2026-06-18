<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PermissionService;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    protected PermissionService $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    /**
     * Affiche l'interface de gestion des permissions pour un utilisateur.
     */
    public function editUserPermissions(User $user)
    {
        $this->authorize('permission.manage');

        $permissions = Permission::orderBy('name')->get();
        $roles = Role::orderBy('name')->get();

        // Récupérer les permissions directes de l'utilisateur
        $directPermissionNames = $user->getDirectPermissions()->pluck('name')->toArray();

        // Récupérer les permissions que l'utilisateur a via ses rôles
        $rolePermissionNames = $user->getPermissionsViaRoles()->pluck('name')->toArray();

        // Récupérer les noms des rôles actuels de l'utilisateur
        $currentUserRoleNames = $user->getRoleNames()->toArray();

        // Grouper toutes les permissions par catégorie (ressource)
        $groupedPermissions = $permissions->groupBy(function ($permission) {
            $parts = explode('.', $permission->name);
            return count($parts) > 1 ? $parts[0] : 'other';
        });

        return view('admin.users.permissions', compact(
            'user',
            'roles',
            'groupedPermissions',
            'directPermissionNames',
            'rolePermissionNames',
            'currentUserRoleNames'
        ));
    }

    /**
     * Met à jour les rôles et permissions directes d'un utilisateur.
     */
    public function updateUserPermissions(Request $request, User $user)
    {
        $this->authorize('permission.manage');

        $data = $request->validate([
            'roles'         => ['nullable', 'array'],
            'roles.*'       => ['string', 'exists:roles,name'],
            'permissions'   => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $roleNames = $data['roles'] ?? [];
        $permissionNames = $data['permissions'] ?? [];

        // Synchroniser les rôles
        $this->permissionService->syncUserRoles($user, $roleNames);

        // Synchroniser les permissions directes
        $this->permissionService->syncUserDirectPermissions($user, $permissionNames);

        return redirect()->route('admin.users.permissions.edit', $user)
            ->with('success', "Les rôles et permissions de {$user->first_name} {$user->name} ont été mis à jour.");
    }
}
