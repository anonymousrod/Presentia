@extends('layouts.app')

@section('title', 'Gestion des Églises Clientes')

@push('css')
<style>
    /* =========================================================================
       HERO SECTION
    ========================================================================= */
    .churches-hero {
        background-color: #0b0f19;
        background-image:
            radial-gradient(circle at 10% 20%, rgba(var(--vz-primary-rgb), 0.35) 0%, transparent 45%),
            radial-gradient(circle at 90% 80%, rgba(var(--vz-warning-rgb), 0.25) 0%, transparent 45%),
            radial-gradient(circle at 50% 0%, rgba(var(--vz-info-rgb), 0.12) 0%, transparent 50%);
        padding: 2.75rem 0 6rem 0;
        position: relative;
        overflow: hidden;
        margin: -1.5rem -1.5rem 0 -1.5rem;
        border-bottom-left-radius: 2.5rem;
        border-bottom-right-radius: 2.5rem;
    }

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

    .hero-orb {
        position: absolute;
        border-radius: 50%;
        filter: blur(90px);
        opacity: 0.5;
        z-index: 0;
        animation: orbFloat 14s infinite alternate ease-in-out;
    }
    .orb-blue  { width: 380px; height: 380px; background: var(--vz-primary); top: -140px; left: -100px; }
    .orb-amber { width: 300px; height: 300px; background: var(--vz-warning); bottom: -80px; right: 6%; animation-delay: -7s; }
    .orb-teal  { width: 200px; height: 200px; background: var(--vz-info);    top: 30%;    right: 30%;  animation-delay: -3s; opacity: 0.25; }

    @keyframes orbFloat {
        0%   { transform: translateY(0) scale(1); }
        100% { transform: translateY(28px) scale(1.07); }
    }

    .hero-content { position: relative; z-index: 5; }

    .hero-badge {
        background: rgba(var(--vz-warning-rgb), 0.15);
        border: 1px solid rgba(var(--vz-warning-rgb), 0.35);
        color: #f7b84b;
        backdrop-filter: blur(6px);
        font-size: 0.72rem;
        letter-spacing: 1.2px;
        font-weight: 600;
    }

    /* =========================================================================
       BOUTONS HERO
    ========================================================================= */
    .btn-hero-primary {
        padding: 11px 22px;
        font-size: 0.9rem;
        font-weight: 600;
        border-radius: 50px;
        box-shadow: 0 8px 22px rgba(247, 184, 75, 0.38);
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: linear-gradient(135deg, #f7b84b 0%, #d97706 100%);
        border: none;
        color: #111827;
        text-decoration: none;
        white-space: nowrap;
    }
    .btn-hero-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 14px 28px rgba(247, 184, 75, 0.52);
        color: #111827;
    }
    .btn-hero-secondary {
        padding: 11px 22px;
        font-size: 0.9rem;
        font-weight: 500;
        border-radius: 50px;
        background: rgba(255,255,255,0.1);
        border: 1px solid rgba(255,255,255,0.22);
        color: #fff;
        backdrop-filter: blur(8px);
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        white-space: nowrap;
    }
    .btn-hero-secondary:hover {
        background: rgba(255,255,255,0.2);
        color: #fff;
        transform: translateY(-2px);
    }

    /* =========================================================================
       KPI CARDS FLOTTANTES
    ========================================================================= */
    .kpi-row {
        margin-top: -4.5rem;
        position: relative;
        z-index: 10;
    }

    .kpi-card {
        border-radius: 1.25rem;
        border: none;
        box-shadow: 0 8px 32px rgba(0,0,0,0.1), 0 1px 4px rgba(0,0,0,0.04);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        cursor: default;
    }
    .kpi-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 16px 40px rgba(0,0,0,0.13);
    }
    .kpi-icon {
        width: 46px; height: 46px;
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.3rem;
        flex-shrink: 0;
    }
    .kpi-value {
        font-size: 1.75rem;
        font-weight: 800;
        line-height: 1;
        letter-spacing: -0.5px;
    }
    .kpi-label {
        font-size: 0.72rem;
        letter-spacing: 0.6px;
        text-transform: uppercase;
        font-weight: 600;
    }

    /* =========================================================================
       FILTRE CARD
    ========================================================================= */
    .filter-card {
        border-radius: 1.25rem;
        border: none;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
    }
    .filter-card .form-control,
    .filter-card .form-select {
        border-radius: 10px;
        border-color: var(--vz-border-color);
        font-size: 0.875rem;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .filter-card .form-control:focus,
    .filter-card .form-select:focus {
        border-color: var(--vz-primary);
        box-shadow: 0 0 0 3px rgba(var(--vz-primary-rgb), 0.12);
    }
    .filter-card .input-group-text {
        border-radius: 10px 0 0 10px;
        background: var(--vz-light);
        border-color: var(--vz-border-color);
    }
    .quick-filter-badge {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 5px 14px;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s ease;
        border: 1.5px solid transparent;
    }
    .quick-filter-badge.active-all   { background: var(--vz-primary); color: #fff; }
    .quick-filter-badge.inactive-all { background: var(--vz-light); color: var(--vz-secondary); border-color: var(--vz-border-color); }
    .quick-filter-badge.active-ok    { background: #22c55e; color: #fff; }
    .quick-filter-badge.inactive-ok  { background: rgba(34,197,94,.1); color: #16a34a; border-color: rgba(34,197,94,.3); }
    .quick-filter-badge.active-exp   { background: var(--vz-danger); color: #fff; }
    .quick-filter-badge.inactive-exp { background: rgba(var(--vz-danger-rgb),.1); color: var(--vz-danger); border-color: rgba(var(--vz-danger-rgb),.3); }
    .quick-filter-badge.active-sus   { background: #6b7280; color: #fff; }
    .quick-filter-badge.inactive-sus { background: rgba(107,114,128,.1); color: #6b7280; border-color: rgba(107,114,128,.3); }
    .quick-filter-badge:hover { filter: brightness(1.08); transform: translateY(-1px); }

    /* =========================================================================
       TABLE PREMIUM
    ========================================================================= */
    .table-card {
        border-radius: 1.25rem;
        border: none;
        box-shadow: 0 4px 24px rgba(0,0,0,0.06);
        overflow: visible !important;
    }
    .table-card .card-header {
        border-radius: 1.25rem 1.25rem 0 0;
        background: transparent;
    }

    .table-churches th {
        font-size: 0.68rem;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        font-weight: 700;
        color: var(--vz-gray-500);
        background: var(--vz-light);
        padding: 14px 16px;
        border-bottom: 1px solid var(--vz-border-color);
    }
    .table-churches td {
        padding: 14px 16px;
        vertical-align: middle;
        border-color: var(--vz-border-color);
    }
    .table-churches tbody tr {
        transition: background 0.15s ease;
    }
    .table-churches tbody tr:hover {
        background: rgba(var(--vz-primary-rgb), 0.03);
    }

    /* Avatar Église */
    .church-avatar {
        width: 42px; height: 42px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.95rem;
        font-weight: 800;
        flex-shrink: 0;
        letter-spacing: -0.5px;
    }

    /* Statut badge enrichi */
    .status-pill {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 5px 12px;
        border-radius: 50px;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.3px;
    }
    .status-dot {
        width: 7px; height: 7px;
        border-radius: 50%;
        flex-shrink: 0;
    }
    .status-active  { background: rgba(34,197,94,.12); color: #16a34a; }
    .status-expired { background: rgba(var(--vz-danger-rgb),.1); color: var(--vz-danger); }
    .status-suspended { background: rgba(107,114,128,.12); color: #6b7280; }
    .dot-active    { background: #22c55e; animation: pulseDot 2s infinite; }
    .dot-expired   { background: var(--vz-danger); }
    .dot-suspended { background: #9ca3af; }

    @keyframes pulseDot {
        0%, 100% { transform: scale(1); opacity: 1; }
        50%       { transform: scale(1.4); opacity: 0.7; }
    }

    /* Jours restants progress */
    .days-bar { height: 4px; border-radius: 2px; background: var(--vz-light); overflow: hidden; }
    .days-bar-fill { height: 100%; border-radius: 2px; transition: width 0.4s ease; }

    /* Actions directes (plus de dropdown) */
    .action-btn {
        width: 32px; height: 32px;
        border-radius: 9px;
        border: none;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 1rem;
        transition: all 0.2s ease;
        cursor: pointer;
        text-decoration: none;
        flex-shrink: 0;
    }
    .action-btn:hover { transform: translateY(-2px); filter: brightness(1.1); }
    .action-btn-view    { background: rgba(var(--vz-primary-rgb),.1); color: var(--vz-primary); }
    .action-btn-renew   { background: rgba(34,197,94,.1); color: #16a34a; }
    .action-btn-support { background: rgba(var(--vz-warning-rgb),.12); color: var(--vz-warning); }
    .action-btn-edit    { background: rgba(var(--vz-secondary-rgb),.08); color: var(--vz-secondary); }
    .action-btn-suspend { background: rgba(var(--vz-danger-rgb),.08); color: var(--vz-danger); }
    .action-btn-activate{ background: rgba(34,197,94,.1); color: #16a34a; }
    .action-btn-delete  { background: rgba(var(--vz-danger-rgb),.1); color: var(--vz-danger); }
    .action-btn-delete:hover { background: var(--vz-danger) !important; color: #fff !important; }

    /* =========================================================================
       MOBILE CARDS
    ========================================================================= */
    .mobile-church-card {
        border-radius: 1rem;
        border: 1.5px solid var(--vz-border-color);
        background: var(--vz-card-bg);
        transition: box-shadow 0.2s, transform 0.2s;
        overflow: hidden;
    }
    .mobile-church-card:hover {
        box-shadow: 0 6px 20px rgba(0,0,0,0.08);
        transform: translateY(-2px);
    }
    .mobile-church-card .card-accent {
        height: 4px;
        width: 100%;
    }

    /* =========================================================================
       EMPTY STATE
    ========================================================================= */
    .empty-state-icon {
        width: 80px; height: 80px;
        border-radius: 24px;
        display: flex; align-items: center; justify-content: center;
        font-size: 2.2rem;
        margin: 0 auto 1rem;
    }

    /* =========================================================================
       RESPONSIVE
    ========================================================================= */
    @media (max-width: 991.98px) {
        .churches-hero {
            padding: 2rem 0 6.5rem 0;
            text-align: center;
        }
        .hero-actions-wrap {
            justify-content: center !important;
            margin-top: 1.5rem;
        }
    }
    @media (max-width: 575.98px) {
        .kpi-value { font-size: 1.4rem; }
        .kpi-icon  { width: 38px; height: 38px; font-size: 1.1rem; border-radius: 11px; }
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">

        {{-- =====================================================================
             HERO HEADER
        ===================================================================== --}}
        <div class="churches-hero px-4">
            <div class="hero-grid"></div>
            <div class="hero-orb orb-blue"></div>
            <div class="hero-orb orb-amber"></div>
            <div class="hero-orb orb-teal"></div>

            <div class="container-fluid hero-content">
                <div class="row align-items-center">
                    <div class="col-lg-7">
                        <div class="mb-3 d-flex justify-content-center justify-content-lg-start gap-2 flex-wrap">
                            <span class="badge hero-badge px-3 py-2 rounded-pill">
                                <i class="ri-building-line me-1"></i> GESTION DES CLIENTS SAAS
                            </span>
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill d-inline-flex align-items-center gap-1" style="font-size:0.72rem;">
                                <span style="width:6px;height:6px;border-radius:50%;background:#22c55e;display:inline-block;"></span>
                                {{ $activeCount ?? 0 }} active(s)
                            </span>
                        </div>
                        <h1 class="display-6 fw-bold text-white mb-2 text-center text-lg-start" style="line-height:1.2;">
                            Églises <span style="color:#f7b84b; filter:brightness(1.1);">Clientes</span>
                        </h1>
                        <p class="fs-15 mb-0 d-none d-lg-block" style="max-width:580px; line-height:1.65; color:rgba(255,255,255,0.72);">
                            Consultez l'état des abonnements, administrez les accès et intervenez en mode support pour chaque église cliente de la plateforme.
                        </p>
                        <p class="fs-14 mb-0 d-lg-none text-center" style="color:rgba(255,255,255,0.72);">
                            Supervision et gestion de toutes les églises clientes.
                        </p>
                    </div>

                    <div class="col-lg-5 d-flex justify-content-lg-end hero-actions-wrap mt-3 mt-lg-0">
                        <div class="d-flex flex-column flex-sm-row gap-2 justify-content-center">
                            <a href="{{ route('super-admin.churches.create') }}" class="btn-hero-primary">
                                <i class="ri-add-circle-fill fs-18"></i> Inscrire une église
                            </a>
                            <a href="{{ route('super-admin.dashboard') }}" class="btn-hero-secondary">
                                <i class="ri-dashboard-line fs-18"></i> Vue supervision
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- =====================================================================
             KPI CARDS FLOTTANTES
        ===================================================================== --}}
        <div class="row g-3 kpi-row px-1 px-md-0">
            {{-- Total --}}
            <div class="col-6 col-md-3">
                <div class="card kpi-card h-100">
                    <div class="card-body p-3 p-md-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="kpi-icon bg-primary-subtle text-primary">
                                <i class="ri-building-4-line"></i>
                            </div>
                            <span class="badge bg-light text-muted border rounded-pill fs-10 px-2">Total</span>
                        </div>
                        <div class="kpi-value text-body">{{ $totalCount ?? $churches->total() }}</div>
                        <div class="kpi-label text-muted mt-1">Églises inscrites</div>
                    </div>
                </div>
            </div>

            {{-- Actives --}}
            <div class="col-6 col-md-3">
                <div class="card kpi-card h-100">
                    <div class="card-body p-3 p-md-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="kpi-icon bg-success-subtle text-success">
                                <i class="ri-checkbox-circle-line"></i>
                            </div>
                            @php $activeRate = ($totalCount ?? $churches->total()) > 0 ? round(($activeCount / ($totalCount ?? $churches->total())) * 100) : 0; @endphp
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill fs-10 px-2">{{ $activeRate }}%</span>
                        </div>
                        <div class="kpi-value text-success">{{ $activeCount ?? 0 }}</div>
                        <div class="kpi-label text-muted mt-1">Abonnements actifs</div>
                    </div>
                </div>
            </div>

            {{-- Expirées --}}
            <div class="col-6 col-md-3">
                <div class="card kpi-card h-100">
                    <div class="card-body p-3 p-md-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="kpi-icon bg-danger-subtle text-danger">
                                <i class="ri-error-warning-line"></i>
                            </div>
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill fs-10 px-2">Expiré</span>
                        </div>
                        <div class="kpi-value text-danger">{{ $expiredCount ?? 0 }}</div>
                        <div class="kpi-label text-muted mt-1">Abonnements expirés</div>
                    </div>
                </div>
            </div>

            {{-- Suspendues --}}
            <div class="col-6 col-md-3">
                <div class="card kpi-card h-100">
                    <div class="card-body p-3 p-md-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="kpi-icon" style="background:rgba(107,114,128,.12); color:#6b7280;">
                                <i class="ri-indeterminate-circle-line"></i>
                            </div>
                            <span class="badge rounded-pill fs-10 px-2" style="background:rgba(107,114,128,.1); color:#6b7280; border:1px solid rgba(107,114,128,.25);">Suspendu</span>
                        </div>
                        @php $suspendedCount = ($totalCount ?? $churches->total()) - ($activeCount ?? 0) - ($expiredCount ?? 0); $suspendedCount = max(0, $suspendedCount); @endphp
                        <div class="kpi-value" style="color:#6b7280;">{{ $suspendedCount }}</div>
                        <div class="kpi-label text-muted mt-1">Accès suspendus</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- =====================================================================
             BARRE DE FILTRES — Pattern collapse mobile (identique à /admin/activities)
        ===================================================================== --}}
        <div class="row mt-4">
            <div class="col-12">
                <div class="card filter-card">
                    <div class="card-body p-3 p-md-4">
                        <form action="{{ route('super-admin.churches.index') }}" method="GET" id="churches-filter-form">
                            <div class="row g-2 g-md-3 align-items-end">

                                {{-- ── Colonne 1 : Recherche toujours visible + bouton Filtres (mobile seulement) ── --}}
                                <div class="col-12 col-md-5 col-lg-5">
                                    <div class="d-flex align-items-center justify-content-between mb-2 d-md-none">
                                        <label class="form-label fw-semibold text-muted mb-0 fs-11" style="text-transform:uppercase;letter-spacing:.6px;">Recherche</label>
                                        <button type="button"
                                            class="btn btn-sm btn-soft-primary rounded-pill px-3 py-1 fs-12 d-flex align-items-center gap-1"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#churchesMobileFilterCollapse"
                                            aria-expanded="{{ request('status') ? 'true' : 'false' }}">
                                            <i class="ri-filter-3-line"></i>
                                            <span>Filtres</span>
                                            @if(request('status'))
                                                <span class="badge bg-primary ms-1 rounded-circle" style="width:8px;height:8px;padding:0;display:inline-block;"></span>
                                            @endif
                                        </button>
                                    </div>
                                    <label class="form-label fw-semibold text-muted mb-1 d-none d-md-block fs-11" style="text-transform:uppercase;letter-spacing:.6px;">Recherche</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="ri-search-line text-muted"></i></span>
                                        <input type="text" name="search" class="form-control"
                                            placeholder="Nom, ville, code, email…"
                                            value="{{ request('search') }}">
                                    </div>
                                </div>

                                {{-- ── Colonne 2 : Filtres avancés (collapse sur mobile, toujours visibles sur desktop) ── --}}
                                <div class="col-12 col-md-7 col-lg-7 collapse d-md-block {{ request('status') ? 'show' : '' }}"
                                     id="churchesMobileFilterCollapse">
                                    <div class="row g-2 g-md-3 align-items-end">
                                        {{-- Statut --}}
                                        <div class="col-12 col-sm-7 col-md-6">
                                            <label class="form-label fw-semibold text-muted mb-1 fs-11" style="text-transform:uppercase;letter-spacing:.6px;">Statut</label>
                                            <select name="status" class="form-select">
                                                <option value="">Tous les statuts</option>
                                                <option value="active"    {{ request('status')==='active'    ? 'selected' : '' }}>✅ Abonnement actif</option>
                                                <option value="expired"   {{ request('status')==='expired'   ? 'selected' : '' }}>⚠️ Abonnement expiré</option>
                                                <option value="suspended" {{ request('status')==='suspended' ? 'selected' : '' }}>🚫 Accès suspendu</option>
                                            </select>
                                        </div>

                                        {{-- Boutons Filtrer / Reset --}}
                                        <div class="col-12 col-sm-5 col-md-6 d-flex gap-2">
                                            <button type="submit"
                                                class="btn btn-primary rounded-pill flex-grow-1 d-inline-flex align-items-center justify-content-center gap-1 fw-semibold"
                                                style="font-size:.875rem;">
                                                <i class="ri-filter-3-line"></i> Filtrer
                                            </button>
                                            <button type="button" id="btn-churches-reset"
                                                class="btn btn-secondary rounded-pill flex-shrink-0 d-inline-flex align-items-center justify-content-center px-3"
                                                title="Réinitialiser les filtres">
                                                <i class="ri-refresh-line"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- ── Filtres rapides (vue tablette / desktop) ── --}}
                            <div class="d-flex flex-wrap gap-2 mt-3 pt-3 border-top align-items-center">
                                <span class="text-muted fs-12 me-1"><i class="ri-price-tag-3-line me-1"></i>Vue rapide :</span>
                                <a href="{{ route('super-admin.churches.index') }}"
                                   class="quick-filter-badge {{ !request('status') ? 'active-all' : 'inactive-all' }}">
                                    <i class="ri-grid-line"></i> Toutes ({{ $totalCount ?? $churches->total() }})
                                </a>
                                <a href="{{ route('super-admin.churches.index', ['status' => 'active']) }}"
                                   class="quick-filter-badge {{ request('status')==='active' ? 'active-ok' : 'inactive-ok' }}">
                                    <span class="status-dot dot-active"></span> Actives ({{ $activeCount ?? 0 }})
                                </a>
                                <a href="{{ route('super-admin.churches.index', ['status' => 'expired']) }}"
                                   class="quick-filter-badge {{ request('status')==='expired' ? 'active-exp' : 'inactive-exp' }}">
                                    <i class="ri-error-warning-line"></i> Expirées ({{ $expiredCount ?? 0 }})
                                </a>
                                <a href="{{ route('super-admin.churches.index', ['status' => 'suspended']) }}"
                                   class="quick-filter-badge {{ request('status')==='suspended' ? 'active-sus' : 'inactive-sus' }}">
                                    <i class="ri-indeterminate-circle-line"></i> Suspendues ({{ $suspendedCount }})
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- =====================================================================
             LISTE DES ÉGLISES
        ===================================================================== --}}
        <div class="row mt-4 mb-5 pb-3">
            <div class="col-12">
                <div class="card table-card">
                    {{-- Header --}}
                    <div class="card-header py-3 px-3 px-md-4 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <div class="kpi-icon bg-primary-subtle text-primary" style="width:38px;height:38px;border-radius:10px;font-size:1.1rem;">
                                <i class="ri-building-4-line"></i>
                            </div>
                            <div>
                                <h5 class="card-title mb-0 fw-bold fs-15">Répertoire des Églises</h5>
                                <span class="text-muted fs-11">{{ $churches->total() }} résultat(s) — Page {{ $churches->currentPage() }}/{{ $churches->lastPage() }}</span>
                            </div>
                        </div>
                        <a href="{{ route('super-admin.churches.create') }}" class="btn btn-primary btn-sm rounded-pill px-3 d-none d-sm-inline-flex align-items-center gap-1" style="font-size:.8rem; font-weight:600;">
                            <i class="ri-add-line"></i> Nouvelle église
                        </a>
                    </div>

                    <div class="card-body p-0" style="overflow:visible !important;">

                        {{-- ── MOBILE (< md) : Cards verticales ── --}}
                        <div class="d-md-none p-3">
                            <div class="d-flex flex-column gap-3">
                                @forelse($churches as $church)
                                    @php
                                        $isActive    = $church->status !== 'suspended' && $church->isSubscriptionActive();
                                        $isSuspended = $church->status === 'suspended';
                                        $isExpired   = !$isActive && !$isSuspended;
                                        $accentColor = $isActive ? '#22c55e' : ($isSuspended ? '#9ca3af' : 'var(--vz-danger)');
                                        $daysLeft    = $church->daysLeftInSubscription();
                                        $avatarColors = ['bg-primary-subtle text-primary','bg-warning-subtle text-warning','bg-info-subtle text-info','bg-success-subtle text-success','bg-danger-subtle text-danger','bg-secondary-subtle text-secondary'];
                                        $avatarColor  = $avatarColors[$church->id % count($avatarColors)];
                                    @endphp
                                    <div class="mobile-church-card">
                                        <div class="card-accent" style="background:{{ $accentColor }};"></div>
                                        <div class="p-3">
                                            {{-- Ligne 1 : Nom + Statut --}}
                                            <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                                                <div class="d-flex align-items-center gap-2 min-w-0">
                                                    <div class="church-avatar {{ $avatarColor }}" style="width:38px;height:38px;border-radius:10px;font-size:.85rem;">
                                                        {{ strtoupper(substr($church->name, 0, 2)) }}
                                                    </div>
                                                    <div class="min-w-0">
                                                        <h6 class="fs-14 fw-bold mb-0 text-truncate">
                                                            <a href="{{ route('super-admin.churches.show', $church) }}" class="text-body">{{ $church->name }}</a>
                                                        </h6>
                                                        <span class="text-muted fs-11 text-truncate d-block">
                                                            <i class="ri-map-pin-line me-1"></i>{{ $church->city ?? 'Non spécifié' }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <span class="status-pill flex-shrink-0
                                                    {{ $isActive ? 'status-active' : ($isSuspended ? 'status-suspended' : 'status-expired') }}">
                                                    <span class="status-dot {{ $isActive ? 'dot-active' : ($isSuspended ? 'dot-suspended' : 'dot-expired') }}"></span>
                                                    {{ $isActive ? 'Actif' : ($isSuspended ? 'Suspendu' : 'Expiré') }}
                                                </span>
                                            </div>

                                            {{-- Ligne 2 : Infos --}}
                                            <div class="row g-2 fs-11 text-muted border-top border-bottom py-2 my-2">
                                                <div class="col-4 text-center">
                                                    <div class="fw-bold text-body fs-13">{{ $church->users_count }}</div>
                                                    <div>Membres</div>
                                                </div>
                                                <div class="col-4 text-center border-start border-end">
                                                    <div class="fw-bold text-body fs-13">{{ $church->groups_count }}</div>
                                                    <div>Groupes</div>
                                                </div>
                                                <div class="col-4 text-center">
                                                    <div class="fw-bold text-body fs-12">{{ $church->subscription_expires_at ? $church->subscription_expires_at->format('d/m/Y') : '—' }}</div>
                                                    <div>Échéance</div>
                                                </div>
                                            </div>

                                            {{-- Barre de jours restants --}}
                                            @if($isActive && $daysLeft !== null && $daysLeft >= 0)
                                                @php $pct = min(100, round(($daysLeft / 365) * 100)); $barColor = $pct > 40 ? '#22c55e' : ($pct > 15 ? '#f7b84b' : 'var(--vz-danger)'); @endphp
                                                <div class="mb-2">
                                                    <div class="d-flex justify-content-between fs-10 text-muted mb-1">
                                                        <span>{{ $daysLeft }}j restants</span>
                                                        <span>{{ $pct }}%</span>
                                                    </div>
                                                    <div class="days-bar">
                                                        <div class="days-bar-fill" style="width:{{ $pct }}%; background:{{ $barColor }};"></div>
                                                    </div>
                                                </div>
                                            @endif

                                            {{-- Actions --}}
                                            <div class="d-flex gap-2 justify-content-end mt-1 flex-wrap">
                                                <a href="{{ route('super-admin.churches.show', $church) }}" class="action-btn action-btn-view" title="Consulter la fiche">
                                                    <i class="ri-eye-line"></i>
                                                </a>
                                                <a href="{{ route('super-admin.churches.renew.form', $church) }}" class="action-btn action-btn-renew" title="Renouveler 1 An">
                                                    <i class="ri-refresh-line"></i>
                                                </a>
                                                <a href="{{ route('super-admin.churches.impersonate', $church) }}" class="action-btn action-btn-support" title="Mode Support">
                                                    <i class="ri-customer-service-2-line"></i>
                                                </a>
                                                <a href="{{ route('super-admin.churches.edit', $church) }}" class="action-btn action-btn-edit" title="Modifier">
                                                    <i class="ri-pencil-line"></i>
                                                </a>
                                                <button type="button" class="action-btn {{ $church->status === 'active' ? 'action-btn-suspend' : 'action-btn-activate' }}"
                                                        onclick="openToggleStatusModal('{{ addslashes($church->name) }}', '{{ $church->code }}', '{{ $church->status }}', '{{ route('super-admin.churches.toggle-status', $church) }}')"
                                                        title="{{ $church->status === 'active' ? 'Suspendre' : 'Activer' }}">
                                                    <i class="ri-{{ $church->status === 'active' ? 'forbid-line' : 'checkbox-circle-line' }}"></i>
                                                </button>
                                                @if($church->id !== 1 && $church->code !== 'EBER-001')
                                                <button type="button" class="action-btn action-btn-delete"
                                                        onclick="openDeleteChurchModal('{{ addslashes($church->name) }}', '{{ $church->code }}', '{{ route('super-admin.churches.destroy', $church) }}')"
                                                        title="Supprimer cette église">
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-5">
                                        <div class="empty-state-icon bg-light text-muted">
                                            <i class="ri-building-line"></i>
                                        </div>
                                        <h6 class="fw-semibold text-muted mb-1">Aucune église trouvée</h6>
                                        <p class="text-muted fs-13">Aucun résultat ne correspond à votre recherche.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        {{-- ── DESKTOP (≥ md) : Table premium ── --}}
                        <div class="d-none d-md-block" style="overflow:visible !important;">
                            <table class="table table-hover align-middle mb-0 table-churches w-100" style="table-layout:fixed;">
                                <thead>
                                    <tr>
                                        <th class="ps-4" style="width:30%;">Église &amp; Localisation</th>
                                        <th style="width:10%;">Code</th>
                                        <th style="width:17%;">Statut &amp; Abonnement</th>
                                        <th style="width:18%;">Échéance / Progression</th>
                                        <th style="width:10%;">Membres</th>
                                        <th class="text-center pe-4" style="width:15%;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody style="overflow:visible !important;">
                                    @forelse($churches as $church)
                                        @php
                                            $isActive    = $church->status !== 'suspended' && $church->isSubscriptionActive();
                                            $isSuspended = $church->status === 'suspended';
                                            $isExpired   = !$isActive && !$isSuspended;
                                            $daysLeft    = $church->daysLeftInSubscription();
                                            $pct         = ($isActive && $daysLeft !== null && $daysLeft >= 0) ? min(100, round(($daysLeft / 365) * 100)) : 0;
                                            $barColor    = $pct > 40 ? '#22c55e' : ($pct > 15 ? '#f7b84b' : 'var(--vz-danger)');
                                            $avatarColors = ['bg-primary-subtle text-primary','bg-warning-subtle text-warning','bg-info-subtle text-info','bg-success-subtle text-success','bg-danger-subtle text-danger','bg-secondary-subtle text-secondary'];
                                            $avatarColor  = $avatarColors[$church->id % count($avatarColors)];
                                        @endphp
                                        <tr>
                                            {{-- Église --}}
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center gap-3 min-w-0">
                                                    <div class="church-avatar {{ $avatarColor }}">
                                                        {{ strtoupper(substr($church->name, 0, 2)) }}
                                                    </div>
                                                    <div class="min-w-0 flex-grow-1">
                                                        <h6 class="fs-13 mb-0 fw-bold text-truncate">
                                                            <a href="{{ route('super-admin.churches.show', $church) }}" class="text-body">{{ $church->name }}</a>
                                                        </h6>
                                                        <span class="text-muted fs-11 d-block text-truncate">
                                                            <i class="ri-map-pin-line me-1"></i>{{ $church->city ?? 'Non spécifié' }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </td>

                                            {{-- Code --}}
                                            <td>
                                                <span class="badge bg-body-tertiary text-body border px-2 py-1 fs-11 font-monospace rounded-2">
                                                    {{ $church->code ?? '—' }}
                                                </span>
                                            </td>

                                            {{-- Statut --}}
                                            <td>
                                                <span class="status-pill {{ $isActive ? 'status-active' : ($isSuspended ? 'status-suspended' : 'status-expired') }}">
                                                    <span class="status-dot {{ $isActive ? 'dot-active' : ($isSuspended ? 'dot-suspended' : 'dot-expired') }}"></span>
                                                    @if($isActive)
                                                        Actif · {{ $daysLeft }}j
                                                    @elseif($isSuspended)
                                                        Suspendu
                                                    @else
                                                        Expiré
                                                    @endif
                                                </span>
                                                <div class="text-muted fs-10 mt-1">
                                                    {{ number_format($church->subscription_amount, 0, ',', ' ') }} FCFA/an
                                                </div>
                                            </td>

                                            {{-- Échéance + barre --}}
                                            <td>
                                                <div class="fs-12 fw-semibold text-body mb-1">
                                                    {{ $church->subscription_expires_at ? $church->subscription_expires_at->format('d/m/Y') : '—' }}
                                                </div>
                                                @if($isActive && $pct > 0)
                                                    <div class="days-bar" style="width:90%;">
                                                        <div class="days-bar-fill" style="width:{{ $pct }}%; background:{{ $barColor }};"></div>
                                                    </div>
                                                @endif
                                            </td>

                                            {{-- Membres --}}
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <i class="ri-team-line text-muted fs-15"></i>
                                                    <span class="fs-13 fw-bold text-body">{{ $church->users_count }}</span>
                                                </div>
                                                <span class="text-muted fs-10">{{ $church->groups_count }} groupe(s)</span>
                                            </td>

                                            {{-- Actions directes --}}
                                            <td class="text-center pe-4" style="overflow:visible !important;">
                                                <div class="d-inline-flex gap-1 align-items-center justify-content-center flex-wrap">
                                                    <a href="{{ route('super-admin.churches.show', $church) }}"
                                                       class="action-btn action-btn-view" title="Consulter la fiche">
                                                        <i class="ri-eye-line"></i>
                                                    </a>
                                                    <a href="{{ route('super-admin.churches.renew.form', $church) }}"
                                                       class="action-btn action-btn-renew" title="Renouveler 1 An">
                                                        <i class="ri-refresh-line"></i>
                                                    </a>
                                                    <a href="{{ route('super-admin.churches.impersonate', $church) }}"
                                                       class="action-btn action-btn-support" title="Mode Support">
                                                        <i class="ri-customer-service-2-line"></i>
                                                    </a>
                                                    <a href="{{ route('super-admin.churches.edit', $church) }}"
                                                       class="action-btn action-btn-edit" title="Modifier coordonnées">
                                                        <i class="ri-pencil-line"></i>
                                                    </a>
                                                    <button type="button"
                                                            class="action-btn {{ $church->status === 'active' ? 'action-btn-suspend' : 'action-btn-activate' }}"
                                                            onclick="openToggleStatusModal('{{ addslashes($church->name) }}', '{{ $church->code }}', '{{ $church->status }}', '{{ route('super-admin.churches.toggle-status', $church) }}')"
                                                            title="{{ $church->status === 'active' ? 'Suspendre l\'accès' : 'Activer l\'accès' }}">
                                                        <i class="ri-{{ $church->status === 'active' ? 'forbid-line' : 'checkbox-circle-line' }}"></i>
                                                    </button>
                                                    @if($church->id !== 1 && $church->code !== 'EBER-001')
                                                    <button type="button" class="action-btn action-btn-delete"
                                                            onclick="openDeleteChurchModal('{{ addslashes($church->name) }}', '{{ $church->code }}', '{{ route('super-admin.churches.destroy', $church) }}')"
                                                            title="Supprimer cette église">
                                                        <i class="ri-delete-bin-line"></i>
                                                    </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="py-5 text-center">
                                                <div class="empty-state-icon bg-light text-muted">
                                                    <i class="ri-building-line"></i>
                                                </div>
                                                <h6 class="fw-semibold text-muted mb-1">Aucune église trouvée</h6>
                                                <p class="text-muted fs-13 mb-3">Aucun résultat ne correspond à votre recherche.</p>
                                                <a href="{{ route('super-admin.churches.create') }}" class="btn btn-primary btn-sm rounded-pill px-4">
                                                    <i class="ri-add-circle-line me-1"></i> Inscrire une église
                                                </a>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Pagination --}}
                    @if($churches->hasPages())
                        <div class="card-footer border-top bg-transparent py-3 px-4 d-flex justify-content-center">
                            {{ $churches->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>

{{-- =========================================================================
     MODAL DE SUPPRESSION D'ÉGLISE
========================================================================= --}}
<div class="modal fade" id="deleteChurchModal" tabindex="-1" aria-labelledby="deleteChurchModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-danger-subtle text-danger border-0 py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="avatar-xs bg-danger text-white rounded-circle d-flex align-items-center justify-content-center">
                        <i class="ri-delete-bin-line fs-14"></i>
                    </div>
                    <h5 class="modal-title fw-bold fs-16 mb-0" id="deleteChurchModalLabel">Confirmer la suppression</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="avatar-lg bg-danger-subtle text-danger rounded-circle mx-auto d-flex align-items-center justify-content-center mb-3" style="width:70px; height:70px;">
                    <i class="ri-alert-line fs-32"></i>
                </div>
                <h5 class="fw-bold text-body mb-1" id="delete-church-name-display">Nom de l'église</h5>
                <span class="badge bg-light text-muted border px-2 py-1 fs-11 font-monospace mb-3 d-inline-block" id="delete-church-code-display">CH-XXXX</span>

                <div class="alert alert-warning border-0 fs-12 mb-0 rounded-3 text-start">
                    <div class="d-flex align-items-start gap-2">
                        <i class="ri-error-warning-fill fs-16 flex-shrink-0 mt-0 text-warning"></i>
                        <div>
                            <strong>Attention :</strong> La suppression de cette église désactivera immédiatement les accès pour l'ensemble de ses membres et administrateurs. Les données seront archivées de façon sécurisée.
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-0 py-3 px-4 d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-light rounded-pill px-4 fs-13" data-bs-dismiss="modal">Annuler</button>
                <form id="delete-church-form" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger rounded-pill px-4 fs-13 shadow-sm d-inline-flex align-items-center gap-1" id="btn-confirm-delete-church">
                        <i class="ri-delete-bin-line"></i> Oui, supprimer l'église
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- =========================================================================
     MODAL DE MODIFICATION DU STATUT (SUSPENDRE / ACTIVER)
========================================================================= --}}
<div class="modal fade" id="toggleStatusModal" tabindex="-1" aria-labelledby="toggleStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-0 py-3 px-4" id="toggle-modal-header">
                <div class="d-flex align-items-center gap-2">
                    <div class="avatar-xs rounded-circle d-flex align-items-center justify-content-center text-white" id="toggle-modal-icon-bg">
                        <i class="ri-lock-line fs-14" id="toggle-modal-icon"></i>
                    </div>
                    <h5 class="modal-title fw-bold fs-16 mb-0" id="toggleStatusModalLabel">Modifier le statut</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="avatar-lg rounded-circle mx-auto d-flex align-items-center justify-content-center mb-3" id="toggle-modal-large-icon" style="width:70px; height:70px;">
                    <i class="fs-32" id="toggle-modal-large-i"></i>
                </div>
                <h5 class="fw-bold text-body mb-1" id="toggle-church-name-display">Nom de l'église</h5>
                <span class="badge bg-light text-muted border px-2 py-1 fs-11 font-monospace mb-3 d-inline-block" id="toggle-church-code-display">CH-XXXX</span>

                <p class="text-muted fs-13 mb-0" id="toggle-modal-desc">
                    Description de l'action de changement de statut.
                </p>
            </div>
            <div class="modal-footer bg-light border-0 py-3 px-4 d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-light rounded-pill px-4 fs-13" data-bs-dismiss="modal">Annuler</button>
                <form id="toggle-status-form" method="POST" action="">
                    @csrf
                    <button type="submit" class="btn rounded-pill px-4 fs-13 shadow-sm d-inline-flex align-items-center gap-1" id="toggle-modal-submit-btn">
                        <span>Confirmer</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Scripts : reset filtre + tooltips + modals --}}
@push('scripts')
<script>
    // Fonctions globales d'ouverture des modals
    function openDeleteChurchModal(name, code, url) {
        document.getElementById('delete-church-name-display').textContent = name;
        document.getElementById('delete-church-code-display').textContent = code;
        document.getElementById('delete-church-form').setAttribute('action', url);
        const modalEl = document.getElementById('deleteChurchModal');
        const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        modal.show();
    }

    function openToggleStatusModal(name, code, status, url) {
        document.getElementById('toggle-church-name-display').textContent = name;
        document.getElementById('toggle-church-code-display').textContent = code;
        document.getElementById('toggle-status-form').setAttribute('action', url);

        const isSuspending = (status === 'active');
        const header      = document.getElementById('toggle-modal-header');
        const title       = document.getElementById('toggleStatusModalLabel');
        const iconBg      = document.getElementById('toggle-modal-icon-bg');
        const icon        = document.getElementById('toggle-modal-icon');
        const largeIcon   = document.getElementById('toggle-modal-large-icon');
        const largeI      = document.getElementById('toggle-modal-large-i');
        const desc        = document.getElementById('toggle-modal-desc');
        const submitBtn   = document.getElementById('toggle-modal-submit-btn');

        if (isSuspending) {
            header.className = 'modal-header bg-warning-subtle text-warning border-0 py-3 px-4';
            title.textContent = 'Suspendre l\'accès';
            iconBg.className = 'avatar-xs bg-warning text-white rounded-circle d-flex align-items-center justify-content-center';
            icon.className = 'ri-forbid-line fs-14';
            largeIcon.className = 'avatar-lg bg-warning-subtle text-warning rounded-circle mx-auto d-flex align-items-center justify-content-center mb-3';
            largeI.className = 'ri-forbid-line fs-32';
            desc.innerHTML = 'En suspendant cette église, ses utilisateurs et administrateurs ne pourront plus accéder à l\'application jusqu\'à sa réactivation.';
            submitBtn.className = 'btn btn-warning rounded-pill px-4 fs-13 shadow-sm d-inline-flex align-items-center gap-1 text-white';
            submitBtn.innerHTML = '<i class="ri-forbid-line"></i> Suspendre l\'accès';
        } else {
            header.className = 'modal-header bg-success-subtle text-success border-0 py-3 px-4';
            title.textContent = 'Activer l\'accès';
            iconBg.className = 'avatar-xs bg-success text-white rounded-circle d-flex align-items-center justify-content-center';
            icon.className = 'ri-checkbox-circle-line fs-14';
            largeIcon.className = 'avatar-lg bg-success-subtle text-success rounded-circle mx-auto d-flex align-items-center justify-content-center mb-3';
            largeI.className = 'ri-checkbox-circle-line fs-32';
            desc.innerHTML = 'En réactivant cette église, l\'accès complet à la plateforme sera immédiatement rétabli pour tous ses utilisateurs.';
            submitBtn.className = 'btn btn-success rounded-pill px-4 fs-13 shadow-sm d-inline-flex align-items-center gap-1';
            submitBtn.innerHTML = '<i class="ri-checkbox-circle-line"></i> Rétablir l\'accès';
        }

        const modalEl = document.getElementById('toggleStatusModal');
        const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        modal.show();
    }

    document.addEventListener('DOMContentLoaded', function () {
        // ── Reset filtre ──
        const btnReset = document.getElementById('btn-churches-reset');
        if (btnReset) {
            btnReset.addEventListener('click', function () {
                const form = document.getElementById('churches-filter-form');
                form.querySelectorAll('input, select').forEach(function (el) {
                    el.value = '';
                });
                if (window.location.search) {
                    window.location.href = "{{ route('super-admin.churches.index') }}";
                }
            });
        }

        // ── Anti double-soumission sur suppression ──
        const deleteForm = document.getElementById('delete-church-form');
        if (deleteForm) {
            deleteForm.addEventListener('submit', function () {
                const btn = document.getElementById('btn-confirm-delete-church');
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Suppression…';
            });
        }

        // ── Tooltips Bootstrap ──
        document.querySelectorAll('[title]').forEach(function (el) {
            new bootstrap.Tooltip(el, { trigger: 'hover', placement: 'top' });
        });
    });
</script>
@endpush
@endsection

