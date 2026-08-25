@extends('layouts.app')

@section('title', 'Mon Espace')

@push('css')
<style>
    /* Hero Section */
    .activity-hero {
        background-color: #0b0f19;
        background-image: 
            radial-gradient(circle at 0% 0%, rgba(var(--vz-primary-rgb), 0.25) 0%, transparent 50%),
            radial-gradient(circle at 100% 100%, rgba(var(--vz-info-rgb), 0.2) 0%, transparent 50%);
        padding: 4rem 0 6rem 0;
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
            padding: 3rem 0 7rem 0; /* Plus de padding en bas pour la superposition */
            text-align: center;
        }
        .hero-title {
            font-size: 2.2rem !important;
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
                        <div class="col-lg-7">
                            <div class="d-flex align-items-center mb-4 justify-content-center justify-content-lg-start">
                                <span class="badge hero-badge px-3 py-2 rounded-pill shadow-sm" style="font-size: 0.75rem; letter-spacing: 1px;">
                                    <i class="mdi mdi-account-circle-outline text-info me-1"></i> ESPACE PERSONNEL
                                </span>
                            </div>
                            <h1 class="hero-title text-white fw-bold display-4 mb-3" style="letter-spacing: -1px; text-shadow: 0 2px 10px rgba(0,0,0,0.3);">
                                Bonjour, <span style="color: var(--vz-info); filter: brightness(1.2);">{{ $user->first_name }} !</span>
                            </h1>
                            <p class="fs-16 mb-0 d-none d-lg-block" style="max-width: 550px; line-height: 1.6; color: rgba(255,255,255,0.7);">
                                Bienvenue sur votre tableau de bord. Scannez le code QR à l'église pour marquer votre présence, ou consultez vos prochaines activités.
                            </p>
                            <p class="fs-15 mb-0 d-lg-none" style="color: rgba(255,255,255,0.7);">
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

            <!-- Stats Row (Overlapping the Hero) -->
            <div class="row g-2 g-md-3" style="margin-top: -5rem; position: relative; z-index: 10;">
                <div class="col-4 col-md-6 col-xl-4">
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
                                    <a href="{{ route('activities.index') }}" class="text-primary text-decoration-underline fw-medium fs-11 fs-md-14 d-none d-sm-inline">Voir le calendrier</a>
                                </div>
                                <div class="avatar-xs avatar-sm-md flex-shrink-0">
                                    <span class="avatar-title bg-primary-subtle text-primary rounded fs-16 fs-md-20 shadow-sm">
                                        <i class="ri-calendar-event-line"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-4 col-md-6 col-xl-4">
                    <div class="card premium-card overflow-hidden h-100">
                        <div class="card-body p-2 p-sm-3 p-md-4">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1 overflow-hidden">
                                    <p class="text-uppercase fw-semibold text-muted text-truncate mb-0 fs-10 fs-md-12" style="letter-spacing: 0.5px;">Présences</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-2 mt-md-4">
                                <div>
                                    <h4 class="fs-18 fs-md-24 fw-bold ff-secondary mb-1 mb-md-3"><span class="counter-value" data-target="{{ $stats['attended_activities'] }}">0</span></h4>
                                    <span class="text-muted fw-medium fs-11 fs-md-14 d-none d-sm-inline">Participations</span>
                                </div>
                                <div class="avatar-xs avatar-sm-md flex-shrink-0">
                                    <span class="avatar-title bg-success-subtle text-success rounded fs-16 fs-md-20 shadow-sm">
                                        <i class="ri-check-double-line"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-4 col-md-6 col-xl-4">
                    <div class="card premium-card overflow-hidden h-100">
                        <div class="card-body p-2 p-sm-3 p-md-4">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1 overflow-hidden">
                                    <p class="text-uppercase fw-semibold text-muted text-truncate mb-0 fs-10 fs-md-12" style="letter-spacing: 0.5px;">Cotisations</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-2 mt-md-4">
                                <div>
                                    <h4 class="fs-14 fs-sm-18 fs-md-24 fw-bold ff-secondary mb-1 mb-md-3"><span class="counter-value" data-target="{{ $stats['my_contributions_amount'] }}">0</span> <small class="fs-10 fs-md-14">FCFA</small></h4>
                                    <span class="text-muted fw-medium fs-11 fs-md-14 d-none d-sm-inline">Versements</span>
                                </div>
                                <div class="avatar-xs avatar-sm-md flex-shrink-0">
                                    <span class="avatar-title bg-warning-subtle text-warning rounded fs-16 fs-md-20 shadow-sm">
                                        <i class="ri-wallet-3-line"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upcoming Activities List -->
            <div class="row mt-3">
                <div class="col-lg-12">
                    <div class="card premium-card">
                        <div class="card-header align-items-center d-flex border-bottom-dashed py-3 py-md-4 px-3 px-md-4">
                            <h4 class="card-title mb-0 flex-grow-1 fs-15 fs-md-16 fw-semibold"><i class="ri-history-line text-primary me-1"></i> Mes prochaines activités</h4>
                            <div class="flex-shrink-0">
                                <a href="{{ route('activities.index') }}" class="btn btn-soft-primary btn-sm rounded-pill px-3">
                                    Tout voir
                                </a>
                            </div>
                        </div>
                        <div class="card-body p-3 p-md-4">
                            <!-- Desktop Table View -->
                            <div class="table-responsive d-none d-md-block">
                                <table class="table table-borderless table-centered table-premium align-middle table-nowrap mb-0">
                                    <thead>
                                        <tr>
                                            <th scope="col" style="width: 40%">Activité</th>
                                            <th scope="col">Date et Heure</th>
                                            <th scope="col">Lieu</th>
                                            <th scope="col" class="text-end">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($upcoming_activities as $activity)
                                            <tr class="border-bottom border-light">
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm me-3 flex-shrink-0">
                                                            <div class="avatar-title bg-info-subtle text-info rounded-circle fs-18">
                                                                <i class="ri-calendar-event-fill"></i>
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <h5 class="fs-15 fw-semibold mb-2">{{ $activity->title }}</h5>
                                                            <span class="badge bg-primary-subtle text-primary py-1 px-2">{{ $activity->type->name ?? 'Activité' }}</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="mb-1"><span class="fw-medium text-body fs-14">{{ \Carbon\Carbon::parse($activity->start_time)->translatedFormat('d M Y') }}</span></div>
                                                    <span class="text-muted fs-13"><i class="ri-time-line align-middle me-1"></i> {{ \Carbon\Carbon::parse($activity->start_time)->format('H:i') }}</span>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center text-muted fs-14">
                                                        <i class="ri-map-pin-line fs-16 me-2 text-primary"></i>
                                                        <span class="text-truncate" style="max-width: 200px;">{{ $activity->location }}</span>
                                                    </div>
                                                </td>
                                                <td class="text-end">
                                                    <a href="{{ route('activities.show', $activity) }}" class="btn btn-sm btn-outline-info rounded-pill px-3 transition-300">
                                                        Détails <i class="ri-arrow-right-line align-middle ms-1"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-5">
                                                    <div class="avatar-md mx-auto mb-3">
                                                        <div class="avatar-title bg-soft-light text-muted rounded-circle fs-24">
                                                            <i class="ri-calendar-2-line"></i>
                                                        </div>
                                                    </div>
                                                    <h5 class="fs-15 mb-1">Aucune activité prévue</h5>
                                                    <p class="text-muted mb-0">Revenez plus tard pour voir le programme.</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Mobile Native App Touchable Card List View -->
                            <div class="d-block d-md-none vstack gap-2">
                                @forelse($upcoming_activities as $activity)
                                    <a href="{{ route('activities.show', $activity) }}" class="card border border-light-subtle shadow-none mb-0 text-body text-decoration-none rounded-3 p-3 transition-300">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center gap-3 overflow-hidden">
                                                <div class="avatar-sm flex-shrink-0">
                                                    <div class="avatar-title bg-info-subtle text-info rounded-circle fs-18">
                                                        <i class="ri-calendar-event-fill"></i>
                                                    </div>
                                                </div>
                                                <div class="overflow-hidden">
                                                    <h6 class="fs-14 fw-semibold mb-1 text-truncate">{{ $activity->title }}</h6>
                                                    <div class="d-flex align-items-center gap-2 fs-12 text-muted">
                                                        <span><i class="ri-time-line align-middle me-1"></i>{{ \Carbon\Carbon::parse($activity->start_time)->translatedFormat('d M H:i') }}</span>
                                                        @if($activity->location)
                                                            <span class="text-truncate" style="max-width: 120px;"><i class="ri-map-pin-line align-middle me-1"></i>{{ $activity->location }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <i class="ri-arrow-right-s-line fs-20 text-muted flex-shrink-0 ms-2"></i>
                                        </div>
                                    </a>
                                @empty
                                    <div class="text-center py-4">
                                        <div class="avatar-sm mx-auto mb-2">
                                            <div class="avatar-title bg-soft-light text-muted rounded-circle fs-20">
                                                <i class="ri-calendar-2-line"></i>
                                            </div>
                                        </div>
                                        <h6 class="fs-14 mb-1">Aucune activité prévue</h6>
                                        <p class="text-muted small mb-0">Revenez plus tard pour voir le programme.</p>
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
