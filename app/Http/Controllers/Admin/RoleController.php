<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PermissionService;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    protected PermissionService $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    /**
     * Liste de tous les rôles.
     */
    public function index()
    {
        $roles = Role::withCount('users', 'permissions')
            ->orderBy('name')
            ->get();

        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Formulaire de création d'un rôle.
     */
    public function create()
    {
        $permissions = Permission::orderBy('name')->get();

        // Grouper les permissions par catégorie (ressource)
        $groupedPermissions = $permissions->groupBy(function ($permission) {
            $parts = explode('.', $permission->name);
            return count($parts) > 1 ? $parts[0] : 'other';
        });

        return view('admin.roles.create', compact('groupedPermissions'));
    }

    /**
     * Enregistre un nouveau rôle.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => ['required', 'string', 'max:255', 'unique:roles,name'],
            'permissions'   => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $permissionNames = $data['permissions'] ?? [];

        $role = $this->permissionService->createRole($data['name'], $permissionNames);

        return redirect()->route('admin.roles.index')
            ->with('success', "Le rôle « {$role->name} » a été créé avec succès.");
    }

    /**
     * Affiche un rôle et ses permissions.
     */
    public function show(Role $role)
    {
        $rolePermissions = $role->permissions()->orderBy('name')->get();
        $groupedPermissions = $rolePermissions->groupBy(function ($permission) {
            $parts = explode('.', $permission->name);
            return count($parts) > 1 ? $parts[0] : 'other';
        });

        return view('admin.roles.show', compact('role', 'groupedPermissions'));
    }

    /**
     * Formulaire d'édition d'un rôle.
     */
    public function edit(Role $role)
    {
        if (in_array($role->name, ['Administrateur'])) {
            return redirect()->route('admin.roles.index')
                ->with('error', "Le rôle 'Administrateur' ne peut pas être modifié.");
        }

        $permissions = Permission::orderBy('name')->get();
        $rolePermissionNames = $role->permissions->pluck('name')->toArray();

        // Grouper toutes les permissions par catégorie (ressource)
        $groupedPermissions = $permissions->groupBy(function ($permission) {
            $parts = explode('.', $permission->name);
            return count($parts) > 1 ? $parts[0] : 'other';
        });

        return view('admin.roles.edit', compact('role', 'groupedPermissions', 'rolePermissionNames'));
    }

    /**
     * Met à jour le rôle et ses permissions.
     */
    public function update(Request $request, Role $role)
    {
        if (in_array($role->name, ['Administrateur'])) {
            return redirect()->route('admin.roles.index')
                ->with('error', "Le rôle 'Administrateur' ne peut pas être modifié.");
        }

        $data = $request->validate([
            'name'          => ['required', 'string', 'max:255', 'unique:roles,name,' . $role->id],
            'permissions'   => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $permissionNames = $data['permissions'] ?? [];

        $this->permissionService->updateRole($role, $data['name'], $permissionNames);

        return redirect()->route('admin.roles.index')
            ->with('success', "Le rôle « {$role->name} » a été mis à jour.");
    }

    /**
     * Supprime un rôle de la base de données.
     */
    public function destroy(Role $role)
    {
        try {
            $this->permissionService->deleteRole($role);

            return redirect()->route('admin.roles.index')
                ->with('success', "Le rôle « {$role->name} » a été supprimé.");
        } catch (\Exception $e) {
            return redirect()->route('admin.roles.index')
                ->with('error', $e->getMessage());
        }
    }
}
