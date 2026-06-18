@extends('layouts.app')

@section('title', 'Créer un Rôle')

@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Création de Rôle</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">Rôles</a></li>
                        <li class="breadcrumb-item active">Nouveau</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row justify-content-center">
        <div class="col-xxl-8 col-lg-10">
            <div class="card mt-2">
                <div class="card-header align-items-center d-flex border-bottom-dashed">
                    <h4 class="card-title mb-0 flex-grow-1">Configuration du Nouveau Rôle</h4>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('admin.roles.store') }}" method="POST">
                        @csrf

                        <!-- Nom du Rôle -->
                        <div class="mb-4">
                            <label for="name" class="form-label fw-bold">Nom du rôle <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ri-shield-user-line"></i></span>
                                <input type="text" name="name" id="name" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       placeholder="ex: Modérateur, Comptable..."
                                       value="{{ old('name') }}" required>
                            </div>
                            @error('name')
                                <div class="text-danger mt-1 fs-12">{{ $message }}</div>
                            @enderror
                            <div class="form-text text-muted">Le nom doit être unique. Il définit l'identité du rôle.</div>
                        </div>

                        <!-- Attribution des Permissions -->
                        <div class="border-top border-top-dashed pt-4 mt-4">
                            <h5 class="fs-15 mb-3">Attribution des Permissions <span class="badge bg-info-subtle text-info fs-11 ms-2">Groupées par ressource</span></h5>
                            
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

                            <div class="accordion custom-accordionwithicon accordion-border-box" id="permissionsAccordion">
                                @foreach($groupedPermissions as $resource => $permissions)
                                    <div class="accordion-item shadow-none overflow-hidden" 
                                         x-data="{ 
                                            selected: [{{ collect(old('permissions', []))->intersect($permissions->pluck('name'))->map(fn($n) => "'$n'")->implode(',') }}],
                                            all: [{{ collect($permissions)->pluck('name')->map(fn($n) => "'$n'")->implode(',') }}],
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
                                                    <span class="text-uppercase letter-spacing-1 fs-12">{{ $resourceTranslations[$resource] ?? ucfirst($resource) }}</span>
                                                    <span class="badge bg-primary-subtle text-primary ms-2">{{ $permissions->count() }}</span>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="collapse-{{ $resource }}" class="accordion-collapse collapse" aria-labelledby="heading-{{ $resource }}">
                                            <div class="accordion-body border-top border-top-dashed">
                                                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom border-bottom-dashed">
                                                    <span class="text-muted fs-12">Activer les droits d'accès pour ce groupe</span>
                                                    <button type="button" @click="toggleAll()" class="btn btn-sm btn-link text-decoration-none p-0 fs-12 text-primary fw-medium">
                                                        <span x-text="selected.length === all.length ? 'Tout désélectionner' : 'Tout sélectionner'"></span>
                                                    </button>
                                                </div>
                                                
                                                <div class="row g-3">
                                                    @foreach($permissions as $permission)
                                                        <div class="col-md-6">
                                                            <div class="p-3 border rounded h-100 transition-all permission-card">
                                                                <div class="form-check form-switch form-switch-success form-switch-md mb-0">
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

                        <!-- Actions -->
                        <div class="d-flex justify-content-end gap-2 mt-4 pt-2 border-top border-top-dashed">
                            <a href="{{ route('admin.roles.index') }}" class="btn btn-light">Annuler</a>
                            <button type="submit" class="btn btn-success">
                                <span class="d-flex align-items-center">
                                    <i class="ri-check-line me-1"></i> Créer le rôle
                                </span>
                            </button>
                        </div>
                    </form>
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
        [x-cloak] { display: none !important; }
    </style>
@endsection
