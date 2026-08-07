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

        // 1. Organigramme (Responsables)
        // Récupérer les rôles spécifiques. On utilisera Spatie permission.
        $leaders = User::whereHas('roles', function ($q) {
            $q->whereIn('name', [
                'Administrateur',
                'Président',
                'Vice Président',
                'Membre du bureau'
            ]);
        })->with('roles')->get();

        // Trier grossièrement par importance (Admin d'abord, etc.)
        $rolePriority = [
            'Administrateur' => 1,
            'Président' => 2,
            'Vice Président' => 3,
            'Membre du bureau' => 4,
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
        $groups = Group::with(['leader', 'collector'])->withCount('members')->get();

        // 3. Les Statistiques
        $stats = [
            'users' => User::count(),
            'groups' => Group::count(),
            'events' => Activity::count(),
            'leaders' => $leaders->count(),
        ];

        // 4. Galerie
        $galleries = Gallery::where('is_active', true)->latest()->paginate(8, ['*'], 'gallery_page');

        // 5. Actualités (Prochaines activités)
        $activities = Activity::where('start_time', '>=', now())
            ->whereIn('status', ['published', 'ongoing'])
            ->orderBy('start_time', 'asc')
            ->take(3)
            ->get();

        // 6. Admin phone number
        $admin = $leaders->first(function ($user) {
            return $user->roles->contains('name', 'Administrateur');
        });
        $adminPhone = $admin ? $admin->phone : '+228 00 00 00 00';

        return view('welcome', compact('settings', 'leaders', 'groups', 'stats', 'galleries', 'activities', 'adminPhone'));
    }
}
