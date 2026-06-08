@extends('layouts.app')

@section('title', 'Gérer les Permissions')

@section('content')
<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Gérer les Permissions</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Utilisateurs</a></li>
                    <li class="breadcrumb-item active">Permissions</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<!-- end page title -->

<div class="row justify-content-center" x-data="{ 
    tab: window.location.hash === '#effective' ? 'view' : 'edit',
    init() {
        window.addEventListener('hashchange', () => {
            this.tab = window.location.hash === '#effective' ? 'view' : 'edit';
        });
    }
}">
    <div class="col-xxl-10 col-lg-12">
        
        <!-- User Profile Card -->
        <div class="card mt-2">
            <div class="card-body p-4">
                <div class="row align-items-center g-3">
                    <div class="col-md-auto">
                        <div class="avatar-md">
                            @if($user->photo)
                                <img src="{{ asset('storage/' . $user->photo) }}" alt="" class="img-fluid rounded-circle shadow-sm">
                            @else
                                <div class="avatar-title bg-primary-subtle text-primary rounded-circle shadow-sm fs-24 fw-bold">
                                    {{ strtoupper(substr($user->first_name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md">
                        <div>
                            <h4 class="fw-bold mb-1">{{ $user->full_name }}</h4>
                            <div class="hstack gap-3 flex-wrap text-muted">
                                <div><i class="ri-mail-line align-bottom me-1"></i> {{ $user->email }}</div>
                                <div class="vr"></div>
                                <div><i class="ri-shield-user-line align-bottom me-1"></i> 
                                    @forelse($user->roles as $role)
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle">{{ $role->name }}</span>
                                    @empty
                                        <span class="text-muted">Aucun rôle</span>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4">
                <i class="ri-check-line me-1 align-middle fs-16"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Main Card with Tabbed Navigation -->
        <div class="card">
            <div class="card-header border-bottom-0">
                <ul class="nav nav-tabs-custom card-header-tabs border-bottom-0" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link fw-semibold" :class="tab === 'edit' ? 'active' : ''" @click.prevent="tab = 'edit'; window.location.hash = ''" href="javascript:void(0);">
                            <i class="ri-edit-box-line align-bottom me-1"></i> Modifier les Droits
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-semibold" :class="tab === 'view' ? 'active' : ''" @click.prevent="tab = 'view'; window.location.hash = 'effective'" href="javascript:void(0);">
                            <i class="ri-eye-line align-bottom me-1"></i> Droits Effectifs
                        </a>
                    </li>
                </ul>
            </div>
            
            <div class="card-body p-4">
                {{-- Onglet : Modifier --}}
                <div x-show="tab === 'edit'" x-transition>
                    <form action="{{ route('admin.users.permissions.update', $user) }}" method="POST">
                        @csrf
                        
                        {{-- Attribution des Rôles --}}
                        <div class="mb-5">
                            <h5 class="fs-15 mb-3"><i class="ri-shield-star-line me-1 align-bottom text-primary"></i> Attribution des Rôles</h5>
                            <div class="row g-3">
                                @foreach($roles as $role)
                                    <div class="col-md-4 col-xl-3">
                                        <div class="p-3 border rounded h-100 transition-all permission-card">
                                            <div class="form-check form-switch form-switch-primary form-switch-md mb-0">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                       name="roles[]" 
                                                       value="{{ $role->name }}" 
                                                       id="role-{{ $role->id }}"
                                                       {{ in_array($role->name, $currentUserRoleNames) ? 'checked' : '' }}>
                                                <label class="form-check-label ms-2 cursor-pointer" for="role-{{ $role->id }}">
                                                    <span class="fs-14 fw-semibold d-block">{{ $role->name }}</span>
                                                    <span class="text-muted fs-11 d-block mt-1">{{ $role->permissions()->count() }} permissions</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Permissions Directes --}}
                        <div class="border-top border-top-dashed pt-4">
                            <div class="d-flex align-items-center mb-3">
                                <h5 class="fs-15 mb-0 flex-grow-1"><i class="ri-key-2-line me-1 align-bottom text-primary"></i> Permissions Directes Spécifiques</h5>
                                <div class="flex-shrink-0">
                                    <button type="submit" class="btn btn-primary btn-sm px-4">
                                        <i class="ri-save-line me-1 align-bottom"></i> Enregistrer
                                    </button>
                                </div>
                            </div>

                            <div class="alert alert-warning border-0 rounded-3 mb-4" role="alert">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <i class="ri-alert-line fs-20 align-middle"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-2">
                                        <h6 class="alert-heading fw-bold mb-1">Note sur les permissions directes</h6>
                                        <p class="mb-0 fs-12 text-muted">
                                            Les permissions directes s'ajoutent à celles des rôles. Elles permettent d'accorder des privilèges exceptionnels à un utilisateur spécifique.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            @php use App\Enums\PermissionEnum; @endphp

                            <div class="accordion custom-accordionwithicon accordion-border-box" id="directPermissionsAccordion">
                                @foreach($groupedPermissions as $resource => $permissions)
                                    <div class="accordion-item shadow-none overflow-hidden" 
                                         x-data="{ 
                                            selected: [{{ collect($permissions)->whereIn('name', $directPermissionNames)->pluck('name')->map(fn($n) => "'$n'")->implode(',') }}],
                                            all: [{{ collect($permissions)->pluck('name')->map(fn($n) => "'$n'")->implode(',') }}],
                                            inherited: [{{ collect($permissions)->whereIn('name', $rolePermissionNames)->pluck('name')->map(fn($n) => "'$n'")->implode(',') }}],
                                            toggleAll() {
                                                if (this.selected.length === this.all.length) {
                                                    this.selected = [];
                                                } else {
                                                    this.selected = [...this.all];
                                                }
                                            }
                                         }">
                                        <h2 class="accordion-header" id="heading-{{ $resource }}">
                                            <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $resource }}" aria-expanded="false" aria-controls="collapse-{{ $resource }}">
                                                <div class="d-flex align-items-center w-100 me-3">
                                                    <span class="text-uppercase letter-spacing-1 fs-12">{{ $resource }}</span>
                                                    <span class="badge bg-primary-subtle text-primary ms-2">{{ count($permissions) }}</span>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="collapse-{{ $resource }}" class="accordion-collapse collapse" aria-labelledby="heading-{{ $resource }}">
                                            <div class="accordion-body border-top border-top-dashed">
                                                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom border-bottom-dashed">
                                                    <span class="text-muted fs-12">Activer les permissions directes pour ce groupe</span>
                                                    <button type="button" @click="toggleAll()" class="btn btn-sm btn-link text-decoration-none p-0 fs-12 text-primary fw-medium">
                                                        <span x-text="selected.length === all.length ? 'Tout désélectionner' : 'Tout sélectionner'"></span>
                                                    </button>
                                                </div>
                                                
                                                <div class="row g-3">
                                                    @foreach($permissions as $permission)
                                                        <div class="col-md-6 col-xl-4">
                                                            <div class="p-3 border rounded h-100 transition-all permission-card position-relative"
                                                                 :class="inherited.includes('{{ $permission->name }}') ? 'opacity-75' : ''">
                                                                <div class="form-check form-switch form-switch-info form-switch-md mb-0">
                                                                    <input class="form-check-input" type="checkbox" role="switch"
                                                                           name="permissions[]" 
                                                                           value="{{ $permission->name }}" 
                                                                           id="perm-{{ $permission->id }}"
                                                                           x-model="selected">
                                                                    <label class="form-check-label ms-2 cursor-pointer" for="perm-{{ $permission->id }}">
                                                                        <span class="fs-13 fw-semibold d-block">
                                                                            {{ PermissionEnum::tryFrom($permission->name)?->label() ?? $permission->name }}
                                                                        </span>
                                                                        <span class="text-muted fs-11 d-block mt-1">{{ $permission->name }}</span>
                                                                    </label>
                                                                </div>
                                                                @if(in_array($permission->name, $rolePermissionNames))
                                                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle position-absolute top-0 end-0 m-2 fs-9">HÉRITÉ</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Footer Actions --}}
                        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top border-top-dashed">
                            <a href="{{ route('admin.users.show', $user) }}" class="btn btn-light">Annuler</a>
                            <button type="submit" class="btn btn-primary">Enregistrer les rôles et permissions</button>
                        </div>
                    </form>
                </div>

                {{-- Onglet : Voir Effectifs --}}
                <div x-show="tab === 'view'" x-transition x-cloak>
                    <div class="row g-4">
                        @foreach($groupedPermissions as $resource => $permissions)
                            @php
                                $effectiveInResource = collect($permissions)->filter(fn($p) => in_array($p->name, $rolePermissionNames) || in_array($p->name, $directPermissionNames));
                            @endphp
                            @if($effectiveInResource->isNotEmpty())
                            <div class="col-12">
                                <div class="d-flex align-items-center mb-3">
                                    <h6 class="mb-0 text-uppercase fs-12 fw-bold text-muted letter-spacing-1">{{ $resource }}</h6>
                                    <div class="flex-grow-1 border-top border-top-dashed ms-3 opacity-25"></div>
                                </div>
                                <div class="row g-2">
                                    @foreach($effectiveInResource as $permission)
                                        @php
                                            $hasViaRole = in_array($permission->name, $rolePermissionNames);
                                            $hasDirect = in_array($permission->name, $directPermissionNames);
                                        @endphp
                                        <div class="col-md-6 col-xl-4">
                                            <div class="p-3 border rounded h-100 d-flex align-items-center">
                                                <i class="ri-checkbox-circle-fill text-success fs-18 me-3"></i>
                                                <div class="flex-grow-1">
                                                    <div class="fw-semibold fs-13">{{ PermissionEnum::tryFrom($permission->name)?->label() ?? $permission->name }}</div>
                                                    <div class="d-flex gap-1 mt-2">
                                                        @if($hasViaRole)
                                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-9">VIA RÔLE</span>
                                                        @endif
                                                        @if($hasDirect)
                                                            <span class="badge bg-info-subtle text-info border border-info-subtle fs-9">DIRECT</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>

<style>
    .letter-spacing-1 { letter-spacing: 1px; }
    .cursor-pointer { cursor: pointer; }
    .permission-card {
        transition: all 0.2s ease-in-out;
    }
    .permission-card:hover {
        border-color: var(--vz-primary) !important;
        box-shadow: var(--vz-box-shadow-sm);
    }
    .fs-9 { font-size: 0.65rem; }
    [x-cloak] { display: none !important; }
</style>
@endsection
