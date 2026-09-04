@extends('layouts.app')

@section('title', 'Tableau de Bord Administrateur')

@push('css')
<style>
    /* Hero Section */
    .activity-hero {
        background-color: #0b0f19;
        background-image: 
            radial-gradient(circle at 0% 0%, rgba(var(--vz-primary-rgb), 0.25) 0%, transparent 50%),
            radial-gradient(circle at 100% 100%, rgba(var(--vz-info-rgb), 0.2) 0%, transparent 50%);
        padding: 3rem 0 4rem 0;
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
        opacity: 0.6;
        z-index: 0;
        animation: orbFloat 12s infinite alternate ease-in-out;
    }
    .orb-1 {
        width: 350px; height: 350px;
        background: var(--vz-primary);
        top: -150px; left: -100px;
    }
    .orb-2 {
        width: 300px; height: 300px;
        background: var(--vz-info);
        bottom: -100px; right: 5%;
        animation-delay: -6s;
    }

    @keyframes orbFloat {
        0% { transform: translateY(0) scale(1); }
        100% { transform: translateY(40px) scale(1.1); }
    }
    
    .hero-content {
        position: relative;
        z-index: 5;
    }

    .hero-badge {
        background: rgba(var(--vz-primary-rgb), 0.15);
        border: 1px solid rgba(var(--vz-primary-rgb), 0.3);
        color: #fff;
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
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    }

    /* Scan Button */
    .scan-btn {
        padding: 15px 30px;
        font-size: 1.1rem;
        font-weight: 500;
        border-radius: 50px;
        box-shadow: 0 10px 20px rgba(41, 156, 219, 0.3);
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: linear-gradient(135deg, var(--vz-info) 0%, #2970db 100%);
        border: none;
        color: white;
        text-decoration: none;
    }
    
    .scan-btn:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(41, 156, 219, 0.5);
        color: white;
    }
    
    .scan-btn i {
        font-size: 1.5rem;
    }
    
    /* Table Styling */
    .table-premium th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        color: var(--vz-gray-600);
        background-color: var(--vz-light);
        padding: 15px;
    }
    
    .table-premium td {
        padding: 15px;
        vertical-align: middle;
    }

    @media (max-width: 991.98px) {
        .activity-hero {
            padding: 3rem 0 7rem 0;
            text-align: center;
        }
        .scan-btn-container {
            justify-content: center !important;
            margin-top: 1.5rem;
        }
    }
</style>
@endpush

@section('content')

<div class="row">
    <div class="col">
        <div class="h-100">
            <!-- Hero Section -->
            <div class="activity-hero px-4">
                <div class="hero-grid"></div>
                <div class="hero-orb orb-1"></div>
                <div class="hero-orb orb-2"></div>
                
                <div class="container-fluid max-w-1200 hero-content">
                    <div class="row align-items-center">
                        @php
                            $inSupportMode = session()->has('tenant_church_id');
                            $supportChurch = $inSupportMode ? \App\Models\Church::find(session('tenant_church_id')) : null;
                        @endphp
                        <div class="col-lg-7">
                            <div class="mb-4 d-flex justify-content-center justify-content-lg-start">
                                @if($inSupportMode && $supportChurch)
                                    <span class="badge bg-warning text-dark px-3 py-2 rounded-pill shadow-sm mb-3 fw-bold" style="font-size: 0.75rem; letter-spacing: 1px;">
                                        <i class="ri-shield-flash-line me-1"></i> MODE SUPPORT : {{ strtoupper($supportChurch->name) }}
                                    </span>
                                @else
                                    <span class="badge hero-badge px-3 py-2 rounded-pill shadow-sm mb-3" style="font-size: 0.75rem; letter-spacing: 1px;">
                                        <i class="ri-shield-user-line me-1"></i> ESPACE ADMINISTRATEUR
                                    </span>
                                @endif
                            </div>
                            <h1 class="display-5 fw-bold text-white mb-2 text-center text-lg-start" style="line-height: 1.2;">
                                @if($inSupportMode && $supportChurch && isset($displayAdmin))
                                    Bonjour, <span style="color: var(--vz-warning); filter: brightness(1.2);">{{ $displayAdmin->first_name }} {{ $displayAdmin->name }} !</span>
                                @else
                                    Bonjour, <span style="color: var(--vz-primary); filter: brightness(1.3);">{{ auth()->user()->first_name }} !</span>
                                @endif
                            </h1>
                            @if($inSupportMode && $supportChurch && isset($displayAdmin))
                                <p class="fs-16 mb-0 d-none d-lg-block" style="max-width: 580px; line-height: 1.6; color: rgba(255,255,255,0.85);">
                                    Vous naviguez actuellement dans l'espace d'administration de <strong>« {{ $supportChurch->name }} »</strong> sous le compte de <strong>{{ $displayAdmin->full_name }}</strong>.
                                </p>
                            @else
                                <p class="fs-16 mb-0 d-none d-lg-block" style="max-width: 550px; line-height: 1.6; color: rgba(255,255,255,0.7);">
                                    Voici un résumé complet de l'activité globale sur {{ config('app.name') }} aujourd'hui.
                                </p>
                            @endif
                            <p class="fs-15 mb-0 d-lg-none text-center" style="color: rgba(255,255,255,0.7);">
                                Scannez le code QR à l'église pour marquer votre présence.
                            </p>
                        </div>

                        <div class="col-lg-5 d-flex justify-content-lg-end scan-btn-container">
                            <div class="text-center">
                                <a href="{{ route('attendance.scan') }}" class="scan-btn">
                                    <i class="ri-qr-scan-2-line"></i> Scanner ma présence
                                </a>
                                <p class="text-white-50 mt-3 mb-0" style="font-size: 0.85rem;">Activez votre caméra pour badger à l'entrée</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Row -->
            <div class="row g-2 g-md-3" style="margin-top: -5rem; position: relative; z-index: 10;">
                <!-- Total Membres -->
                <div class="col-6 col-md-6 col-xl-3">
                    <div class="card premium-card overflow-hidden h-100">
                        <div class="card-body p-2 p-sm-3 p-md-4">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1 overflow-hidden">
                                    <p class="text-uppercase fw-semibold text-muted text-truncate mb-0 fs-10 fs-md-12" style="letter-spacing: 0.5px;">Membres</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-2 mt-md-4">
                                <div>
                                    <h4 class="fs-18 fs-md-24 fw-bold ff-secondary mb-1 mb-md-3"><span class="counter-value" data-target="{{ $stats['total_users'] }}">0</span></h4>
                                    <a href="{{ route('admin.users.index') }}" class="text-primary text-decoration-underline fw-medium fs-11 fs-md-14 d-none d-sm-inline">Voir membres</a>
                                </div>
                                <div class="avatar-xs avatar-sm-md flex-shrink-0">
                                    <span class="avatar-title bg-primary-subtle text-primary rounded fs-16 fs-md-20 shadow-sm">
                                        <i class="ri-team-line"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Activités -->
                <div class="col-6 col-md-6 col-xl-3">
                    <div class="card premium-card overflow-hidden h-100">
                        <div class="card-body p-2 p-sm-3 p-md-4">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1 overflow-hidden">
                                    <p class="text-uppercase fw-semibold text-muted text-truncate mb-0 fs-10 fs-md-12" style="letter-spacing: 0.5px;">Activités</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-2 mt-md-4">
                                <div>
                                    <h4 class="fs-18 fs-md-24 fw-bold ff-secondary mb-1 mb-md-3"><span class="counter-value" data-target="{{ $stats['total_activities'] }}">0</span></h4>
                                    <a href="{{ route('activities.index') }}" class="text-info text-decoration-underline fw-medium fs-11 fs-md-14 d-none d-sm-inline">Toutes activités</a>
                                </div>
                                <div class="avatar-xs avatar-sm-md flex-shrink-0">
                                    <span class="avatar-title bg-info-subtle text-info rounded fs-16 fs-md-20 shadow-sm">
                                        <i class="ri-calendar-event-line"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Activités à venir -->
                <div class="col-6 col-md-6 col-xl-3">
                    <div class="card premium-card overflow-hidden h-100">
                        <div class="card-body p-2 p-sm-3 p-md-4">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1 overflow-hidden">
                                    <p class="text-uppercase fw-semibold text-muted text-truncate mb-0 fs-10 fs-md-12" style="letter-spacing: 0.5px;">À venir</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-2 mt-md-4">
                                <div>
                                    <h4 class="fs-18 fs-md-24 fw-bold ff-secondary mb-1 mb-md-3"><span class="counter-value" data-target="{{ $stats['upcoming_activities'] }}">0</span></h4>
                                    <a href="{{ route('activities.index', ['status_filter' => 'upcoming']) }}" class="text-warning text-decoration-underline fw-medium fs-11 fs-md-14 d-none d-sm-inline">Voir à venir</a>
                                </div>
                                <div class="avatar-xs avatar-sm-md flex-shrink-0">
                                    <span class="avatar-title bg-warning-subtle text-warning rounded fs-16 fs-md-20 shadow-sm">
                                        <i class="ri-time-line"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Groupes -->
                <div class="col-6 col-md-6 col-xl-3">
                    <div class="card premium-card overflow-hidden h-100">
                        <div class="card-body p-2 p-sm-3 p-md-4">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1 overflow-hidden">
                                    <p class="text-uppercase fw-semibold text-muted text-truncate mb-0 fs-10 fs-md-12" style="letter-spacing: 0.5px;">Groupes</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-2 mt-md-4">
                                <div>
                                    <h4 class="fs-18 fs-md-24 fw-bold ff-secondary mb-1 mb-md-3"><span class="counter-value" data-target="{{ $stats['total_groups'] }}">0</span></h4>
                                    <a href="{{ route('admin.groups.index') }}" class="text-success text-decoration-underline fw-medium fs-11 fs-md-14 d-none d-sm-inline">Gérer les groupes</a>
                                </div>
                                <div class="avatar-xs avatar-sm-md flex-shrink-0">
                                    <span class="avatar-title bg-success-subtle text-success rounded fs-16 fs-md-20 shadow-sm">
                                        <i class="ri-group-line"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Activités Récentes Section -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card premium-card overflow-hidden">
                        <div class="card-header border-0 bg-transparent py-3 px-3 px-md-4 border-bottom border-light-subtle d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-xs bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="ri-history-line fs-16"></i>
                                </div>
                                <h5 class="card-title mb-0 fw-bold fs-15 text-body">Activités Récentes</h5>
                            </div>
                            <a href="{{ route('activities.index') }}" class="btn btn-sm btn-soft-primary rounded-pill fs-12 px-3">
                                Tout voir <i class="ri-arrow-right-line ms-1"></i>
                            </a>
                        </div>

                        <div class="card-body p-0">
                            <!-- Mobile View (Cards) -->
                            <div class="d-block d-md-none p-3">
                                <div class="d-flex flex-column gap-2">
                                    @forelse($recent_activities as $activity)
                                    <div class="card border border-light-subtle rounded-3 shadow-none mb-1 p-3">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <h6 class="fs-14 fw-bold mb-1">
                                                    <a href="{{ route('activities.show', $activity) }}" class="text-body">{{ $activity->title }}</a>
                                                </h6>
                                                <span class="text-muted fs-11">
                                                    <i class="ri-calendar-event-line me-1"></i>
                                                    {{ \Carbon\Carbon::parse($activity->start_time)->format('d/m/Y H:i') }}
                                                </span>
                                            </div>
                                            <span class="badge {{ $activity->status === 'En cours' ? 'bg-success-subtle text-success' : ($activity->status === 'Terminée' ? 'bg-secondary-subtle text-secondary' : 'bg-warning-subtle text-warning') }} rounded-pill px-2 py-1 fs-11">
                                                {{ $activity->status }}
                                            </span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top border-light-subtle">
                                            <span class="text-muted fs-11">
                                                <i class="ri-map-pin-line me-1"></i> {{ $activity->location ?? 'Non défini' }}
                                            </span>
                                            <a href="{{ route('activities.show', $activity) }}" class="btn btn-sm btn-soft-primary rounded-pill px-3 fs-12">
                                                Détails
                                            </a>
                                        </div>
                                    </div>
                                    @empty
                                    <div class="text-center py-4 text-muted fs-13">
                                        Aucune activité récente.
                                    </div>
                                    @endforelse
                                </div>
                            </div>

                            <!-- Desktop View (Table) -->
                            <div class="table-responsive d-none d-md-block">
                                <table class="table table-hover align-middle table-nowrap mb-0 table-premium">
                                    <thead class="table-light">
                                        <tr class="text-uppercase fs-11">
                                            <th class="ps-4">Titre</th>
                                            <th>Date de début</th>
                                            <th>Date de fin</th>
                                            <th>Lieu</th>
                                            <th>Statut</th>
                                            <th class="text-end pe-4">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recent_activities as $activity)
                                        <tr>
                                            <td class="ps-4">
                                                <h6 class="fs-13 mb-0 fw-bold">
                                                    <a href="{{ route('activities.show', $activity) }}" class="text-body">{{ $activity->title }}</a>
                                                </h6>
                                                <span class="text-muted fs-11">{{ Str::limit($activity->description, 35) }}</span>
                                            </td>
                                            <td class="fs-12 text-muted">
                                                {{ \Carbon\Carbon::parse($activity->start_time)->format('d M Y H:i') }}
                                            </td>
                                            <td class="fs-12 text-muted">
                                                {{ \Carbon\Carbon::parse($activity->end_time)->format('d M Y H:i') }}
                                            </td>
                                            <td class="fs-12 text-body">
                                                <i class="ri-map-pin-line text-muted me-1"></i>{{ $activity->location ?? 'Non spécifié' }}
                                            </td>
                                            <td>
                                                <span class="badge {{ $activity->status === 'En cours' ? 'bg-success-subtle text-success' : ($activity->status === 'Terminée' ? 'bg-secondary-subtle text-secondary' : 'bg-warning-subtle text-warning') }} rounded-pill px-2 py-1">
                                                    {{ $activity->status }}
                                                </span>
                                            </td>
                                            <td class="text-end pe-4">
                                                <a href="{{ route('activities.show', $activity) }}" class="btn btn-sm btn-soft-primary rounded-pill px-3">Voir détails</a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted fs-13">Aucune activité récente.</td>
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
