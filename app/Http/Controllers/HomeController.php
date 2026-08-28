<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\Activity;
use App\Models\Group;
use App\Models\Gallery;
use App\Models\User;

class HomeController extends Controller
{
    public function index()
    {
        $settings = AppSetting::first() ?? new AppSetting();

        // Déterminer l'église active pour la page d'accueil
        $churchId = session('tenant_church_id')
            ?? (auth()->check() ? auth()->user()->church_id : null)
            ?? \App\Models\Church::first()?->id
            ?? 1;

        // Configurer le team_id de Spatie Permissions pour les requêtes de rôles
        if (function_exists('setPermissionsTeamId')) {
            setPermissionsTeamId($churchId);
        }

        // 1. Organigramme (Responsables / Bureau exécutif)
        $executiveRoles = [
            'Pasteur',
            'Super Admin',
            'Administrateur',
            'Président',
            'Vice-président',
            'Vice Président',
            'Secrétaire Général',
            'Trésorier Général',
            'Membre du bureau',
        ];

        $leaders = User::where('church_id', $churchId)
            ->whereHas('roles', function ($q) use ($executiveRoles, $churchId) {
                $q->whereIn('name', $executiveRoles)
                  ->where('roles.church_id', $churchId);
            })
            ->with(['roles' => function ($q) use ($churchId) {
                $q->where('roles.church_id', $churchId);
            }])
            ->get();

        // Trier par importance hiérarchique
        $rolePriority = [
            'Pasteur' => 1,
            'Super Admin' => 2,
            'Administrateur' => 3,
            'Président' => 4,
            'Vice-président' => 5,
            'Vice Président' => 5,
            'Secrétaire Général' => 6,
            'Trésorier Général' => 7,
            'Membre du bureau' => 8,
        ];

        $leaders = $leaders->sortBy(function ($user) use ($rolePriority) {
            $highestPriority = 99;
            foreach ($user->roles as $role) {
                $priority = $rolePriority[$role->name] ?? 99;
                if ($priority < $highestPriority) {
                    $highestPriority = $priority;
                }
            }
            return $highestPriority;
        });

        // 2. Les Groupes
        $groups = Group::where('church_id', $churchId)->with(['leader', 'collector'])->withCount('members')->get();

        // 3. Les Statistiques
        $stats = [
            'users' => User::where('church_id', $churchId)->count(),
            'groups' => $groups->count(),
            'events' => Activity::where('church_id', $churchId)->count(),
            'leaders' => $leaders->count(),
        ];

        // 4. Galerie
        $galleries = Gallery::where('church_id', $churchId)->where('is_active', true)->latest()->paginate(8, ['*'], 'gallery_page');

        // 5. Actualités (Prochaines activités)
        $activities = Activity::where('church_id', $churchId)
            ->where('start_time', '>=', now())
            ->whereIn('status', ['published', 'ongoing'])
            ->orderBy('start_time', 'asc')
            ->take(3)
            ->get();

        // 6. Numéro de téléphone de contact
        $admin = $leaders->first(function ($user) {
            return $user->roles->contains(fn($r) => in_array($r->name, ['Administrateur', 'Super Admin', 'Président']));
        });
        $adminPhone = $admin?->phone ?: ($settings->church_phone ?? '+229 00 00 00 00');

        return view('welcome', compact('settings', 'leaders', 'groups', 'stats', 'galleries', 'activities', 'adminPhone'));
    }
}
