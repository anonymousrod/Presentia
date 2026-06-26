<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use App\Models\Contribution;
use App\Models\Group;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ContributionController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // Si l'utilisateur peut tout voir (Trésorier/Admin), il peut choisir le groupe
        if ($user->can('finance.view_all')) {
            $groupId = $request->input('group_id');
            if ($groupId) {
                $group = Group::find($groupId);
            } else {
                $group = Group::first();
            }
            $allGroups = Group::all();
        } else {
            // Sinon, c'est un chargé de collecte normal
            $group = $user->collectedGroups()->first();
            $allGroups = collect($group ? [$group] : []); // Il ne voit que son groupe
        }

        if (!$group) {
            return redirect()->route('dashboard')->with('error', 'Vous n\'êtes responsable d\'aucun groupe pour la collecte.');
        }

        // Année et Mois
        $year = $request->input('year', Carbon::now()->format('Y'));
        $month = $request->input('month', Carbon::now()->format('m'));
        if ($month < 2) {
            $month = '02';
        }
        if ($month > 11) {
            $month = '11';
        }
        $month = str_pad($month, 2, '0', STR_PAD_LEFT);

        $startOfMonth = Carbon::parse("$year-$month-01")->startOfMonth();
        $endOfMonth = Carbon::parse("$year-$month-01")->endOfMonth();

        // Récupérer tous les dimanches de ce mois
        $sundays = [];
        $date = $startOfMonth->copy()->next(Carbon::SUNDAY);
        if ($startOfMonth->isSunday()) {
            $date = $startOfMonth->copy();
        }
        while ($date->lte($endOfMonth)) {
            $sundays[] = $date->copy();
            $date->addWeek();
        }

        // Récupérer les membres du groupe
        $members = $group->members()->get();

        // Récupérer les contributions de ce mois
        $contributions = Contribution::whereIn('user_id', $members->pluck('id'))
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->get()
            ->groupBy('user_id');

        // Calcul du total en attente de versement
        $pendingAmount = Contribution::whereIn('user_id', $members->pluck('id'))
            ->whereNull('remittance_id')
            ->sum('amount');

        // Calcul des totaux de l'année (Février à Novembre)
        $startOfYear = Carbon::parse("$year-02-01")->startOfDay();
        $endOfYear = Carbon::parse("$year-11-30")->endOfDay();

        $totalSundaysInYear = 0;
        $dateIt = $startOfYear->copy()->next(Carbon::SUNDAY);
        if ($startOfYear->isSunday()) {
            $dateIt = $startOfYear->copy();
        }
        while ($dateIt->lte($endOfYear)) {
            $totalSundaysInYear++;
            $dateIt->addWeek();
        }

        $yearlyContributions = Contribution::whereIn('user_id', $members->pluck('id'))
            ->whereBetween('date', [$startOfYear, $endOfYear])
            ->selectRaw('user_id, SUM(amount) as total_paid')
            ->groupBy('user_id')
            ->pluck('total_paid', 'user_id');

        return view('admin.finance.contributions.index', compact(
            'group',
            'allGroups',
            'members',
            'sundays',
            'contributions',
            'year',
            'month',
            'pendingAmount',
            'totalSundaysInYear',
            'yearlyContributions'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'contributions' => 'array',
            'contributions.*.*' => 'nullable|numeric|min:0'
        ]);

        $user = auth()->user();
        if ($user->can('finance.view_all')) {
            $groupId = $request->input('group_id');
            $group = Group::find($groupId) ?? Group::first();
        } else {
            $group = $user->collectedGroups()->first();
        }

        if (!$group) {
            abort(403);
        }

        $memberIds = $group->members()->pluck('users.id')->toArray();
        $inputs = $request->input('contributions', []);

        foreach ($inputs as $userId => $dates) {
            if (!in_array($userId, $memberIds)) {
                continue;
            }

            foreach ($dates as $date => $amount) {
                // Si montant vide, on supprime la contribution existante non versée
                if (is_null($amount) || $amount === '') {
                    Contribution::where('user_id', $userId)
                        ->where('date', $date)
                        ->whereNull('remittance_id') // On ne peut pas supprimer une cotis déjà versée
                        ->delete();
                    continue;
                }

                $contribution = Contribution::firstOrNew([
                    'user_id' => $userId,
                    'date' => $date
                ]);

                // On ne modifie pas si c'est déjà dans un versement
                if ($contribution->remittance_id) {
                    continue;
                }

                $contribution->collected_by = auth()->id();
                $contribution->amount = $amount;
                $contribution->save();
            }
        }

        return redirect()->back()->with('success', 'Cotisations enregistrées avec succès.');
    }
}
