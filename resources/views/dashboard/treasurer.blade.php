@extends('layouts.app')

@section('title', 'Tableau de bord - Finance')

@push('css')
<style>
    /* Hero Section */
    .activity-hero {
        background-color: #0b0f19;
        background-image: 
            radial-gradient(circle at 0% 0%, rgba(var(--vz-primary-rgb), 0.25) 0%, transparent 50%),
            radial-gradient(circle at 100% 100%, rgba(var(--vz-warning-rgb), 0.2) 0%, transparent 50%);
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
        background: var(--vz-warning);
        top: -150px; left: -100px;
    }
    .orb-2 {
        width: 300px; height: 300px;
        background: var(--vz-primary);
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
                                    <i class="ri-bank-line me-1"></i> ESPACE TRÉSORIER GÉNÉRAL
                                </span>
                            </div>
                            <h1 class="display-5 fw-bold text-white mb-2 text-center text-lg-start" style="line-height: 1.2;">
                                Gestion Financière <span style="color: var(--vz-warning); filter: brightness(1.2);">Centrale</span>
                            </h1>
                            <p class="fs-16 mb-0 d-none d-lg-block" style="max-width: 550px; line-height: 1.6; color: rgba(255,255,255,0.7);">
                                Consultez l'état des caisses et validez les versements des groupes en temps réel.
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
            <div class="row g-2 g-md-3" style="margin-top: -5rem; position: relative; z-index: 10;">
                <!-- Total Validé en Caisse -->
                <div class="col-6 col-md-6 col-xl-4">
                    <div class="card premium-card overflow-hidden h-100">
                        <div class="card-body p-2 p-sm-3 p-md-4">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1 overflow-hidden">
                                    <p class="text-uppercase fw-semibold text-muted text-truncate mb-0 fs-10 fs-md-12" style="letter-spacing: 0.5px;">Total Caisse</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-2 mt-md-4">
                                <div>
                                    <h4 class="fs-16 fs-md-24 fw-bold ff-secondary mb-1 mb-md-2"><span class="counter-value" data-target="{{ $stats['validated_remittances_amount'] }}">0</span> <small class="fs-11 fs-md-14">FCFA</small></h4>
                                    <span class="text-success fw-medium fs-11 fs-md-13 d-none d-sm-inline"><i class="ri-checkbox-circle-line me-1"></i>Approuvés</span>
                                </div>
                                <div class="avatar-xs avatar-sm-md flex-shrink-0">
                                    <span class="avatar-title bg-success-subtle text-success rounded fs-16 fs-md-20 shadow-sm">
                                        <i class="ri-safe-2-line"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Versements en Attente -->
                <div class="col-6 col-md-6 col-xl-4">
                    <div class="card premium-card overflow-hidden h-100">
                        <div class="card-body p-2 p-sm-3 p-md-4">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1 overflow-hidden">
                                    <p class="text-uppercase fw-semibold text-muted text-truncate mb-0 fs-10 fs-md-12" style="letter-spacing: 0.5px;">En Attente</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-2 mt-md-4">
                                <div>
                                    <h4 class="fs-16 fs-md-24 fw-bold ff-secondary mb-1 mb-md-2"><span class="counter-value" data-target="{{ $stats['pending_remittances_amount'] }}">0</span> <small class="fs-11 fs-md-14">FCFA</small></h4>
                                    <span class="text-warning fw-medium fs-11 fs-md-13 d-none d-sm-inline"><i class="ri-time-line me-1"></i>{{ $stats['pending_remittances_count'] }} versement(s)</span>
                                </div>
                                <div class="avatar-xs avatar-sm-md flex-shrink-0">
                                    <span class="avatar-title bg-warning-subtle text-warning rounded fs-16 fs-md-20 shadow-sm">
                                        <i class="ri-error-warning-line"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Total Contributions -->
                <div class="col-12 col-md-12 col-xl-4">
                    <div class="card premium-card overflow-hidden h-100">
                        <div class="card-body p-2 p-sm-3 p-md-4">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1 overflow-hidden">
                                    <p class="text-uppercase fw-semibold text-muted text-truncate mb-0 fs-10 fs-md-12" style="letter-spacing: 0.5px;">Contributions</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-2 mt-md-4">
                                <div>
                                    <h4 class="fs-18 fs-md-24 fw-bold ff-secondary mb-1 mb-md-2"><span class="counter-value" data-target="{{ $stats['total_contributions'] }}">0</span></h4>
                                    <span class="text-info fw-medium fs-11 fs-md-13 d-none d-sm-inline"><i class="ri-exchange-dollar-line me-1"></i>Opérations enregistrées</span>
                                </div>
                                <div class="avatar-xs avatar-sm-md flex-shrink-0">
                                    <span class="avatar-title bg-info-subtle text-info rounded fs-16 fs-md-20 shadow-sm">
                                        <i class="ri-exchange-dollar-line"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Remittances List -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card premium-card overflow-hidden">
                        <div class="card-header border-0 bg-transparent py-3 px-3 px-md-4 border-bottom border-light-subtle d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-xs bg-warning-subtle text-warning rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="ri-time-line fs-16"></i>
                                </div>
                                <h5 class="card-title mb-0 fw-bold fs-15 text-body">Versements en attente de validation</h5>
                            </div>
                        </div>

                        <div class="card-body p-0">
                            <!-- Mobile View (Cards) -->
                            <div class="d-block d-md-none p-3">
                                <div class="d-flex flex-column gap-2">
                                    @forelse($pending_remittances as $remittance)
                                    <div class="card border border-light-subtle rounded-3 shadow-none mb-1 p-3">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <span class="badge bg-soft-secondary text-secondary fs-10 mb-1">#{{ $remittance->id }}</span>
                                                <h6 class="fs-14 fw-bold mb-0 text-body">
                                                    {{ $remittance->group->name ?? 'N/A' }}
                                                </h6>
                                            </div>
                                            <span class="badge bg-warning-subtle text-warning fs-12 py-1 px-2 rounded-pill">
                                                {{ number_format($remittance->amount, 0, ',', ' ') }} FCFA
                                            </span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top border-light-subtle">
                                            <div class="d-flex align-items-center gap-2">
                                                <img src="{{ $remittance->collector->avatar_url ?? asset('assets/images/users/avatar-1.jpg') }}" alt="" class="avatar-xs rounded-circle shadow-sm" />
                                                <span class="text-muted fs-12 fw-medium">
                                                    {{ $remittance->collector->first_name ?? 'Inconnu' }}
                                                </span>
                                            </div>
                                            <span class="text-muted fs-11">
                                                <i class="ri-time-line me-1"></i> {{ $remittance->created_at->format('d/m H:i') }}
                                            </span>
                                        </div>
                                    </div>
                                    @empty
                                    <div class="text-center py-4 text-muted fs-13">
                                        <div class="avatar-md mx-auto mb-2">
                                            <div class="avatar-title bg-soft-success text-success rounded-circle fs-20">
                                                <i class="ri-check-double-line"></i>
                                            </div>
                                        </div>
                                        Aucun versement en attente.
                                    </div>
                                    @endforelse
                                </div>
                            </div>

                            <!-- Desktop View (Table) -->
                            <div class="table-responsive d-none d-md-block">
                                <table class="table table-hover align-middle table-nowrap mb-0 table-premium">
                                    <thead class="table-light">
                                        <tr class="text-uppercase fs-11">
                                            <th class="ps-4"># ID</th>
                                            <th>Groupe</th>
                                            <th>Collecteur</th>
                                            <th>Montant</th>
                                            <th class="text-end pe-4">Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($pending_remittances as $remittance)
                                            <tr>
                                                <td class="ps-4">
                                                    <span class="badge bg-soft-secondary text-secondary">#{{ $remittance->id }}</span>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-xs me-2 flex-shrink-0">
                                                            <span class="avatar-title bg-primary-subtle text-primary rounded-circle fs-14">
                                                                <i class="ri-team-fill"></i>
                                                            </span>
                                                        </div>
                                                        <span class="fw-semibold text-body fs-13">{{ $remittance->group->name ?? 'N/A' }}</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex-shrink-0 me-2">
                                                            <img src="{{ $remittance->collector->avatar_url ?? asset('assets/images/users/avatar-1.jpg') }}" alt="" class="avatar-xs rounded-circle shadow-sm" />
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <h6 class="fs-13 mb-0">{{ $remittance->collector->first_name ?? 'Inconnu' }} {{ $remittance->collector->last_name ?? '' }}</h6>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-warning-subtle text-warning fs-13 py-1 px-3 rounded-pill border border-warning-subtle">
                                                        <i class="ri-coins-line me-1"></i> {{ number_format($remittance->amount, 0, ',', ' ') }} FCFA
                                                    </span>
                                                </td>
                                                <td class="text-end pe-4">
                                                    <span class="fw-medium text-body fs-13 d-block">{{ $remittance->created_at->format('d/m/Y') }}</span>
                                                    <span class="text-muted fs-11"><i class="ri-time-line me-1"></i> {{ $remittance->created_at->format('H:i') }}</span>
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
                                                    <h5 class="fs-15 mb-1">Aucun versement en attente</h5>
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
