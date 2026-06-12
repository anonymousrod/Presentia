<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => \App\Models\User::count(),
            'total_activities' => \App\Models\Activity::count(),
            'upcoming_activities' => \App\Models\Activity::where('start_time', '>=', now())->count(),
            'total_groups' => \App\Models\Group::count(),
        ];
        
        $recent_activities = \App\Models\Activity::latest()->take(5)->get();

        return view('dashboard.index', compact('stats', 'recent_activities'));
    }
}
