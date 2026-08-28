@extends('layouts.app')

@section('title', 'Fiche Église : ' . $church->name)

@push('css')
<style>
    /* ==========================================================================
       SUPER ADMIN HERO SECTION - EXACTEMENT IDENTIQUE À /SUPER-ADMIN
       ========================================================================== */
    .saas-hero {
        background-color: #0b0f19;
        background-image: 
            radial-gradient(circle at 0% 0%, rgba(var(--vz-primary-rgb), 0.3) 0%, transparent 50%),
            radial-gradient(circle at 100% 100%, rgba(var(--vz-warning-rgb), 0.2) 0%, transparent 50%);
        padding: 3rem 0 5.5rem 0;
        position: relative;
        overflow: hidden;
        margin: -1.5rem -1.5rem 2rem -1.5rem;
        border-bottom-left-radius: 2rem;
        border-bottom-right-radius: 2rem;
        box-shadow: inset 0 -20px 40px -20px rgba(0,0,0,0.5);
    }
    
    /* Grid Pattern */
    .hero-grid {
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        background-image: 
            linear-gradient(to right, rgba(255,255,255,0.03) 1px, transparent 1px),
            linear-gradient(to bottom, rgba(255,255,255,0.03) 1px, transparent 1px);
        background-size: 40px 40px;
        pointer-events: none;
        z-index: 1;
    }
    
    /* Glowing Orbs */
    .hero-orb {
        position: absolute;
        border-radius: 50%;
        filter: blur(80px);
        opacity: 0.55;
        z-index: 0;
        animation: orbFloat 12s infinite alternate ease-in-out;
    }
    .orb-1 {
        width: 380px; height: 380px;
        background: var(--vz-primary);
        top: -150px; left: -100px;
    }
    .orb-2 {
        width: 320px; height: 320px;
        background: var(--vz-warning);
        bottom: -100px; right: 5%;
        animation-delay: -6s;
    }

    @keyframes orbFloat {
        0% { transform: translateY(0) scale(1); }
        100% { transform: translateY(35px) scale(1.08); }
    }
    
    .hero-content {
        position: relative;
        z-index: 5;
    }

    .hero-badge {
        background: rgba(var(--vz-warning-rgb), 0.15);
        border: 1px solid rgba(var(--vz-warning-rgb), 0.35);
        color: #f7b84b;
        backdrop-filter: blur(5px);
    }

    /* Premium Custom Cards */
    .premium-card {
        border-radius: 1.25rem;
        border: none;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .premium-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    }

    /* Bouton d'action principale */
    .saas-cta-btn {
        padding: 12px 24px;
        font-size: 0.92rem;
        font-weight: 600;
        border-radius: 50px;
        box-shadow: 0 8px 18px rgba(247, 184, 75, 0.35);
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: linear-gradient(135deg, #f7b84b 0%, #d97706 100%);
        border: none;
        color: #111827;
        text-decoration: none;
    }
    
    .saas-cta-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 24px rgba(247, 184, 75, 0.5);
        color: #111827;
    }

    .saas-secondary-btn {
        padding: 12px 22px;
        font-size: 0.92rem;
        font-weight: 500;
        border-radius: 50px;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: #ffffff;
        backdrop-filter: blur(8px);
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }

    .saas-secondary-btn:hover {
        background: rgba(255, 255, 255, 0.2);
        color: #ffffff;
        transform: translateY(-3px);
    }
    
    /* Table Styling */
    .table-premium th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.72rem;
        letter-spacing: 0.5px;
        color: var(--vz-gray-600);
        background-color: var(--vz-light);
        padding: 13px 16px;
    }
    
    .table-premium td {
        padding: 13px 16px;
        vertical-align: middle;
    }

    /* Pulse dot */
    .pulse-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
        box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7);
        animation: pulse-green 2s infinite;
    }
    @keyframes pulse-green {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(34, 197, 94, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
    }

    .info-tile {
        background: rgba(248, 250, 252, 0.8);
        border: 1px solid rgba(226, 232, 240, 0.9);
        border-radius: 14px;
        padding: 14px 16px;
        transition: all 0.2s ease;
    }
    .info-tile:hover {
        background: #ffffff;
        border-color: rgba(99, 102, 241, 0.3);
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.06);
    }

    .admin-user-card {
        background: #ffffff;
        border: 1px solid rgba(226, 232, 240, 0.9);
        border-radius: 16px;
        padding: 18px;
        transition: all 0.2s ease;
    }
    .admin-user-card:hover {
        border-color: rgba(99, 102, 241, 0.35);
        box-shadow: 0 6px 20px rgba(99, 102, 241, 0.08);
    }

    .subscription-progress-bar {
        height: 6px;
        border-radius: 10px;
        background: rgba(226, 232, 240, 0.8);
        overflow: hidden;
    }

    @media (max-width: 991.98px) {
        .saas-hero {
            padding: 2.5rem 0 6rem 0;
            text-align: center;
        }
        .hero-actions-container {
            justify-content: center !important;
            margin-top: 1.25rem;
        }
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="h-100">

            {{-- ========================================================================= --}}
            {{-- HERO SECTION (IDENTIQUE AU DESIGN /SUPER-ADMIN) --}}
            {{-- ========================================================================= --}}
            <div class="saas-hero px-4">
                <div class="hero-grid"></div>
                <div class="hero-orb orb-1"></div>
                <div class="hero-orb orb-2"></div>
                
                <div class="container-fluid max-w-1200 hero-content">
                    <div class="row align-items-center">
                        <div class="col-lg-7">
                            
                            {{-- Badges & Status --}}
                            <div class="mb-3 d-flex justify-content-center justify-content-lg-start gap-2 flex-wrap align-items-center">
                                <span class="badge hero-badge px-3 py-2 rounded-pill shadow-sm" style="font-size: 0.75rem; letter-spacing: 1px;">
                                    <i class="ri-shield-star-line me-1"></i> FICHE ÉGLISE SAAS
                                </span>
                                
                                @if($church->status === 'suspended')
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 rounded-pill d-inline-flex align-items-center gap-1" style="font-size: 0.75rem;">
                                        <span class="pulse-dot bg-danger"></span> Compte Suspendu
                                    </span>
                                @elseif($church->isSubscriptionActive())
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill d-inline-flex align-items-center gap-1" style="font-size: 0.75rem;">
                                        <span class="pulse-dot bg-success"></span> Abonnement Actif ({{ $church->daysLeftInSubscription() }}j restants)
                                    </span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-2 rounded-pill d-inline-flex align-items-center gap-1" style="font-size: 0.75rem;">
                                        <i class="ri-alert-line me-1"></i> Abonnement Expiré
                                    </span>
                                @endif
                            </div>

                            {{-- Titre de l'église --}}
                            <h1 class="display-6 fw-bold text-white mb-2 text-center text-lg-start" style="line-height: 1.2;">
                                {{ $church->name }}
                                <span style="color: var(--vz-warning); filter: brightness(1.2); font-size: 0.75em; font-family: monospace;">[{{ $church->code }}]</span>
                            </h1>

                            <p class="fs-15 mb-0 text-center text-lg-start" style="max-width: 650px; line-height: 1.6; color: rgba(255,255,255,0.8);">
                                @if($church->city)
                                    <i class="ri-map-pin-line text-warning me-1"></i> {{ $church->city }} &nbsp;|&nbsp; 
                                @endif
                                <i class="ri-calendar-line me-1"></i> Inscrite le {{ $church->created_at->format('d/m/Y') }}
                                @if($church->phone)
                                    &nbsp;|&nbsp; <i class="ri-phone-line text-success me-1"></i> {{ $church->phone }}
                                @endif
                            </p>
                        </div>

                        {{-- Action Buttons Cluster --}}
                        <div class="col-lg-5 d-flex justify-content-lg-end hero-actions-container">
                            <div class="text-center text-lg-end d-flex flex-wrap gap-2 justify-content-center">
                                {{-- Mode Support --}}
                                <a href="{{ route('super-admin.churches.impersonate', $church) }}" class="saas-cta-btn">
                                    <i class="ri-user-shared-line fs-18"></i> Mode Support
                                </a>

                                {{-- Renouveler --}}
                                <a href="{{ route('super-admin.churches.renew.form', $church) }}" class="saas-secondary-btn">
                                    <i class="ri-refresh-line fs-18"></i> Renouveler (1 An)
                                </a>

                                {{-- Modifier --}}
                                <a href="{{ route('super-admin.churches.edit', $church) }}" class="saas-secondary-btn">
                                    <i class="ri-edit-line fs-18"></i> Modifier
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ========================================================================= --}}
            {{-- 4 CARTES KPI (STATS ROW) - DESIGN /SUPER-ADMIN RESPONSIVE --}}
            {{-- ========================================================================= --}}
            @php
                $daysLeft = $church->daysLeftInSubscription();
                $totalDaysInYear = 365;
                $progressPercent = max(0, min(100, round(($daysLeft / $totalDaysInYear) * 100)));
            @endphp

            <div class="container-fluid max-w-1200 px-0">
                <div class="row g-2 g-md-3 mb-4" style="margin-top: -5rem; position: relative; z-index: 10;">
                    
                    {{-- 1. Abonnement Annuel --}}
                    <div class="col-6 col-md-6 col-xl-3">
                        <div class="card premium-card overflow-hidden h-100">
                            <div class="card-body p-2 p-sm-3 p-md-4">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="text-uppercase fw-semibold text-muted text-truncate mb-0 fs-10 fs-md-12" style="letter-spacing: 0.5px;">Abonnement</p>
                                    </div>
                                    <div class="avatar-xxs avatar-sm-xs flex-shrink-0">
                                        <span class="avatar-title bg-success-subtle text-success rounded-circle fs-13 fs-md-16">
                                            <i class="ri-shield-check-line"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-2 mt-md-3">
                                    <div>
                                        <h4 class="fs-18 fs-md-24 fw-bold ff-secondary mb-1 mb-md-2">
                                            @if($church->status === 'suspended')
                                                Suspendu
                                            @elseif($daysLeft > 0)
                                                {{ $daysLeft }} <span class="fs-12 text-muted fw-normal">jours</span>
                                            @else
                                                Expiré
                                            @endif
                                        </h4>
                                        <span class="text-{{ $church->status === 'suspended' ? 'danger' : ($daysLeft > 30 ? 'success' : 'warning') }} fw-medium fs-11 fs-md-13">
                                            Échéance : {{ $church->subscription_expires_at ? $church->subscription_expires_at->format('d/m/Y') : 'N/A' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="subscription-progress-bar mt-2">
                                    <div class="h-100 bg-{{ $church->status === 'suspended' ? 'danger' : ($daysLeft > 30 ? 'success' : ($daysLeft > 0 ? 'warning' : 'danger')) }}" 
                                         style="width: {{ $church->status === 'suspended' ? 100 : $progressPercent }}%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Membres Inscrits --}}
                    <div class="col-6 col-md-6 col-xl-3">
                        <div class="card premium-card overflow-hidden h-100">
                            <div class="card-body p-2 p-sm-3 p-md-4">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="text-uppercase fw-semibold text-muted text-truncate mb-0 fs-10 fs-md-12" style="letter-spacing: 0.5px;">Membres</p>
                                    </div>
                                    <div class="avatar-xxs avatar-sm-xs flex-shrink-0">
                                        <span class="avatar-title bg-primary-subtle text-primary rounded-circle fs-13 fs-md-16">
                                            <i class="ri-user-3-line"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-2 mt-md-3">
                                    <div>
                                        <h4 class="fs-18 fs-md-24 fw-bold ff-secondary mb-1 mb-md-2">{{ number_format($church->users_count, 0, ',', ' ') }}</h4>
                                        <span class="text-primary fw-medium fs-11 fs-md-13">
                                            <i class="ri-checkbox-circle-line me-1"></i>Utilisateurs enregistrés
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 3. Groupes / Ministères --}}
                    <div class="col-6 col-md-6 col-xl-3">
                        <div class="card premium-card overflow-hidden h-100">
                            <div class="card-body p-2 p-sm-3 p-md-4">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="text-uppercase fw-semibold text-muted text-truncate mb-0 fs-10 fs-md-12" style="letter-spacing: 0.5px;">Groupes</p>
                                    </div>
                                    <div class="avatar-xxs avatar-sm-xs flex-shrink-0">
                                        <span class="avatar-title bg-warning-subtle text-warning rounded-circle fs-13 fs-md-16">
                                            <i class="ri-team-line"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-2 mt-md-3">
                                    <div>
                                        <h4 class="fs-18 fs-md-24 fw-bold ff-secondary mb-1 mb-md-2">{{ number_format($church->groups_count, 0, ',', ' ') }}</h4>
                                        <span class="text-warning fw-medium fs-11 fs-md-13">
                                            <i class="ri-community-line me-1"></i>Groupes & ministères
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 4. Activités Organisées --}}
                    <div class="col-6 col-md-6 col-xl-3">
                        <div class="card premium-card overflow-hidden h-100">
                            <div class="card-body p-2 p-sm-3 p-md-4">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="text-uppercase fw-semibold text-muted text-truncate mb-0 fs-10 fs-md-12" style="letter-spacing: 0.5px;">Activités</p>
                                    </div>
                                    <div class="avatar-xxs avatar-sm-xs flex-shrink-0">
                                        <span class="avatar-title bg-info-subtle text-info rounded-circle fs-13 fs-md-16">
                                            <i class="ri-calendar-event-line"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-2 mt-md-3">
                                    <div>
                                        <h4 class="fs-18 fs-md-24 fw-bold ff-secondary mb-1 mb-md-2">{{ number_format($church->activities_count, 0, ',', ' ') }}</h4>
                                        <span class="text-info fw-medium fs-11 fs-md-13">
                                            <i class="ri-time-line me-1"></i>Événements créés
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- ========================================================================= --}}
                {{-- SECTION 2 : COORDONNÉES ET COMPTES ADMINISTRATEURS --}}
                {{-- ========================================================================= --}}
                <div class="row g-3 g-md-4 mb-4">
                    
                    {{-- Coordonnées & Fiche Église --}}
                    <div class="col-lg-6">
                        <div class="card premium-card h-100 overflow-hidden">
                            <div class="card-header bg-transparent border-0 py-3 px-4 d-flex align-items-center justify-content-between" 
                                 style="border-bottom: 1px solid rgba(var(--vz-dark-rgb), 0.05) !important;">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-xs bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="ri-building-4-line fs-16"></i>
                                    </div>
                                    <h5 class="card-title mb-0 fw-bold fs-15 text-body">Coordonnées de l'Église</h5>
                                </div>
                                <a href="{{ route('super-admin.churches.edit', $church) }}" class="btn btn-sm btn-soft-primary rounded-pill px-3 fs-12">
                                    <i class="ri-edit-line me-1"></i>Modifier
                                </a>
                            </div>

                            <div class="card-body p-4">
                                <div class="row g-3">
                                    
                                    {{-- Ville --}}
                                    <div class="col-sm-6">
                                        <div class="info-tile h-100">
                                            <span class="text-muted fs-11 fw-bold text-uppercase d-block mb-1">
                                                <i class="ri-map-pin-2-line me-1 text-primary"></i>Ville
                                            </span>
                                            <span class="text-body fw-semibold fs-14">{{ $church->city ?? 'Non spécifiée' }}</span>
                                        </div>
                                    </div>

                                    {{-- Tarif Annuel --}}
                                    <div class="col-sm-6">
                                        <div class="info-tile h-100">
                                            <span class="text-muted fs-11 fw-bold text-uppercase d-block mb-1">
                                                <i class="ri-money-dollar-circle-line me-1 text-success"></i>Tarif Annuel Souscrit
                                            </span>
                                            <span class="text-success fw-bold fs-14">{{ number_format($church->subscription_amount, 0, ',', ' ') }} FCFA</span>
                                        </div>
                                    </div>

                                    {{-- Téléphone --}}
                                    <div class="col-sm-6">
                                        <div class="info-tile h-100">
                                            <span class="text-muted fs-11 fw-bold text-uppercase d-block mb-1">
                                                <i class="ri-phone-line me-1 text-info"></i>Téléphone
                                            </span>
                                            @if($church->phone)
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <span class="text-body fw-semibold fs-14">{{ $church->phone }}</span>
                                                    <button type="button" class="btn btn-xs btn-soft-info rounded-circle" onclick="copyToClipboard('{{ $church->phone }}', 'Téléphone copié !')" title="Copier">
                                                        <i class="ri-file-copy-line fs-12"></i>
                                                    </button>
                                                </div>
                                            @else
                                                <span class="text-muted fs-13">Non renseigné</span>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Email --}}
                                    <div class="col-sm-6">
                                        <div class="info-tile h-100">
                                            <span class="text-muted fs-11 fw-bold text-uppercase d-block mb-1">
                                                <i class="ri-mail-line me-1 text-warning"></i>Email
                                            </span>
                                            @if($church->email)
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <span class="text-body fw-semibold fs-13 text-truncate me-2" title="{{ $church->email }}">{{ $church->email }}</span>
                                                    <button type="button" class="btn btn-xs btn-soft-warning rounded-circle" onclick="copyToClipboard('{{ $church->email }}', 'Email copié !')" title="Copier">
                                                        <i class="ri-file-copy-line fs-12"></i>
                                                    </button>
                                                </div>
                                            @else
                                                <span class="text-muted fs-13">Non renseigné</span>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Adresse Géographique --}}
                                    <div class="col-12">
                                        <div class="info-tile">
                                            <span class="text-muted fs-11 fw-bold text-uppercase d-block mb-1">
                                                <i class="ri-road-map-line me-1 text-danger"></i>Adresse Géographique
                                            </span>
                                            <span class="text-body fw-medium fs-13">{{ $church->address ?? 'Aucune adresse spécifiée' }}</span>
                                        </div>
                                    </div>

                                    {{-- Notes --}}
                                    @if($church->notes)
                                        <div class="col-12">
                                            <div class="info-tile bg-body-tertiary">
                                                <span class="text-muted fs-11 fw-bold text-uppercase d-block mb-1">
                                                    <i class="ri-sticky-note-line me-1 text-secondary"></i>Notes Administratives
                                                </span>
                                                <p class="text-body-secondary fs-13 mb-0">{{ $church->notes }}</p>
                                            </div>
                                        </div>
                                    @endif

                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Administrateurs Locaux --}}
                    <div class="col-lg-6">
                        <div class="card premium-card h-100 overflow-hidden">
                            <div class="card-header bg-transparent border-0 py-3 px-4 d-flex align-items-center justify-content-between" 
                                 style="border-bottom: 1px solid rgba(var(--vz-dark-rgb), 0.05) !important;">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-xs bg-warning-subtle text-warning rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="ri-user-star-line fs-16"></i>
                                    </div>
                                    <h5 class="card-title mb-0 fw-bold fs-15 text-body">Administrateur(s) de l'Église</h5>
                                </div>
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2 py-1 fs-11">
                                    {{ $admins->count() }} compte(s)
                                </span>
                            </div>

                            <div class="card-body p-4">
                                <div class="d-flex flex-column gap-3">
                                    @forelse($admins as $admin)
                                        <div class="admin-user-card">
                                            <div class="d-flex align-items-start gap-3">
                                                {{-- Avatar --}}
                                                @if($admin->photo)
                                                    <img src="{{ asset('storage/' . $admin->photo) }}" alt="Photo" class="rounded-circle shadow-sm border" width="48" height="48" style="object-fit: cover; flex-shrink: 0;">
                                                @else
                                                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm flex-shrink-0" 
                                                         style="width: 48px; height: 48px; background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); color: #ffffff; font-size: 1.1rem;">
                                                        {{ strtoupper(substr($admin->first_name, 0, 1) . substr($admin->name, 0, 1)) }}
                                                    </div>
                                                @endif

                                                {{-- Admin Info --}}
                                                <div class="flex-grow-1 min-w-0">
                                                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-1">
                                                        <h6 class="fw-bold mb-0 fs-15 text-body text-truncate">{{ $admin->first_name }} {{ $admin->name }}</h6>
                                                        <span class="badge bg-danger-subtle text-danger rounded-pill px-2 py-0-5 fs-10 fw-semibold">
                                                            <i class="ri-shield-user-line me-1"></i>Administrateur
                                                        </span>
                                                    </div>

                                                    <div class="d-flex flex-column gap-1 mt-2 fs-13">
                                                        {{-- Email --}}
                                                        <div class="d-flex align-items-center justify-content-between text-muted">
                                                            <span class="text-truncate me-2">
                                                                <i class="ri-mail-line me-1 text-primary"></i> {{ $admin->email }}
                                                            </span>
                                                            <button type="button" class="btn btn-xs btn-link text-muted p-0" onclick="copyToClipboard('{{ $admin->email }}', 'Email administrateur copié !')" title="Copier">
                                                                <i class="ri-file-copy-line fs-12"></i>
                                                            </button>
                                                        </div>

                                                        {{-- Téléphone --}}
                                                        @if($admin->phone)
                                                            <div class="d-flex align-items-center justify-content-between text-muted">
                                                                <span>
                                                                    <i class="ri-phone-line me-1 text-success"></i> {{ $admin->phone }}
                                                                </span>
                                                                <button type="button" class="btn btn-xs btn-link text-muted p-0" onclick="copyToClipboard('{{ $admin->phone }}', 'Téléphone copié !')" title="Copier">
                                                                    <i class="ri-file-copy-line fs-12"></i>
                                                                </button>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center py-4 text-muted">
                                            <i class="ri-user-unfollow-line fs-32 text-muted opacity-50 d-block mb-2"></i>
                                            <span>Aucun compte administrateur identifié pour cette église.</span>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- ========================================================================= --}}
                {{-- SECTION 3 : HISTORIQUE DES ABONNEMENTS (RESPONSIVE TABLE + MOBILE CARDS) --}}
                {{-- ========================================================================= --}}
                <div class="card premium-card overflow-hidden mb-4">
                    <div class="card-header bg-transparent border-0 py-3 px-4 d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2" 
                         style="border-bottom: 1px solid rgba(var(--vz-dark-rgb), 0.05) !important;">
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar-xs bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center">
                                <i class="ri-history-line fs-16"></i>
                            </div>
                            <div>
                                <h5 class="card-title mb-0 fw-bold fs-15 text-body">Historique des Abonnements (1 An)</h5>
                                <span class="fs-12 text-muted">Tous les cycles et paiements enregistrés</span>
                            </div>
                        </div>
                        <a href="{{ route('super-admin.churches.renew.form', $church) }}" class="btn btn-sm btn-success rounded-pill px-3 fs-12 fw-semibold shadow-sm">
                            <i class="ri-add-circle-line me-1"></i> Nouveau Renouvellement
                        </a>
                    </div>

                    <div class="card-body p-0">
                        
                        {{-- Vue Tableau Desktop (d-none d-md-block) --}}
                        <div class="table-responsive d-none d-md-block">
                            <table class="table table-premium align-middle table-hover table-nowrap mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-4">Formule</th>
                                        <th>Période de Validité</th>
                                        <th>Montant</th>
                                        <th>Moyen de Paiement</th>
                                        <th>Référence</th>
                                        <th>Statut</th>
                                        <th class="text-end pe-4">Enregistré par</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($subscriptions as $sub)
                                        <tr class="border-bottom border-light-subtle">
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="avatar-xs bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center">
                                                        <i class="ri-calendar-check-line fs-14"></i>
                                                    </div>
                                                    <span class="fw-semibold text-body fs-13">{{ $sub->plan_name }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="fw-medium text-body fs-13">
                                                        {{ $sub->starts_at->format('d/m/Y') }} ➔ {{ $sub->expires_at->format('d/m/Y') }}
                                                    </span>
                                                    <span class="fs-11 text-muted">Cycle Annuel (365 jours)</span>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fs-12 fw-bold">
                                                    {{ number_format($sub->amount, 0, ',', ' ') }} FCFA
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-body-tertiary text-body border px-2 py-1 fs-12">
                                                    <i class="ri-bank-card-line me-1 text-muted"></i>{{ $sub->payment_method ?? 'Espèces' }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="font-monospace fs-12 text-muted bg-body-tertiary px-2 py-0-5 rounded border">
                                                    {{ $sub->payment_reference ?? '—' }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($sub->status === 'cancelled')
                                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 fs-11">Annulé</span>
                                                @elseif($sub->isActive())
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fs-11">
                                                        <i class="ri-checkbox-circle-line me-1"></i>En cours
                                                    </span>
                                                @else
                                                    <span class="badge bg-body-tertiary text-muted border px-2 py-1 fs-11">Échu</span>
                                                @endif
                                            </td>
                                            <td class="text-end pe-4">
                                                <span class="fs-12 text-muted">
                                                    {{ $sub->creator ? $sub->creator->first_name . ' ' . $sub->creator->name : 'Système' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4 text-muted">
                                                <i class="ri-file-list-3-line fs-32 text-muted opacity-50 d-block mb-1"></i>
                                                Aucun historique d'abonnement pour cette église.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Vue Cartes Mobile (d-block d-md-none) --}}
                        <div class="d-block d-md-none p-3">
                            <div class="d-flex flex-column gap-3">
                                @forelse($subscriptions as $sub)
                                    <div class="card border rounded-3 p-3 bg-white shadow-none mb-0">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="fw-bold text-body fs-14">{{ $sub->plan_name }}</span>
                                            @if($sub->status === 'cancelled')
                                                <span class="badge bg-danger-subtle text-danger fs-11">Annulé</span>
                                            @elseif($sub->isActive())
                                                <span class="badge bg-success-subtle text-success fs-11">En cours</span>
                                            @else
                                                <span class="badge bg-body-tertiary text-muted fs-11">Échu</span>
                                            @endif
                                        </div>

                                        <div class="bg-body-tertiary rounded-2 p-2 fs-13 d-flex flex-column gap-1 mb-2">
                                            <div class="d-flex justify-content-between">
                                                <span class="text-muted">Période :</span>
                                                <span class="fw-medium text-body">{{ $sub->starts_at->format('d/m/Y') }} ➔ {{ $sub->expires_at->format('d/m/Y') }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span class="text-muted">Montant :</span>
                                                <strong class="text-success">{{ number_format($sub->amount, 0, ',', ' ') }} FCFA</strong>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span class="text-muted">Paiement :</span>
                                                <span class="text-body">{{ $sub->payment_method ?? 'Espèces' }}</span>
                                            </div>
                                            @if($sub->payment_reference)
                                                <div class="d-flex justify-content-between">
                                                    <span class="text-muted">Réf :</span>
                                                    <span class="font-monospace fs-11 text-muted">{{ $sub->payment_reference }}</span>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="fs-11 text-muted text-end">
                                            Enregistré par : {{ $sub->creator ? $sub->creator->first_name . ' ' . $sub->creator->name : 'Système' }}
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-4 text-muted">
                                        <i class="ri-file-list-3-line fs-32 text-muted opacity-50 d-block mb-1"></i>
                                        Aucun historique d'abonnement.
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

@endsection

@push('scripts')
<script>
    // Presse-papier Toast rapide
    function copyToClipboard(text, message) {
        if (!navigator.clipboard) {
            const temp = document.createElement('input');
            temp.value = text;
            document.body.appendChild(temp);
            temp.select();
            document.execCommand('copy');
            document.body.removeChild(temp);
        } else {
            navigator.clipboard.writeText(text);
        }
        
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: message || 'Copié dans le presse-papier',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            });
        }
    }
</script>
@endpush
