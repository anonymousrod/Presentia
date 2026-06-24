@extends('layouts.app')

@section('title', 'Gestion des Types d\'Activités')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Types d'Activités</h2>
        <a href="{{ route('admin.activity-types.create') }}" class="btn btn-primary">
            <i class="mdi mdi-plus me-2"></i>Nouveau Type
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nom</th>
                            <th>Couleur</th>
                            <th>Activités Liées</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($types as $type)
                            <tr>
                                <td><strong>{{ $type->name }}</strong></td>
                                <td>
                                    <span class="badge" style="background-color: {{ $type->color }}; color: #fff;">
                                        {{ $type->color }}
                                    </span>
                                </td>
                                <td>{{ $type->activities_count }} activité(s)</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.activity-types.edit', $type) }}" class="btn btn-sm btn-primary" title="Modifier">
                                        <i class="mdi mdi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete({{ $type->id }})" title="Supprimer" {{ $type->activities_count > 0 ? 'disabled' : '' }}>
                                        <i class="mdi mdi-trash-can"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    Aucun type d'activité trouvé.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Forms for delete -->
@foreach($types as $type)
<form id="delete-form-{{ $type->id }}" action="{{ route('admin.activity-types.destroy', $type) }}" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>
@endforeach

@endsection

@push('scripts')
<script>
    function confirmDelete(id) {
        confirmAction(
            'Êtes-vous sûr de vouloir supprimer ce type d\'activité ?',
            () => document.getElementById('delete-form-' + id).submit(),
            'Supprimer le type',
            'Supprimer',
            'btn-danger'
        );
    }
</script>
@endpush
