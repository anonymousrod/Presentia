<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PermissionService;
use Illuminate\Http\Request;
use App\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    protected PermissionService $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    /**
     * Retourne le church_id actif pour la session courante.
     */
    protected function getActiveChurchId(): ?int
    {
        return session('tenant_church_id') ?? auth()->user()?->church_id ?? null;
    }

    /**
     * Liste de tous les rôles de l'église active — STRICTEMENT filtrés.
     */
    public function index()
    {
        $this->authorize('role.manage');

        $churchId = $this->getActiveChurchId();

        // On récupère UNIQUEMENT les rôles appartenant à cette église
        // (church_id = $churchId), plus le rôle Super Admin global (church_id IS NULL)
        // uniquement si l'utilisateur est Super Admin et n'est PAS en mode support
        $roles = Role::where(function ($q) use ($churchId) {
            if ($churchId) {
                $q->where('church_id', $churchId);
            }
        })
            ->withCount('users', 'permissions')
            ->orderByRaw("CASE WHEN code = 'admin' THEN 1 ELSE 2 END")
            ->orderBy('is_system', 'desc')
            ->orderBy('name')
            ->get();

        // Si Super Admin hors mode support → montrer aussi son rôle global
        if (auth()->user()->isSuperAdmin() && !session()->has('tenant_church_id') && $churchId) {
            $superAdminRole = Role::whereNull('church_id')->where('name', 'Super Admin')
                ->withCount('users', 'permissions')
                ->first();
            if ($superAdminRole && !$roles->contains('id', $superAdminRole->id)) {
                $roles->prepend($superAdminRole);
            }
        }

        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Formulaire de création d'un rôle.
     */
    public function create()
    {
        $this->authorize('role.manage');

        $permissions = Permission::orderBy('name')->get();

        // Grouper les permissions par catégorie (ressource)
        $groupedPermissions = $permissions->groupBy(function ($permission) {
            $parts = explode('.', $permission->name);
            return count($parts) > 1 ? $parts[0] : 'other';
        });

        return view('admin.roles.create', compact('groupedPermissions'));
    }

    /**
     * Enregistre un nouveau rôle pour l'église active.
     */
    public function store(Request $request)
    {
        $this->authorize('role.manage');

        $churchId = $this->getActiveChurchId();

        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name')->where('church_id', $churchId)
            ],
            'description'   => ['nullable', 'string', 'max:255'],
            'permissions'   => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $permissionNames = $data['permissions'] ?? [];

        $role = $this->permissionService->createRole($data['name'], $data['description'] ?? null, $permissionNames, $churchId);

        return redirect()->route('admin.roles.index')
            ->with('success', "Le rôle « {$role->name} » a été créé avec succès pour votre église.");
    }

    /**
     * Affiche un rôle et ses permissions.
     */
    public function show(Role $role)
    {
        $this->authorize('role.manage');

        $churchId = $this->getActiveChurchId();
        if ($role->church_id && $role->church_id !== $churchId && !auth()->user()->isSuperAdmin()) {
            abort(403, 'Accès non autorisé à ce rôle.');
        }

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
        $this->authorize('role.manage');

        $churchId = $this->getActiveChurchId();
        if ($role->church_id && $role->church_id !== $churchId && !auth()->user()->isSuperAdmin()) {
            abort(403, 'Accès non autorisé à ce rôle.');
        }

        if ($role->code === 'admin' || $role->name === 'Super Admin') {
            return redirect()->route('admin.roles.index')
                ->with('error', "Ce rôle système ne peut pas être modifié.");
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
     * Met à jour le rôle et ses permissions pour l'église active.
     */
    public function update(Request $request, Role $role)
    {
        $this->authorize('role.manage');

        $churchId = $this->getActiveChurchId();
        if ($role->church_id && $role->church_id !== $churchId && !auth()->user()->isSuperAdmin()) {
            abort(403, 'Accès non autorisé à ce rôle.');
        }

        if ($role->code === 'admin' || $role->name === 'Super Admin') {
            return redirect()->route('admin.roles.index')
                ->with('error', "Ce rôle système ne peut pas être modifié.");
        }

        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name')
                    ->where('church_id', $role->church_id)
                    ->ignore($role->id)
            ],
            'description'   => ['nullable', 'string', 'max:255'],
            'permissions'   => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $permissionNames = $data['permissions'] ?? [];

        $this->permissionService->updateRole($role, $data['name'], $data['description'] ?? null, $permissionNames);

        return redirect()->route('admin.roles.index')
            ->with('success', "Les permissions du rôle « {$role->name} » ont été mises à jour pour votre église.");
    }

    /**
     * Supprime un rôle de l'église.
     */
    public function destroy(Role $role)
    {
        $this->authorize('role.manage');

        $churchId = $this->getActiveChurchId();
        if ($role->church_id && $role->church_id !== $churchId && !auth()->user()->isSuperAdmin()) {
            abort(403, 'Accès non autorisé à ce rôle.');
        }

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
