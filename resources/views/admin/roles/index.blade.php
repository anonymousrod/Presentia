@extends('layouts.app')

@section('content')
<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Rôles & Permissions</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Rôles</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<!-- end page title -->

<div class="row">
    <div class="col-lg-12">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4">
                <i class="ri-check-line me-1 align-middle fs-16"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4">
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
                    <div class="modal-content border-0 shadow-lg">
                        <div class="modal-header border-bottom p-3">
                            <h5 class="modal-title text-danger d-flex align-items-center">
                                <i class="ri-error-warning-fill me-2 fs-20"></i> Supprimer un rôle
                            </h5>
                            <button type="button" class="btn-close" @click="deleteTarget = null"></button>
                        </div>
                        <div class="modal-body p-4 text-center">
                            <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#f06548,secondary:#f7b84b" style="width:100px;height:100px"></lord-icon>
                            <div class="mt-4">
                                <h4 class="mb-2">Confirmation de suppression</h4>
                                <p class="text-muted fs-15 mb-0">
                                    Êtes-vous sûr de vouloir supprimer le rôle <strong x-text="'« ' + deleteName + ' »'"></strong> ?
                                </p>
                                <p class="text-muted small mt-2">
                                    Cette action est irréversible et affectera tous les utilisateurs liés.
                                </p>
                            </div>
                        </div>
                        <div class="modal-footer bg-light p-3 border-0">
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

            <div class="card" id="roleList">
                <div class="card-header border-0">
                    <div class="d-flex align-items-center">
                        <h5 class="card-title mb-0 flex-grow-1">Liste des Rôles</h5>
                        <div class="flex-shrink-0">
                            <a href="{{ route('admin.roles.create') }}" class="btn btn-success add-btn">
                                <i class="ri-add-line align-bottom me-1"></i> Nouveau rôle
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="table-responsive table-card mb-1">
                        <table class="table table-nowrap align-middle">
                            <thead class="text-muted table-light">
                                <tr class="text-uppercase">
                                    <th class="ps-4">Nom du rôle</th>
                                    <th>Permissions</th>
                                    <th>Utilisateurs</th>
                                    <th class="text-end pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="list form-check-all">
                                @forelse($roles as $role)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 me-3">
                                                <div class="avatar-sm">
                                                    <div class="avatar-title rounded-circle bg-primary-subtle text-primary fs-16">
                                                        <i class="ri-shield-user-line"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h5 class="fs-14 mb-1">
                                                    <a href="{{ route('admin.roles.show', $role) }}" class="text-body fw-semibold">{{ $role->name }}</a>
                                                </h5>
                                                @if(in_array($role->name, ['Administrateur', 'Jeune', 'Chef de groupe']))
                                                    <span class="badge bg-secondary-subtle text-secondary fs-10">SYSTÈME</span>
                                                @else
                                                    <span class="badge bg-info-subtle text-info fs-10">PERSONNALISÉ</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info-subtle text-info fs-12">
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
                                                                 <img src="{{ asset('storage/' . $user->photo) }}" alt="" class="rounded-circle avatar-xxs">
                                                            @else
                                                                <div class="avatar-xxs">
                                                                    <span class="avatar-title rounded-circle bg-primary-subtle text-primary fs-10">
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
                                                            <span class="avatar-title rounded-circle bg-light text-primary fs-10">
                                                                +{{ $role->users_count - 3 }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                            <span class="text-muted small">({{ $role->users_count }})</span>
                                        </div>
                                    </td>
                                    <td class="text-end pe-4">
                                        <ul class="list-inline hstack gap-2 justify-content-end mb-0">
                                            <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Voir">
                                                <a href="{{ route('admin.roles.show', $role) }}" class="text-info d-inline-block">
                                                    <i class="ri-eye-fill fs-16"></i>
                                                </a>
                                            </li>
                                            @unless(in_array($role->name, ['Administrateur']))
                                            <li class="list-inline-item edit" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Modifier">
                                                <a href="{{ route('admin.roles.edit', $role) }}" class="text-primary d-inline-block edit-item-btn">
                                                    <i class="ri-pencil-fill fs-16"></i>
                                                </a>
                                            </li>
                                            <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Supprimer">
                                                <a class="text-danger d-inline-block remove-item-btn" 
                                                   href="javascript:void(0)"
                                                   @click="deleteTarget = '{{ route('admin.roles.destroy', $role) }}'; deleteName = '{{ $role->name }}'">
                                                    <i class="ri-delete-bin-5-fill fs-16"></i>
                                                </a>
                                            </li>
                                            @endunless
                                        </ul>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4">
                                        <div class="noresult py-5">
                                            <div class="text-center">
                                                <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop" colors="primary:#121331,secondary:#08a88a" style="width:75px;height:75px"></lord-icon>
                                                <h5 class="mt-2 text-muted">Désolé ! Aucun rôle trouvé.</h5>
                                            </div>
                                        </div>
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
</div>

<style>
    [x-cloak] { display: none !important; }
    .avatar-xxs {
        width: 1.5rem;
        height: 1.5rem;
    }
</style>
@endsection
