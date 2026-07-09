<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->hasRole('Administrateur') || $user->hasRole('Membre du bureau')) {
            return $this->adminDashboard();
        } elseif ($user->hasRole('Trésorier Général')) {
            return $this->treasurerDashboard();
        } elseif ($user->hasRole('Chef de groupe')) {
            return $this->leaderDashboard();
        } else {
            return $this->userDashboard($user);
        }
    }

    private function adminDashboard()
    {
        $stats = [
            'total_users' => \App\Models\User::count(),
            'total_activities' => \App\Models\Activity::count(),
            'upcoming_activities' => \App\Models\Activity::where('start_time', '>=', now())->count(),
            'total_groups' => \App\Models\Group::count(),
        ];

        $recent_activities = \App\Models\Activity::latest()->take(5)->get();

        return view('dashboard.admin', compact('stats', 'recent_activities'));
    }

    private function treasurerDashboard()
    {
        $stats = [
            'pending_remittances_count' => \App\Models\Remittance::where('status', 'pending')->count(),
            'pending_remittances_amount' => \App\Models\Remittance::where('status', 'pending')->sum('amount'),
            'validated_remittances_amount' => \App\Models\Remittance::where('status', 'validated')->sum('amount'),
            'total_contributions' => \App\Models\Contribution::count(),
        ];

        $pending_remittances = \App\Models\Remittance::with(['group', 'collector'])
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.treasurer', compact('stats', 'pending_remittances'));
    }

    private function leaderDashboard()
    {
        $user = auth()->user();
        // A leader usually has a group where they are the group_leader (or similar).
        // Let's assume the user has a group.
        $group = \App\Models\Group::where('leader_id', $user->id)
            ->orWhere('collector_id', $user->id)
            ->first();

        // If no group found as leader, fallback to their assigned group
        if (!$group) {
            $group = $user->groups()->first();
        }

        $stats = [
            'group_members_count' => $group ? $group->members()->count() : 0,
            'group_upcoming_activities' => \App\Models\Activity::where('start_time', '>=', now())->count(), // Adjust if activities are group-specific
            'total_group_contributions' => $group ? \App\Models\Contribution::whereHas('user', function ($q) use ($group) {
                $q->whereHas('groups', function ($q2) use ($group) {
                    $q2->where('groups.id', $group->id);
                });
            })->sum('amount') : 0,
        ];

        $recent_activities = \App\Models\Activity::latest()->take(5)->get();

        return view('dashboard.leader', compact('stats', 'recent_activities', 'group'));
    }

    private function userDashboard($user)
    {
        $stats = [
            'upcoming_activities' => \App\Models\Activity::where('start_time', '>=', now())->count(),
            'attended_activities' => \App\Models\Attendance::where('user_id', $user->id)->where('status', 'present')->count(),
            'my_contributions_amount' => \App\Models\Contribution::where('user_id', $user->id)->sum('amount'),
        ];

        $upcoming_activities = \App\Models\Activity::where('start_time', '>=', now())
            ->orderBy('start_time', 'asc')
            ->take(3)
            ->get();

        return view('dashboard.user', compact('stats', 'upcoming_activities', 'user'));
    }
}
