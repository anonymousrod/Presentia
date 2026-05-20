@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Logs d'Audit</h2>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('admin.audit-logs.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Action</label>
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
                    <label class="form-label">Type d'Entité</label>
                    <input type="text" name="auditable_type" class="form-control" value="{{ request('auditable_type') }}" placeholder="Ex: App\Models\User">
                </div>
                <div class="col-md-3">
                    <label class="form-label">ID Utilisateur</label>
                    <input type="number" name="user_id" class="form-control" value="{{ request('user_id') }}">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">Filtrer</button>
                    <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-secondary">Réinitialiser</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Logs Table -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Utilisateur</th>
                            <th>Action</th>
                            <th>Cible (ID)</th>
                            <th>IP / Agent</th>
                            <th>Détails</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td>{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                            <td>
                                {{ $log->user ? $log->user->first_name . ' ' . $log->user->name : 'Système' }}<br>
                                <small class="text-muted">ID: {{ $log->user_id }}</small>
                            </td>
                            <td>
                                <span class="badge bg-{{ match($log->action) {
                                    'created' => 'success',
                                    'updated' => 'warning',
                                    'deleted' => 'danger',
                                    'login' => 'info',
                                    'logout' => 'secondary',
                                    default => 'primary'
                                } }}">
                                    {{ strtoupper($log->action) }}
                                </span>
                            </td>
                            <td>
                                {{ class_basename($log->auditable_type) }}
                                @if($log->auditable_id)
                                    (ID: {{ $log->auditable_id }})
                                @endif
                            </td>
                            <td>
                                {{ $log->ip_address }}<br>
                                <small class="text-muted text-truncate d-inline-block" style="max-width: 150px;" title="{{ $log->user_agent }}">
                                    {{ $log->user_agent }}
                                </small>
                            </td>
                            <td>
                                @if($log->old_values || $log->new_values)
                                    <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#logModal{{ $log->id }}">
                                        Voir
                                    </button>
                                    
                                    <!-- Modal -->
                                    <div class="modal fade" id="logModal{{ $log->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Détails de l'action</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        @if($log->old_values)
                                                        <div class="col-md-6">
                                                            <h6>Anciennes Valeurs</h6>
                                                            <pre class="bg-light p-2 rounded"><code>{{ json_encode($log->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                                                        </div>
                                                        @endif
                                                        @if($log->new_values)
                                                        <div class="col-md-6">
                                                            <h6>Nouvelles Valeurs</h6>
                                                            <pre class="bg-light p-2 rounded"><code>{{ json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                                                        </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">Aucun log trouvé.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection
