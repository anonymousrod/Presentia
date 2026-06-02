<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PermissionService;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

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
        $permissions = Permission::orderBy('name')->get();

        // Récupérer les permissions directes de l'utilisateur
        $directPermissionNames = $user->getDirectPermissions()->pluck('name')->toArray();

        // Récupérer les permissions que l'utilisateur a via ses rôles
        $rolePermissionNames = $user->getPermissionsViaRoles()->pluck('name')->toArray();

        // Grouper toutes les permissions par catégorie (ressource)
        $groupedPermissions = $permissions->groupBy(function ($permission) {
            $parts = explode('.', $permission->name);
            return count($parts) > 1 ? $parts[0] : 'other';
        });

        return view('admin.users.permissions', compact(
            'user',
            'groupedPermissions',
            'directPermissionNames',
            'rolePermissionNames'
        ));
    }

    /**
     * Met à jour les permissions directes d'un utilisateur.
     */
    public function updateUserPermissions(Request $request, User $user)
    {
        $data = $request->validate([
            'permissions'   => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $permissionNames = $data['permissions'] ?? [];

        $this->permissionService->syncUserDirectPermissions($user, $permissionNames);

        return redirect()->route('admin.users.permissions.edit', $user)
            ->with('success', "Les permissions directes de {$user->first_name} {$user->name} ont été mises à jour.");
    }
}
