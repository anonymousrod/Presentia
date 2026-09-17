@extends('layouts.app')

@section('title', 'Profil & Paramètres')

@section('content')
@php
    $showPasswordTab = (isset($errors) && ($errors->has('current_password') || $errors->has('password'))) || session('status') === 'password-updated';
    $activeTab = request('tab');
    if (!$activeTab) {
        $activeTab = $showPasswordTab ? 'changePassword' : 'personalDetails';
    }
@endphp
<style>
    /* Responsive Profile Layout & Mobile Quick-Nav */
    @media (max-width: 1399.98px) {
        .profile-tabs-column {
            order: 1 !important;
        }
        .profile-sidebar-column {
            order: 2 !important;
        }
    }
    @media (min-width: 1400px) {
        .profile-sidebar-column {
            order: 1 !important;
        }
        .profile-tabs-column {
            order: 2 !important;
        }
    }

    /* Mobile Quick-Nav Pills */
    .profile-mobile-nav {
        scrollbar-width: none;
        -ms-overflow-style: none;
        white-space: nowrap;
        -webkit-overflow-scrolling: touch;
    }
    .profile-mobile-nav::-webkit-scrollbar {
        display: none;
    }
    .profile-nav-pill {
        border-radius: 12px;
        padding: 0.55rem 0.95rem;
        font-weight: 600;
        font-size: 0.83rem;
        color: #4B5563;
        background: #F3F4F6;
        border: 1px solid transparent;
        display: inline-flex;
        align-items: center;
        flex-shrink: 0;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none;
    }
    .profile-nav-pill:hover {
        background: #E5E7EB;
        color: #111827;
    }
    .profile-nav-pill.active {
        background: linear-gradient(135deg, #4F46E5, #4338CA) !important;
        color: #ffffff !important;
        border-color: transparent !important;
        box-shadow: 0 4px 14px rgba(79, 70, 229, 0.35) !important;
    }
    .profile-nav-pill.active .badge {
        background: rgba(255, 255, 255, 0.25) !important;
        color: #ffffff !important;
    }
    .card-header-tabs {
        flex-wrap: nowrap !important;
        overflow-x: auto !important;
        scrollbar-width: none;
        -webkit-overflow-scrolling: touch;
    }
    .card-header-tabs::-webkit-scrollbar {
        display: none;
    }
    .card-header-tabs .nav-link {
        white-space: nowrap;
        font-size: 0.88rem;
        padding: 0.75rem 1rem;
    }

    /* Collapsible Sidebar Ribbon Cards */
    .profile-collapse-trigger {
        cursor: pointer;
        user-select: none;
        min-height: 28px;
        transition: all 0.2s ease;
    }
    .profile-collapse-trigger .ribbon {
        cursor: pointer;
        transition: transform 0.2s ease, filter 0.2s ease, box-shadow 0.2s ease;
    }
    .profile-collapse-trigger:hover .ribbon {
        filter: brightness(1.08);
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.12);
    }
    .profile-collapse-trigger .collapse-chevron {
        transition: transform 0.28s cubic-bezier(0.4, 0, 0.2, 1), background-color 0.2s ease, color 0.2s ease;
    }
    .profile-collapse-trigger[aria-expanded="true"] .collapse-chevron {
        transform: rotate(180deg);
        background-color: #E5E7EB !important;
        color: #111827 !important;
    }
    .profile-collapse-trigger:hover .collapse-chevron {
        background-color: #E5E7EB !important;
        color: #111827 !important;
    }
</style>

{{-- Bannière de couverture avec action intégrée en haut à droite --}}
<div class="profile-foreground position-relative mx-n4 mt-n4">
    <div class="profile-wid-bg">
        @if($user->cover_photo)
            <img src="{{ asset('storage/' . $user->cover_photo) }}" alt="cover-img" class="profile-wid-img" />
        @else
            <img src="{{ asset('assets/images/profile-bg.jpg') }}" alt="cover-img" class="profile-wid-img" />
        @endif
    </div>
    
    {{-- Bouton discret & élégant de modification de la couverture --}}
    <div class="position-absolute top-0 end-0 p-3 z-3">
        <form id="cover-form" action="{{ route('profile.cover') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input id="profile-foreground-img-file-input" name="cover_photo" type="file" class="d-none" onchange="document.getElementById('cover-form').submit();">
            <label for="profile-foreground-img-file-input" class="btn-profile-cover-action shadow-sm mb-0">
                <i class="ri-camera-lens-line align-middle me-1"></i>
                <span class="d-none d-sm-inline">Modifier la couverture</span>
            </label>
        </form>
    </div>
</div>

{{-- En-tête profil compact & ultra-responsive --}}
<div class="pt-3 pt-md-4 mb-2 mb-md-3 profile-wrapper position-relative">
    <div class="row g-2 g-md-4 align-items-center">
        <div class="col-auto">
            <div class="avatar-lg profile-avatar-box">
                <img src="{{ $user->avatar_url }}" alt="user-img" class="rounded-circle profile-header-avatar w-100 h-100" />
            </div>
        </div>
        <div class="col min-w-0">
            <div class="p-1 p-md-2">
                <h3 class="text-white mb-1 fs-18 fs-md-22 fw-bold text-truncate">{{ $user->full_name }}</h3>
                <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                    <span class="badge bg-{{ match($user->status->value) {
                        'ACTIVE' => 'success',
                        'PENDING' => 'warning',
                        'INACTIVE' => 'secondary',
                        'SUSPENDED' => 'danger',
                        default => 'primary'
                    } }} fs-11 px-2 py-1">
                        {{ $user->status->label() }}
                    </span>
                    <span class="text-white text-opacity-75 fs-12 fw-medium">{{ $user->roles->first()?->name ?? 'Membre' }}</span>
                </div>
                <div class="d-flex flex-wrap align-items-center gap-2 gap-md-3 text-white-50 fs-12 mt-1">
                    @if($user->email)
                        <div class="text-truncate text-white text-opacity-75" style="max-width: 240px;" title="{{ $user->email }}">
                            <i class="ri-mail-line me-1 text-white text-opacity-75 fs-14 align-middle"></i>{{ $user->email }}
                        </div>
                    @endif
                    @if($user->phone)
                        <div class="text-white text-opacity-75">
                            <i class="ri-phone-line me-1 text-white text-opacity-75 fs-14 align-middle"></i>{{ $user->phone }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <!--end col-->

        {{-- Barre de statistiques compacte (glassmorphism sur mobile, intégrée sur desktop) --}}
        <div class="col-12 col-lg-auto order-last order-lg-0 mt-2 mt-lg-0">
            <div class="profile-stats-bar d-flex align-items-center justify-content-around py-1 px-2 rounded-3">
                <a class="stat-item text-center flex-fill px-2 px-md-3 py-1 text-decoration-none" data-bs-toggle="tab" href="#groups" role="tab" style="cursor: pointer;">
                    <span class="d-block fw-bold text-white fs-15 fs-md-18 mb-0 lh-1">{{ $user->groups()->count() }}</span>
                    <span class="d-block text-white text-opacity-75 text-uppercase fw-semibold stat-label mt-1">Groupes</span>
                </a>
                <div class="stat-divider"></div>
                <div class="stat-item text-center flex-fill px-2 px-md-3 py-1">
                    <span class="d-block fw-bold text-white fs-15 fs-md-18 mb-0 lh-1">{{ $user->registrations()->count() }}</span>
                    <span class="d-block text-white text-opacity-75 text-uppercase fw-semibold stat-label mt-1">Inscriptions</span>
                </div>
                <div class="stat-divider"></div>
                <a class="stat-item text-center flex-fill px-2 px-md-3 py-1 text-decoration-none" data-bs-toggle="tab" href="#activities" role="tab" style="cursor: pointer;">
                    <span class="d-block fw-bold text-white fs-15 fs-md-18 mb-0 lh-1">{{ $user->attendances()->count() }}</span>
                    <span class="d-block text-white text-opacity-75 text-uppercase fw-semibold stat-label mt-1">Présences</span>
                </a>
            </div>
        </div>
        <!--end col-->
    </div>
    <!--end row-->
</div>

{{-- Barre de navigation mobile rapide en 1 clic --}}
<div class="d-xxl-none mb-3 profile-mobile-nav-container">
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-0 bg-white">
        <div class="card-body p-2">
            <div class="profile-mobile-nav d-flex gap-2 overflow-x-auto pb-1" role="tablist">
                <a class="profile-nav-pill {{ $activeTab === 'personalDetails' ? 'active' : '' }}" data-bs-toggle="tab" href="#personalDetails" role="tab">
                    <i class="ri-user-3-line me-1 fs-15 align-middle"></i>
                    <span>Infos</span>
                </a>
                <a class="profile-nav-pill {{ $activeTab === 'groups' ? 'active' : '' }}" data-bs-toggle="tab" href="#groups" role="tab">
                    <i class="ri-team-line me-1 fs-15 align-middle"></i>
                    <span>Groupes</span>
                    <span class="badge bg-secondary-subtle text-secondary rounded-pill ms-1">{{ $user->groups()->count() }}</span>
                </a>
                <a class="profile-nav-pill {{ $activeTab === 'activities' ? 'active' : '' }}" data-bs-toggle="tab" href="#activities" role="tab">
                    <i class="ri-calendar-check-line me-1 fs-15 align-middle"></i>
                    <span>Activités</span>
                    <span class="badge bg-secondary-subtle text-secondary rounded-pill ms-1">{{ $user->attendances()->count() }}</span>
                </a>
                <a class="profile-nav-pill {{ $activeTab === 'deviceBadge' ? 'active' : '' }}" data-bs-toggle="tab" href="#deviceBadge" role="tab">
                    <i class="ri-smartphone-line me-1 fs-15 align-middle"></i>
                    <span>Appareil-Badge</span>
                    @if($currentEnrolledDevice)
                        <span class="badge bg-success rounded-circle p-1 ms-1" style="width: 7px; height: 7px; display: inline-block;"></span>
                    @endif
                </a>
                <a class="profile-nav-pill {{ $activeTab === 'changePassword' ? 'active' : '' }}" data-bs-toggle="tab" href="#changePassword" role="tab">
                    <i class="ri-lock-password-line me-1 fs-15 align-middle"></i>
                    <span>Mot de passe</span>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row pt-1 pt-md-2">
    <div class="col-12 col-xxl-3 profile-sidebar-column">
        <div class="card">
            <div class="card-body p-4">
                <div class="text-center">
                    <form id="avatar-form" action="{{ route('profile.avatar') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="profile-user position-relative d-inline-block mx-auto mb-4">
                            <img src="{{ $user->avatar_url }}" class="rounded-circle avatar-xl img-thumbnail user-profile-image shadow" alt="user-profile-image" style="object-fit: cover;">
                            <div class="avatar-xs p-0 rounded-circle profile-photo-edit">
                                <input id="profile-img-file-input" name="photo" type="file" class="profile-img-file-input d-none" onchange="document.getElementById('avatar-form').submit();">
                                <label for="profile-img-file-input" class="profile-photo-edit avatar-xs">
                                    <span class="avatar-title rounded-circle bg-light text-body shadow">
                                        <i class="ri-camera-fill"></i>
                                    </span>
                                </label>
                            </div>
                        </div>
                    </form>
                    <h5 class="fs-16 mb-1">{{ $user->full_name }}</h5>
                    <p class="text-muted mb-0">{{ $user->roles->first()?->name ?? 'Membre' }}</p>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center mb-4">
                    <div class="flex-grow-1">
                        <h5 class="card-title mb-0">Complétez votre profil</h5>
                    </div>
                </div>
                <div class="progress animated-progress custom-progress progress-label">
                    <div class="progress-bar {{ $completionPercentage == 100 ? 'bg-success' : 'bg-primary' }}" role="progressbar" style="width: {{ $completionPercentage }}%" aria-valuenow="{{ $completionPercentage }}" aria-valuemin="0" aria-valuemax="100">
                        <div class="label">{{ $completionPercentage }}%</div>
                    </div>
                </div>
                <p class="text-muted mt-2 mb-0 fs-13">Complétez toutes vos informations pour atteindre 100%.</p>
            </div>
        </div>

        <!-- Card: Finances & Rôles -->
        <div class="card ribbon-box border shadow-none mb-3">
            <div class="card-body">
                <div class="profile-collapse-trigger d-flex align-items-center justify-content-between" 
                     data-bs-toggle="collapse" 
                     href="#collapseFinances" 
                     role="button" 
                     aria-expanded="false" 
                     aria-controls="collapseFinances"
                     title="Cliquer pour afficher / masquer">
                    <div class="ribbon ribbon-warning round-shape">
                        <i class="ri-money-dollar-circle-line me-1 align-middle"></i>Finances & Rôles
                    </div>
                    <span class="collapse-chevron badge bg-light text-muted rounded-circle p-1 d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 26px; height: 26px; margin-left: auto;">
                        <i class="ri-arrow-down-s-line fs-16"></i>
                    </span>
                </div>
                <div class="collapse" id="collapseFinances">
                    <div class="mt-4 pt-2">
                        <ul class="list-unstyled vstack gap-3 mb-0">
                            <li>
                                <div class="d-flex">
                                    <div class="flex-shrink-0 avatar-xs">
                                        <div class="avatar-title rounded bg-warning-subtle text-warning">
                                            <i class="ri-money-dollar-circle-line"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1 fs-14">Cotisation hebdomadaire</h6>
                                        <p class="text-muted mb-0">{{ $user->weekly_contribution ? number_format($user->weekly_contribution, 0, ',', ' ') . ' FCFA' : 'Non renseignée' }}</p>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="d-flex">
                                    <div class="flex-shrink-0 avatar-xs">
                                        <div class="avatar-title rounded bg-success-subtle text-success">
                                            <i class="ri-wallet-3-line"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1 fs-14">Avancement des cotisations</h6>
                                        <p class="text-muted mb-1">
                                            <strong>{{ number_format($paidContribution, 0, ',', ' ') }} FCFA</strong> / {{ number_format($expectedContribution, 0, ',', ' ') }} FCFA
                                        </p>
                                        @php
                                            $percent = $expectedContribution > 0 ? min(100, round(($paidContribution / $expectedContribution) * 100)) : 0;
                                        @endphp
                                        <div class="progress animated-progress" style="height: 6px;">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percent }}%" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="d-flex">
                                    <div class="flex-shrink-0 avatar-xs">
                                        <div class="avatar-title rounded bg-info-subtle text-info">
                                            <i class="ri-shield-user-line"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1 fs-14">Rôles attribués</h6>
                                        <div class="d-flex flex-wrap gap-1 mt-1">
                                            @forelse($user->getRoleNames() as $role)
                                                <span class="badge bg-info-subtle text-info">{{ $role }}</span>
                                            @empty
                                                <span class="text-muted">Aucun rôle</span>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card: Académique & Pro -->
        <div class="card ribbon-box border shadow-none mb-3">
            <div class="card-body">
                <div class="profile-collapse-trigger d-flex align-items-center justify-content-between" 
                     data-bs-toggle="collapse" 
                     href="#collapseAcademique" 
                     role="button" 
                     aria-expanded="false" 
                     aria-controls="collapseAcademique"
                     title="Cliquer pour afficher / masquer">
                    <div class="ribbon ribbon-success round-shape">
                        <i class="ri-graduation-cap-line me-1 align-middle"></i>Académique & Pro
                    </div>
                    <span class="collapse-chevron badge bg-light text-muted rounded-circle p-1 d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 26px; height: 26px; margin-left: auto;">
                        <i class="ri-arrow-down-s-line fs-16"></i>
                    </span>
                </div>
                <div class="collapse" id="collapseAcademique">
                    <div class="mt-4 pt-2">
                        <ul class="list-unstyled vstack gap-2 mb-0">
                            <li>
                                <div class="d-flex">
                                    <div class="flex-shrink-0 avatar-xs">
                                        <div class="avatar-title rounded bg-secondary-subtle text-secondary">
                                            <i class="ri-book-open-line"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1 fs-14">Niveau d'études</h6>
                                        <p class="text-muted mb-0">{{ $user->education_level ?? 'Non renseigné' }}</p>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="d-flex">
                                    <div class="flex-shrink-0 avatar-xs">
                                        <div class="avatar-title rounded bg-secondary-subtle text-secondary">
                                            <i class="ri-microscope-line"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1 fs-14">Domaine d'études</h6>
                                        <p class="text-muted mb-0">{{ $user->education_field ?? 'Non renseigné' }}</p>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="d-flex">
                                    <div class="flex-shrink-0 avatar-xs">
                                        <div class="avatar-title rounded bg-warning-subtle text-warning">
                                            <i class="ri-briefcase-line"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1 fs-14">Statut professionnel</h6>
                                        <p class="text-muted mb-0">{{ $user->professional_status ?? 'Non renseigné' }}</p>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="d-flex">
                                    <div class="flex-shrink-0 avatar-xs">
                                        <div class="avatar-title rounded bg-warning-subtle text-warning">
                                            <i class="ri-building-4-line"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1 fs-14">Profession actuelle</h6>
                                        <p class="text-muted mb-0">{{ $user->current_profession ?? 'Non renseigné' }}</p>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="d-flex">
                                    <div class="flex-shrink-0 avatar-xs">
                                        <div class="avatar-title rounded bg-primary-subtle text-primary">
                                            <i class="ri-hand-heart-line"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1 fs-14">Service à l'église</h6>
                                        <p class="text-muted mb-0">{{ $user->church_service ?? 'Non renseigné' }}</p>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        @php
            $additionalInfos = is_string($user->additional_info) ? json_decode($user->additional_info, true) : $user->additional_info;
        @endphp
        @if(!empty($additionalInfos) && is_array($additionalInfos))
        <!-- Card: Informations Complémentaires -->
        <div class="card ribbon-box border shadow-none mb-3">
            <div class="card-body">
                <div class="profile-collapse-trigger d-flex align-items-center justify-content-between" 
                     data-bs-toggle="collapse" 
                     href="#collapseNotes" 
                     role="button" 
                     aria-expanded="false" 
                     aria-controls="collapseNotes"
                     title="Cliquer pour afficher / masquer">
                    <div class="ribbon ribbon-dark round-shape">
                        <i class="ri-file-text-line me-1 align-middle"></i>Notes & Remarques
                    </div>
                    <span class="collapse-chevron badge bg-light text-muted rounded-circle p-1 d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 26px; height: 26px; margin-left: auto;">
                        <i class="ri-arrow-down-s-line fs-16"></i>
                    </span>
                </div>
                <div class="collapse" id="collapseNotes">
                    <div class="mt-4 pt-2">
                        <ul class="list-unstyled vstack gap-2 mb-0">
                            @foreach($additionalInfos as $info)
                                @if(is_array($info))
                                <li>
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 avatar-xs">
                                            <div class="avatar-title rounded bg-info-subtle text-info">
                                                <i class="ri-information-line"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-1 fs-14">{{ $info['title'] ?? 'Info' }}</h6>
                                            <p class="text-muted mb-0">{{ $info['value'] ?? '' }}</p>
                                        </div>
                                    </div>
                                </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
    <!--end col-->
    
    <div class="col-12 col-xxl-9 profile-tabs-column" id="profile-main-tabs-col">
        <div class="card" id="profile-main-tabs-card">
            <div class="card-header d-none d-xxl-block">
                <ul class="nav nav-tabs-custom rounded card-header-tabs border-bottom-0 flex-nowrap overflow-x-auto" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link {{ $activeTab === 'personalDetails' ? 'active' : '' }}" data-bs-toggle="tab" href="#personalDetails" role="tab">
                            <i class="fas fa-home"></i> Informations Personnelles
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $activeTab === 'groups' ? 'active' : '' }}" data-bs-toggle="tab" href="#groups" role="tab">
                            <i class="fas fa-users"></i> Mes Groupes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $activeTab === 'activities' ? 'active' : '' }}" data-bs-toggle="tab" href="#activities" role="tab">
                            <i class="fas fa-calendar-check"></i> Mes Activités
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $activeTab === 'deviceBadge' ? 'active' : '' }}" data-bs-toggle="tab" href="#deviceBadge" role="tab">
                            <i class="ri-smartphone-line align-middle me-1"></i> Appareil-Badge
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $activeTab === 'changePassword' ? 'active' : '' }}" data-bs-toggle="tab" href="#changePassword" role="tab">
                            <i class="far fa-user"></i> Changer le mot de passe
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body p-3 p-md-4">
                <div class="tab-content">
                    <div class="tab-pane {{ $activeTab === 'personalDetails' ? 'active show' : '' }}" id="personalDetails" role="tabpanel">
                        @if (session('status') === 'profile-updated')
                            <div class="alert alert-success">
                                Profil mis à jour avec succès.
                            </div>
                        @endif

                        @if (session('status') === 'avatar-updated')
                            <div class="alert alert-success">
                                Photo de profil mise à jour avec succès.
                            </div>
                        @endif

                        @if (session('status') === 'cover-updated')
                            <div class="alert alert-success">
                                Photo de couverture mise à jour avec succès.
                            </div>
                        @endif

                        <form action="{{ route('profile.update') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="first_name" class="form-label">Prénom</label>
                                        <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name" placeholder="Votre prénom" value="{{ old('first_name', $user->first_name) }}" required>
                                        @error('first_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Nom</label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="Votre nom" value="{{ old('name', $user->name) }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Adresse Email</label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="Votre email" value="{{ old('email', $user->email) }}" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Numéro de Téléphone</label>
                                        <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" placeholder="Votre numéro de téléphone" value="{{ old('phone', $user->phone) }}">
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="birth_date" class="form-label">Date de Naissance</label>
                                        <input type="date" class="form-control @error('birth_date') is-invalid @enderror" id="birth_date" name="birth_date" value="{{ old('birth_date', $user->birth_date?->format('Y-m-d')) }}">
                                        @error('birth_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12 mt-3 mb-2">
                                    <h6 class="fw-bold">Informations Démographiques et Professionnelles</h6>
                                    <hr class="mt-1 mb-3">
                                </div>

                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="education_level" class="form-label">Niveau d'études</label>
                                        <input type="text" class="form-control @error('education_level') is-invalid @enderror" id="education_level" name="education_level" placeholder="Ex: Licence, Master, etc." value="{{ old('education_level', $user->education_level) }}">
                                        @error('education_level')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="education_field" class="form-label">Formation ou domaine d’études</label>
                                        <input type="text" class="form-control @error('education_field') is-invalid @enderror" id="education_field" name="education_field" placeholder="Ex: Informatique, Droit, etc." value="{{ old('education_field', $user->education_field) }}">
                                        @error('education_field')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="professional_status" class="form-label">Situation professionnelle actuelle</label>
                                        <input type="text" class="form-control @error('professional_status') is-invalid @enderror" id="professional_status" name="professional_status" placeholder="Ex: Étudiant, Employé, Sans emploi" value="{{ old('professional_status', $user->professional_status) }}">
                                        @error('professional_status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="current_profession" class="form-label">Profession actuelle</label>
                                        <input type="text" class="form-control @error('current_profession') is-invalid @enderror" id="current_profession" name="current_profession" placeholder="Ex: Développeur, Comptable" value="{{ old('current_profession', $user->current_profession) }}">
                                        @error('current_profession')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="residence_municipality" class="form-label">Commune de résidence</label>
                                        <input type="text" class="form-control @error('residence_municipality') is-invalid @enderror" id="residence_municipality" name="residence_municipality" placeholder="Commune" value="{{ old('residence_municipality', $user->residence_municipality) }}">
                                        @error('residence_municipality')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="residence_neighborhood" class="form-label">Quartier de résidence</label>
                                        <input type="text" class="form-control @error('residence_neighborhood') is-invalid @enderror" id="residence_neighborhood" name="residence_neighborhood" placeholder="Quartier" value="{{ old('residence_neighborhood', $user->residence_neighborhood) }}">
                                        @error('residence_neighborhood')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <label for="church_service" class="form-label">Service au sein de l'église</label>
                                        <input type="text" class="form-control @error('church_service') is-invalid @enderror" id="church_service" name="church_service" placeholder="Ex: Chorale, Intercession, etc." value="{{ old('church_service', $user->church_service) }}">
                                        @error('church_service')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-12 mt-3">
                                    <div class="d-flex flex-column flex-sm-row gap-2 justify-content-end">
                                        <button type="submit" class="btn btn-primary w-100 w-sm-auto">Mettre à jour</button>
                                        <a href="{{ route('dashboard') }}" class="btn btn-soft-success w-100 w-sm-auto">Annuler</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!--end tab-pane-->

                    <div class="tab-pane {{ $activeTab === 'groups' ? 'active show' : '' }}" id="groups" role="tabpanel">
                        <h5 class="card-title mb-3 mb-md-4">Mes Groupes d'appartenance</h5>

                        @php
                            $memberGroups = $user->groups->keyBy('id');
                            $ledGroups = $user->ledGroups->keyBy('id');
                            $allGroups = $memberGroups->merge($ledGroups);
                        @endphp

                        {{-- Vue Desktop : Tableau complet --}}
                        <div class="d-none d-md-block table-responsive table-card">
                            <table class="table table-borderless table-nowrap align-middle mb-0">
                                <thead class="table-light text-muted">
                                    <tr>
                                        <th scope="col">Nom du groupe</th>
                                        <th scope="col">Rôle</th>
                                        <th scope="col">Date d'adhésion</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($allGroups as $group)
                                    @php
                                        $isLeader = $ledGroups->has($group->id);
                                        $isMember = $memberGroups->has($group->id);
                                        $originalGroup = $memberGroups->get($group->id);
                                        $joinedAt = $originalGroup && $originalGroup->pivot && $originalGroup->pivot->joined_at 
                                                    ? \Carbon\Carbon::parse($originalGroup->pivot->joined_at)->format('d M Y') 
                                                    : '-';
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-xs me-2">
                                                    <div class="avatar-title bg-primary-subtle text-primary rounded-circle fs-16">
                                                        {{ substr($group->name, 0, 1) }}
                                                    </div>
                                                </div>
                                                <h6 class="fs-14 mb-0">{{ $group->name }}</h6>
                                            </div>
                                        </td>
                                        <td>
                                            @if($isLeader)
                                                <span class="badge bg-success-subtle text-success">Leader</span>
                                            @endif
                                            @if($isMember && !$isLeader)
                                                <span class="badge bg-secondary-subtle text-secondary">Membre</span>
                                            @endif
                                        </td>
                                        <td>{{ $joinedAt }}</td>
                                        <td>
                                            <a href="{{ route('admin.groups.show', $group) }}" class="btn btn-sm btn-soft-info" data-bs-toggle="tooltip" title="Voir le groupe">
                                                <i class="ri-eye-fill"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center p-4">
                                            <div class="text-muted">
                                                <i class="ri-group-line display-5 text-muted mb-3 d-block"></i>
                                                Vous n'appartenez à aucun groupe pour le moment.
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Vue Mobile : Cartes épurées et lisibles --}}
                        <div class="d-block d-md-none">
                            @forelse($allGroups as $group)
                            @php
                                $isLeader = $ledGroups->has($group->id);
                                $isMember = $memberGroups->has($group->id);
                                $originalGroup = $memberGroups->get($group->id);
                                $joinedAt = $originalGroup && $originalGroup->pivot && $originalGroup->pivot->joined_at 
                                            ? \Carbon\Carbon::parse($originalGroup->pivot->joined_at)->format('d M Y') 
                                            : '-';
                            @endphp
                            <div class="card border rounded-3 p-3 mb-2 shadow-none bg-light bg-opacity-40">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-xs me-2">
                                            <div class="avatar-title bg-primary-subtle text-primary rounded-circle fs-15 fw-bold">
                                                {{ substr($group->name, 0, 1) }}
                                            </div>
                                        </div>
                                        <h6 class="fs-14 mb-0 fw-semibold">{{ $group->name }}</h6>
                                    </div>
                                    <div>
                                        @if($isLeader)
                                            <span class="badge bg-success-subtle text-success">Leader</span>
                                        @endif
                                        @if($isMember && !$isLeader)
                                            <span class="badge bg-secondary-subtle text-secondary">Membre</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="d-flex align-items-center justify-content-between pt-2 border-top border-light fs-12">
                                    <span class="text-muted"><i class="ri-calendar-line me-1"></i>Adhésion : <strong>{{ $joinedAt }}</strong></span>
                                    <a href="{{ route('admin.groups.show', $group) }}" class="btn btn-sm btn-soft-info py-1 px-2">
                                        <i class="ri-eye-line me-1"></i>Détails
                                    </a>
                                </div>
                            </div>
                            @empty
                            <div class="text-center p-4 text-muted">
                                <i class="ri-group-line display-6 text-muted mb-2 d-block"></i>
                                Vous n'appartenez à aucun groupe pour le moment.
                            </div>
                            @endforelse
                        </div>
                    </div>
                    <!--end tab-pane-->

                    <div class="tab-pane {{ $activeTab === 'activities' ? 'active show' : '' }}" id="activities" role="tabpanel">
                        <h5 class="card-title mb-3 mb-md-4">Mes Présences aux Activités</h5>

                        @php
                            $attendances = $user->attendances()->with('activity')->orderBy('scanned_at', 'desc')->get();
                        @endphp

                        {{-- Vue Desktop : Tableau complet --}}
                        <div class="d-none d-md-block table-responsive table-card">
                            <table class="table table-borderless table-nowrap align-middle mb-0">
                                <thead class="table-light text-muted">
                                    <tr>
                                        <th scope="col">Activité</th>
                                        <th scope="col">Date scannée</th>
                                        <th scope="col">Statut</th>
                                        <th scope="col">Source</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($attendances as $attendance)
                                    <tr>
                                        <td class="text-wrap" style="max-width: 250px;">
                                            <h6 class="fs-14 mb-0">{{ $attendance->activity->title ?? 'Activité inconnue' }}</h6>
                                            <p class="text-muted fs-12 mb-0">{{ $attendance->activity?->start_date?->format('d M Y') ?? '' }}</p>
                                        </td>
                                        <td>{{ $attendance->scanned_at?->format('d M Y à H:i') ?? '-' }}</td>
                                        <td>
                                            @if($attendance->status->value === 'PRESENT')
                                                <span class="badge bg-success-subtle text-success"><i class="ri-check-line align-bottom"></i> Présent</span>
                                            @elseif($attendance->status->value === 'ABSENT')
                                                <span class="badge bg-danger-subtle text-danger"><i class="ri-close-line align-bottom"></i> Absent</span>
                                            @elseif($attendance->status->value === 'LATE')
                                                <span class="badge bg-warning-subtle text-warning"><i class="ri-time-line align-bottom"></i> En retard</span>
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary">{{ $attendance->status->label() }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-body">{{ $attendance->scan_source ?? 'N/A' }}</span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center p-4">
                                            <div class="text-muted">
                                                <i class="ri-calendar-event-line display-5 text-muted mb-3 d-block"></i>
                                                Aucune présence enregistrée.
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Vue Mobile : Cartes compactes --}}
                        <div class="d-block d-md-none">
                            @forelse($attendances as $attendance)
                            <div class="card border rounded-3 p-3 mb-2 shadow-none bg-light bg-opacity-40">
                                <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                                    <div>
                                        <h6 class="fs-14 mb-1 fw-bold">{{ $attendance->activity->title ?? 'Activité inconnue' }}</h6>
                                        <span class="text-muted fs-11"><i class="ri-calendar-event-line me-1"></i>{{ $attendance->activity?->start_date?->format('d M Y') ?? '' }}</span>
                                    </div>
                                    <div>
                                        @if($attendance->status->value === 'PRESENT')
                                            <span class="badge bg-success-subtle text-success"><i class="ri-check-line align-bottom"></i> Présent</span>
                                        @elseif($attendance->status->value === 'ABSENT')
                                            <span class="badge bg-danger-subtle text-danger"><i class="ri-close-line align-bottom"></i> Absent</span>
                                        @elseif($attendance->status->value === 'LATE')
                                            <span class="badge bg-warning-subtle text-warning"><i class="ri-time-line align-bottom"></i> Retard</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary">{{ $attendance->status->label() }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="d-flex align-items-center justify-content-between pt-2 border-top border-light fs-12 text-muted">
                                    <span><i class="ri-time-line me-1"></i>{{ $attendance->scanned_at?->format('d/m/Y à H:i') ?? '-' }}</span>
                                    <span class="badge bg-light text-body border">{{ $attendance->scan_source ?? 'N/A' }}</span>
                                </div>
                            </div>
                            @empty
                            <div class="text-center p-4 text-muted">
                                <i class="ri-calendar-event-line display-6 text-muted mb-2 d-block"></i>
                                Aucune présence enregistrée.
                            </div>
                            @endforelse
                        </div>
                    </div>
                    <!--end tab-pane-->

                    {{-- ================= TAB: APPAREIL-BADGE ================= --}}
                    <div class="tab-pane {{ $activeTab === 'deviceBadge' ? 'active show' : '' }}" id="deviceBadge" role="tabpanel">
                        <div class="d-flex align-items-center justify-content-between mb-3 mb-md-4">
                            <div>
                                <h5 class="card-title mb-1 d-flex align-items-center gap-2 fs-16 fs-md-18">
                                    <i class="ri-smartphone-line text-primary"></i> Appareil-Badge de Présence
                                </h5>
                                <p class="text-muted mb-0 fs-13">Transformez votre smartphone en badge personnel pour pointer directement par scan sans avoir à vous reconnecter.</p>
                            </div>
                        </div>

                        {{-- Carte d'état de l'appareil actuel --}}
                        @if($currentEnrolledDevice)
                            <div class="card border border-success bg-success-subtle shadow-none rounded-3 mb-4">
                                <div class="card-body p-3 p-md-4">
                                    <div class="d-flex align-items-center gap-3 mb-2">
                                        <div class="avatar-md bg-success rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 text-white shadow-sm">
                                            <i class="ri-checkbox-circle-fill fs-28"></i>
                                        </div>
                                        <div>
                                            <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                                <h6 class="fs-16 fw-bold mb-0 text-success">Cet appareil est configuré comme votre badge !</h6>
                                                <span class="badge bg-success text-white fs-11">Badge Actif</span>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="text-muted mb-3 fs-13 lh-base">
                                        <strong>{{ $currentEnrolledDevice->device_name }}</strong> • Enrôlé le {{ $currentEnrolledDevice->created_at->format('d/m/Y') }}
                                        @if($currentEnrolledDevice->last_used_at)
                                            • Dernier scan : {{ $currentEnrolledDevice->last_used_at->diffForHumans() }}
                                        @endif
                                    </p>
                                    <div class="text-md-end">
                                        <form action="{{ route('profile.device-badge.enroll') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success fw-medium w-100 w-md-auto py-2 px-3">
                                                <i class="ri-refresh-line me-1"></i> Réenrôler / Renouveler
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="card border border-primary border-opacity-25 bg-primary-subtle shadow-none rounded-3 mb-4">
                                <div class="card-body p-3 p-md-4">
                                    <div class="d-flex align-items-center gap-3 mb-3">
                                        <div class="avatar-md bg-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 text-white shadow-sm">
                                            <i class="ri-qr-scan-2-line fs-28"></i>
                                        </div>
                                        <h6 class="fs-16 fw-bold mb-0 text-primary">Activez le pointage instantané sur ce téléphone</h6>
                                    </div>
                                    <p class="text-muted mb-3 fs-13 lh-base">
                                        En un seul clic, associez ce téléphone à votre compte. Lors des activités, il vous suffira de viser le QR Code affiché à l'église avec votre appareil photo natif pour valider votre présence en <strong>3 secondes chrono</strong> !
                                    </p>
                                    <div class="text-md-end">
                                        <form action="{{ route('profile.device-badge.enroll') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold shadow-sm w-100 w-md-auto">
                                                <i class="ri-shield-check-line me-1 fs-16 align-middle"></i> Faire de ce téléphone mon badge
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Astuce Navigateur (iOS / Android) --}}
                        @if(!empty($browserGuidance['warning']))
                            <div class="alert alert-warning border-0 d-flex align-items-start gap-2 p-3 mb-4 shadow-sm rounded-3">
                                <i class="ri-information-line fs-20 text-warning flex-shrink-0 mt-1"></i>
                                <div class="fs-13">
                                    <strong>Conseil pour votre appareil :</strong> {{ $browserGuidance['warning'] }}
                                </div>
                            </div>
                        @endif

                        {{-- Comment ça marche --}}
                        <div class="row g-2 g-md-3 mb-4">
                            <div class="col-12 col-md-4">
                                <div class="border rounded-3 p-3 h-100 bg-light bg-opacity-50">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="badge bg-primary rounded-pill">Étape 1</span>
                                        <h6 class="mb-0 fs-14 fw-bold">Enrôlement (1 fois)</h6>
                                    </div>
                                    <p class="text-muted fs-12 mb-0">Cliquez sur le bouton ci-dessus pour faire de ce navigateur votre badge sécurisé.</p>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="border rounded-3 p-3 h-100 bg-light bg-opacity-50">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="badge bg-success rounded-pill">Étape 2</span>
                                        <h6 class="mb-0 fs-14 fw-bold">Scan à l'église</h6>
                                    </div>
                                    <p class="text-muted fs-12 mb-0">Ouvrez simplement l'appareil photo ou le scanner de votre smartphone et visez le QR Code de l'activité.</p>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="border rounded-3 p-3 h-100 bg-light bg-opacity-50">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="badge bg-info rounded-pill">Étape 3</span>
                                        <h6 class="mb-0 fs-14 fw-bold">Validation Express</h6>
                                    </div>
                                    <p class="text-muted fs-12 mb-0">Le système vous identifie automatiquement et affiche immédiatement l'écran vert de confirmation !</p>
                                </div>
                            </div>
                        </div>

                        {{-- Liste de tous les appareils enregistrés --}}
                        <div class="border-top pt-4 mt-2">
                            <h6 class="fs-15 fw-semibold mb-3">Vos appareils enregistrés</h6>

                            {{-- Vue Desktop : Tableau classique --}}
                            <div class="d-none d-md-block table-responsive">
                                <table class="table table-hover align-middle table-nowrap mb-0">
                                    <thead class="table-light fs-12">
                                        <tr>
                                            <th>Appareil / Navigateur</th>
                                            <th>Date d'ajout</th>
                                            <th>Dernier pointage</th>
                                            <th>Statut</th>
                                            <th class="text-end">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($trustedDevices as $dev)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="avatar-xs rounded bg-light d-flex align-items-center justify-content-center text-primary">
                                                            <i class="ri-smartphone-line fs-16"></i>
                                                        </div>
                                                        <div>
                                                            <h6 class="fs-13 mb-0 fw-semibold">{{ $dev->device_name }}</h6>
                                                            <span class="text-muted fs-11">IP: {{ $dev->ip_address ?? '—' }}</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="fs-13">{{ $dev->created_at->format('d/m/Y H:i') }}</td>
                                                <td class="fs-13">
                                                    @if($dev->last_used_at)
                                                        <span class="text-success fw-medium">{{ $dev->last_used_at->format('d/m/Y H:i') }}</span>
                                                        <span class="text-muted fs-11 d-block">{{ $dev->last_used_at->diffForHumans() }}</span>
                                                    @else
                                                        <span class="text-muted">Jamais utilisé</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($dev->isValid())
                                                        <span class="badge bg-success-subtle text-success border border-success-subtle">Actif</span>
                                                    @else
                                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Expiré / Inactif</span>
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    <button type="button" 
                                                            class="btn btn-sm btn-ghost-danger btn-open-revoke-modal" 
                                                            title="Supprimer ce badge"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#revokeDeviceModal"
                                                            data-device-name="{{ $dev->device_name }}"
                                                            data-revoke-url="{{ route('profile.device-badge.revoke', $dev->id) }}">
                                                        <i class="ri-delete-bin-line fs-15 align-middle"></i> Révoquer
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted fs-13">
                                                    <i class="ri-smartphone-line fs-24 d-block mb-1 text-muted"></i>
                                                    Aucun appareil n'est encore enregistré comme badge.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            {{-- Vue Mobile : Cartes individuelles optimisées tactile --}}
                            <div class="d-block d-md-none">
                                @forelse($trustedDevices as $dev)
                                    <div class="card border rounded-3 p-3 mb-3 shadow-none bg-light bg-opacity-40">
                                        <div class="d-flex align-items-start justify-content-between mb-2">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="avatar-xs rounded bg-primary-subtle d-flex align-items-center justify-content-center text-primary flex-shrink-0">
                                                    <i class="ri-smartphone-line fs-18"></i>
                                                </div>
                                                <div>
                                                    <h6 class="fs-14 mb-0 fw-bold">{{ $dev->device_name }}</h6>
                                                    <span class="text-muted fs-11">IP : {{ $dev->ip_address ?? '—' }}</span>
                                                </div>
                                            </div>
                                            <div>
                                                @if($dev->isValid())
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fs-11">Actif</span>
                                                @else
                                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 fs-11">Expiré</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="bg-white rounded-2 p-2 mb-2 border border-light fs-12">
                                            <div class="d-flex justify-content-between mb-1">
                                                <span class="text-muted">Ajouté le :</span>
                                                <span class="fw-medium">{{ $dev->created_at->format('d/m/Y H:i') }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span class="text-muted">Dernier scan :</span>
                                                @if($dev->last_used_at)
                                                    <span class="text-success fw-medium">{{ $dev->last_used_at->diffForHumans() }} ({{ $dev->last_used_at->format('H:i') }})</span>
                                                @else
                                                    <span class="text-muted">Jamais utilisé</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <button type="button" 
                                                    class="btn btn-sm btn-soft-danger w-100 py-1 fw-medium btn-open-revoke-modal"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#revokeDeviceModal"
                                                    data-device-name="{{ $dev->device_name }}"
                                                    data-revoke-url="{{ route('profile.device-badge.revoke', $dev->id) }}">
                                                <i class="ri-delete-bin-line me-1"></i> Révoquer ce badge
                                            </button>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-4 text-muted fs-13">
                                        <i class="ri-smartphone-line fs-28 d-block mb-2 text-muted"></i>
                                        Aucun appareil n'est encore enregistré comme badge.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    <!--end tab-pane-->
                    
                    <div class="tab-pane {{ $activeTab === 'changePassword' ? 'active show' : '' }}" id="changePassword" role="tabpanel">
                        @if (session('status') === 'password-updated')
                            <div class="alert alert-success">
                                Mot de passe modifié avec succès.
                            </div>
                        @endif

                        <form action="{{ route('profile.password') }}" method="POST">
                            @csrf
                            <div class="row g-2">
                                <div class="col-lg-4">
                                    <div>
                                        <label for="current_password" class="form-label">Ancien mot de passe*</label>
                                        <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" placeholder="Ancien mot de passe" required>
                                        @error('current_password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div>
                                        <label for="password" class="form-label">Nouveau mot de passe*</label>
                                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Nouveau mot de passe" required>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div>
                                        <label for="password_confirmation" class="form-label">Confirmer le mot de passe*</label>
                                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Confirmer le mot de passe" required>
                                    </div>
                                </div>
                                <div class="col-lg-12 mt-4">
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-success">Changer le mot de passe</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!--end tab-pane-->
                </div>
            </div>
        </div>
    </div>
    <!--end col-->
</div>
<!--end row-->

{{-- Modal de confirmation de révocation d'appareil-badge --}}
<div class="modal fade" id="revokeDeviceModal" tabindex="-1" aria-labelledby="revokeDeviceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-0 py-3 px-4 bg-danger-subtle">
                <div class="d-flex align-items-center gap-2">
                    <div class="avatar-xs rounded-circle bg-danger d-flex align-items-center justify-content-center text-white">
                        <i class="ri-delete-bin-line fs-14"></i>
                    </div>
                    <h5 class="modal-title fw-bold fs-16 mb-0 text-danger" id="revokeDeviceModalLabel">Révoquer l'appareil-badge</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="avatar-lg rounded-circle mx-auto d-flex align-items-center justify-content-center mb-3 bg-danger-subtle text-danger" style="width:70px; height:70px;">
                    <i class="ri-smartphone-line fs-32"></i>
                </div>
                <h5 class="fw-bold text-body mb-2" id="revoke-device-name-display">Appareil-Badge</h5>
                <p class="text-muted fs-14 mb-3">
                    Êtes-vous sûr de vouloir révoquer cet appareil badge ?
                </p>
                <div class="alert alert-warning border-0 py-2 px-3 fs-12 mb-0 text-start rounded-3">
                    <i class="ri-alert-line me-1 text-warning"></i>
                    Cet appareil ne pourra plus être utilisé pour émarger instantanément tant qu'il n'est pas réenrôlé.
                </div>
            </div>
            <div class="modal-footer bg-light border-0 py-3 px-4 d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-light rounded-pill px-4 fs-13" data-bs-dismiss="modal">Annuler</button>
                <form id="revoke-device-form" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger rounded-pill px-4 fs-13 shadow-sm d-inline-flex align-items-center gap-1">
                        <i class="ri-delete-bin-line me-1"></i>
                        <span>Oui, révoquer</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        function switchToTab(targetHref) {
            if (!targetHref) return;
            if (!targetHref.startsWith('#')) targetHref = '#' + targetHref;
            var targetId = targetHref.substring(1);
            
            var targetPane = document.getElementById(targetId);
            if (!targetPane) return;

            // 1. Switch active & show class on tab panes
            document.querySelectorAll('.tab-content > .tab-pane').forEach(function(pane) {
                pane.classList.remove('active', 'show');
            });
            targetPane.classList.add('active', 'show');

            // 2. Synchronize active state on all triggers
            document.querySelectorAll('[data-bs-toggle="tab"]').forEach(function(trigger) {
                if (trigger.getAttribute('href') === targetHref) {
                    trigger.classList.add('active');
                } else {
                    trigger.classList.remove('active');
                }
            });

            // 3. Scroll to tabs content on mobile
            if (window.innerWidth < 1400) {
                var card = document.getElementById('profile-main-tabs-card');
                if (card) {
                    var cardTop = card.getBoundingClientRect().top + window.pageYOffset - 80;
                    window.scrollTo({ top: cardTop, behavior: 'smooth' });
                }
            }

            // 4. Update browser history hash
            if (history.replaceState && window.location.hash !== targetHref) {
                history.replaceState(null, null, targetHref);
            }
        }

        // Attach click listeners to all tab triggers
        document.querySelectorAll('[data-bs-toggle="tab"]').forEach(function(trigger) {
            trigger.addEventListener('click', function(e) {
                e.preventDefault();
                var href = this.getAttribute('href');
                switchToTab(href);
            });
        });

        // Check hash or URL query param on load & on hashchange
        function checkHashAndActivate() {
            var hash = window.location.hash;
            var urlParams = new URLSearchParams(window.location.search);
            var tabParam = urlParams.get('tab');

            if (hash && document.getElementById(hash.substring(1))) {
                switchToTab(hash);
            } else if (tabParam && document.getElementById(tabParam)) {
                switchToTab('#' + tabParam);
            }
        }
        checkHashAndActivate();
        window.addEventListener('hashchange', checkHashAndActivate);

        // Configuration dynamique du modal de révocation de badge
        var revokeModal = document.getElementById('revokeDeviceModal');
        if (revokeModal) {
            revokeModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                if (!button) return;
                var deviceName = button.getAttribute('data-device-name') || 'Appareil-Badge';
                var revokeUrl = button.getAttribute('data-revoke-url');
                
                var nameEl = document.getElementById('revoke-device-name-display');
                var formEl = document.getElementById('revoke-device-form');
                
                if (nameEl) nameEl.textContent = deviceName;
                if (formEl && revokeUrl) formEl.action = revokeUrl;
            });
        }
    });
</script>
@endpush

@endsection
