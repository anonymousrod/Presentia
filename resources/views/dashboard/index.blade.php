@extends('layouts.app')

@section('title', 'Tableau de Bord')

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

    .hero-icon-container {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 130px;
        height: 130px;
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 2.5rem;
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        transform: rotate(12deg);
        box-shadow: 0 20px 40px rgba(0,0,0,0.3), inset 0 0 20px rgba(255,255,255,0.05);
        transition: transform 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .hero-icon-container:hover {
        transform: rotate(0deg) scale(1.05);
    }
    .hero-icon-container i {
        font-size: 4.5rem;
        background: linear-gradient(135deg, #ffffff, rgba(255,255,255,0.4));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        filter: drop-shadow(0 5px 15px rgba(0,0,0,0.5));
    }

    .hero-badge {
        background: rgba(var(--vz-primary-rgb), 0.15);
        border: 1px solid rgba(var(--vz-primary-rgb), 0.3);
        color: #fff;
        backdrop-filter: blur(5px);
    }
</style>
@endpush

@section('content')
    <div class="row">
        <div class="col">

            <div class="h-100">
                <div class="activity-hero px-4">
                    <div class="hero-grid"></div>
                    <div class="hero-orb orb-1"></div>
                    <div class="hero-orb orb-2"></div>
                    
                    <div class="container-fluid max-w-1200 hero-content">
                        <div class="row align-items-center">
                            <div class="col-lg-8">
                                <div class="d-flex align-items-center mb-4">
                                    <span class="badge hero-badge px-3 py-2 rounded-pill shadow-sm" style="font-size: 0.75rem; letter-spacing: 1px;"><i class="mdi mdi-view-dashboard text-warning me-1"></i> TABLEAU DE BORD</span>
                                </div>
                                <h1 class="text-white fw-bold display-4 mb-3" style="letter-spacing: -1px; text-shadow: 0 2px 10px rgba(0,0,0,0.3);">Bonjour, <span style="color: var(--vz-primary); filter: brightness(1.3);">{{ auth()->user()->first_name }} !</span></h1>
                                <p class="fs-16 mb-0" style="max-width: 550px; line-height: 1.6; color: rgba(255,255,255,0.7);">
                                    Voici un résumé complet de l'activité sur Me voici aujourd'hui.
                                </p>
                            </div>
                            <div class="col-lg-4 d-none d-lg-flex justify-content-end align-items-center">
                                <div class="hero-icon-container">
                                    <i class="mdi mdi-view-dashboard-outline"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row" style="margin-top: -5rem; position: relative; z-index: 10;">
                    <div class="col-12 col-md-6 col-xl-3">
                        <!-- card -->
                        <div class="card card-animate">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Total Membres</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value" data-target="{{ $stats['total_users'] }}">0</span></h4>
                                        <a href="{{ route('admin.users.index') }}" class="text-decoration-underline">Voir les membres</a>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-success-subtle text-success rounded fs-3">
                                            <i class="bx bx-user-circle"></i>
                                        </span>
                                    </div>
                                </div>
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div><!-- end col -->

                    <div class="col-12 col-md-6 col-xl-3">
                        <!-- card -->
                        <div class="card card-animate">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Total Activités</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value" data-target="{{ $stats['total_activities'] }}">0</span></h4>
                                        <a href="{{ route('activities.index') }}" class="text-decoration-underline">Toutes les activités</a>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-info-subtle text-info rounded fs-3">
                                            <i class="bx bx-calendar"></i>
                                        </span>
                                    </div>
                                </div>
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div><!-- end col -->

                    <div class="col-12 col-md-6 col-xl-3">
                        <!-- card -->
                        <div class="card card-animate">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Activités à venir</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value" data-target="{{ $stats['upcoming_activities'] }}">0</span></h4>
                                        <a href="{{ route('activities.index', ['status_filter' => 'upcoming']) }}" class="text-decoration-underline">Voir à venir</a>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-warning-subtle text-warning rounded fs-3">
                                            <i class="bx bx-time-five"></i>
                                        </span>
                                    </div>
                                </div>
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div><!-- end col -->

                    <div class="col-12 col-md-6 col-xl-3">
                        <!-- card -->
                        <div class="card card-animate">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Groupes</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value" data-target="{{ $stats['total_groups'] }}">0</span></h4>
                                        <a href="{{ route('admin.groups.index') }}" class="text-decoration-underline">Gérer les groupes</a>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-primary-subtle text-primary rounded fs-3">
                                            <i class="bx bx-group"></i>
                                        </span>
                                    </div>
                                </div>
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div><!-- end col -->
                </div> <!-- end row-->

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header align-items-center d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">Activités Récentes</h4>
                            </div><!-- end card header -->

                            <div class="card-body">
                                <!-- Mobile View (Cards) -->
                                <div class="d-md-none">
                                    @forelse($recent_activities as $activity)
                                        <div class="card border mb-3 shadow-none">
                                            <div class="card-body p-3">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <h5 class="fs-14 mb-0 text-truncate">{{ $activity->title }}</h5>
                                                    <span class="badge {{ $activity->status === 'En cours' ? 'bg-success' : ($activity->status === 'Terminée' ? 'bg-secondary' : 'bg-warning') }}">
                                                        {{ $activity->status }}
                                                    </span>
                                                </div>
                                                <div class="mb-2 text-muted fs-12">
                                                    <i class="ri-calendar-event-line align-bottom me-1"></i> 
                                                    {{ \Carbon\Carbon::parse($activity->start_time)->format('d/m/Y H:i') }}
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center mt-3">
                                                    <span class="text-muted fs-12"><i class="ri-map-pin-line align-bottom me-1"></i> {{ $activity->location ?? 'Non défini' }}</span>
                                                    <a href="{{ route('activities.show', $activity) }}" class="btn btn-sm btn-soft-primary">Détails</a>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center py-3 text-muted">Aucune activité récente.</div>
                                    @endforelse
                                </div>

                                <!-- Desktop View (Table) -->
                                <div class="table-responsive table-card d-none d-md-block">
                                    <table class="table table-hover table-centered align-middle table-nowrap mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Titre</th>
                                                <th>Date de début</th>
                                                <th>Date de fin</th>
                                                <th>Lieu</th>
                                                <th>Statut</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($recent_activities as $activity)
                                                <tr>
                                                    <td>
                                                        <h5 class="fs-14 my-1"><a href="{{ route('activities.show', $activity) }}" class="text-reset">{{ $activity->title }}</a></h5>
                                                        <span class="text-muted">{{ Str::limit($activity->description, 30) }}</span>
                                                    </td>
                                                    <td>
                                                        <h5 class="fs-14 my-1 fw-normal">{{ \Carbon\Carbon::parse($activity->start_time)->format('d M Y H:i') }}</h5>
                                                    </td>
                                                    <td>
                                                        <h5 class="fs-14 my-1 fw-normal">{{ \Carbon\Carbon::parse($activity->end_time)->format('d M Y H:i') }}</h5>
                                                    </td>
                                                    <td>
                                                        <h5 class="fs-14 my-1 fw-normal">{{ $activity->location ?? 'Non spécifié' }}</h5>
                                                    </td>
                                                    <td>
                                                        <span class="badge {{ $activity->status === 'En cours' ? 'bg-success' : ($activity->status === 'Terminée' ? 'bg-secondary' : 'bg-warning') }}">
                                                            {{ $activity->status }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('activities.show', $activity) }}" class="btn btn-sm btn-soft-primary">Voir détails</a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center">Aucune activité récente.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div> <!-- end .h-100-->

        </div> <!-- end col -->
    </div> <!-- end row -->
@endsection