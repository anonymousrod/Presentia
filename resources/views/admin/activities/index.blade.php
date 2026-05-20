@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Gestion des activités</h2>
        <div>
            <a href="{{ route('admin.activities.create') }}" class="btn btn-primary">
                <i class="mdi mdi-plus"></i> Nouvelle activité
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('admin.activities.index') }}" method="GET" class="row g-3" id="filter-form">
                <div class="col-md-4">
                    <label class="form-label">Recherche</label>
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Titre de l'activité...">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Type</label>
                    <select name="type" class="form-select">
                        <option value="">Tous les types</option>
                        @foreach(\App\Enums\ActivityType::cases() as $type)
                            <option value="{{ $type->value }}" {{ request('type') === $type->value ? 'selected' : '' }}>{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Statut</label>
                    <select name="status" class="form-select">
                        <option value="">Tous les statuts</option>
                        @foreach(\App\Enums\ActivityStatus::cases() as $status)
                            <option value="{{ $status->value }}" {{ request('status') === $status->value ? 'selected' : '' }}>{{ $status->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">Filtrer</button>
                    <button type="button" class="btn btn-secondary" id="btn-reset">Réinitialiser</button>
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
            <div class="table-responsive">
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
                                <span class="badge bg-soft-info text-info">{{ $activity->type->value }}</span>
                            </td>
                            <td>{{ $activity->start_time->format('d/m/Y H:i') }}</td>
                            <td>{{ $activity->end_time->format('d/m/Y H:i') }}</td>
                            <td>
                                <span class="badge bg-soft-dark text-dark">{{ $activity->visibility->value }}</span>
                            </td>
                            <td>
                                @if($activity->visibility === \App\Enums\ActivityVisibility::GROUP)
                                    {{ $activity->group?->name ?? 'N/A' }}
                                @elseif($activity->visibility === \App\Enums\ActivityVisibility::ROLE)
                                    {{ $activity->role?->name ?? 'N/A' }}
                                @else
                                    Tout le monde
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ match($activity->status->value) {
                                    'PUBLISHED' => 'success',
                                    'DRAFT' => 'warning',
                                    'CANCELLED' => 'danger',
                                    'ARCHIVED' => 'secondary',
                                    default => 'primary'
                                } }}">
                                    {{ $activity->status->value }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.activities.show', $activity) }}" class="btn btn-sm btn-info" title="Voir">
                                    <i class="mdi mdi-eye"></i>
                                </a>
                                <a href="{{ route('admin.activities.edit', $activity) }}" class="btn btn-sm btn-primary" title="Modifier">
                                    <i class="mdi mdi-pencil"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete({{ $activity->id }})" title="Supprimer">
                                    <i class="mdi mdi-trash-can"></i>
                                </button>
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
        if (confirm('Êtes-vous sûr de vouloir supprimer cette activité ?')) {
            document.getElementById('delete-form-' + id).submit();
        }
    }
</script>
@endpush
