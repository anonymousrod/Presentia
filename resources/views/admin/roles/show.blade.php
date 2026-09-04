@extends('layouts.app')

@section('title', 'Détails du Rôle - ' . $role->name)

@section('content')
<div class="container-fluid p-0">
    <!-- start page title -->
    <div class="row align-items-center mb-3 g-2">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between py-2">
                <h4 class="mb-sm-0 fw-bold fs-18 fs-md-22">Détails du Rôle</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0 fs-12">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">Rôles</a></li>
                        <li class="breadcrumb-item active">Détails</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-12">
            <!-- Hero Card -->
            <div class="card border-0 shadow-sm rounded-3 mb-3 mb-md-4 overflow-hidden" style="background: linear-gradient(135deg, var(--vz-primary) 0%, var(--vz-info) 100%);">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex flex-column flex-xl-row align-items-center justify-content-between gap-3">
                        <div class="d-flex flex-column flex-md-row align-items-center text-center text-md-start gap-3 w-100 w-xl-auto">
                            <div class="avatar-md flex-shrink-0">
                                <div class="avatar-title bg-white rounded-circle text-primary fs-20 shadow-sm">
                                    <i class="ri-shield-user-fill"></i>
                                </div>
                            </div>
                            <div class="text-white">
                                <h3 class="text-white fw-bold mb-1 fs-18 fs-md-24">{{ $role->name }}</h3>
                                <div class="d-flex flex-wrap justify-content-center justify-content-md-start align-items-center gap-2">
                                    @if(in_array($role->name, ['Administrateur', 'Super Admin', 'Jeune', 'Chef de groupe']) || $role->is_system)
                                        <span class="badge bg-light text-primary fs-11 px-2 py-1">RÔLE SYSTÈME</span>
                                    @else
                                        <span class="badge bg-white text-info fs-11 px-2 py-1">RÔLE PERSONNALISÉ</span>
                                    @endif
                                    <span class="fs-12 opacity-75"><i class="ri-calendar-line align-bottom me-1"></i> Créé le {{ $role->created_at->format('d M Y') }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-center justify-content-center gap-3 gap-md-4 text-center text-white bg-white bg-opacity-10 rounded-3 p-2 px-3 shadow-none w-100 w-xl-auto">
                            <div>
                                <h4 class="text-white fw-bold mb-0 fs-18 fs-md-22">{{ $role->permissions()->count() }}</h4>
                                <p class="mb-0 opacity-75 fs-10 text-uppercase letter-spacing-1">Permissions</p>
                            </div>
                            <div class="border-start border-white opacity-25" style="height: 30px;"></div>
                            <div>
                                <h4 class="text-white fw-bold mb-0 fs-18 fs-md-22">{{ $role->users()->count() }}</h4>
                                <p class="mb-0 opacity-75 fs-10 text-uppercase letter-spacing-1">Utilisateurs</p>
                            </div>
                        </div>

                        @unless(in_array($role->name, ['Administrateur', 'Super Admin']) || $role->code === 'admin' || $role->code === 'super_admin')
                        <div class="w-100 w-xl-auto text-center text-xl-end">
                            <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-light btn-sm text-primary fw-bold shadow-none rounded-pill px-3 py-2 w-100 w-md-auto">
                                <i class="ri-pencil-fill align-bottom me-1"></i> Modifier le rôle
                            </a>
                        </div>
                        @endunless
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Section Utilisateurs possédant ce rôle --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header border-0 bg-transparent py-3 px-3 px-md-4 border-bottom border-light-subtle d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <i class="ri-user-follow-line text-info fs-18"></i>
                <h5 class="card-title mb-0 fw-bold fs-14 fs-md-15 text-body">Membres possédant ce rôle</h5>
            </div>
            <span class="badge bg-info-subtle text-info rounded-pill px-2 py-1 fs-11">{{ $role->users()->count() }} membre(s)</span>
        </div>
        <div class="card-body p-3">
            @php $roleUsers = $role->users()->take(12)->get(); @endphp
            @if($roleUsers->count() > 0)
                <div class="row g-2">
                    @foreach($roleUsers as $u)
                        <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                            <div class="d-flex align-items-center gap-2 p-2 border border-light-subtle rounded-3">
                                <div class="flex-shrink-0">
                                    @if($u->photo)
                                        <img src="{{ asset('storage/' . $u->photo) }}" alt="" class="avatar-xs rounded-circle object-cover">
                                    @else
                                        <div class="avatar-xs">
                                            <span class="avatar-title rounded-circle bg-primary-subtle text-primary fw-bold fs-11">
                                                {{ strtoupper(substr($u->first_name, 0, 1)) }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-grow-1 overflow-hidden">
                                    <h6 class="fs-12 mb-0 fw-bold text-body text-truncate">
                                        <a href="{{ route('admin.users.show', $u) }}" class="text-body">{{ $u->full_name }}</a>
                                    </h6>
                                    <span class="text-muted fs-10 d-block text-truncate">{{ $u->email ?? $u->phone }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-muted mb-0 fs-12 text-center py-3">Aucun utilisateur n'est assigné à ce rôle pour le moment.</p>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="mb-0 fw-bold fs-15 text-body"><i class="ri-lock-2-line text-primary me-2 align-middle"></i>Permissions Associées</h5>
                <span class="badge bg-primary-subtle text-primary fs-11 px-2 py-1 rounded-pill">Groupées par module</span>
            </div>
        </div>
    </div>

    @php 
        use App\Enums\PermissionEnum; 
        $resourceTranslations = [
            'member' => 'Membres',
            'group' => 'Groupes',
            'activity' => 'Activités',
            'attendance' => 'Présences',
            'registration' => 'Inscriptions',
            'notification' => 'Notifications',
            'stats' => 'Statistiques',
            'report' => 'Rapports',
            'role' => 'Rôles',
            'permission' => 'Permissions',
            'audit' => 'Audit & Logs',
            'qrcode' => 'Codes QR',
            'finance' => 'Finances'
        ];
    @endphp

    <div class="permission-grid">
        @forelse($groupedPermissions as $resource => $permissions)
            <div class="card shadow-sm border border-light-subtle rounded-3 mb-3 permission-card">
                <div class="card-header bg-primary-subtle border-0 py-2 px-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar-xs me-2 flex-shrink-0">
                            <div class="avatar-title bg-white text-primary rounded fs-14 shadow-none">
                                <i class="ri-stack-fill"></i>
                            </div>
                        </div>
                        <h6 class="card-title mb-0 flex-grow-1 text-uppercase fw-bold text-primary fs-12 letter-spacing-1">{{ $resourceTranslations[$resource] ?? ucfirst($resource) }}</h6>
                        <span class="badge bg-white text-primary fs-11">{{ count($permissions) }}</span>
                    </div>
                </div>
                <div class="card-body p-3">
                    <ul class="list-unstyled mb-0 vstack gap-2">
                        @foreach($permissions as $permission)
                            <li class="d-flex align-items-start">
                                <i class="ri-checkbox-circle-fill text-success fs-16 me-2 flex-shrink-0 mt-1"></i>
                                <span class="fs-12 fw-medium text-body text-wrap">{{ PermissionEnum::tryFrom($permission->name)?->label() ?? $permission->name }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @empty
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body text-center py-5">
                    <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop" colors="primary:#405189,secondary:#0ab39c" style="width:90px;height:90px"></lord-icon>
                    <h5 class="mt-3 text-muted fw-bold fs-14">Aucune permission associée à ce rôle.</h5>
                    <p class="text-muted mb-0 fs-12">Modifiez ce rôle pour lui accorder des accès.</p>
                </div>
            </div>
        @endforelse
    </div>
</div>

<style>
    .letter-spacing-1 { letter-spacing: 1px; }
    
    .permission-grid {
        column-count: 1;
        column-gap: 1rem;
    }
    .permission-card {
        break-inside: avoid;
        display: inline-block;
        width: 100%;
    }
    
    @media (min-width: 768px) {
        .permission-grid {
            column-count: 2;
        }
    }
    @media (min-width: 1200px) {
        .permission-grid {
            column-count: 3;
        }
    }
    @media (min-width: 1600px) {
        .permission-grid {
            column-count: 4;
        }
    }
</style>
@endsection
