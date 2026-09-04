<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Attendance;
use App\Models\Contribution;
use App\Models\Group;
use App\Models\Remittance;
use App\Models\User;

class DashboardController extends Controller
{
    /**
     * Retourne le church_id actif (support mode ou utilisateur normal).
     */
    private function getActiveChurchId(): ?int
    {
        return session('tenant_church_id') ?? auth()->user()?->church_id ?? null;
    }

    public function index()
    {
        $user = auth()->user();

        // Le Super Administrateur (y compris en mode support) a toujours accès au tableau de bord administrateur
        if ($user->isSuperAdmin()) {
            return $this->adminDashboard();
        }

        // Récupérer les codes de tous les rôles de l'utilisateur
        $userRoleCodes = $user->roles->pluck('code')->toArray();

        if (in_array('admin', $userRoleCodes) || in_array('bureau_member', $userRoleCodes)) {
            return $this->adminDashboard();
        } elseif (in_array('treasurer', $userRoleCodes)) {
            return $this->treasurerDashboard();
        } elseif (in_array('group_leader', $userRoleCodes)) {
            return $this->leaderDashboard();
        } else {
            return $this->userDashboard($user);
        }
    }

    private function adminDashboard()
    {
        $churchId = $this->getActiveChurchId();
        $isSupportMode = session()->has('tenant_church_id') && auth()->check() && auth()->user()->isSuperAdmin();
        $supportChurch = $isSupportMode ? \App\Models\Church::find($churchId) : null;

        $displayAdmin = auth()->user();
        if ($isSupportMode && $supportChurch) {
            setPermissionsTeamId($supportChurch->id);
            $localAdmin = User::withoutGlobalScopes()
                ->where('church_id', $supportChurch->id)
                ->whereHas('roles', fn ($q) => $q->where('name', 'Administrateur'))
                ->first() ?? User::withoutGlobalScopes()->where('church_id', $supportChurch->id)->first();

            if ($localAdmin) {
                $displayAdmin = $localAdmin;
            }
        }

        $stats = [
            'total_users'          => User::when($churchId, fn ($q) => $q->where('church_id', $churchId))->count(),
            'total_activities'     => Activity::count(),
            'upcoming_activities'  => Activity::where('start_time', '>=', now())->count(),
            'total_groups'         => Group::count(),
        ];

        $recent_activities = Activity::latest()->take(5)->get();

        return view('dashboard.admin', compact('stats', 'recent_activities', 'displayAdmin', 'supportChurch', 'isSupportMode'));
    }

    private function treasurerDashboard()
    {
        $stats = [
            'pending_remittances_count'    => Remittance::where('status', 'pending')->count(),
            'pending_remittances_amount'   => Remittance::where('status', 'pending')->sum('amount'),
            'validated_remittances_amount' => Remittance::where('status', 'validated')->sum('amount'),
            'total_contributions'          => Contribution::count(),
        ];

        $pending_remittances = Remittance::with(['group', 'collector'])
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.treasurer', compact('stats', 'pending_remittances'));
    }

    private function leaderDashboard()
    {
        $user = auth()->user();
        $group = Group::where('leader_id', $user->id)
            ->orWhere('collector_id', $user->id)
            ->first();

        if (!$group) {
            $group = $user->groups()->first();
        }

        $stats = [
            'group_members_count'         => $group ? $group->members()->count() : 0,
            'group_upcoming_activities'   => Activity::where('start_time', '>=', now())->count(),
            'total_group_contributions'   => $group ? Contribution::whereHas('user', function ($q) use ($group) {
                $q->whereHas('groups', function ($q2) use ($group) {
                    $q2->where('groups.id', $group->id);
                });
            })->sum('amount') : 0,
        ];

        $recent_activities = Activity::latest()->take(5)->get();

        return view('dashboard.leader', compact('stats', 'recent_activities', 'group'));
    }

    private function userDashboard($user)
    {
        $stats = [
            'upcoming_activities'    => Activity::where('start_time', '>=', now())->count(),
            'attended_activities'    => Attendance::where('user_id', $user->id)->where('status', 'present')->count(),
            'my_contributions_amount' => Contribution::where('user_id', $user->id)->sum('amount'),
        ];

        $upcoming_activities = Activity::where('start_time', '>=', now())
            ->orderBy('start_time', 'asc')
            ->take(3)
            ->get();

        return view('dashboard.user', compact('stats', 'upcoming_activities', 'user'));
    }
}
