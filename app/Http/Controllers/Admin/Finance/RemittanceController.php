<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use App\Models\Contribution;
use App\Models\Remittance;
use App\Models\Group;
use Illuminate\Http\Request;

class RemittanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Remittance::with(['collector', 'group']);

        if ($request->filled('group_id')) {
            $query->where('group_id', $request->group_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $remittances = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        $groups = Group::orderBy('name')->get();

        // Sommes globales
        $totalCollected = Contribution::sum('amount');
        $totalValidated = Remittance::where('status', 'validated')->sum('amount');
        $totalPending = Remittance::where('status', 'pending')->sum('amount');

        return view('admin.finance.treasury.index', compact('remittances', 'totalCollected', 'totalValidated', 'totalPending', 'groups'));
    }

    public function store(Request $request)
    {
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

        $contributions = Contribution::whereIn('user_id', $memberIds)
            ->whereNull('remittance_id')
            ->get();

        if ($contributions->isEmpty()) {
            return redirect()->back()->with('error', 'Aucune cotisation en attente de versement.');
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

        return redirect()->back()->with('success', 'Versement de ' . $totalAmount . ' FCFA déclaré à la trésorerie. En attente de validation.');
    }

    public function validateRemittance(Request $request, Remittance $remittance)
    {
        $remittance->status = 'validated';
        $remittance->treasurer_id = auth()->id();
        $remittance->validated_at = now();
        $remittance->save();

        return redirect()->back()->with('success', 'Versement validé avec succès.');
    }
}
