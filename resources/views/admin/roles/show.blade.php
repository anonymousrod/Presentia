@extends('layouts.app')

@section('title', 'Détails du Rôle')

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

<div class="row justify-content-center">
    <div class="col-xxl-10 col-lg-12">
        <div class="row">
            <!-- Colonne gauche: Fiche d'informations -->
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-4">
                            <div class="flex-shrink-0">
                                <div class="avatar-md">
                                    <div class="avatar-title rounded-circle bg-primary-subtle text-primary fs-24 shadow-sm">
                                        <i class="ri-shield-user-fill"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h4 class="mb-1 fw-bold">{{ $role->name }}</h4>
                                @if(in_array($role->name, ['Administrateur', 'Jeune', 'Chef de groupe']))
                                    <span class="badge bg-secondary-subtle text-secondary fs-11">RÔLE SYSTÈME</span>
                                @else
                                    <span class="badge bg-info-subtle text-info fs-11">RÔLE PERSONNALISÉ</span>
                                @endif
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-6">
                                <div class="p-3 border border-dashed rounded text-center h-100">
                                    <h5 class="text-primary mb-1 fw-bold">{{ $role->permissions()->count() }}</h5>
                                    <p class="text-muted mb-0 fs-12">Permissions</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 border border-dashed rounded text-center h-100">
                                    <h5 class="text-primary mb-1 fw-bold">{{ $role->users()->count() }}</h5>
                                    <p class="text-muted mb-0 fs-12">Utilisateurs</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 pt-2 border-top border-top-dashed">
                            <p class="text-muted mb-4 small italic">
                                Ce rôle est utilisé pour définir les droits de base. Les changements sont appliqués en temps réel.
                            </p>
                            @unless(in_array($role->name, ['Administrateur']))
                            <div class="d-grid">
                                <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-primary">
                                    <i class="ri-pencil-line me-1 align-bottom"></i> Modifier le rôle
                                </a>
                            </div>
                            @endunless
                        </div>
                    </div>
                </div>
            </div>

            <!-- Colonne droite: Liste des permissions -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header border-bottom-dashed">
                        <h5 class="card-title mb-0">Permissions Associées</h5>
                    </div>
                    <div class="card-body">
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
                                'qrcode' => 'Codes QR'
                            ];
                        @endphp

                        <div class="row g-4">
                            @forelse($groupedPermissions as $resource => $permissions)
                                <div class="col-12">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="avatar-xs me-2">
                                            <div class="avatar-title rounded-circle bg-primary-subtle text-primary fs-14">
                                                <i class="ri-stack-line"></i>
                                            </div>
                                        </div>
                                        <h6 class="mb-0 text-uppercase fw-bold text-muted small letter-spacing-1">{{ $resourceTranslations[$resource] ?? ucfirst($resource) }}</h6>
                                        <div class="flex-grow-1 border-top border-top-dashed ms-3 opacity-25"></div>
                                    </div>
                                    
                                    <div class="row g-2">
                                        @foreach($permissions as $permission)
                                            <div class="col-md-6 col-xl-4">
                                                <div class="d-flex align-items-center p-3 rounded border h-100 shadow-none">
                                                    <i class="ri-checkbox-circle-fill text-success fs-16 me-2"></i>
                                                    <div class="overflow-hidden">
                                                        <div class="fw-semibold fs-13 text-truncate">{{ PermissionEnum::tryFrom($permission->name)?->label() ?? $permission->name }}</div>
                                                        <div class="text-muted fs-11 text-truncate mt-1">{{ $permission->name }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-5">
                                    <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop" colors="primary:#405189,secondary:#0ab39c" style="width:75px;height:75px"></lord-icon>
                                    <h5 class="mt-2 text-muted">Aucune permission associée.</h5>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .letter-spacing-1 { letter-spacing: 1px; }
</style>
@endsection
