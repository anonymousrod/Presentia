@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 mb-1">Rôles & Permissions</h1>
            <p class="text-muted mb-0">Gérer les rôles et leurs permissions dans l'application.</p>
        </div>
        <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
            <i class="mdi mdi-plus me-1"></i> Nouveau rôle
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="mdi mdi-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="mdi mdi-alert-circle me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Tableau des rôles avec modal de confirmation Alpine.js --}}
    <div x-data="{ deleteTarget: null, deleteName: '' }">

        {{-- Modal de confirmation --}}
        <div x-show="deleteTarget !== null" x-cloak
             class="modal d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title text-danger">
                            <i class="mdi mdi-alert-circle me-2"></i>Supprimer un rôle
                        </h5>
                    </div>
                    <div class="modal-body pt-2">
                        <p class="mb-0">
                            Êtes-vous sûr de vouloir supprimer le rôle
                            <strong x-text="'« ' + deleteName + ' »'"></strong> ?
                        </p>
                        <p class="text-muted small mt-2 mb-0">
                            <i class="mdi mdi-information-outline me-1"></i>
                            Cette action est irréversible. Les permissions liées à ce rôle seront retirées de tous les utilisateurs concernés.
                        </p>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-outline-secondary"
                            @click="deleteTarget = null; deleteName = ''">
                            Annuler
                        </button>
                        <form :action="deleteTarget" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="mdi mdi-delete me-1"></i> Supprimer définitivement
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Nom du rôle</th>
                                <th>Permissions</th>
                                <th>Utilisateurs</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($roles as $role)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-soft-primary text-primary d-flex align-items-center justify-content-center"
                                            style="width:36px;height:36px;font-size:1rem;">
                                            <i class="mdi mdi-shield-account"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold text-dark">{{ $role->name }}</div>
                                            @if(in_array($role->name, ['Administrateur', 'Jeune', 'Chef de groupe']))
                                                <span class="badge bg-secondary" style="font-size:0.7rem;">Système</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-soft-info text-info border border-info-subtle px-2 py-1">
                                        <i class="mdi mdi-key-outline me-1"></i>{{ $role->permissions_count }} permission{{ $role->permissions_count > 1 ? 's' : '' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-soft-primary text-primary border border-primary-subtle px-2 py-1">
                                        <i class="mdi mdi-account-multiple me-1"></i>{{ $role->users_count }} utilisateur{{ $role->users_count > 1 ? 's' : '' }}
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('admin.roles.show', $role) }}"
                                       class="btn btn-sm btn-outline-info border-0" title="Voir">
                                        <i class="mdi mdi-eye fs-5"></i>
                                    </a>
                                    @unless(in_array($role->name, ['Administrateur']))
                                    <a href="{{ route('admin.roles.edit', $role) }}"
                                       class="btn btn-sm btn-outline-primary border-0" title="Modifier">
                                        <i class="mdi mdi-pencil fs-5"></i>
                                    </a>
                                    <button type="button"
                                        class="btn btn-sm btn-outline-danger border-0" title="Supprimer"
                                        @click="deleteTarget = '{{ route('admin.roles.destroy', $role) }}'; deleteName = '{{ $role->name }}'">
                                        <i class="mdi mdi-delete fs-5"></i>
                                    </button>
                                    @endunless
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="mdi mdi-shield-off-outline fs-1 d-block mb-2 opacity-50"></i>
                                    Aucun rôle trouvé.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-soft-primary { background-color: rgba(13,110,253,.1); }
    .bg-soft-info    { background-color: rgba(13,202,240,.1); }
    [x-cloak]        { display: none !important; }
</style>
@endsection
