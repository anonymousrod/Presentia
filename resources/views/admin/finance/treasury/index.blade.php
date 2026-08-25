@extends('layouts.app')

@section('title', 'Trésorerie Générale')

@section('content')
<div class="container-fluid max-w-1200 py-3 py-md-4">
    {{-- =================== EN-TÊTE =================== --}}
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-4 gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-muted">Tableau de bord</a></li>
                    <li class="breadcrumb-item"><a href="#" class="text-muted">Finances</a></li>
                    <li class="breadcrumb-item active fw-medium" aria-current="page">Trésorerie Générale</li>
                </ol>
            </nav>
            <h3 class="fw-bold mb-0 fs-20 fs-md-24">Trésorerie Générale</h3>
            <p class="text-muted mb-0 fs-13 mt-1">Supervisez l'ensemble des fonds et validez les versements.</p>
        </div>
    </div>

    {{-- Alertes --}}
    @if(session('success'))
        <div class="alert border-0 mb-4 d-flex align-items-center gap-3 p-3 shadow-sm rounded-3"
             style="background: rgba(var(--vz-success-rgb), 0.12); border-left: 4px solid var(--vz-success) !important;">
            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                 style="width:40px; height:40px; background: rgba(var(--vz-success-rgb), 0.2);">
                <i class="mdi mdi-check-circle fs-20" style="color: var(--vz-success);"></i>
            </div>
            <div class="flex-grow-1">
                <h6 class="mb-0 fw-bold text-success">Succès !</h6>
                <span class="fs-13 text-body">{{ session('success') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert border-0 mb-4 d-flex align-items-center gap-3 p-3 shadow-sm rounded-3"
             style="background: rgba(var(--vz-danger-rgb), 0.12); border-left: 4px solid var(--vz-danger) !important;">
            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                 style="width:40px; height:40px; background: rgba(var(--vz-danger-rgb), 0.2);">
                <i class="mdi mdi-alert-circle fs-20" style="color: var(--vz-danger);"></i>
            </div>
            <div class="flex-grow-1">
                <h6 class="mb-0 fw-bold text-danger">Erreur !</h6>
                <span class="fs-13 text-body">{{ session('error') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- =================== STATISTIQUES =================== --}}
    {{-- Vue Mobile --}}
    <div class="d-md-none mb-4">
        <div class="row g-2">
            <div class="col-12">
                <div class="card border-0 shadow-sm h-100 rounded-3 mb-0" style="background: linear-gradient(135deg, rgba(var(--vz-success-rgb), 0.1) 0%, rgba(var(--vz-success-rgb), 0.02) 100%); border-left: 4px solid var(--vz-success) !important;">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fs-12 text-muted fw-semibold text-uppercase tracking-wider">Total en Caisse (Validé)</span>
                            <div class="avatar-xs bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bx bx-dollar-circle fs-16"></i>
                            </div>
                        </div>
                        <h4 class="fw-bold mb-0 text-success">{{ number_format($totalValidated, 0, ',', ' ') }} <small class="fs-12 text-muted">FCFA</small></h4>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="card border-0 shadow-sm h-100 rounded-3 mb-0">
                    <div class="card-body p-2">
                        <div class="d-flex align-items-center gap-1 mb-1">
                            <div class="avatar-xs bg-warning-subtle text-warning rounded-circle d-flex align-items-center justify-content-center flex-shrink-0">
                                <i class="bx bx-time fs-12"></i>
                            </div>
                            <span class="fs-10 text-muted text-truncate fw-medium">En attente</span>
                        </div>
                        <h6 class="fw-bold mb-0 text-warning fs-12">{{ number_format($totalPending, 0, ',', ' ') }} <small class="fs-9 text-muted">F</small></h6>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="card border-0 shadow-sm h-100 rounded-3 mb-0">
                    <div class="card-body p-2">
                        <div class="d-flex align-items-center gap-1 mb-1">
                            <div class="avatar-xs bg-danger-subtle text-danger rounded-circle d-flex align-items-center justify-content-center flex-shrink-0">
                                <i class="bx bx-user-x fs-12"></i>
                            </div>
                            <span class="fs-10 text-muted text-truncate fw-medium">Non versé</span>
                        </div>
                        <h6 class="fw-bold mb-0 text-danger fs-12">{{ number_format($totalUnremitted, 0, ',', ' ') }} <small class="fs-9 text-muted">F</small></h6>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="card border-0 shadow-sm h-100 rounded-3 mb-0">
                    <div class="card-body p-2">
                        <div class="d-flex align-items-center gap-1 mb-1">
                            <div class="avatar-xs bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0">
                                <i class="bx bx-wallet fs-12"></i>
                            </div>
                            <span class="fs-10 text-muted text-truncate fw-medium">Collecté</span>
                        </div>
                        <h6 class="fw-bold mb-0 text-primary fs-12">{{ number_format($totalCollected, 0, ',', ' ') }} <small class="fs-9 text-muted">F</small></h6>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Vue Desktop --}}
    <div class="row g-3 mb-4 d-none d-md-flex">
        <!-- Total en Caisse (Validé) -->
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 border-bottom border-3 border-success shadow-sm h-100 rounded-3 overflow-hidden position-relative">
                <div class="position-absolute top-0 end-0 p-3 opacity-25">
                    <i class="bx bx-dollar-circle" style="font-size: 70px; color: var(--vz-success);"></i>
                </div>
                <div class="card-body p-3 position-relative z-1">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="avatar-xs flex-shrink-0">
                            <span class="avatar-title bg-success-subtle text-success rounded-3 fs-18">
                                <i class="bx bx-dollar-circle"></i>
                            </span>
                        </div>
                        <p class="text-uppercase fw-semibold fs-11 text-muted mb-0 tracking-wider">Total Caisse (Validé)</p>
                    </div>
                    <h3 class="mb-0 fw-bold fs-22 text-success">{{ number_format($totalValidated, 0, ',', ' ') }} <span class="fs-13 text-muted fw-normal">FCFA</span></h3>
                </div>
            </div>
        </div>

        <!-- En attente de validation -->
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 border-bottom border-3 border-warning shadow-sm h-100 rounded-3 overflow-hidden">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="avatar-xs flex-shrink-0">
                            <span class="avatar-title bg-warning-subtle text-warning rounded-3 fs-18">
                                <i class="bx bx-time"></i>
                            </span>
                        </div>
                        <p class="text-uppercase fw-semibold fs-11 text-muted mb-0 tracking-wider">En attente versement</p>
                    </div>
                    <h3 class="mb-0 fw-bold fs-22 text-warning">{{ number_format($totalPending, 0, ',', ' ') }} <span class="fs-13 text-muted fw-normal">FCFA</span></h3>
                </div>
            </div>
        </div>

        <!-- Cotisations non versées (Chez collecteurs) -->
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 border-bottom border-3 border-danger shadow-sm h-100 rounded-3 overflow-hidden">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="avatar-xs flex-shrink-0">
                            <span class="avatar-title bg-danger-subtle text-danger rounded-3 fs-18">
                                <i class="bx bx-user-x"></i>
                            </span>
                        </div>
                        <p class="text-uppercase fw-semibold fs-11 text-muted mb-0 tracking-wider">Non versé (Collecteurs)</p>
                    </div>
                    <h3 class="mb-0 fw-bold fs-22 text-danger">{{ number_format($totalUnremitted, 0, ',', ' ') }} <span class="fs-13 text-muted fw-normal">FCFA</span></h3>
                </div>
            </div>
        </div>

        <!-- Total Collecté (Saisi) -->
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 border-bottom border-3 border-primary shadow-sm h-100 rounded-3 overflow-hidden">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="avatar-xs flex-shrink-0">
                            <span class="avatar-title bg-primary-subtle text-primary rounded-3 fs-18">
                                <i class="bx bx-wallet"></i>
                            </span>
                        </div>
                        <p class="text-uppercase fw-semibold fs-11 text-muted mb-0 tracking-wider">Total Collecté (Saisi)</p>
                    </div>
                    <h3 class="mb-0 fw-bold fs-22 text-primary">{{ number_format($totalCollected, 0, ',', ' ') }} <span class="fs-13 text-muted fw-normal">FCFA</span></h3>
                </div>
            </div>
        </div>
    </div>

    {{-- =================== FILTRES & TABLEAU =================== --}}
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        {{-- En-tête de la carte avec filtres --}}
        <div class="card-header border-0 bg-white py-3 px-4" style="border-bottom: 1px solid rgba(var(--vz-dark-rgb), 0.05) !important;">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="avatar-xs bg-light rounded-circle d-flex align-items-center justify-content-center">
                        <i class="mdi mdi-history fs-16 text-muted"></i>
                    </div>
                    <h5 class="card-title mb-0 fw-bold fs-15 text-dark">Historique des versements déclarés</h5>
                </div>
                
                {{-- Bouton Filtres Mobile --}}
                <div class="d-md-none">
                    <button class="btn btn-light w-100 d-flex align-items-center justify-content-center gap-2" type="button" data-bs-toggle="collapse" data-bs-target="#tableFilters">
                        <i class="mdi mdi-filter-variant"></i> Filtres
                    </button>
                </div>
                
                {{-- Formulaire de Filtres --}}
                <div class="collapse d-md-block" id="tableFilters">
                    <form action="{{ route('admin.finance.treasury.index') }}" method="GET" class="d-flex flex-column flex-md-row gap-2 mt-2 mt-md-0">
                        <div class="input-group input-group-sm" style="min-width: 200px;">
                            <span class="input-group-text bg-light border-light-subtle"><i class="mdi mdi-account-group-outline"></i></span>
                            <select name="group_id" class="form-select border-light-subtle bg-light text-muted" onchange="this.form.submit()">
                                <option value="">Tous les groupes</option>
                                @foreach($groups as $g)
                                    <option value="{{ encode_id($g->id) }}" {{ decode_id(request('group_id')) == $g->id ? 'selected' : '' }}>{{ $g->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="input-group input-group-sm" style="min-width: 180px;">
                            <span class="input-group-text bg-light border-light-subtle"><i class="mdi mdi-list-status"></i></span>
                            <select name="status" class="form-select border-light-subtle bg-light text-muted" onchange="this.form.submit()">
                                <option value="">Tous les statuts</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                                <option value="validated" {{ request('status') == 'validated' ? 'selected' : '' }}>Validés</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejetés</option>
                            </select>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Vue Desktop / Tablette (Tableau) --}}
        <div class="d-none d-md-block card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle table-hover table-nowrap mb-0">
                    <thead style="background: rgba(var(--vz-light-rgb), 0.5);">
                        <tr>
                            <th class="ps-4 fw-semibold text-muted fs-12 text-uppercase tracking-wider py-3"># ID</th>
                            <th class="fw-semibold text-muted fs-12 text-uppercase tracking-wider py-3">Date</th>
                            <th class="fw-semibold text-muted fs-12 text-uppercase tracking-wider py-3">Groupe</th>
                            <th class="fw-semibold text-muted fs-12 text-uppercase tracking-wider py-3">Chargé de collecte</th>
                            <th class="fw-semibold text-muted fs-12 text-uppercase tracking-wider py-3">Montant</th>
                            <th class="fw-semibold text-muted fs-12 text-uppercase tracking-wider py-3">Statut</th>
                            <th class="fw-semibold text-muted fs-12 text-uppercase tracking-wider py-3 text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($remittances as $rem)
                            <tr class="border-bottom border-light-subtle">
                                <td class="ps-4"><span class="fw-medium text-muted">#{{ $rem->id }}</span></td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-medium">{{ $rem->created_at->format('d/m/Y') }}</span>
                                        <span class="fs-11 text-muted">{{ $rem->created_at->format('H:i') }}</span>
                                    </div>
                                </td>
                                <td>
                                    @if($rem->group)
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">{{ $rem->group->name }}</span>
                                    @else
                                        <span class="text-muted fst-italic">Inconnu</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-xxs bg-light rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="mdi mdi-account text-muted"></i>
                                        </div>
                                        <span class="fw-medium text-dark">{{ $rem->collector->first_name }} {{ $rem->collector->name }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-bold fs-14 text-dark">{{ number_format($rem->amount, 0, ',', ' ') }} <small class="text-muted fw-normal">FCFA</small></span>
                                </td>
                                <td>
                                    @if($rem->status == 'pending')
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1"><i class="mdi mdi-timer-sand text-warning me-1"></i>En attente</span>
                                    @elseif($rem->status == 'validated')
                                        <div class="d-flex flex-column align-items-start">
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"><i class="mdi mdi-check-circle text-success me-1"></i>Validé le {{ $rem->validated_at ? $rem->validated_at->format('d/m/Y') : '' }}</span>
                                            @if($rem->treasurer)
                                                <span class="fs-11 text-muted mt-1"><i class="mdi mdi-account-check me-1 text-success"></i>par {{ $rem->treasurer->first_name }} {{ $rem->treasurer->name }}</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1"><i class="mdi mdi-close-circle text-danger me-1"></i>Rejeté</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    @if($rem->status == 'pending')
                                        @can('remittance.validate')
                                        <button type="button" class="btn btn-sm btn-success rounded-pill shadow-sm px-3" data-bs-toggle="modal" data-bs-target="#validateModal{{ $rem->id }}">
                                            <i class="mdi mdi-check-decagram me-1"></i> Valider
                                        </button>
                                        @else
                                        <span class="fs-12 text-muted fst-italic">Aucune action</span>
                                        @endcan
                                    @else
                                        <div class="d-inline-flex align-items-center text-success bg-success-subtle rounded-pill px-3 py-1 fs-12 fw-medium">
                                            <i class="mdi mdi-check-all me-1"></i> Terminé
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <i class="mdi mdi-inbox-outline fs-36 text-muted mb-2 opacity-50"></i>
                                        <p class="mb-0 fs-14">Aucun versement déclaré pour le moment.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Vue Mobile (Cartes de liste) --}}
        <div class="d-md-none card-body p-0 bg-light">
            @forelse($remittances as $rem)
                <div class="card border-0 mb-2 mx-2 mt-2 shadow-sm rounded-3 position-relative overflow-hidden">
                    {{-- Liseret de statut sur la gauche --}}
                    <div class="position-absolute top-0 bottom-0 start-0" style="width: 4px; background-color: {{ $rem->status == 'pending' ? 'var(--vz-warning)' : ($rem->status == 'validated' ? 'var(--vz-success)' : 'var(--vz-danger)') }};"></div>
                    
                    <div class="card-body p-3 ps-4">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h6 class="mb-1 fw-bold">#{{ $rem->id }} - {{ $rem->group ? $rem->group->name : 'Inconnu' }}</h6>
                                <p class="text-muted fs-12 mb-0"><i class="mdi mdi-calendar-clock text-muted me-1"></i>{{ $rem->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                            <span class="fw-bold fs-15 text-dark">{{ number_format($rem->amount, 0, ',', ' ') }}<small class="text-muted fw-normal ms-1">F</small></span>
                        </div>
                        
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div class="avatar-xxs bg-light rounded-circle d-flex align-items-center justify-content-center flex-shrink-0">
                                <i class="mdi mdi-account text-muted fs-10"></i>
                            </div>
                            <span class="fs-13 text-muted text-truncate">{{ $rem->collector->first_name }} {{ $rem->collector->name }}</span>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top border-light-subtle">
                             <div>
                                 @if($rem->status == 'pending')
                                     <span class="badge bg-warning-subtle text-warning px-2"><i class="mdi mdi-timer-sand me-1"></i>Attente</span>
                                 @elseif($rem->status == 'validated')
                                     <div class="d-flex flex-column align-items-start">
                                         <span class="badge bg-success-subtle text-success px-2"><i class="mdi mdi-check-circle me-1"></i>Validé le {{ $rem->validated_at ? $rem->validated_at->format('d/m/Y') : '' }}</span>
                                         @if($rem->treasurer)
                                             <span class="fs-11 text-muted mt-1"><i class="mdi mdi-account-check me-1 text-success"></i>par {{ $rem->treasurer->first_name }} {{ $rem->treasurer->name }}</span>
                                         @endif
                                     </div>
                                 @else
                                     <span class="badge bg-danger-subtle text-danger px-2"><i class="mdi mdi-close-circle me-1"></i>Rejeté</span>
                                 @endif
                             </div>
                            
                            <div>
                                @if($rem->status == 'pending')
                                    @can('remittance.validate')
                                    <button type="button" class="btn btn-sm btn-success rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#validateModal{{ $rem->id }}">
                                        Valider
                                    </button>
                                    @endcan
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <i class="mdi mdi-inbox-outline fs-36 text-muted mb-2 opacity-50"></i>
                    <p class="text-muted fs-14 mb-0">Aucun versement trouvé.</p>
                </div>
            @endforelse
        </div>

        @if($remittances->hasPages())
        <div class="card-footer bg-white border-top border-light-subtle p-3">
            {{ $remittances->links() }}
        </div>
        @endif
    </div>
</div>

{{-- =================== MODALES DE VALIDATION =================== --}}
@foreach($remittances as $rem)
    @if($rem->status == 'pending')
        <div class="modal fade" id="validateModal{{ $rem->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="modal-header border-0 pb-0 pt-4 px-4">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center p-4 pt-0">
                        <div class="mb-4">
                            <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center mx-auto" style="width: 80px; height: 80px;">
                                <i class="bx bx-check-shield text-success fs-40"></i>
                            </div>
                        </div>
                        <h4 class="fw-bold mb-3">Valider la réception</h4>
                        <p class="text-muted fs-14 mb-4">
                            Confirmez-vous avoir reçu physiquement <br>
                            <strong class="fs-20 text-dark">{{ number_format($rem->amount, 0, ',', ' ') }} FCFA</strong> <br>
                            de la part de <strong>{{ $rem->collector->first_name }} {{ $rem->collector->name }}</strong> ?
                        </p>
                        <form action="{{ route('admin.finance.remittances.validate', $rem) }}" method="POST">
                            @csrf
                            <div class="d-flex gap-2 justify-content-center">
                                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Annuler</button>
                                <button type="submit" class="btn btn-success rounded-pill px-4 shadow-sm"><i class="mdi mdi-check-decagram me-1"></i> Confirmer réception</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endforeach

@endsection
