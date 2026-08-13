<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Activity;
use App\Models\User;
use App\Models\Group;

class GlobalSearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('q');

        if (!$query || strlen($query) < 2) {
            return response()->json([]);
        }

        $results = [];

        // Recherche des Utilisateurs (Membres)
        if (auth()->user()->can('manage-users')) {
            $users = User::where('name', 'LIKE', "%{$query}%")
                ->orWhere('first_name', 'LIKE', "%{$query}%")
                ->orWhere('email', 'LIKE', "%{$query}%")
                ->limit(5)
                ->get()
                ->map(function ($user) {
                    return [
                        'type' => 'Membre',
                        'title' => $user->first_name . ' ' . $user->name,
                        'subtitle' => $user->email,
                        'url' => route('admin.users.show', $user),
                        'icon' => 'ri-user-line',
                    ];
                });
            $results = array_merge($results, $users->toArray());
        }

        // Recherche des Groupes
        if (auth()->user()->can('access-group-management')) {
            $groups = Group::where('name', 'LIKE', "%{$query}%")
                ->orWhere('category', 'LIKE', "%{$query}%")
                ->limit(5)
                ->get()
                ->map(function ($group) {
                    return [
                        'type' => 'Groupe',
                        'title' => $group->name,
                        'subtitle' => $group->category ?? 'Sans catégorie',
                        'url' => route('admin.groups.show', $group),
                        'icon' => 'ri-group-line',
                    ];
                });
            $results = array_merge($results, $groups->toArray());
        }

        // Recherche des Activités
        if (auth()->user()->can('access-activities')) {
            $activities = Activity::where('title', 'LIKE', "%{$query}%")
                ->limit(5)
                ->get()
                ->map(function ($activity) {
                    return [
                        'type' => 'Activité',
                        'title' => $activity->title,
                        'subtitle' => optional($activity->start_time)->format('d/m/Y H:i') ?? 'Non planifiée',
                        'url' => route('admin.activities.show', $activity),
                        'icon' => 'ri-calendar-event-line',
                    ];
                });
            $results = array_merge($results, $activities->toArray());
        }

        return response()->json($results);
    }
}
