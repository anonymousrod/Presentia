<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use App\Models\Contribution;
use App\Models\Remittance;
use App\Models\Group;
use App\Models\User;
use App\Notifications\Finance\RemittanceSubmittedNotification;
use App\Notifications\Finance\RemittanceValidatedNotification;
use Illuminate\Http\Request;

class RemittanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Remittance::with(['collector', 'group', 'treasurer']);

        $groupId = $request->filled('group_id') ? decode_id($request->group_id) : null;

        $contributionsQuery = Contribution::query();
        $remittancesQuery = Remittance::query();

        if ($groupId) {
            $query->where('group_id', $groupId);
            $remittancesQuery->where('group_id', $groupId);

            $group = Group::find($groupId);
            if ($group) {
                $memberIds = $group->members()->pluck('users.id')->toArray();
                $contributionsQuery->whereIn('user_id', $memberIds);
            }
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $remittances = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        $groups = Group::orderBy('name')->get();

        // Sommes financières
        $totalCollected  = (clone $contributionsQuery)->sum('amount');
        $totalValidated  = (clone $remittancesQuery)->where('status', 'validated')->sum('amount');
        $totalPending    = (clone $remittancesQuery)->where('status', 'pending')->sum('amount');
        $totalUnremitted = (clone $contributionsQuery)->whereNull('remittance_id')->sum('amount');

        return view('admin.finance.treasury.index', compact('remittances', 'totalCollected', 'totalValidated', 'totalPending', 'totalUnremitted', 'groups'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        if ($user->can('finance.view_all')) {
            $groupIdHash = $request->input('group_id');
            $groupId = $groupIdHash ? decode_id($groupIdHash) : null;
            $group = Group::find($groupId) ?? Group::first();
        } else {
            $group = $user->collectedGroups()->first() ?? $user->ledGroups()->first() ?? $user->groups()->first();
        }

        if (!$group) {
            abort(403);
        }

        $memberIds = $group->members()->pluck('users.id')->toArray();

        $query = Contribution::whereIn('user_id', $memberIds)
            ->whereNull('remittance_id');

        if ($request->filled('month')) {
            $year = $request->input('year', \Carbon\Carbon::now()->format('Y'));
            $month = str_pad($request->input('month'), 2, '0', STR_PAD_LEFT);
            $startOfMonth = \Carbon\Carbon::parse("$year-$month-01")->startOfMonth();
            $endOfMonth = \Carbon\Carbon::parse("$year-$month-01")->endOfMonth();

            $query->whereBetween('date', [$startOfMonth, $endOfMonth]);
        }

        $contributions = $query->get();

        if ($contributions->isEmpty()) {
            return redirect()->back()->with('error', 'Aucune cotisation en attente de validation.');
        }

        $totalAmount = $contributions->sum('amount');

        $remittance = Remittance::create([
            'group_id' => $group->id,
            'collector_id' => auth()->id(),
            'amount' => $totalAmount,
            'status' => 'pending'
        ]);

        foreach ($contributions as $contribution) {
            $contribution->remittance_id = $remittance->id;
            $contribution->save();
        }

        // Notifier les trésoriers généraux
        $collector = auth()->user();
        if (\Spatie\Permission\Models\Role::where('name', 'Trésorier Général')->exists()) {
            User::role('Trésorier Général')->each(fn ($treasurer) => $treasurer->notify(new RemittanceSubmittedNotification($collector, $group, (int) $totalAmount)));
        }

        return redirect()->back()->with('success', 'Versement de ' . $totalAmount . ' FCFA déclaré à la trésorerie. En attente de validation.');
    }

    public function validateRemittance(Request $request, Remittance $remittance)
    {
        $oldValues = $remittance->getAttributes();

        $remittance->status = 'validated';
        $remittance->treasurer_id = auth()->id();
        $remittance->validated_at = now();
        $remittance->save();

        \App\Services\AuditService::log('validated', $remittance, $oldValues, $remittance->getAttributes());

        // Notifier le collecteur
        if ($remittance->collector) {
            $remittance->collector->notify(new RemittanceValidatedNotification((int) $remittance->amount));
        }

        return redirect()->back()->with('success', 'Versement validé avec succès.');
    }
}
