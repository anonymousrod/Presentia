@extends('layouts.app')

@section('title', 'Journal d\'activités & Audit')

@section('content')
@php
    // Dictionnaire de traduction des types d'entités en français lisible
    $typeLabels = [
        'App\Models\User' => 'Membre / Utilisateur',
        'App\Models\Group' => 'Groupe',
        'App\Models\Contribution' => 'Cotisation',
        'App\Models\Remittance' => 'Versement Trésorerie',
        'App\Models\Activity' => 'Activité',
        'App\Models\ActivityType' => 'Type d\'activité',
        'App\Models\AppSetting' => 'Paramètres de l\'application',
        'App\Models\Gallery' => 'Galerie Médias',
        'App\Models\ScheduledNotification' => 'Notification programmée',
        'App\Models\Role' => 'Rôle & Permissions',
    ];

    // Dictionnaire de traduction des champs de base de données en français
    $fieldLabels = [
        'first_name' => 'Prénom',
        'name' => 'Nom',
        'email' => 'Adresse e-mail',
        'phone' => 'Numéro de téléphone',
        'status' => 'Statut',
        'weekly_contribution' => 'Cotisation hebdomadaire',
        'amount' => 'Montant',
        'date' => 'Date',
        'description' => 'Description',
        'group_id' => 'Nom du groupe',
        'leader_id' => 'Chef de groupe',
        'collector_id' => 'Chargé de collecte',
        'treasurer_id' => 'Trésorier ayant validé',
        'user_id' => 'Membre concerné',
        'collected_by' => 'Collecté par',
        'category' => 'Catégorie',
        'color' => 'Couleur',
        'title' => 'Titre',
        'joined_at' => 'Date d\'adhésion',
        'left_at' => 'Date de départ',
        'validated_at' => 'Date de validation',
        'created_at' => 'Date de création',
        'updated_at' => 'Date de modification',
    ];
@endphp

<div class="container-fluid max-w-1200 py-3 py-md-4">
    {{-- =================== EN-TÊTE =================== --}}
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-4 gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-muted">Tableau de bord</a></li>
                    <li class="breadcrumb-item active fw-medium" aria-current="page">Journal d'audit</li>
                </ol>
            </nav>
            <h3 class="fw-bold mb-0 fs-20 fs-md-24"><i class="mdi mdi-shield-check-outline text-primary me-2"></i>Journal d'activités & Sécurité</h3>
            <p class="text-muted mb-0 fs-13 mt-1">Consultez l'historique clair et traçable de toutes les actions effectuées dans l'application.</p>
        </div>
    </div>

    {{-- =================== FILTRES DE RECHERCHE CONVIVIAUX =================== --}}
    <div class="card border-0 shadow-sm mb-4 rounded-3 overflow-hidden">
        <div class="card-header border-0 py-3 px-4 d-md-none" data-bs-toggle="collapse" data-bs-target="#auditFilterCollapse" style="cursor: pointer;">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded p-1 bg-body-tertiary shadow-sm d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                        <i class="mdi mdi-filter-variant text-primary fs-18"></i>
                    </div>
                    <h6 class="mb-0 fw-semibold text-body">Filtres de recherche</h6>
                </div>
                <i class="mdi mdi-chevron-down fs-20 text-muted"></i>
            </div>
        </div>

        <div id="auditFilterCollapse" class="collapse d-md-block">
            <div class="card-body p-3 p-md-4 border-top border-light-subtle">
                <form action="{{ route('admin.audit-logs.index') }}" method="GET" class="row g-3">
                    {{-- Action --}}
                    <div class="col-md-3 col-sm-6">
                        <label class="form-label fs-12 fw-semibold text-muted text-uppercase tracking-wider mb-1"><i class="mdi mdi-gesture-tap me-1"></i>Type d'action</label>
                        <select name="action" class="form-select border-light-subtle">
                            <option value="">Toutes les actions</option>
                            <option value="created" {{ request('action') === 'created' ? 'selected' : '' }}>Création (Ajout)</option>
                            <option value="updated" {{ request('action') === 'updated' ? 'selected' : '' }}>Modification</option>
                            <option value="validated" {{ request('action') === 'validated' ? 'selected' : '' }}>Validation de versement</option>
                            <option value="deleted" {{ request('action') === 'deleted' ? 'selected' : '' }}>Suppression</option>
                            <option value="login" {{ request('action') === 'login' ? 'selected' : '' }}>Connexion</option>
                            <option value="logout" {{ request('action') === 'logout' ? 'selected' : '' }}>Déconnexion</option>
                            <option value="scan_qr" {{ request('action') === 'scan_qr' ? 'selected' : '' }}>Scan QR Code</option>
                        </select>
                    </div>

                    {{-- Élément concerné --}}
                    <div class="col-md-3 col-sm-6">
                        <label class="form-label fs-12 fw-semibold text-muted text-uppercase tracking-wider mb-1"><i class="mdi mdi-cube-outline me-1"></i>Élément concerné</label>
                        <select name="auditable_type" class="form-select border-light-subtle">
                            <option value="">Tous les éléments</option>
                            @foreach($auditableTypes as $class => $label)
                                <option value="{{ $class }}" {{ request('auditable_type') === $class ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Effectué par (Utilisateur) --}}
                    <div class="col-md-3 col-sm-6">
                        <label class="form-label fs-12 fw-semibold text-muted text-uppercase tracking-wider mb-1"><i class="mdi mdi-account-outline me-1"></i>Effectué par</label>
                        <select name="user_id" class="form-select border-light-subtle">
                            <option value="">Tous les utilisateurs</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->first_name }} {{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Période (Date Début & Fin) --}}
                    <div class="col-md-3 col-sm-6">
                        <label class="form-label fs-12 fw-semibold text-muted text-uppercase tracking-wider mb-1"><i class="mdi mdi-calendar-range me-1"></i>Période</label>
                        <div class="input-group">
                            <input type="date" name="date_from" class="form-control border-light-subtle fs-12" value="{{ request('date_from') }}" placeholder="Du">
                            <input type="date" name="date_to" class="form-control border-light-subtle fs-12" value="{{ request('date_to') }}" placeholder="Au">
                        </div>
                    </div>

                    {{-- Boutons d'action --}}
                    <div class="col-12 d-flex justify-content-end gap-2 mt-3">
                        <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-light rounded-pill px-3 fs-13">
                            <i class="mdi mdi-refresh me-1"></i> Réinitialiser
                        </a>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fs-13 shadow-sm">
                            <i class="mdi mdi-magnify me-1"></i> Filtrer les résultats
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- =================== HISTORIQUE DES LOGS =================== --}}
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="card-header border-0 py-3 px-4" style="border-bottom: 1px solid rgba(var(--vz-dark-rgb), 0.05) !important;">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <div class="avatar-xs bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center">
                        <i class="mdi mdi-history fs-16"></i>
                    </div>
                    <h5 class="card-title mb-0 fw-bold fs-15 text-body">Historique détaillé des événements</h5>
                </div>
                <span class="badge bg-body-tertiary text-muted border px-3 py-2 rounded-pill fs-12">Total : {{ $logs->total() }} action(s)</span>
            </div>
        </div>

        {{-- Table Desktop --}}
        <div class="d-none d-md-block card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle table-hover table-nowrap mb-0">
                    <thead class="bg-body-tertiary">
                        <tr>
                            <th class="ps-4 fw-semibold text-muted fs-12 text-uppercase tracking-wider py-3">Date & Heure</th>
                            <th class="fw-semibold text-muted fs-12 text-uppercase tracking-wider py-3">Effectué par</th>
                            <th class="fw-semibold text-muted fs-12 text-uppercase tracking-wider py-3">Action</th>
                            <th class="fw-semibold text-muted fs-12 text-uppercase tracking-wider py-3">Cible / Élément concerné</th>
                            <th class="fw-semibold text-muted fs-12 text-uppercase tracking-wider py-3">Appareil & IP</th>
                            <th class="fw-semibold text-muted fs-12 text-uppercase tracking-wider py-3 text-end pe-4">Détails</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr class="border-bottom border-light-subtle">
                                {{-- Date --}}
                                <td class="ps-4">
                                    <div class="d-flex flex-column">
                                        <span class="fw-medium text-body">{{ $log->created_at ? $log->created_at->format('d/m/Y') : '' }}</span>
                                        <span class="fs-11 text-muted">{{ $log->created_at ? $log->created_at->format('H:i:s') : '' }}</span>
                                    </div>
                                </td>

                                {{-- Utilisateur --}}
                                <td>
                                    @if($log->user)
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar-xxs bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold fs-11">
                                                {{ strtoupper(substr($log->user->first_name ?? 'U', 0, 1)) }}
                                            </div>
                                            <div class="d-flex flex-column">
                                                <span class="fw-semibold text-body fs-13">{{ $log->user->first_name }} {{ $log->user->name }}</span>
                                                <span class="fs-11 text-muted">{{ $log->user->email }}</span>
                                            </div>
                                        </div>
                                    @else
                                        <span class="badge bg-body-tertiary text-muted border px-2 py-1"><i class="mdi mdi-cog-outline me-1"></i>Système automatique</span>
                                    @endif
                                </td>

                                {{-- Action --}}
                                <td>
                                    @php
                                        $actionConfig = match($log->action) {
                                            'created' => ['bg' => 'success-subtle', 'text' => 'success', 'icon' => 'mdi-plus-circle-outline', 'label' => 'Création'],
                                            'updated' => ['bg' => 'warning-subtle', 'text' => 'warning', 'icon' => 'mdi-pencil-outline', 'label' => 'Modification'],
                                            'validated' => ['bg' => 'success-subtle', 'text' => 'success', 'icon' => 'mdi-check-decagram', 'label' => 'Validation Versement'],
                                            'deleted' => ['bg' => 'danger-subtle', 'text' => 'danger', 'icon' => 'mdi-trash-can-outline', 'label' => 'Suppression'],
                                            'login' => ['bg' => 'info-subtle', 'text' => 'info', 'icon' => 'mdi-login', 'label' => 'Connexion'],
                                            'logout' => ['bg' => 'secondary-subtle', 'text' => 'secondary', 'icon' => 'mdi-logout', 'label' => 'Déconnexion'],
                                            'scan_qr' => ['bg' => 'purple-subtle', 'text' => 'purple', 'icon' => 'mdi-qrcode-scan', 'label' => 'Scan QR Code'],
                                            default => ['bg' => 'primary-subtle', 'text' => 'primary', 'icon' => 'mdi-information-outline', 'label' => ucfirst($log->action)],
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $actionConfig['bg'] }} text-{{ $actionConfig['text'] }} border border-{{ $actionConfig['text'] }}-subtle px-2 py-1 fs-12">
                                        <i class="mdi {{ $actionConfig['icon'] }} me-1"></i>{{ $actionConfig['label'] }}
                                    </span>
                                </td>

                                {{-- Cible --}}
                                <td>
                                    @php
                                        $typeLabel = $typeLabels[$log->auditable_type] ?? class_basename($log->auditable_type);
                                        
                                        // Nom explicite de l'objet si disponible
                                        $targetName = null;
                                        if ($log->auditable) {
                                            if ($log->auditable instanceof \App\Models\User) {
                                                $targetName = $log->auditable->first_name . ' ' . $log->auditable->name;
                                            } elseif ($log->auditable instanceof \App\Models\Group) {
                                                $targetName = $log->auditable->name;
                                            } elseif ($log->auditable instanceof \App\Models\Contribution && $log->auditable->user) {
                                                $targetName = 'Cotisation de ' . $log->auditable->user->first_name . ' ' . $log->auditable->user->name;
                                            } elseif ($log->auditable instanceof \App\Models\Remittance && $log->auditable->group) {
                                                $targetName = 'Versement : ' . $log->auditable->group->name;
                                            } elseif (isset($log->auditable->name)) {
                                                $targetName = $log->auditable->name;
                                            } elseif (isset($log->auditable->title)) {
                                                $targetName = $log->auditable->title;
                                            }
                                        }
                                    @endphp
                                    <div class="d-flex flex-column">
                                        <span class="fw-semibold text-body fs-13">{{ $typeLabel }}</span>
                                        @if($targetName)
                                            <span class="fs-11 text-muted">{{ $targetName }}</span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Appareil & IP --}}
                                <td>
                                    @php
                                        $agent = $log->user_agent ?? '';
                                        $isMobile = str_contains($agent, 'Mobile') || str_contains($agent, 'Android') || str_contains($agent, 'iPhone');
                                    @endphp
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="mdi {{ $isMobile ? 'mdi-cellphone text-warning' : 'mdi-laptop text-primary' }} fs-18"></i>
                                        <div class="d-flex flex-column">
                                            <span class="fs-12 fw-medium text-body">{{ $isMobile ? 'Mobile' : 'Ordinateur' }}</span>
                                            <span class="fs-11 text-muted">{{ $log->ip_address ?? '127.0.0.1' }}</span>
                                        </div>
                                    </div>
                                </td>

                                {{-- Actions / Détails --}}
                                <td class="text-end pe-4">
                                    @if($log->old_values || $log->new_values)
                                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#auditModal{{ $log->id }}">
                                            <i class="mdi mdi-eye-outline me-1"></i> Consulter
                                        </button>
                                    @else
                                        <span class="fs-12 text-muted fst-italic">Aucune donnée</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <i class="mdi mdi-shield-search-outline text-muted fs-48 mb-2 opacity-50"></i>
                                        <h6 class="fw-bold text-muted mb-1">Aucun événement enregistré</h6>
                                        <p class="text-muted fs-13 mb-0">Aucun journal d'audit ne correspond à vos critères de recherche.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Vue Mobile --}}
        <div class="d-md-none card-body p-0 bg-body-tertiary">
            @forelse($logs as $log)
                @php
                    $actionConfig = match($log->action) {
                        'created' => ['bg' => 'success-subtle', 'text' => 'success', 'icon' => 'mdi-plus-circle-outline', 'label' => 'Création'],
                        'updated' => ['bg' => 'warning-subtle', 'text' => 'warning', 'icon' => 'mdi-pencil-outline', 'label' => 'Modification'],
                        'validated' => ['bg' => 'success-subtle', 'text' => 'success', 'icon' => 'mdi-check-decagram', 'label' => 'Validation Versement'],
                        'deleted' => ['bg' => 'danger-subtle', 'text' => 'danger', 'icon' => 'mdi-trash-can-outline', 'label' => 'Suppression'],
                        'login' => ['bg' => 'info-subtle', 'text' => 'info', 'icon' => 'mdi-login', 'label' => 'Connexion'],
                        'logout' => ['bg' => 'secondary-subtle', 'text' => 'secondary', 'icon' => 'mdi-logout', 'label' => 'Déconnexion'],
                        default => ['bg' => 'primary-subtle', 'text' => 'primary', 'icon' => 'mdi-information-outline', 'label' => ucfirst($log->action)],
                    };
                    $typeLabel = $typeLabels[$log->auditable_type] ?? class_basename($log->auditable_type);
                @endphp
                <div class="card border-0 mb-2 mx-2 mt-2 shadow-sm rounded-3">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <span class="badge bg-{{ $actionConfig['bg'] }} text-{{ $actionConfig['text'] }} px-2 py-1 fs-11 me-1">
                                    <i class="mdi {{ $actionConfig['icon'] }} me-1"></i>{{ $actionConfig['label'] }}
                                </span>
                                <span class="fw-bold fs-13 text-body">{{ $typeLabel }}</span>
                            </div>
                            <span class="fs-11 text-muted">{{ $log->created_at ? $log->created_at->format('d/m H:i') : '' }}</span>
                        </div>

                        <p class="fs-12 text-muted mb-2">
                            <i class="mdi mdi-account text-muted me-1"></i>Par : 
                            <strong>{{ $log->user ? $log->user->first_name . ' ' . $log->user->name : 'Système' }}</strong>
                        </p>

                        @if($log->old_values || $log->new_values)
                            <div class="pt-2 border-top border-light-subtle d-flex justify-content-end">
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#auditModal{{ $log->id }}">
                                    <i class="mdi mdi-eye-outline me-1"></i> Consulter les détails
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <i class="mdi mdi-shield-search-outline fs-36 text-muted mb-2 opacity-50"></i>
                    <p class="text-muted fs-13 mb-0">Aucun log trouvé.</p>
                </div>
            @endforelse
        </div>

        @if($logs->hasPages())
            <div class="card-footer border-top border-light-subtle p-3">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>

{{-- =================== MODALES DE DÉTAILS AUDIT (HUMANISÉES) =================== --}}
@foreach($logs as $log)
    @if($log->old_values || $log->new_values)
        <div class="modal fade" id="auditModal{{ $log->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                    {{-- En-tête de la modale --}}
                    <div class="modal-header border-0 bg-body-tertiary p-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar-sm bg-primary-subtle text-primary rounded-3 d-flex align-items-center justify-content-center">
                                <i class="mdi mdi-file-search-outline fs-24"></i>
                            </div>
                            <div>
                                <h5 class="modal-title fw-bold mb-0">Détails des modifications</h5>
                                <span class="fs-12 text-muted">Événement enregistré le {{ $log->created_at ? $log->created_at->format('d/m/Y à H:i:s') : '' }}</span>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    {{-- Corps de la modale --}}
                    <div class="modal-body p-4">
                        {{-- Métadonnées de l'action --}}
                        <div class="row g-3 p-3 bg-body-tertiary rounded-3 mb-4 border border-light-subtle">
                            <div class="col-sm-4">
                                <span class="text-muted fs-12 d-block mb-1">Auteur de l'action</span>
                                <strong class="text-body fs-13">{{ $log->user ? $log->user->first_name . ' ' . $log->user->name : 'Système automatique' }}</strong>
                            </div>
                            <div class="col-sm-4">
                                <span class="text-muted fs-12 d-block mb-1">Élément concerné</span>
                                <strong class="text-body fs-13">{{ $typeLabels[$log->auditable_type] ?? class_basename($log->auditable_type) }}</strong>
                            </div>
                            <div class="col-sm-4">
                                <span class="text-muted fs-12 d-block mb-1">Adresse IP & Appareil</span>
                                <strong class="text-body fs-13">{{ $log->ip_address ?? '127.0.0.1' }}</strong>
                            </div>
                        </div>

                        {{-- Tableau comparatif lisible en français --}}
                        <h6 class="fw-bold mb-3 fs-14"><i class="mdi mdi-compare me-1 text-primary"></i>Comparatif des données</h6>
                        
                        <div class="table-responsive border rounded-3 overflow-hidden">
                            <table class="table align-middle table-striped mb-0 fs-13">
                                <thead class="bg-body-tertiary">
                                    <tr>
                                        <th style="width: 30%;">Champ concerné</th>
                                        <th style="width: 35%;" class="text-danger"><i class="mdi mdi-minus-circle-outline me-1"></i>Ancienne valeur</th>
                                        <th style="width: 35%;" class="text-success"><i class="mdi mdi-plus-circle-outline me-1"></i>Nouvelle valeur</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $oldVals = is_array($log->old_values) ? $log->old_values : [];
                                        $newVals = is_array($log->new_values) ? $log->new_values : [];
                                        $allKeys = array_unique(array_merge(array_keys($oldVals), array_keys($newVals)));
                                    @endphp

                                    @forelse($allKeys as $key)
                                        {{-- Ignorer les identifiants techniques et tokens de sécurité bruts --}}
                                        @if(in_array($key, ['id', 'auditable_id', 'password', 'remember_token', 'token', 'two_factor_secret', 'two_factor_recovery_codes']))
                                            @continue
                                        @endif
                                        
                                        @php
                                            $oldVal = $oldVals[$key] ?? null;
                                            $newVal = $newVals[$key] ?? null;
                                            
                                            // Ne pas afficher si aucun changement
                                            if ($oldVal === $newVal) continue;

                                            $formattedKey = $fieldLabels[$key] ?? ucfirst(str_replace('_', ' ', $key));

                                            $formatVal = function($k, $val) use ($userNames, $groupNames) {
                                                if (is_null($val)) return '—';
                                                if (is_bool($val)) return $val ? 'Oui' : 'Non';

                                                if ($k === 'status') {
                                                    return match((string)$val) {
                                                        'pending' => 'En attente de versement',
                                                        'validated' => 'Validé à la trésorerie',
                                                        'rejected' => 'Rejeté',
                                                        'active' => 'Actif',
                                                        'inactive' => 'Inactif',
                                                        default => ucfirst((string)$val)
                                                    };
                                                }

                                                if ($k === 'group_id') {
                                                    return $groupNames[$val] ?? "Groupe #$val";
                                                }

                                                if (in_array($k, ['collector_id', 'leader_id', 'treasurer_id', 'user_id', 'collected_by'])) {
                                                    return $userNames[$val] ?? "Utilisateur #$val";
                                                }

                                                if (in_array($k, ['amount', 'weekly_contribution']) && is_numeric($val)) {
                                                    return number_format((float)$val, 0, ',', ' ') . ' FCFA';
                                                }

                                                if (is_array($val)) return json_encode($val, JSON_UNESCAPED_UNICODE);

                                                return (string)$val;
                                            };
                                        @endphp

                                        <tr>
                                            <td class="fw-semibold text-body">{{ $formattedKey }}</td>
                                            <td class="bg-danger bg-opacity-10 text-danger fw-medium">
                                                {{ $formatVal($key, $oldVal) }}
                                            </td>
                                            <td class="bg-success bg-opacity-10 text-success fw-medium">
                                                {{ $formatVal($key, $newVal) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted py-3">Aucun détail de modification disponible.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Pied de la modale --}}
                    <div class="modal-footer border-0 bg-body-tertiary p-3">
                        <button type="button" class="btn btn-secondary rounded-pill px-4 fs-13" data-bs-dismiss="modal">Fermer</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endforeach

@endsection
