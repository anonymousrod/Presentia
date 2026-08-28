<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class NotificationController extends Controller
{
    public function showSendAllForm()
    {
        return view('admin.notifications.send-all');
    }

    public function sendAll(Request $request)
    {
        $data = $request->validate([
            'title'   => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $churchId = session('tenant_church_id') ?? auth()->user()?->church_id;
        $users = User::when($churchId, fn($q) => $q->where('church_id', $churchId))->get();
        $this->dispatchNotifications($users, $data['title'], $data['message']);

        return redirect()->route('admin.notifications.send-all')
            ->with('success', 'Notification globale envoyée avec succès à tous les membres.');
    }

    public function showSendGroupForm()
    {
        $churchId = session('tenant_church_id') ?? auth()->user()?->church_id;
        $groups = Group::when($churchId, fn($q) => $q->where('church_id', $churchId))->orderBy('name')->get();
        return view('admin.notifications.send-group', compact('groups'));
    }

    public function sendGroup(Request $request)
    {
        $request->merge([
            'group_id' => decode_id($request->input('group_id'))
        ]);

        $data = $request->validate([
            'title'   => 'required|string|max:255',
            'message' => 'required|string',
            'group_id' => 'required|exists:groups,id',
        ]);

        $group = Group::findOrFail($data['group_id']);
        // Récupérer uniquement les membres actuellement actifs dans le groupe
        $users = $group->members()->wherePivotNull('left_at')->get();

        $this->dispatchNotifications($users, $data['title'], $data['message']);

        return redirect()->route('admin.notifications.send-group')
            ->with('success', "Notification envoyée avec succès au groupe « {$group->name} ».");
    }

    public function showSendRoleForm()
    {
        $churchId = session('tenant_church_id') ?? auth()->user()?->church_id;
        $roles = Role::when($churchId, fn($q) => $q->where('church_id', $churchId))->orderBy('name')->get();
        return view('admin.notifications.send-role', compact('roles'));
    }

    public function sendRole(Request $request)
    {
        $request->merge([
            'role_id' => decode_id($request->input('role_id'))
        ]);

        $data = $request->validate([
            'title'   => 'required|string|max:255',
            'message' => 'required|string',
            'role_id'  => 'required|exists:roles,id',
        ]);

        $role = Role::findOrFail($data['role_id']);
        $churchId = session('tenant_church_id') ?? auth()->user()?->church_id;
        $users = User::role($role->name)->when($churchId, fn($q) => $q->where('church_id', $churchId))->get();

        $this->dispatchNotifications($users, $data['title'], $data['message']);

        return redirect()->route('admin.notifications.send-role')
            ->with('success', "Notification envoyée avec succès au rôle « {$role->name} ».");
    }

    public function showSendIndividualForm()
    {
        $users = User::orderBy('name')->get();
        return view('admin.notifications.send-individual', compact('users'));
    }

    public function sendIndividual(Request $request)
    {
        $request->merge([
            'user_id' => decode_id($request->input('user_id'))
        ]);

        $data = $request->validate([
            'title'   => 'required|string|max:255',
            'message' => 'required|string',
            'user_id'  => 'required|exists:users,id',
        ]);

        $user = User::findOrFail($data['user_id']);
        $user->notify(new \App\Notifications\CustomNotification($data['title'], $data['message']));

        return redirect()->route('admin.notifications.send-individual')
            ->with('success', "Notification individuelle envoyée avec succès à {$user->name}.");
    }

    private function dispatchNotifications($users, string $title, string $message): void
    {
        foreach ($users as $user) {
            $user->notify(new \App\Notifications\CustomNotification($title, $message));
        }
    }
}
