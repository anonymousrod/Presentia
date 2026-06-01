<?php

namespace App\Http\Controllers\Admin;

use App\Events\GroupLeaderAssigned;
use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function index(Request $request)
    {
        $query = Group::withCount('members')->with('leader');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        $groups = $query->orderBy('name')->paginate(15)->withQueryString();

        return view('admin.groups.index', compact('groups'));
    }

    public function create()
    {
        $users = User::orderBy('name')->get();

        return view('admin.groups.create', compact('users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category'    => ['nullable', 'string', 'max:255'],
            'leader_id'   => ['nullable', 'exists:users,id'],
        ]);

        $group = Group::create($data);

        // Dispatch event si un chef est défini dès la création
        if ($group->leader_id) {
            GroupLeaderAssigned::dispatch($group, $group->leader);
        }

        return redirect()->route('admin.groups.show', $group)
            ->with('success', "Le groupe « {$group->name} » a été créé avec succès.");
    }

    public function show(Group $group)
    {
        // Membres actuellement dans le groupe (left_at IS NULL)
        $activeMembers = $group->members()
            ->wherePivotNull('left_at')
            ->orderBy('name')
            ->get();

        // Historique complet (incluant les anciens membres)
        $allMembers = $group->members()
            ->wherePivotNotNull('left_at')
            ->orderByPivot('left_at', 'desc')
            ->get();

        // Utilisateurs pouvant être ajoutés (non encore membres actifs)
        $activeMemberIds = $activeMembers->pluck('id');
        $availableUsers = User::whereNotIn('id', $activeMemberIds)
            ->orderBy('name')
            ->get();

        return view('admin.groups.show', compact('group', 'activeMembers', 'allMembers', 'availableUsers'));
    }

    public function edit(Group $group)
    {
        $users = User::orderBy('name')->get();

        return view('admin.groups.edit', compact('group', 'users'));
    }

    public function update(Request $request, Group $group)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category'    => ['nullable', 'string', 'max:255'],
            'leader_id'   => ['nullable', 'exists:users,id'],
        ]);

        $previousLeaderId = $group->leader_id;
        $group->update($data);

        // Dispatcher l'événement seulement si le chef a changé
        if ($data['leader_id'] && $data['leader_id'] != $previousLeaderId) {
            $group->refresh();
            GroupLeaderAssigned::dispatch($group, $group->leader);
        }

        return redirect()->route('admin.groups.show', $group)
            ->with('success', "Le groupe « {$group->name} » a été mis à jour.");
    }

    public function destroy(Group $group)
    {
        $group->delete(); // Soft delete — historique pivot conservé

        return redirect()->route('admin.groups.index')
            ->with('success', "Le groupe « {$group->name} » a été archivé.");
    }

    /**
     * Affecter un membre au groupe (pivot avec joined_at).
     */
    public function assignMember(Request $request, Group $group)
    {
        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);

        $userId = $request->user_id;

        // Vérifier si déjà membre actif (left_at IS NULL)
        $existing = $group->members()
            ->wherePivot('user_id', $userId)
            ->wherePivotNull('left_at')
            ->first();

        if ($existing) {
            return back()->with('error', 'Ce membre est déjà dans le groupe.');
        }

        // Attacher avec joined_at = maintenant
        $group->members()->attach($userId, [
            'joined_at' => Carbon::now(),
            'left_at'   => null,
        ]);

        return back()->with('success', 'Membre ajouté au groupe avec succès.');
    }

    /**
     * Retirer un membre du groupe : update left_at = now(), ne jamais supprimer la ligne pivot.
     */
    public function removeMember(Request $request, Group $group, User $user)
    {
        // Trouver la ligne pivot active (left_at IS NULL)
        $pivot = $group->members()
            ->wherePivot('user_id', $user->id)
            ->wherePivotNull('left_at')
            ->first();

        if (! $pivot) {
            return back()->with('error', 'Ce membre n\'est pas actif dans ce groupe.');
        }

        // Update left_at sur la ligne pivot — jamais de delete
        $group->members()->updateExistingPivot($user->id, [
            'left_at' => Carbon::now(),
        ]);

        return back()->with('success', "{$user->first_name} {$user->name} a été retiré du groupe.");
    }
}
