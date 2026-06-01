@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Gestion des groupes</h2>
        <a href="{{ route('admin.groups.create') }}" class="btn btn-primary">
            <i class="mdi mdi-plus"></i> Nouveau groupe
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Filtres --}}
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('admin.groups.index') }}" method="GET" class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Recherche</label>
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Nom ou catégorie...">
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">Filtrer</button>
                    <a href="{{ route('admin.groups.index') }}" class="btn btn-secondary">Réinitialiser</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Liste des groupes</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nom</th>
                            <th>Catégorie</th>
                            <th>Chef de groupe</th>
                            <th>Membres actifs</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($groups as $group)
                        <tr>
                            <td><strong>{{ $group->name }}</strong></td>
                            <td>{{ $group->category ?? '—' }}</td>
                            <td>
                                @if($group->leader)
                                    <span class="badge bg-info text-dark">
                                        {{ $group->leader->first_name }} {{ $group->leader->name }}
                                    </span>
                                @else
                                    <span class="text-muted">Non désigné</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ $group->members_count }}</span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.groups.show', $group) }}" class="btn btn-sm btn-info" title="Voir">
                                    <i class="mdi mdi-eye"></i>
                                </a>
                                <a href="{{ route('admin.groups.edit', $group) }}" class="btn btn-sm btn-primary" title="Modifier">
                                    <i class="mdi mdi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.groups.destroy', $group) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Archiver"
                                        onclick="return confirm('Archiver le groupe « {{ $group->name }} » ?')">
                                        <i class="mdi mdi-archive"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Aucun groupe trouvé.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($groups->hasPages())
        <div class="card-footer">
            {{ $groups->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
