@extends('layouts.app')

@section('content')
<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Logs d'Audit</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Logs d'Audit</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<!-- end page title -->

<div class="row">
    <div class="col-lg-12">
        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-header border-0">
                <h5 class="card-title mb-0 flex-grow-1">Filtres de recherche</h5>
            </div>
            <div class="card-body pt-0">
                <form action="{{ route('admin.audit-logs.index') }}" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Action</label>
                        <select name="action" class="form-select">
                            <option value="">Toutes les actions</option>
                            <option value="created" {{ request('action') === 'created' ? 'selected' : '' }}>Création</option>
                            <option value="updated" {{ request('action') === 'updated' ? 'selected' : '' }}>Modification</option>
                            <option value="deleted" {{ request('action') === 'deleted' ? 'selected' : '' }}>Suppression</option>
                            <option value="login" {{ request('action') === 'login' ? 'selected' : '' }}>Connexion</option>
                            <option value="logout" {{ request('action') === 'logout' ? 'selected' : '' }}>Déconnexion</option>
                            <option value="scan_qr" {{ request('action') === 'scan_qr' ? 'selected' : '' }}>Scan QR</option>
                            <option value="export" {{ request('action') === 'export' ? 'selected' : '' }}>Export</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Type d'Entité</label>
                        <input type="text" name="auditable_type" class="form-control" value="{{ request('auditable_type') }}" placeholder="Ex: App\Models\User">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">ID Utilisateur</label>
                        <input type="number" name="user_id" class="form-control" value="{{ request('user_id') }}" placeholder="Ex: 5">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2 w-100-mobile"><i class="ri-filter-line align-bottom me-1"></i> Filtrer</button>
                        <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-light w-100-mobile"><i class="ri-refresh-line align-bottom me-1"></i> Réinitialiser</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Logs Table -->
        <div class="card">
            <div class="card-header border-0">
                <h5 class="card-title mb-0">Historique des actions</h5>
            </div>
            <div class="card-body pt-0">
                <div class="table-responsive table-card mb-1">
                    <table class="table table-nowrap align-middle">
                        <thead class="text-muted table-light">
                            <tr class="text-uppercase">
                                <th class="ps-4">Date</th>
                                <th>Utilisateur</th>
                                <th>Action</th>
                                <th>Cible</th>
                                <th>IP / Agent</th>
                                <th class="text-end pe-4">Détails</th>
                            </tr>
                        </thead>
                        <tbody class="list">
                            @forelse($logs as $log)
                            <tr>
                                <td class="ps-4 fw-medium text-body">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <h5 class="fs-13 mb-0 fw-semibold text-body">
                                                {{ $log->user ? $log->user->first_name . ' ' . $log->user->name : 'Système' }}
                                            </h5>
                                            <span class="text-muted small">ID: {{ $log->user_id ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ match($log->action) {
                                        'created' => 'success-subtle text-success',
                                        'updated' => 'warning-subtle text-warning',
                                        'deleted' => 'danger-subtle text-danger',
                                        'login' => 'info-subtle text-info',
                                        'logout' => 'secondary-subtle text-secondary',
                                        default => 'primary-subtle text-primary'
                                    } }} fs-11">
                                        {{ match($log->action) {
                                            'created' => 'CRÉATION',
                                            'updated' => 'MODIFICATION',
                                            'deleted' => 'SUPPRESSION',
                                            'login' => 'CONNEXION',
                                            'logout' => 'DÉCONNEXION',
                                            'scan_qr' => 'SCAN QR',
                                            'export' => 'EXPORT',
                                            default => strtoupper($log->action)
                                        } }}
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-semibold text-body">{{ class_basename($log->auditable_type) }}</span>
                                    @if($log->auditable_id)
                                        <span class="badge bg-light text-body border">ID: {{ $log->auditable_id }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-1">
                                        <i class="ri-computer-line text-muted"></i>
                                        <div>
                                            <span class="fw-medium d-block fs-12 text-body">{{ $log->ip_address }}</span>
                                            <small class="text-muted text-truncate d-inline-block" style="max-width: 150px;" title="{{ $log->user_agent }}">
                                                {{ $log->user_agent }}
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-end pe-4">
                                    @if($log->old_values || $log->new_values)
                                        <button type="button" class="btn btn-sm btn-soft-info" data-bs-toggle="modal" data-bs-target="#logModal{{ $log->id }}">
                                            <i class="ri-eye-line align-bottom me-1"></i> Voir
                                        </button>
                                        
                                        <!-- Modal -->
                                        <div class="modal fade" id="logModal{{ $log->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                                <div class="modal-content border-0 shadow-lg">
                                                    <div class="modal-header border-bottom p-3">
                                                        <h5 class="modal-title d-flex align-items-center">
                                                            <i class="ri-file-search-line me-2 text-primary fs-20"></i> Détails du Log d'Audit #{{ $log->id }}
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body p-4 text-start">
                                                        <div class="row g-3 mb-3">
                                                            <div class="col-sm-4">
                                                                <span class="text-muted d-block small">Utilisateur</span>
                                                                <span class="fw-semibold text-body">{{ $log->user ? $log->user->first_name . ' ' . $log->user->name : 'Système' }}</span>
                                                            </div>
                                                            <div class="col-sm-4">
                                                                <span class="text-muted d-block small">Action</span>
                                                                <span class="badge bg-{{ match($log->action) {
                                                                    'created' => 'success-subtle text-success',
                                                                    'updated' => 'warning-subtle text-warning',
                                                                    'deleted' => 'danger-subtle text-danger',
                                                                    'login' => 'info-subtle text-info',
                                                                    'logout' => 'secondary-subtle text-secondary',
                                                                    default => 'primary-subtle text-primary'
                                                                } }}">{{ match($log->action) {
                                                                    'created' => 'CRÉATION',
                                                                    'updated' => 'MODIFICATION',
                                                                    'deleted' => 'SUPPRESSION',
                                                                    'login' => 'CONNEXION',
                                                                    'logout' => 'DÉCONNEXION',
                                                                    'scan_qr' => 'SCAN QR',
                                                                    'export' => 'EXPORT',
                                                                    default => strtoupper($log->action)
                                                                } }}</span>
                                                            </div>
                                                            <div class="col-sm-4">
                                                                <span class="text-muted d-block small">Date & Heure</span>
                                                                <span class="fw-semibold text-body">{{ $log->created_at->format('d/m/Y H:i:s') }}</span>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            @if($log->old_values)
                                                            <div class="col-md-6">
                                                                <h6 class="fs-13 fw-bold text-muted mb-2">Anciennes Valeurs</h6>
                                                                <pre class="p-3 rounded border text-body" style="background-color: var(--vz-light); max-height: 300px; overflow-y: auto;"><code>{{ json_encode($log->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                                                            </div>
                                                            @endif
                                                            @if($log->new_values)
                                                            <div class="col-md-6">
                                                                <h6 class="fs-13 fw-bold text-muted mb-2">Nouvelles Valeurs</h6>
                                                                <pre class="p-3 rounded border text-body" style="background-color: var(--vz-light); max-height: 300px; overflow-y: auto;"><code>{{ json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                                                            </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer bg-light p-3 border-0">
                                                        <button type="button" class="btn btn-light w-sm" data-bs-dismiss="modal">Fermer</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted me-2">-</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6">
                                    <div class="noresult py-5">
                                        <div class="text-center">
                                            <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop" colors="primary:#121331,secondary:#08a88a" style="width:75px;height:75px"></lord-icon>
                                            <h5 class="mt-2 text-muted">Désolé ! Aucun log trouvé.</h5>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            @if($logs->hasPages())
            <div class="card-footer border-top-0 d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="text-muted small">
                    Affichage de {{ $logs->firstItem() ?? 0 }} à {{ $logs->lastItem() ?? 0 }} sur {{ $logs->total() }} entrée(s)
                </div>
                <div>
                    {{ $logs->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
    @media (max-width: 575.98px) {
        .w-100-mobile {
            width: 100% !important;
            margin-bottom: 5px;
        }
    }
</style>
@endsection
