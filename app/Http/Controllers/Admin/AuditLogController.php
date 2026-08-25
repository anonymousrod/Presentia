<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('audit.view');

        $query = AuditLog::with(['user', 'auditable'])->orderBy('created_at', 'desc');

        if ($request->filled('action')) {
            $query->where('action', $request->input('action'));
        }

        if ($request->filled('user_id')) {
            $userIdInput = $request->input('user_id');
            $userId = is_numeric($userIdInput) ? (int)$userIdInput : decode_id($userIdInput);
            if ($userId) {
                $query->where('user_id', $userId);
            }
        }

        if ($request->filled('auditable_type')) {
            $query->where('auditable_type', $request->input('auditable_type'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', Carbon::parse($request->input('date_from'))->startOfDay());
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', Carbon::parse($request->input('date_to'))->endOfDay());
        }

        $logs = $query->paginate(20)->withQueryString();

        // Liste des utilisateurs pour le filtre
        $users = User::orderBy('first_name')->orderBy('name')->get();

        // Types d'entités auditées principales avec noms explicites
        $auditableTypes = [
            'App\Models\User' => 'Membres / Utilisateurs',
            'App\Models\Group' => 'Groupes',
            'App\Models\Contribution' => 'Cotisations',
            'App\Models\Remittance' => 'Versements Trésorerie',
            'App\Models\Activity' => 'Activités',
            'App\Models\ActivityType' => 'Types d\'activité',
            'App\Models\AppSetting' => 'Paramètres de l\'application',
            'App\Models\Gallery' => 'Galerie Médias',
            'App\Models\ScheduledNotification' => 'Notifications programmées',
        ];

        $userNames = User::all()->mapWithKeys(fn ($u) => [$u->id => trim($u->first_name . ' ' . $u->name)]);
        $groupNames = \App\Models\Group::all()->pluck('name', 'id');

        return view('admin.audit-logs.index', compact('logs', 'users', 'auditableTypes', 'userNames', 'groupNames'));
    }
}
