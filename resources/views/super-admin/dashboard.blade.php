@extends('layouts.app')

@section('title', 'Supervision SaaS & Administration')

@push('css')
<style>
    /* Hero Section - Synchronisé avec /dashboard */
    .saas-hero {
        background-color: #0b0f19;
        background-image: 
            radial-gradient(circle at 0% 0%, rgba(var(--vz-primary-rgb), 0.3) 0%, transparent 50%),
            radial-gradient(circle at 100% 100%, rgba(var(--vz-warning-rgb), 0.2) 0%, transparent 50%);
        padding: 3rem 0 5rem 0;
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
        font-size: 0.95rem;
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
        font-size: 0.95rem;
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

    @media (max-width: 991.98px) {
        .saas-hero {
            padding: 1.5rem 0 3.75rem 0;
            margin: -1rem -1rem 1.5rem -1rem;
            border-bottom-left-radius: 1.25rem;
            border-bottom-right-radius: 1.25rem;
            text-align: center;
        }
        .saas-hero .display-6 {
            font-size: 1.45rem !important;
            margin-bottom: 0.25rem !important;
        }
        .saas-hero .hero-badge {
            font-size: 0.68rem !important;
            padding: 4px 10px !important;
        }
        .saas-hero .pulse-dot {
            width: 6px;
            height: 6px;
        }
        .hero-orb {
            opacity: 0.35;
            filter: blur(50px);
        }
        .orb-1 { width: 180px; height: 180px; top: -70px; left: -50px; }
        .orb-2 { width: 160px; height: 160px; bottom: -50px; right: 0; }
        .hero-actions-container {
            justify-content: center !important;
            margin-top: 0.85rem;
        }
        .saas-cta-btn, .saas-secondary-btn {
            padding: 8px 16px;
            font-size: 0.82rem;
            gap: 6px;
        }
        .saas-kpi-row {
            margin-top: -2.75rem !important;
        }
    }
    @media (max-width: 575.98px) {
        .saas-hero {
            padding: 1.25rem 0 3.25rem 0;
            margin: -0.75rem -0.75rem 1.25rem -0.75rem;
        }
        .saas-hero .display-6 {
            font-size: 1.25rem !important;
        }
        .saas-hero .badge {
            font-size: 0.65rem !important;
            padding: 3px 8px !important;
        }
        .hero-actions-container .d-flex {
            flex-direction: row !important;
            flex-wrap: wrap;
            justify-content: center !important;
        }
        .saas-cta-btn, .saas-secondary-btn {
            flex: 1 1 auto;
            min-width: 135px;
            justify-content: center;
            padding: 7px 12px;
            font-size: 0.78rem;
        }
        .saas-kpi-row {
            margin-top: -2.25rem !important;
        }
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="h-100">

            {{-- ========================================================================= --}}
            {{-- HERO SECTION (IDENTIQUE AU DESIGN /DASHBOARD) --}}
            {{-- ========================================================================= --}}
            <div class="saas-hero px-3 px-md-4">
                <div class="hero-grid"></div>
                <div class="hero-orb orb-1"></div>
                <div class="hero-orb orb-2"></div>
                
                <div class="container-fluid max-w-1200 hero-content">
                    <div class="row align-items-center">
                        <div class="col-lg-7">
                            <div class="mb-2 mb-md-3 d-flex justify-content-center justify-content-lg-start gap-2 flex-wrap">
                                <span class="badge hero-badge px-3 py-2 rounded-pill shadow-sm" style="font-size: 0.75rem; letter-spacing: 1px;">
                                    <i class="ri-shield-star-line me-1"></i> PORTAIL SUPER ADMINISTRATEUR
                                </span>
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill d-inline-flex align-items-center gap-1" style="font-size: 0.75rem;">
                                    <span class="pulse-dot bg-success"></span> SaaS 100% Opérationnel
                                </span>
                            </div>
                            <h1 class="display-6 fw-bold text-white mb-1 mb-md-2 text-center text-lg-start" style="line-height: 1.2;">
                                Supervision <span style="color: var(--vz-warning); filter: brightness(1.2);">SaaS Multi-Églises</span>
                            </h1>
                            <p class="fs-15 mb-0 d-none d-lg-block" style="max-width: 600px; line-height: 1.6; color: rgba(255,255,255,0.75);">
                                Vue d'ensemble des églises clientes, gestion des abonnements annuels (1 an) et administration centralisée de la plateforme.
                            </p>
                            <p class="fs-13 mb-0 d-lg-none text-center" style="color: rgba(255,255,255,0.75);">
                                Gestion centralisée des églises et abonnements annuels.
                            </p>
                        </div>

                        <div class="col-lg-5 d-flex justify-content-lg-end hero-actions-container">
                            <div class="text-center text-lg-end d-flex flex-column flex-sm-row gap-2 justify-content-center">
                                <a href="{{ route('super-admin.churches.create') }}" class="saas-cta-btn">
                                    <i class="ri-add-circle-line fs-18"></i> Inscrire une église
                                </a>
                                <a href="{{ route('super-admin.churches.index') }}" class="saas-secondary-btn">
                                    <i class="ri-building-line fs-18"></i> Toutes les églises
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ========================================================================= --}}
            {{-- 4 CARTES KPI (STATS ROW) - DESIGN /DASHBOARD RESPONSIVE --}}
            {{-- ========================================================================= --}}
            <div class="row g-2 g-md-3 saas-kpi-row" style="margin-top: -5rem; position: relative; z-index: 10;">
                {{-- Total Églises --}}
                <div class="col-6 col-md-6 col-xl-3">
                    <div class="card premium-card overflow-hidden h-100">
                        <div class="card-body p-2 p-sm-3 p-md-4">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1 overflow-hidden">
                                    <p class="text-uppercase fw-semibold text-muted text-truncate mb-0 fs-10 fs-md-12" style="letter-spacing: 0.5px;">Églises</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-2 mt-md-3">
                                <div>
                                    <h4 class="fs-18 fs-md-24 fw-bold ff-secondary mb-1 mb-md-2">{{ $totalChurches }}</h4>
                                    <span class="text-success fw-medium fs-11 fs-md-13">
                                        <i class="ri-checkbox-circle-line me-1"></i>{{ $activeChurches }} active(s)
                                    </span>
                                </div>
                                <div class="avatar-xs avatar-sm-md flex-shrink-0">
                                    <span class="avatar-title bg-primary-subtle text-primary rounded fs-16 fs-md-20 shadow-sm">
                                        <i class="ri-building-4-line"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Abonnements 1 An --}}
                <div class="col-6 col-md-6 col-xl-3">
                    <div class="card premium-card overflow-hidden h-100">
                        <div class="card-body p-2 p-sm-3 p-md-4">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1 overflow-hidden">
                                    <p class="text-uppercase fw-semibold text-muted text-truncate mb-0 fs-10 fs-md-12" style="letter-spacing: 0.5px;">Abonnements 1 An</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-2 mt-md-3">
                                <div>
                                    <h4 class="fs-18 fs-md-24 fw-bold ff-secondary mb-1 mb-md-2 text-success">{{ $activeChurches }}</h4>
                                    <span class="text-muted fw-medium fs-11 fs-md-13">
                                        {{ $expiredChurches }} expiré(s)
                                    </span>
                                </div>
                                <div class="avatar-xs avatar-sm-md flex-shrink-0">
                                    <span class="avatar-title bg-success-subtle text-success rounded fs-16 fs-md-20 shadow-sm">
                                        <i class="ri-calendar-check-line"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Revenus SaaS --}}
                <div class="col-6 col-md-6 col-xl-3">
                    <div class="card premium-card overflow-hidden h-100">
                        <div class="card-body p-2 p-sm-3 p-md-4">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1 overflow-hidden">
                                    <p class="text-uppercase fw-semibold text-muted text-truncate mb-0 fs-10 fs-md-12" style="letter-spacing: 0.5px;">Revenus SaaS</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-2 mt-md-3">
                                <div class="min-w-0 pe-1">
                                    <h4 class="fs-16 fs-md-22 fw-bold ff-secondary mb-1 mb-md-2 text-truncate">{{ number_format($totalRevenue, 0, ',', ' ') }} <small class="fs-11 text-muted">FCFA</small></h4>
                                    <span class="text-warning fw-medium fs-11 fs-md-13 text-truncate d-block">
                                        {{ number_format($yearlyRevenue, 0, ',', ' ') }} FCFA / an
                                    </span>
                                </div>
                                <div class="avatar-xs avatar-sm-md flex-shrink-0">
                                    <span class="avatar-title bg-warning-subtle text-warning rounded fs-16 fs-md-20 shadow-sm">
                                        <i class="ri-money-dollar-circle-line"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Membres Globaux --}}
                <div class="col-6 col-md-6 col-xl-3">
                    <div class="card premium-card overflow-hidden h-100">
                        <div class="card-body p-2 p-sm-3 p-md-4">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1 overflow-hidden">
                                    <p class="text-uppercase fw-semibold text-muted text-truncate mb-0 fs-10 fs-md-12" style="letter-spacing: 0.5px;">Membres Globaux</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-2 mt-md-3">
                                <div>
                                    <h4 class="fs-18 fs-md-24 fw-bold ff-secondary mb-1 mb-md-2">{{ $totalUsers }}</h4>
                                    <span class="text-info fw-medium fs-11 fs-md-13">
                                        ~{{ $avgMembersPerChurch }}/église
                                    </span>
                                </div>
                                <div class="avatar-xs avatar-sm-md flex-shrink-0">
                                    <span class="avatar-title bg-info-subtle text-info rounded fs-16 fs-md-20 shadow-sm">
                                        <i class="ri-team-line"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ========================================================================= --}}
            {{-- ALERTE : ÉGLISES DONT L'ABONNEMENT EXPIRE SOUS 30 JOURS --}}
            {{-- ========================================================================= --}}
            @if($expiringChurches->isNotEmpty())
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card premium-card overflow-hidden border border-warning-subtle">
                            <div class="card-header border-0 bg-warning-subtle py-3 px-3 px-md-4 d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-xs bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="ri-alarm-warning-line fs-16"></i>
                                    </div>
                                    <div>
                                        <h6 class="card-title mb-0 fw-bold fs-14 text-dark">
                                            Abonnements expirant sous 30 jours ({{ $expiringChurches->count() }})
                                        </h6>
                                        <span class="fs-11 text-muted d-none d-sm-inline">Échéance imminente nécessitant un renouvellement.</span>
                                    </div>
                                </div>
                                <span class="badge bg-warning text-dark rounded-pill px-3 py-1 fs-11 fw-semibold">
                                    Action requise
                                </span>
                            </div>

                            <div class="card-body p-0">
                                {{-- Mobile View (Cards) --}}
                                <div class="d-block d-md-none p-3">
                                    <div class="d-flex flex-column gap-2">
                                        @foreach($expiringChurches as $ch)
                                            <div class="card border border-warning-subtle rounded-3 shadow-none mb-1 p-3 bg-light-subtle">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <div>
                                                        <h6 class="fs-14 fw-bold mb-1">
                                                            <a href="{{ route('super-admin.churches.show', $ch) }}" class="text-body">{{ $ch->name }}</a>
                                                        </h6>
                                                        <span class="text-muted fs-11">
                                                            <i class="ri-map-pin-line me-1"></i>{{ $ch->city ?? 'Bénin' }}
                                                        </span>
                                                    </div>
                                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2 py-1 fs-11">
                                                        {{ $ch->daysLeftInSubscription() }} jour(s)
                                                    </span>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top border-light-subtle">
                                                    <span class="fw-bold fs-12 text-body">
                                                        {{ number_format($ch->subscription_amount, 0, ',', ' ') }} FCFA
                                                    </span>
                                                    <a href="{{ route('super-admin.churches.renew.form', $ch) }}" class="btn btn-sm btn-primary rounded-pill px-3 fs-12">
                                                        <i class="ri-refresh-line me-1"></i> Renouveler
                                                    </a>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Desktop View - Table 100% sans scroll horizontal --}}
                                <div class="d-none d-md-block">
                                    <table class="table table-hover align-middle mb-0 table-premium w-100" style="table-layout: fixed;">
                                        <thead>
                                            <tr class="fs-11">
                                                <th class="ps-4" style="width: 35%;">Église</th>
                                                <th style="width: 20%;">Échéance</th>
                                                <th style="width: 15%;">Jours restants</th>
                                                <th style="width: 15%;">Tarif Annuel</th>
                                                <th class="text-end pe-4" style="width: 15%;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($expiringChurches as $ch)
                                                <tr>
                                                    <td class="ps-4">
                                                        <div class="d-flex align-items-center gap-2 min-w-0">
                                                            <div class="avatar-xs bg-warning-subtle text-warning rounded-circle d-flex align-items-center justify-content-center fw-bold fs-12 flex-shrink-0">
                                                                {{ strtoupper(substr($ch->name, 0, 2)) }}
                                                            </div>
                                                            <div class="min-w-0 flex-grow-1">
                                                                <h6 class="fs-13 mb-0 fw-bold text-truncate">
                                                                    <a href="{{ route('super-admin.churches.show', $ch) }}" class="text-body">{{ $ch->name }}</a>
                                                                </h6>
                                                                <span class="text-muted fs-11 text-truncate d-block">{{ $ch->city ?? 'Bénin' }}</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="fs-12 text-body fw-medium">
                                                        {{ $ch->subscription_expires_at ? $ch->subscription_expires_at->format('d/m/Y') : '—' }}
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2 py-1 fs-11">
                                                            <i class="ri-time-line me-1"></i>{{ $ch->daysLeftInSubscription() }}j
                                                        </span>
                                                    </td>
                                                    <td class="fs-12 fw-bold text-body">
                                                        {{ number_format($ch->subscription_amount, 0, ',', ' ') }} <small class="text-muted fs-10">FCFA</small>
                                                    </td>
                                                    <td class="text-end pe-4">
                                                        <a href="{{ route('super-admin.churches.renew.form', $ch) }}" class="btn btn-sm btn-primary rounded-pill px-2 py-1 fs-11">
                                                            <i class="ri-refresh-line me-1"></i>Renouveler
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ========================================================================= --}}
            {{-- DEUX COLONNES RESPONSIVES : ÉGLISES RÉCENTES & DERNIERS PAIEMENTS --}}
            {{-- SANS AUCUN DÉFILEMENT HORIZONTAL --}}
            {{-- ========================================================================= --}}
            <div class="row g-3 g-lg-4 mt-1 mb-4 mb-md-5 pb-3">
                {{-- Colonne 1 : Églises clientes récentes --}}
                <div class="col-12 col-xl-6">
                    <div class="card premium-card overflow-hidden h-100">
                        <div class="card-header border-0 bg-transparent py-3 px-3 px-md-4 border-bottom border-light-subtle d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-xs bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="ri-building-line fs-16"></i>
                                </div>
                                <h5 class="card-title mb-0 fw-bold fs-15 text-body">Églises Récentes</h5>
                            </div>
                            <a href="{{ route('super-admin.churches.index') }}" class="btn btn-sm btn-soft-primary rounded-pill fs-12 px-3">
                                Tout voir <i class="ri-arrow-right-line ms-1"></i>
                            </a>
                        </div>

                        <div class="card-body p-0">
                            {{-- Mobile View (Cards) - Pas de scroll horizontal --}}
                            <div class="d-block d-md-none p-3">
                                <div class="d-flex flex-column gap-2">
                                    @forelse($recentChurches as $church)
                                        <div class="card border border-light-subtle rounded-3 shadow-none mb-1 p-3">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="avatar-xs bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold fs-11 flex-shrink-0">
                                                        {{ strtoupper(substr($church->name, 0, 2)) }}
                                                    </div>
                                                    <div>
                                                        <h6 class="fs-13 fw-bold mb-0">
                                                            <a href="{{ route('super-admin.churches.show', $church) }}" class="text-body">{{ $church->name }}</a>
                                                        </h6>
                                                        <span class="text-muted fs-11">
                                                            <i class="ri-map-pin-line me-1"></i>{{ $church->city ?? 'Bénin' }}
                                                        </span>
                                                    </div>
                                                </div>
                                                @if($church->isSubscriptionActive())
                                                    <span class="badge bg-success-subtle text-success rounded-pill px-2 py-1 fs-10">Actif</span>
                                                @else
                                                    <span class="badge bg-danger-subtle text-danger rounded-pill px-2 py-1 fs-10">Expiré</span>
                                                @endif
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top border-light-subtle fs-12">
                                                <span class="text-muted fs-11">
                                                    <i class="ri-team-line me-1"></i>{{ $church->users_count }} membre(s)
                                                </span>
                                                <div class="d-flex gap-1">
                                                    <a href="{{ route('super-admin.churches.impersonate', $church) }}" class="btn btn-sm btn-soft-warning rounded-pill px-2 py-0 fs-11">
                                                        Support
                                                    </a>
                                                    <a href="{{ route('super-admin.churches.show', $church) }}" class="btn btn-sm btn-soft-primary rounded-pill px-2 py-0 fs-11">
                                                        Détails
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center py-4 text-muted fs-13">
                                            Aucune église enregistrée.
                                        </div>
                                    @endforelse
                                </div>
                            </div>

                            {{-- Desktop View - Table 100% sans aucun scroll horizontal --}}
                            <div class="d-none d-md-block">
                                <table class="table table-hover align-middle mb-0 table-premium w-100" style="table-layout: fixed;">
                                    <thead>
                                        <tr class="fs-11">
                                            <th class="ps-4" style="width: 44%;">Église</th>
                                            <th style="width: 22%;">Statut</th>
                                            <th style="width: 16%;">Membres</th>
                                            <th class="text-end pe-4" style="width: 18%;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentChurches as $church)
                                            <tr>
                                                <td class="ps-4">
                                                    <div class="d-flex align-items-center gap-2 min-w-0">
                                                        <div class="avatar-xs bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold fs-11 flex-shrink-0">
                                                            {{ strtoupper(substr($church->name, 0, 2)) }}
                                                        </div>
                                                        <div class="min-w-0 flex-grow-1">
                                                            <h6 class="fs-13 mb-0 fw-bold text-truncate">
                                                                <a href="{{ route('super-admin.churches.show', $church) }}" class="text-body">{{ $church->name }}</a>
                                                            </h6>
                                                            <span class="text-muted fs-11 text-truncate d-block">{{ $church->city ?? 'Bénin' }}</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($church->isSubscriptionActive())
                                                        <span class="badge bg-success-subtle text-success rounded-pill px-2 py-1 fs-11">
                                                            <i class="ri-checkbox-circle-line me-1"></i>Actif
                                                        </span>
                                                    @else
                                                        <span class="badge bg-danger-subtle text-danger rounded-pill px-2 py-1 fs-11">
                                                            <i class="ri-error-warning-line me-1"></i>Expiré
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="fs-12 text-muted text-truncate">
                                                    {{ $church->users_count }}
                                                </td>
                                                <td class="text-end pe-4">
                                                    <div class="d-inline-flex gap-1 justify-content-end">
                                                        <a href="{{ route('super-admin.churches.impersonate', $church) }}" class="btn btn-sm btn-soft-warning rounded-pill px-2 py-1 fs-11" title="Mode Support">
                                                            <i class="ri-customer-service-2-line"></i>
                                                        </a>
                                                        <a href="{{ route('super-admin.churches.show', $church) }}" class="btn btn-sm btn-soft-primary rounded-pill px-2 py-1 fs-11" title="Voir fiche">
                                                            <i class="ri-arrow-right-line"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-4 text-muted fs-13">Aucune église enregistrée.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Colonne 2 : Derniers paiements d'abonnements (SANS SCROLL GAUCHE DROITE) --}}
                <div class="col-12 col-xl-6">
                    <div class="card premium-card overflow-hidden h-100">
                        <div class="card-header border-0 bg-transparent py-3 px-3 px-md-4 border-bottom border-light-subtle d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-xs bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="ri-money-dollar-circle-line fs-16"></i>
                                </div>
                                <h5 class="card-title mb-0 fw-bold fs-15 text-body">Derniers Paiements d'Abonnement</h5>
                            </div>
                            <span class="badge bg-success-subtle text-success rounded-pill fs-11 px-3 py-1">
                                1 An / 150 000 FCFA
                            </span>
                        </div>

                        <div class="card-body p-0">
                            {{-- Mobile View (Cards) - Pas de scroll horizontal --}}
                            <div class="d-block d-md-none p-3">
                                <div class="d-flex flex-column gap-2">
                                    @forelse($recentSubscriptions as $sub)
                                        <div class="card border border-light-subtle rounded-3 shadow-none mb-1 p-3">
                                            <div class="d-flex justify-content-between align-items-start mb-1">
                                                <div class="min-w-0 pe-2">
                                                    <h6 class="fs-13 fw-bold mb-0 text-truncate">{{ $sub->church->name ?? 'Église' }}</h6>
                                                    <span class="text-muted fs-11">
                                                        <i class="ri-time-line me-1"></i>{{ $sub->created_at->format('d/m/Y H:i') }}
                                                    </span>
                                                </div>
                                                <span class="text-success fw-bold fs-13 flex-shrink-0">
                                                    {{ number_format($sub->amount, 0, ',', ' ') }} FCFA
                                                </span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top border-light-subtle fs-11 text-muted">
                                                <span><i class="ri-calendar-line me-1"></i>Jusqu'au {{ $sub->expires_at ? $sub->expires_at->format('d/m/Y') : '1 an' }}</span>
                                                <span class="badge bg-light text-muted border rounded-pill px-2 py-1">{{ $sub->payment_method ?? 'Espèces' }}</span>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center py-4 text-muted fs-13">
                                            Aucun paiement enregistré.
                                        </div>
                                    @endforelse
                                </div>
                            </div>

                            {{-- Desktop View - Mise en page compacte sans débordement --}}
                            <div class="d-none d-md-block">
                                <table class="table table-hover align-middle mb-0 table-premium w-100">
                                    <thead>
                                        <tr class="fs-11">
                                            <th class="ps-4" style="width: 40%;">Église</th>
                                            <th style="width: 25%;">Montant</th>
                                            <th style="width: 20%;">Validité</th>
                                            <th class="text-end pe-4" style="width: 15%;">Mode</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentSubscriptions as $sub)
                                            <tr>
                                                <td class="ps-4">
                                                    <div class="min-w-0">
                                                        <h6 class="fs-13 mb-0 fw-bold text-truncate">{{ $sub->church->name ?? 'Église' }}</h6>
                                                        <span class="text-muted fs-11">{{ $sub->created_at->translatedFormat('d M Y à H:i') }}</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="text-success fw-bold fs-13">
                                                        {{ number_format($sub->amount, 0, ',', ' ') }} <small class="text-muted fs-10">FCFA</small>
                                                    </span>
                                                </td>
                                                <td class="fs-12 text-muted">
                                                    {{ $sub->expires_at ? $sub->expires_at->format('d/m/Y') : '1 an' }}
                                                </td>
                                                <td class="text-end pe-4">
                                                    <span class="badge bg-light text-muted border rounded-pill px-2 py-1 fs-11">
                                                        {{ $sub->payment_method ?? 'Espèces' }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-4 text-muted fs-13">Aucun paiement enregistré.</td>
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
    </div>
</div>
@endsection
