@extends('layouts.app')

@section('title', 'Tableau de bord - Finance')

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
        background: rgba(var(--vz-warning-rgb), 0.15);
        border: 1px solid rgba(var(--vz-warning-rgb), 0.3);
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
                        <div class="col-lg-7">
                            <div class="mb-4 d-flex justify-content-center justify-content-lg-start">
                                <span class="badge hero-badge px-3 py-2 rounded-pill shadow-sm mb-3" style="font-size: 0.75rem; letter-spacing: 1px;">
                                    <i class="ri-bank-line me-1"></i> ESPACE FINANCE
                                </span>
                            </div>
                            <h1 class="display-5 fw-bold text-white mb-2 text-center text-lg-start" style="line-height: 1.2;">
                                Gestion Financière <span style="color: var(--vz-warning); filter: brightness(1.2);">Centrale</span>
                            </h1>
                            <p class="fs-16 mb-0 d-none d-lg-block" style="max-width: 550px; line-height: 1.6; color: rgba(255,255,255,0.7);">
                                Consultez l'état des caisses et validez les versements des groupes.
                            </p>
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
            <div class="row" style="margin-top: -5rem; position: relative; z-index: 10;">
                <div class="col-xl-4 col-md-6">
                    <div class="card premium-card overflow-hidden">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1 overflow-hidden">
                                    <p class="text-uppercase fw-semibold text-muted text-truncate mb-0" style="letter-spacing: 0.5px;">Total Validé en Caisse</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-4">
                                <div>
                                    <h4 class="fs-24 fw-bold ff-secondary mb-3"><span class="counter-value" data-target="{{ $stats['validated_remittances_amount'] }}">0</span> FCFA</h4>
                                    <span class="text-muted fw-medium">Versements approuvés</span>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-success-subtle text-success rounded fs-3 shadow-sm">
                                        <i class="ri-safe-2-line"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-4 col-md-6">
                    <div class="card premium-card overflow-hidden">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1 overflow-hidden">
                                    <p class="text-uppercase fw-semibold text-muted text-truncate mb-0" style="letter-spacing: 0.5px;">Versements en Attente</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-4">
                                <div>
                                    <h4 class="fs-24 fw-bold ff-secondary mb-3"><span class="counter-value" data-target="{{ $stats['pending_remittances_amount'] }}">0</span> FCFA</h4>
                                    <span class="text-danger fw-medium">{{ $stats['pending_remittances_count'] }} versement(s) à valider</span>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-warning-subtle text-warning rounded fs-3 shadow-sm">
                                        <i class="ri-error-warning-line"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-4 col-md-6">
                    <div class="card premium-card overflow-hidden">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1 overflow-hidden">
                                    <p class="text-uppercase fw-semibold text-muted text-truncate mb-0" style="letter-spacing: 0.5px;">Total Contributions</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-4">
                                <div>
                                    <h4 class="fs-24 fw-bold ff-secondary mb-3"><span class="counter-value" data-target="{{ $stats['total_contributions'] }}">0</span></h4>
                                    <span class="text-muted fw-medium">Opérations enregistrées</span>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-info-subtle text-info rounded fs-3 shadow-sm">
                                        <i class="ri-exchange-dollar-line"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

<!-- Pending Remittances List -->
            <div class="row mt-3">
                <div class="col-lg-12">
                    <div class="card premium-card">
                        <div class="card-header align-items-center d-flex border-bottom-dashed py-4 px-4">
                            <h4 class="card-title mb-0 flex-grow-1 fs-16 fw-semibold"><i class="ri-time-line text-warning me-1"></i> Versements en attente de validation</h4>
                        </div>
                        <div class="card-body p-4">
                            <div class="table-responsive">
                                <table class="table table-borderless table-centered table-premium align-middle table-nowrap mb-0">
                                    <thead>
                                        <tr>
                                            <th># ID</th>
                                            <th>Groupe</th>
                                            <th>Collecteur</th>
                                            <th>Montant</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($pending_remittances as $remittance)
                                            <tr class="border-bottom border-light">
                                                <td>
                                                    <span class="badge bg-soft-secondary text-secondary">#{{ $remittance->id }}</span>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm me-2 flex-shrink-0">
                                                            <span class="avatar-title bg-primary-subtle text-primary rounded-circle fs-16">
                                                                <i class="ri-team-fill"></i>
                                                            </span>
                                                        </div>
                                                        <span class="fw-semibold text-dark">{{ $remittance->group->name ?? 'N/A' }}</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex-shrink-0 me-2">
                                                            <img src="{{ $remittance->collector->avatar_url ?? asset('assets/images/users/avatar-1.jpg') }}" alt="" class="avatar-xs rounded-circle shadow-sm" />
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <h6 class="fs-14 mb-0">{{ $remittance->collector->first_name ?? 'Inconnu' }} {{ $remittance->collector->last_name ?? '' }}</h6>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-warning-subtle text-warning fs-14 py-2 px-3 shadow-sm border border-warning-subtle"><i class="ri-coins-line me-1"></i> {{ number_format($remittance->amount, 0, ',', ' ') }} FCFA</span>
                                                </td>
                                                <td>
                                                    <div class="mb-1"><span class="fw-medium text-body fs-14">{{ $remittance->created_at->format('d/m/Y') }}</span></div>
                                                    <span class="text-muted fs-13"><i class="ri-time-line align-middle me-1"></i> {{ $remittance->created_at->format('H:i') }}</span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-5">
                                                    <div class="avatar-md mx-auto mb-3">
                                                        <div class="avatar-title bg-soft-success text-success rounded-circle fs-24">
                                                            <i class="ri-check-double-line"></i>
                                                        </div>
                                                    </div>
                                                    <h5 class="fs-15 text-dark mb-1">Aucun versement en attente</h5>
                                                    <p class="text-muted mb-0">Tous les versements ont été traités.</p>
                                                </td>
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
