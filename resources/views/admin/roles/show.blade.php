@extends('layouts.app')

@section('title', 'Détails du Rôle - ' . $role->name)

@section('content')
<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Détails du Rôle</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
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
        <div class="card profile-project-card mb-4 shadow-lg border-0 overflow-hidden" style="background: linear-gradient(135deg, var(--vz-primary) 0%, var(--vz-info) 100%);">
            <div class="card-body p-4 p-md-5">
                <div class="d-flex flex-column flex-xl-row align-items-center justify-content-between gap-4">
                    <div class="d-flex flex-column flex-md-row align-items-center text-center text-md-start gap-4">
                        <div class="avatar-lg">
                            <div class="avatar-title bg-white rounded-circle text-primary fs-3 shadow-lg">
                                <i class="ri-shield-user-fill"></i>
                            </div>
                        </div>
                        <div class="text-white">
                            <h2 class="text-white fw-bolder mb-2 display-6">{{ $role->name }}</h2>
                            <div class="d-flex flex-wrap justify-content-center justify-content-md-start align-items-center gap-3">
                                @if(in_array($role->name, ['Administrateur', 'Jeune', 'Chef de groupe']))
                                    <span class="badge bg-light text-primary fs-12 px-3 py-1 shadow-sm">RÔLE SYSTÈME</span>
                                @else
                                    <span class="badge bg-white text-info fs-12 px-3 py-1 shadow-sm">RÔLE PERSONNALISÉ</span>
                                @endif
                                <span class="fs-14 opacity-75"><i class="ri-calendar-line align-bottom me-1"></i> Créé le {{ $role->created_at->format('d M Y') }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex flex-wrap justify-content-center gap-4 gap-md-5 text-center text-white bg-white bg-opacity-10 rounded-4 p-3 px-4 shadow-sm">
                        <div>
                            <h3 class="text-white fw-bold mb-1 display-6">{{ $role->permissions()->count() }}</h3>
                            <p class="mb-0 opacity-75 fs-12 text-uppercase letter-spacing-1">Permissions</p>
                        </div>
                        <div class="border-start border-white opacity-25"></div>
                        <div>
                            <h3 class="text-white fw-bold mb-1 display-6">{{ $role->users()->count() }}</h3>
                            <p class="mb-0 opacity-75 fs-12 text-uppercase letter-spacing-1">Utilisateurs</p>
                        </div>
                    </div>

                    @unless(in_array($role->name, ['Administrateur']))
                    <div class="mt-3 mt-xl-0">
                        <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-light btn-lg text-primary fw-bold shadow-sm px-4 rounded-pill">
                            <i class="ri-pencil-fill align-bottom me-1"></i> Modifier le rôle
                        </a>
                    </div>
                    @endunless
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="d-flex align-items-center mb-4 mt-2">
            <h5 class="mb-0 fw-bold flex-grow-1"><i class="ri-lock-2-line text-primary me-2 align-bottom fs-4"></i>Permissions Associées</h5>
            <div class="flex-shrink-0">
                <span class="badge bg-primary-subtle text-primary fs-12 px-3 py-2 rounded-pill">Groupées par module</span>
            </div>
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

<!-- Using CSS Columns for Masonry-like layout without JS -->
<div class="permission-grid">
    @forelse($groupedPermissions as $resource => $permissions)
        <div class="card shadow-sm border-0 mb-4 card-animate permission-card">
            <div class="card-header bg-primary-subtle border-0 py-3">
                <div class="d-flex align-items-center">
                    <div class="avatar-xs me-3">
                        <div class="avatar-title bg-white text-primary rounded shadow-sm fs-16">
                            <i class="ri-stack-fill"></i>
                        </div>
                    </div>
                    <h6 class="card-title mb-0 flex-grow-1 text-uppercase fw-bold text-primary letter-spacing-1">{{ $resourceTranslations[$resource] ?? ucfirst($resource) }}</h6>
                </div>
            </div>
            <div class="card-body pt-3 pb-2 px-3">
                <ul class="list-unstyled mb-0 vstack gap-3">
                    @foreach($permissions as $permission)
                        <li class="d-flex align-items-start">
                            <i class="ri-checkbox-circle-fill text-success fs-18 me-2 mt-n1"></i>
                            <span class="fs-13 fw-medium text-body text-wrap">{{ PermissionEnum::tryFrom($permission->name)?->label() ?? $permission->name }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop" colors="primary:#405189,secondary:#0ab39c" style="width:100px;height:100px"></lord-icon>
                    <h5 class="mt-3 text-muted fw-bold">Aucune permission associée à ce rôle.</h5>
                    <p class="text-muted mb-0">Modifiez ce rôle pour lui accorder des accès.</p>
                </div>
            </div>
        </div>
    @endforelse
</div>

<style>
    .letter-spacing-1 { letter-spacing: 1px; }
    
    /* CSS Columns for automatic masonry flow */
    .permission-grid {
        column-count: 1;
        column-gap: 1.5rem;
    }
    .permission-card {
        break-inside: avoid; /* Prevent card splitting across columns */
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
