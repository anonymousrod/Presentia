<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Church;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SaaSOverviewController extends Controller
{
    /**
     * Tableau de bord global SaaS pour le Super Administrateur.
     */
    public function index()
    {
        $totalChurches = Church::count();
        $activeChurches = Church::where('status', 'active')->where('subscription_expires_at', '>', Carbon::now())->count();
        $expiredChurches = Church::where('subscription_expires_at', '<=', Carbon::now())->orWhere('status', 'expired')->count();
        $suspendedChurches = Church::where('status', 'suspended')->count();

        // Églises dont l'abonnement expire sous 30 jours
        $expiringChurches = Church::where('status', 'active')
            ->whereBetween('subscription_expires_at', [Carbon::now(), Carbon::now()->addDays(30)])
            ->orderBy('subscription_expires_at', 'asc')
            ->get();

        // Revenu total des abonnements
        $totalRevenue = Subscription::sum('amount');
        $yearlyRevenue = Subscription::whereYear('created_at', Carbon::now()->year)->sum('amount');

        // Total utilisateurs sur toute la plateforme
        $totalUsers = User::withoutGlobalScopes()->count();

        // Dernières églises enregistrées
        $recentChurches = Church::withCount('users')->latest()->take(5)->get();

        // Derniers paiements d'abonnements
        $recentSubscriptions = Subscription::with('church')->latest()->take(6)->get();

        // Statistiques enrichies pour les jauges et analytics
        $activeRate = $totalChurches > 0 ? round(($activeChurches / $totalChurches) * 100) : 0;
        $expiredRate = $totalChurches > 0 ? round(($expiredChurches / $totalChurches) * 100) : 0;
        $avgMembersPerChurch = $totalChurches > 0 ? round($totalUsers / $totalChurches) : 0;

        return view('super-admin.dashboard', compact(
            'totalChurches',
            'activeChurches',
            'expiredChurches',
            'suspendedChurches',
            'expiringChurches',
            'totalRevenue',
            'yearlyRevenue',
            'totalUsers',
            'recentChurches',
            'recentSubscriptions',
            'activeRate',
            'expiredRate',
            'avgMembersPerChurch'
        ));
    }
}
