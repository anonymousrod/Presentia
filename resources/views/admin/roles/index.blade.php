@extends('layouts.app')

@section('title', 'Rôles & Permissions')

@section('content')
<div class="container-fluid p-0">
    <!-- start page title -->
    <div class="row align-items-center mb-3 g-2">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between py-2">
                <h4 class="mb-sm-0 fw-bold fs-18 fs-md-22">Rôles & Permissions</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0 fs-12">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Rôles</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-12">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-3">
                    <i class="ri-check-line me-1 align-middle fs-16"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-3">
                    <i class="ri-error-warning-line me-1 align-middle fs-16"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div x-data="{ deleteTarget: null, deleteName: '' }">
                {{-- Modal de confirmation --}}
                <div x-show="deleteTarget !== null" x-cloak
                     class="modal fade"
                     :class="{ 'show d-block': deleteTarget !== null }"
                     tabindex="-1" style="background: rgba(0,0,0,0.5); z-index: 1050;">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow-lg rounded-3">
                            <div class="modal-header border-bottom p-3">
                                <h5 class="modal-title text-danger d-flex align-items-center fs-15 fw-bold">
                                    <i class="ri-error-warning-fill me-2 fs-20"></i> Supprimer un rôle
                                </h5>
                                <button type="button" class="btn-close" @click="deleteTarget = null"></button>
                            </div>
                            <div class="modal-body p-4 text-center">
                                <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#f06548,secondary:#f7b84b" style="width:90px;height:90px"></lord-icon>
                                <div class="mt-3">
                                    <h5 class="mb-2 fw-bold">Confirmation de suppression</h5>
                                    <p class="text-muted fs-14 mb-0">
                                        Êtes-vous sûr de vouloir supprimer le rôle <strong x-text="'« ' + deleteName + ' »'"></strong> ?
                                    </p>
                                    <p class="text-muted small mt-1 mb-0">
                                        Cette action est irréversible et affectera tous les utilisateurs liés.
                                    </p>
                                </div>
                            </div>
                            <div class="modal-footer bg-light bg-opacity-50 p-3 border-top border-light-subtle">
                                <button type="button" class="btn btn-light w-sm" @click="deleteTarget = null">Annuler</button>
                                <form :action="deleteTarget" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger w-sm">Supprimer</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-3" id="roleList">
                    <div class="card-header border-0 bg-transparent py-3 px-3 px-md-4 border-bottom border-light-subtle">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-xs bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="ri-shield-user-line fs-16"></i>
                                </div>
                                <h5 class="card-title mb-0 fw-bold fs-15 text-body">Liste des Rôles</h5>
                            </div>
                            <div>
                                @can('role.manage')
                                <a href="{{ route('admin.roles.create') }}" class="btn btn-sm btn-success shadow-none rounded-pill px-3">
                                    <i class="ri-add-line align-bottom me-1"></i> Nouveau rôle
                                </a>
                                @endcan
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body p-0">
                        {{-- VUE DESKTOP --}}
                        <div class="table-responsive d-none d-md-block">
                            <table class="table table-nowrap align-middle mb-0">
                                <thead class="table-light">
                                    <tr class="text-uppercase fs-11">
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
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0 me-3">
                                                    <div class="avatar-xs">
                                                        <div class="avatar-title rounded-circle bg-primary-subtle text-primary fs-14">
                                                            <i class="ri-shield-user-line"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h5 class="fs-13 mb-0">
                                                        <a href="{{ route('admin.roles.show', $role) }}" class="text-body fw-bold">{{ $role->name }}</a>
                                                    </h5>
                                                    @if($role->is_system)
                                                        <span class="badge bg-secondary-subtle text-secondary fs-10 mt-1">SYSTÈME</span>
                                                    @else
                                                        <span class="badge bg-info-subtle text-info fs-10 mt-1">PERSONNALISÉ</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-info-subtle text-info fs-12 px-2 py-1">
                                                <i class="ri-key-2-line me-1 align-bottom"></i>{{ $role->permissions_count }} permission{{ $role->permissions_count > 1 ? 's' : '' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-group me-2">
                                                    @php $users = $role->users()->take(3)->get(); @endphp
                                                    @foreach($users as $user)
                                                        <div class="avatar-group-item">
                                                            <a href="{{ route('admin.users.show', $user) }}" class="d-inline-block" data-bs-toggle="tooltip" title="{{ $user->full_name }}">
                                                                @if($user->photo)
                                                                     <img src="{{ asset('storage/' . $user->photo) }}" alt="" class="rounded-circle avatar-xxs object-cover">
                                                                @else
                                                                    <div class="avatar-xxs">
                                                                        <span class="avatar-title rounded-circle bg-primary-subtle text-primary fs-10 fw-bold">
                                                                            {{ strtoupper(substr($user->first_name, 0, 1)) }}
                                                                        </span>
                                                                    </div>
                                                                @endif
                                                            </a>
                                                        </div>
                                                    @endforeach
                                                    @if($role->users_count > 3)
                                                        <div class="avatar-group-item">
                                                            <div class="avatar-xxs">
                                                                <span class="avatar-title rounded-circle bg-light text-primary fs-10 fw-bold">
                                                                    +{{ $role->users_count - 3 }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                                <span class="text-muted fs-12">({{ $role->users_count }})</span>
                                            </div>
                                        </td>
                                        <td class="text-end pe-4">
                                            <ul class="list-inline hstack gap-2 justify-content-end mb-0">
                                                <li class="list-inline-item" data-bs-toggle="tooltip" title="Voir">
                                                    <a href="{{ route('admin.roles.show', $role) }}" class="btn btn-sm btn-soft-info px-2 py-1">
                                                        <i class="ri-eye-fill"></i>
                                                    </a>
                                                </li>
                                                @unless($role->code === 'admin')
                                                @can('role.manage')
                                                <li class="list-inline-item" data-bs-toggle="tooltip" title="Modifier">
                                                    <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-sm btn-soft-primary px-2 py-1">
                                                        <i class="ri-pencil-fill"></i>
                                                    </a>
                                                </li>
                                                @unless($role->is_system)
                                                <li class="list-inline-item" data-bs-toggle="tooltip" title="Supprimer">
                                                    <button type="button" class="btn btn-sm btn-soft-danger px-2 py-1"
                                                            @click="deleteTarget = '{{ route('admin.roles.destroy', $role) }}'; deleteName = '{{ $role->name }}'">
                                                        <i class="ri-delete-bin-5-fill"></i>
                                                    </button>
                                                </li>
                                                @endunless
                                                @endcan
                                                @endunless
                                            </ul>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4">
                                            <div class="noresult py-5 text-center">
                                                <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop" colors="primary:#121331,secondary:#08a88a" style="width:75px;height:75px"></lord-icon>
                                                <h5 class="mt-2 text-muted fw-bold">Aucun rôle trouvé.</h5>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- VUE MOBILE (Cartes modernes denses) --}}
                        <div class="d-block d-md-none p-3">
                            <div class="d-flex flex-column gap-2">
                                @forelse($roles as $role)
                                <div class="card border border-light-subtle rounded-3 shadow-none mb-1 p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar-xs flex-shrink-0">
                                                <div class="avatar-title rounded-circle bg-primary-subtle text-primary fs-14">
                                                    <i class="ri-shield-user-line"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <h6 class="fs-13 mb-0 fw-bold">
                                                    <a href="{{ route('admin.roles.show', $role) }}" class="text-body">{{ $role->name }}</a>
                                                </h6>
                                                @if($role->is_system)
                                                    <span class="badge bg-secondary-subtle text-secondary fs-10">SYSTÈME</span>
                                                @else
                                                    <span class="badge bg-info-subtle text-info fs-10">PERSONNALISÉ</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row g-2 text-center border-top border-bottom border-light-subtle py-2 my-2 fs-12">
                                        <div class="col-6 border-end border-light-subtle">
                                            <span class="text-muted d-block fs-11 mb-1">Permissions</span>
                                            <span class="badge bg-info-subtle text-info fs-11">
                                                <i class="ri-key-2-line me-1"></i>{{ $role->permissions_count }}
                                            </span>
                                        </div>
                                        <div class="col-6">
                                            <span class="text-muted d-block fs-11 mb-1">Utilisateurs</span>
                                            <div class="d-flex align-items-center justify-content-center">
                                                <div class="avatar-group me-1">
                                                    @php $users = $role->users()->take(3)->get(); @endphp
                                                    @foreach($users as $user)
                                                        <div class="avatar-group-item">
                                                            @if($user->photo)
                                                                 <img src="{{ asset('storage/' . $user->photo) }}" alt="" class="rounded-circle avatar-xxs object-cover">
                                                            @else
                                                                <div class="avatar-xxs">
                                                                    <span class="avatar-title rounded-circle bg-primary-subtle text-primary fs-10 fw-bold">
                                                                        {{ strtoupper(substr($user->first_name, 0, 1)) }}
                                                                    </span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                    @if($role->users_count > 3)
                                                        <div class="avatar-group-item">
                                                            <div class="avatar-xxs">
                                                                <span class="avatar-title rounded-circle bg-light text-primary fs-10 fw-bold">+{{ $role->users_count - 3 }}</span>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                                <span class="text-muted fs-11">({{ $role->users_count }})</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex gap-2 align-items-center pt-1">
                                        <a href="{{ route('admin.roles.show', $role) }}" class="btn btn-sm btn-soft-info flex-grow-1 fs-12 py-1 shadow-none">
                                            <i class="ri-eye-fill me-1"></i>Voir
                                        </a>
                                        @unless($role->code === 'admin')
                                        @can('role.manage')
                                        <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-sm btn-soft-primary flex-grow-1 fs-12 py-1 shadow-none">
                                            <i class="ri-pencil-fill me-1"></i>Modifier
                                        </a>
                                        @unless($role->is_system)
                                        <button type="button" class="btn btn-sm btn-soft-danger flex-grow-1 fs-12 py-1 shadow-none" 
                                                @click="deleteTarget = '{{ route('admin.roles.destroy', $role) }}'; deleteName = '{{ $role->name }}'">
                                            <i class="ri-delete-bin-5-fill me-1"></i>Supprimer
                                        </button>
                                        @endunless
                                        @endcan
                                        @endunless
                                    </div>
                                </div>
                                @empty
                                <div class="noresult py-4 text-center border border-light-subtle rounded-3">
                                    <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop" colors="primary:#121331,secondary:#08a88a" style="width:75px;height:75px"></lord-icon>
                                    <h5 class="mt-2 text-muted fs-14 fw-bold">Aucun rôle trouvé.</h5>
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
    .avatar-xxs {
        width: 1.5rem;
        height: 1.5rem;
    }
</style>
@endsection
