<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\Activity;
use App\Models\Church;
use App\Models\Group;
use App\Models\Gallery;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Page d'accueil générale (redirige vers le dashboard si connecté ou affiche la dernière église visitée / église principale).
     */
    public function index(): View|RedirectResponse
    {
        // Si l'utilisateur est déjà connecté, redirection vers son espace de travail
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }

        // Récupérer la dernière église visitée par ce navigateur ou la première église active
        $churchId = session('last_visited_church_id')
            ?? session('tenant_church_id')
            ?? Church::where('status', 'active')->first()?->id
            ?? 1;

        $church = Church::find($churchId) ?? Church::first();

        if (!$church) {
            abort(404, "Aucune église n'est configurée sur la plateforme.");
        }

        return $this->renderLandingForChurch($church);
    }

    /**
     * Page d'accueil dédiée à une église spécifique via son slug : /c/{slug}
     */
    public function churchLanding(Church $church): View
    {
        // Mémoriser l'église visitée en session pour les prochains accès
        session(['last_visited_church_id' => $church->id]);

        return $this->renderLandingForChurch($church);
    }

    /**
     * Rendu de la page d'accueil personnalisée pour une église donnée.
     */
    protected function renderLandingForChurch(Church $church): View
    {
        $churchId = $church->id;

        // Configurer le team_id de Spatie Permissions pour les requêtes de rôles
        if (function_exists('setPermissionsTeamId')) {
            setPermissionsTeamId($churchId);
        }

        // 1. Paramètres de l'église (Textes, Logos, Bannière)
        $settings = AppSetting::where('church_id', $churchId)->first() ?? new AppSetting();

        // 2. Organigramme (Responsables / Bureau exécutif de cette église)
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

        // 3. Les Groupes de cette église
        $groups = Group::where('church_id', $churchId)->with(['leader', 'collector'])->withCount('members')->get();

        // 4. Les Statistiques
        $stats = [
            'users' => User::where('church_id', $churchId)->count(),
            'groups' => $groups->count(),
            'events' => Activity::where('church_id', $churchId)->count(),
            'leaders' => $leaders->count(),
        ];

        // 5. Galerie photos de cette église
        $galleries = Gallery::where('church_id', $churchId)->where('is_active', true)->latest()->paginate(8, ['*'], 'gallery_page');

        // 6. Prochains Événements de cette église
        $activities = Activity::where('church_id', $churchId)
            ->where('start_time', '>=', now())
            ->whereIn('status', ['published', 'ongoing'])
            ->orderBy('start_time', 'asc')
            ->take(3)
            ->get();

        // 7. Contact responsable
        $admin = $leaders->first(function ($user) {
            return $user->roles->contains(fn ($r) => in_array($r->name, ['Administrateur', 'Super Admin', 'Président']));
        });
        $adminPhone = $settings->contact_phone ?: ($admin?->phone ?: ($church->phone ?? '+229 00 00 00 00'));

        // 8. Liste de toutes les églises actives (pour le sélecteur / modal de changement d'église)
        $allChurches = Church::where('status', 'active')
            ->select(['id', 'name', 'slug', 'city', 'address', 'logo_path'])
            ->orderBy('name')
            ->get();

        $currentChurch = $church;

        return view('welcome', compact(
            'currentChurch',
            'allChurches',
            'settings',
            'leaders',
            'groups',
            'stats',
            'galleries',
            'activities',
            'adminPhone'
        ));
    }
}
