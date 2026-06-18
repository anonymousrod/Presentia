@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Gestion des groupes</h2>
        @can('group.create')
        <a href="{{ route('admin.groups.create') }}" class="btn btn-primary">
            <i class="mdi mdi-plus"></i> Nouveau groupe
        </a>
        @endcan
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
                <div class="col-12 col-md-6 d-flex gap-2 align-items-end mt-2 mt-md-0">
                    <button type="submit" class="btn btn-primary flex-grow-1">Filtrer</button>
                    <a href="{{ route('admin.groups.index') }}" class="btn btn-secondary flex-shrink-0">Réinitialiser</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Liste des groupes</h5>
        </div>
        <div class="card-body p-0">
            {{-- VUE DESKTOP --}}
            <div class="table-responsive d-none d-md-block">
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
                                @can('group.edit')
                                <a href="{{ route('admin.groups.edit', $group) }}" class="btn btn-sm btn-primary" title="Modifier">
                                    <i class="mdi mdi-pencil"></i>
                                </a>
                                @endcan
                                @can('group.delete')
                                <form action="{{ route('admin.groups.destroy', $group) }}" method="POST" class="d-inline confirm-archive-group" data-group-name="{{ $group->name }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Archiver">
                                        <i class="mdi mdi-archive"></i>
                                    </button>
                                </form>
                                @endcan
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

            {{-- VUE MOBILE (Cartes) --}}
            <div class="d-md-none p-3">
                <div class="d-flex flex-column gap-3">
                    @forelse($groups as $group)
                    <div class="card border border-light shadow-sm mb-0">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="fw-bold mb-1">{{ $group->name }}</h6>
                                    <span class="badge bg-soft-primary text-primary">{{ $group->category ?? 'Sans catégorie' }}</span>
                                </div>
                                <span class="badge bg-primary rounded-pill"><i class="mdi mdi-account-group me-1"></i>{{ $group->members_count }}</span>
                            </div>
                            <div class="mb-3">
                                <small class="text-muted d-block mb-1">Chef de groupe</small>
                                @if($group->leader)
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-xxs flex-shrink-0" style="width: 20px; height: 20px;">
                                            <div class="avatar-title rounded-circle bg-info-subtle text-info fs-10">
                                                {{ strtoupper(substr($group->leader->first_name, 0, 1) . substr($group->leader->name, 0, 1)) }}
                                            </div>
                                        </div>
                                        <span class="fs-13 fw-medium text-truncate">{{ $group->leader->first_name }} {{ $group->leader->name }}</span>
                                    </div>
                                @else
                                    <span class="text-muted fs-13 fst-italic">Non désigné</span>
                                @endif
                            </div>
                            <div class="d-flex gap-2 border-top pt-3">
                                <a href="{{ route('admin.groups.show', $group) }}" class="btn btn-sm btn-info flex-grow-1" title="Voir">
                                    <i class="mdi mdi-eye me-1"></i>Voir
                                </a>
                                @can('group.edit')
                                <a href="{{ route('admin.groups.edit', $group) }}" class="btn btn-sm btn-primary flex-grow-1" title="Modifier">
                                    <i class="mdi mdi-pencil me-1"></i>Modif
                                </a>
                                @endcan
                                @can('group.delete')
                                <form action="{{ route('admin.groups.destroy', $group) }}" method="POST" class="d-flex confirm-archive-group m-0" data-group-name="{{ $group->name }}" style="flex-grow: 1;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger w-100" title="Archiver">
                                        <i class="mdi mdi-archive me-1"></i>Archiver
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-muted">
                        <i class="mdi mdi-account-group fs-24 d-block mb-2"></i>
                        Aucun groupe trouvé.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
        @if($groups->hasPages())
        <div class="card-footer">
            {{ $groups->links() }}
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    document.querySelectorAll('.confirm-archive-group').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const groupName = this.getAttribute('data-group-name');
            confirmAction(
                `Archiver le groupe « ${groupName} » ?`,
                () => this.submit(),
                'Archiver le groupe',
                'Archiver',
                'btn-danger'
            );
        });
    });
</script>
@endpush
@endsection
