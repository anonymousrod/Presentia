@extends('layouts.app')

@section('content')
<div class="container-fluid max-w-1200 py-3 py-md-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="fs-18 fs-md-24 fw-bold mb-1">Gestion des activités</h2>
            <p class="text-muted mb-0 fs-12 fs-md-13">Gérez et planifiez les activités de votre organisation.</p>
        </div>
        <div>
            @can('activity.create')
            <a href="{{ route('admin.activities.create') }}" class="btn btn-primary rounded-pill px-3 shadow-sm d-flex align-items-center gap-1">
                <i class="mdi mdi-plus"></i> <span class="d-none d-sm-inline">Nouvelle activité</span><span class="d-inline d-sm-none">Créer</span>
            </a>
            @endcan
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4 rounded-3">
        <div class="card-body p-3 p-md-4">
            <form action="{{ route('admin.activities.index') }}" method="GET" id="filter-form">
                <div class="row g-3 align-items-end">
                    <div class="col-12 col-md-4">
                        <div class="d-flex align-items-center justify-content-between mb-2 d-md-none">
                            <label class="form-label fw-semibold text-muted mb-0 fs-11 uppercase tracking-wider">Recherche</label>
                            <button type="button" class="btn btn-sm btn-soft-primary rounded-pill px-3 py-1 fs-12 d-flex align-items-center gap-1" data-bs-toggle="collapse" data-bs-target="#adminMobileFilterCollapse" aria-expanded="{{ (request('type') || request('status')) ? 'true' : 'false' }}">
                                <i class="mdi mdi-filter-variant"></i>
                                <span>Filtres</span>
                                @if(request('type') || request('status'))
                                    <span class="badge bg-primary rounded-circle p-1"></span>
                                @endif
                            </button>
                        </div>
                        <label class="form-label fw-semibold text-muted mb-1 d-none d-md-block fs-12 uppercase tracking-wider">Recherche</label>
                        <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Titre de l'activité...">
                    </div>

                    <div class="col-12 col-md-8 collapse d-md-block {{ (request('type') || request('status')) ? 'show' : '' }}" id="adminMobileFilterCollapse">
                        <div class="row g-3 align-items-end">
                            <div class="col-12 col-sm-6 col-md-4">
                                <label class="form-label fw-semibold text-muted mb-1 fs-12 uppercase tracking-wider">Type</label>
                                <select name="type" class="form-select">
                                    <option value="">Tous les types</option>
                                    @foreach($activityTypes as $type)
                                        <option value="{{ encode_id($type->id) }}" {{ request('type') == encode_id($type->id) ? 'selected' : '' }}>
                                            {{ $type->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4">
                                <label class="form-label fw-semibold text-muted mb-1 fs-12 uppercase tracking-wider">Statut</label>
                                <select name="status" class="form-select">
                                    <option value="">Tous les statuts</option>
                                    @foreach(\App\Enums\ActivityStatus::cases() as $status)
                                        <option value="{{ $status->value }}" {{ request('status') === $status->value ? 'selected' : '' }}>{{ $status->label() }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-4 d-flex gap-2">
                                <button type="submit" class="btn btn-primary flex-grow-1">Filtrer</button>
                                <button type="button" class="btn btn-secondary flex-shrink-0" id="btn-reset">Réinitialiser</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Activities Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Liste des activités</h5>
        </div>
        <div class="card-body p-0">
            {{-- VUE DESKTOP --}}
            <div class="table-responsive d-none d-md-block">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Titre</th>
                            <th>Type</th>
                            <th>Date début</th>
                            <th>Date fin</th>
                            <th>Visibilité</th>
                            <th>Cible</th>
                            <th>Statut</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activities as $activity)
                        <tr>
                            <td>
                                <strong>{{ $activity->title }}</strong>
                                @if($activity->location)
                                    <br><small class="text-muted"><i class="mdi mdi-map-marker"></i> {{ $activity->location }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge" style="background-color: {{ $activity->activityType?->color ?? '#17a2b8' }}; color: white;">{{ $activity->activityType?->name ?? 'N/A' }}</span>
                            </td>
                            <td>{{ $activity->start_time->format('d/m/Y H:i') }}</td>
                            <td>{{ $activity->end_time->format('d/m/Y H:i') }}</td>
                            <td>
                                <span class="badge bg-secondary-subtle text-body">{{ $activity->visibility?->label() ?? 'Tout le monde' }}</span>
                            </td>
                            <td>
                                @if($activity->visibility?->value === 'GROUP')
                                    {{ $activity->group?->name ?? 'N/A' }}
                                @elseif($activity->visibility?->value === 'ROLE')
                                    {{ $activity->role?->name ?? 'N/A' }}
                                @else
                                    Tout le monde
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ match($activity->status?->value) {
                                    'PUBLISHED' => 'success',
                                    'DRAFT' => 'warning',
                                    'CANCELLED' => 'danger',
                                    'ARCHIVED' => 'secondary',
                                    default => 'primary'
                                } }}">
                                    {{ $activity->status?->label() ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.activities.show', $activity) }}" class="btn btn-sm btn-info" title="Voir">
                                    <i class="mdi mdi-eye"></i>
                                </a>
                                @can('activity.edit')
                                @if($activity->status !== \App\Enums\ActivityStatus::CANCELLED)
                                <a href="{{ route('admin.activities.edit', $activity) }}" class="btn btn-sm btn-primary" title="Modifier">
                                    <i class="mdi mdi-pencil"></i>
                                </a>
                                @endif
                                @endcan
                                @can('activity.delete')
                                @if($activity->status !== \App\Enums\ActivityStatus::PUBLISHED)
                                <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete({{ $activity->id }})" title="Supprimer">
                                    <i class="mdi mdi-trash-can"></i>
                                </button>
                                @endif
                                @endcan
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">Aucune activité trouvée.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- VUE MOBILE (Cartes) --}}
            <div class="d-md-none p-3">
                <div class="d-flex flex-column gap-3">
                    @forelse($activities as $activity)
                    <div class="card border border-light-subtle shadow-none mb-0 rounded-3">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2 gap-2">
                                <div>
                                    <h6 class="fw-bold mb-1 fs-15">{{ $activity->title }}</h6>
                                    <span class="badge rounded-pill px-2 py-1" style="background-color: {{ $activity->activityType?->color ?? '#17a2b8' }}; color: white; font-size: 0.72rem;">{{ $activity->activityType?->name ?? 'N/A' }}</span>
                                </div>
                                <span class="badge rounded-pill bg-{{ match($activity->status?->value) {
                                    'PUBLISHED' => 'success',
                                    'DRAFT' => 'warning',
                                    'CANCELLED' => 'danger',
                                    'ARCHIVED' => 'secondary',
                                    default => 'primary'
                                } }}">
                                    {{ $activity->status?->label() ?? 'N/A' }}
                                </span>
                            </div>
                            
                            <div class="mb-3 text-muted fs-13">
                                <div class="d-flex align-items-center mb-1">
                                    <i class="mdi mdi-calendar-clock me-2 text-primary"></i>
                                    <span>{{ $activity->start_time->format('d/m/Y H:i') }} - {{ $activity->end_time->format('H:i') }}</span>
                                </div>
                                @if($activity->location)
                                <div class="d-flex align-items-center mb-1">
                                    <i class="mdi mdi-map-marker me-2 text-success"></i>
                                    <span>{{ $activity->location }}</span>
                                </div>
                                @endif
                                <div class="d-flex align-items-center">
                                    <i class="mdi mdi-eye-outline me-2 text-info"></i>
                                    <span>
                                        @if($activity->visibility?->value === 'GROUP')
                                            Groupe: {{ $activity->group?->name ?? 'N/A' }}
                                        @elseif($activity->visibility?->value === 'ROLE')
                                            Rôle: {{ $activity->role?->name ?? 'N/A' }}
                                        @else
                                            Tout le monde
                                        @endif
                                    </span>
                                </div>
                            </div>
                            
                            <div class="d-flex gap-2 flex-wrap border-top pt-3">
                                <a href="{{ route('admin.activities.show', $activity) }}" class="btn btn-sm btn-soft-info rounded-pill flex-grow-1" title="Voir">
                                    <i class="mdi mdi-eye me-1"></i>Voir
                                </a>
                                @can('activity.edit')
                                @if($activity->status !== \App\Enums\ActivityStatus::CANCELLED)
                                <a href="{{ route('admin.activities.edit', $activity) }}" class="btn btn-sm btn-primary rounded-pill flex-grow-1" title="Modifier">
                                    <i class="mdi mdi-pencil me-1"></i>Modifier
                                </a>
                                @endif
                                @endcan
                                @can('activity.delete')
                                @if($activity->status !== \App\Enums\ActivityStatus::PUBLISHED)
                                <button type="button" class="btn btn-sm btn-danger rounded-pill px-3" onclick="confirmDelete({{ $activity->id }})" title="Supprimer">
                                    <i class="mdi mdi-trash-can"></i>
                                </button>
                                @endif
                                @endcan
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-muted border rounded bg-light">
                        <i class="mdi mdi-calendar-remove fs-24 d-block mb-2"></i>
                        Aucune activité trouvée.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="card-footer">
            {{ $activities->links() }}
        </div>
    </div>
</div>

<!-- Forms for delete -->
@foreach($activities as $activity)
<form id="delete-form-{{ $activity->id }}" action="{{ route('admin.activities.destroy', $activity) }}" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>
@endforeach

@endsection

@push('scripts')
<script>
    document.getElementById('btn-reset').addEventListener('click', function() {
        const form = document.getElementById('filter-form');
        form.querySelectorAll('input, select').forEach(el => el.value = '');
        if (window.location.search) {
            window.location.href = "{{ route('admin.activities.index') }}";
        }
    });

    function confirmDelete(id) {
        confirmAction(
            'Êtes-vous sûr de vouloir supprimer cette activité ?',
            () => document.getElementById('delete-form-' + id).submit(),
            'Supprimer l\'activité',
            'Supprimer',
            'btn-danger'
        );
    }
</script>
@endpush
