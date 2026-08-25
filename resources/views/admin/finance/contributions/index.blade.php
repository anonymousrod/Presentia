@extends('layouts.app')

@section('title', 'Suivi des contributions - ' . $group->name)

@section('content')
<div class="container-fluid max-w-1200 py-3 py-md-4">
    {{-- =================== EN-TÊTE =================== --}}
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-4 gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-muted">Tableau de bord</a></li>
                    <li class="breadcrumb-item"><a href="#" class="text-muted">Finances</a></li>
                    <li class="breadcrumb-item active fw-medium" aria-current="page">Contributions</li>
                </ol>
            </nav>
            <h3 class="fw-bold mb-0 fs-20 fs-md-24">Contributions - {{ $group->name }}</h3>
            <p class="text-muted mb-0 fs-13 mt-1">Gérez le suivi financier et les versements de ce groupe.</p>
        </div>
        
        <div class="d-flex d-md-none align-items-center gap-2">
            <div class="bg-primary bg-opacity-10 rounded-pill px-3 py-2 d-flex align-items-center gap-2 border border-primary-subtle">
                <span class="fs-12 fw-medium text-primary text-uppercase tracking-wider">À verser</span>
                <span class="fs-15 fw-bold text-primary">{{ number_format($pendingAmount, 0, ',', ' ') }} <small class="fs-10">F</small></span>
            </div>
            @if($pendingAmount > 0)
                @can('remittance.create')
                <button type="button" class="btn btn-success rounded-circle shadow-sm" data-bs-toggle="modal" data-bs-target="#remittanceModal" style="width:40px; height:40px;">
                    <i class="mdi mdi-cash-fast fs-18"></i>
                </button>
                @endcan
            @endif
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

    {{-- =================== FILTRES & BARRE D'OUTILS =================== --}}
    <div class="card border-0 shadow-sm mb-4 rounded-3 overflow-hidden">
        <div class="card-header border-0 bg-light bg-opacity-50 py-3 px-4 d-md-none" data-bs-toggle="collapse" data-bs-target="#filterCollapse" style="cursor: pointer;">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded p-1 bg-white shadow-sm d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                        <i class="mdi mdi-filter-variant text-primary fs-18"></i>
                    </div>
                    <h6 class="mb-0 fw-semibold">Filtres et Options</h6>
                </div>
                <i class="mdi mdi-chevron-down fs-20 text-muted"></i>
            </div>
        </div>
        
        <div id="filterCollapse" class="collapse d-md-block">
            <div class="card-body p-3 p-md-4 border-top border-light-subtle">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-4">
                    <form action="{{ route('admin.finance.contributions.index') }}" method="GET" class="d-flex flex-wrap align-items-end gap-3 flex-grow-1">
                        @if(auth()->user()->can('finance.view_all') && isset($allGroups))
                            <div>
                                <label class="form-label fs-12 fw-semibold text-muted text-uppercase tracking-wider mb-1"><i class="mdi mdi-account-group-outline me-1"></i>Groupe</label>
                                <select name="group_id" class="form-select bg-light border-light-subtle" onchange="this.form.submit()" style="min-width: 180px;">
                                    @foreach($allGroups as $g)
                                        <option value="{{ encode_id($g->id) }}" {{ $g->id == $group->id ? 'selected' : '' }}>{{ $g->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <div>
                            <label class="form-label fs-12 fw-semibold text-muted text-uppercase tracking-wider mb-1"><i class="mdi mdi-calendar-blank-outline me-1"></i>Année</label>
                            <div class="form-control bg-light border-light-subtle text-muted fw-bold d-flex align-items-center justify-content-center" style="min-width: 90px; user-select: none;">
                                {{ $year }}
                            </div>
                        </div>
                        <div>
                            <label class="form-label fs-12 fw-semibold text-muted text-uppercase tracking-wider mb-1"><i class="mdi mdi-calendar-month-outline me-1"></i>Mois</label>
                            <select name="month" class="form-select bg-light border-light-subtle" onchange="this.form.submit()">
                                @foreach(range(2, 11) as $m)
                                    @php $mStr = str_pad($m, 2, '0', STR_PAD_LEFT); @endphp
                                    <option value="{{ $mStr }}" {{ $month == $mStr ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>

                    {{-- Bloc versement Desktop --}}
                    <div class="d-none d-md-flex align-items-center gap-3 border-start ps-4">
                        <div class="d-flex flex-column text-end">
                            <span class="fs-12 fw-semibold text-muted text-uppercase tracking-wider mb-1">Montant à verser</span>
                            <span class="fs-20 fw-bold text-primary" style="line-height: 1;">{{ number_format($pendingAmount, 0, ',', ' ') }} <small class="fs-12 text-muted fw-normal">FCFA</small></span>
                        </div>
                        @if($pendingAmount > 0)
                            @can('remittance.create')
                            <button type="button" class="btn btn-success btn-lg rounded-pill shadow-sm px-4 d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#remittanceModal">
                                <i class="mdi mdi-cash-fast fs-18"></i>
                                <span class="fw-medium">Verser à la trésorerie</span>
                            </button>
                            @endcan
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- =================== STATISTIQUES =================== --}}
    {{-- Vue Mobile --}}
    <div class="d-md-none mb-4">
        <div class="row g-3">
            <div class="col-6">
                <div class="card border-0 shadow-sm h-100 rounded-3 mb-0">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <div class="avatar-xs bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center">
                                <i class="mdi mdi-calendar-check fs-14"></i>
                            </div>
                            <span class="fs-11 text-muted text-truncate">Attendu (mois)</span>
                        </div>
                        <h5 class="fw-bold mb-1 text-primary">{{ number_format($monthlyExpectedTotal, 0, ',', ' ') }} <small class="fs-10 text-muted">F</small></h5>
                        <div class="progress" style="height:4px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $monthlyProgressPercent }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card border-0 shadow-sm h-100 rounded-3 mb-0">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <div class="avatar-xs bg-info-subtle text-info rounded-circle d-flex align-items-center justify-content-center">
                                <i class="mdi mdi-finance fs-14"></i>
                            </div>
                            <span class="fs-11 text-muted text-truncate">Attendu (année)</span>
                        </div>
                        <h5 class="fw-bold mb-1 text-info">{{ number_format($yearlyExpectedTotal, 0, ',', ' ') }} <small class="fs-10 text-muted">F</small></h5>
                        <div class="progress" style="height:4px;">
                            <div class="progress-bar bg-info" role="progressbar" style="width: {{ $yearlyProgressPercent }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card border-0 shadow-sm h-100 rounded-3 mb-0">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <div class="avatar-xs bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center">
                                <i class="mdi mdi-wallet-plus fs-14"></i>
                            </div>
                            <span class="fs-11 text-muted text-truncate">Collecté (année)</span>
                        </div>
                        <h5 class="fw-bold mb-0 text-success">{{ number_format($yearlyCollectedTotal, 0, ',', ' ') }} <small class="fs-10 text-muted">F</small></h5>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card border-0 shadow-sm h-100 rounded-3 mb-0">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <div class="avatar-xs bg-warning-subtle text-warning rounded-circle d-flex align-items-center justify-content-center">
                                <i class="mdi mdi-chart-donut fs-14"></i>
                            </div>
                            <span class="fs-11 text-muted text-truncate">Progression</span>
                        </div>
                        <h5 class="fw-bold mb-1 text-warning">{{ $yearlyProgressPercent }}<small class="fs-10 text-muted">%</small></h5>
                        <div class="progress" style="height:4px;">
                            <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $yearlyProgressPercent }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Vue Desktop --}}
    <div class="row g-4 mb-4 d-none d-md-flex">
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 border-bottom border-3 border-primary shadow-sm h-100 rounded-3 overflow-hidden">
                <div class="card-body p-4 d-flex align-items-center gap-3">
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-primary-subtle text-primary rounded-3 fs-24">
                            <i class="mdi mdi-calendar-check"></i>
                        </span>
                    </div>
                    <div class="flex-grow-1">
                        <p class="text-uppercase fw-semibold fs-12 text-muted mb-1 tracking-wider">Attendu ce mois</p>
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h4 class="mb-0 fw-bold fs-22">{{ number_format($monthlyExpectedTotal, 0, ',', ' ') }} <span class="fs-14 text-muted fw-normal">FCFA</span></h4>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $monthlyProgressPercent }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 border-bottom border-3 border-info shadow-sm h-100 rounded-3 overflow-hidden">
                <div class="card-body p-4 d-flex align-items-center gap-3">
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-info-subtle text-info rounded-3 fs-24">
                            <i class="mdi mdi-finance"></i>
                        </span>
                    </div>
                    <div class="flex-grow-1">
                        <p class="text-uppercase fw-semibold fs-12 text-muted mb-1 tracking-wider">Attendu (année)</p>
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h4 class="mb-0 fw-bold fs-22">{{ number_format($yearlyExpectedTotal, 0, ',', ' ') }} <span class="fs-14 text-muted fw-normal">FCFA</span></h4>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-info" role="progressbar" style="width: {{ $yearlyProgressPercent }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 border-bottom border-3 border-success shadow-sm h-100 rounded-3 overflow-hidden">
                <div class="card-body p-4 d-flex align-items-center gap-3">
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-success-subtle text-success rounded-3 fs-24">
                            <i class="mdi mdi-wallet-plus"></i>
                        </span>
                    </div>
                    <div class="flex-grow-1">
                        <p class="text-uppercase fw-semibold fs-12 text-muted mb-1 tracking-wider">Collecté (année)</p>
                        <h4 class="mb-0 fw-bold fs-22">{{ number_format($yearlyCollectedTotal, 0, ',', ' ') }} <span class="fs-14 text-muted fw-normal">FCFA</span></h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 border-bottom border-3 border-warning shadow-sm h-100 rounded-3 overflow-hidden">
                <div class="card-body p-4 d-flex align-items-center gap-3">
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-warning-subtle text-warning rounded-3 fs-24">
                            <i class="mdi mdi-chart-donut"></i>
                        </span>
                    </div>
                    <div class="flex-grow-1">
                        <p class="text-uppercase fw-semibold fs-12 text-muted mb-1 tracking-wider">Progression (année)</p>
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h4 class="mb-0 fw-bold fs-22">{{ $yearlyProgressPercent }}<span class="fs-14 text-muted fw-normal">%</span></h4>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $yearlyProgressPercent }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- =================== GRILLE DE SAISIE =================== --}}
    <div class="card border-0 shadow-sm mb-4 rounded-3">
        <div class="card-header border-0 py-3 px-4 d-flex align-items-center justify-content-between" style="background: rgba(var(--vz-primary-rgb), 0.03); border-bottom: 1px solid rgba(var(--vz-primary-rgb), 0.1) !important;">
            <div class="d-flex align-items-center gap-2">
                <i class="mdi mdi-format-list-checks fs-20 text-primary"></i>
                <h5 class="mb-0 fw-bold fs-15 text-primary text-uppercase tracking-wider">Saisie - {{ \Carbon\Carbon::create()->month((int)$month)->translatedFormat('F') }} {{ $year }}</h5>
            </div>
        </div>
        <div class="card-body p-0">
            <form action="{{ route('admin.finance.contributions.store') }}" method="POST">
                @csrf
                <input type="hidden" name="group_id" value="{{ encode_id($group->id) }}">
                <div class="table-responsive">
                    <table class="table align-middle table-hover table-nowrap mb-0">
                        <thead style="background: rgba(var(--vz-light-rgb), 0.5);">
                            <tr>
                                <th class="ps-4 fw-semibold text-muted fs-12 text-uppercase tracking-wider py-3" style="width: 50px;">N°</th>
                                <th class="fw-semibold text-muted fs-12 text-uppercase tracking-wider py-3">Membre</th>
                                <th class="fw-semibold text-muted fs-12 text-uppercase tracking-wider py-3" style="width: 200px;">Bilan Annuel</th>
                                @foreach($sundays as $sunday)
                                    <th class="text-center fw-semibold text-muted fs-12 text-uppercase tracking-wider py-3" style="width: 100px;">
                                        <div class="d-flex flex-column">
                                            <span class="fs-10 text-primary">{{ $sunday->translatedFormat('D') }}</span>
                                            <span>{{ $sunday->format('d/m') }}</span>
                                        </div>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($members as $index => $member)
                                <tr class="border-bottom">
                                    <td class="ps-4 text-muted fw-medium">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            @if($member->photo)
                                                <img src="{{ asset('storage/' . $member->photo) }}" class="rounded-circle shadow-sm border" width="36" height="36" style="object-fit: cover;">
                                            @else
                                                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm flex-shrink-0" 
                                                     style="width: 36px; height: 36px; background: rgba(var(--vz-primary-rgb), 0.1); color: var(--vz-primary); font-size: 0.85rem;">
                                                    {{ strtoupper(substr($member->first_name, 0, 1) . substr($member->name, 0, 1)) }}
                                                </div>
                                            @endif
                                            <div>
                                                <h6 class="mb-0 fw-semibold fs-14">{{ $member->first_name }} {{ $member->name }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($member->weekly_contribution)
                                            @php
                                                $expected = $member->weekly_contribution * $totalSundaysInYear;
                                                $paid = $yearlyContributions->get($member->id, 0);
                                                $percent = $expected > 0 ? min(100, round(($paid / $expected) * 100)) : 0;
                                                $color = $percent >= 100 ? 'success' : ($percent >= 50 ? 'warning' : 'danger');
                                            @endphp
                                            <div class="d-flex flex-column justify-content-center h-100">
                                                <div class="d-flex justify-content-between align-items-end mb-1">
                                                    <span class="fs-12 fw-bold text-{{ $color }}">{{ number_format($paid, 0, ',', ' ') }}</span>
                                                    <span class="fs-10 text-muted">/ {{ number_format($expected, 0, ',', ' ') }}</span>
                                                </div>
                                                <div class="progress" style="height: 5px;">
                                                    <div class="progress-bar bg-{{ $color }}" role="progressbar" style="width: {{ $percent }}%"></div>
                                                </div>
                                                <span class="fs-10 text-muted mt-1">Cotis: {{ number_format($member->weekly_contribution, 0, ',', ' ') }} F/sem</span>
                                            </div>
                                        @else
                                            <span class="badge bg-light text-muted fw-normal border">Non définie</span>
                                        @endif
                                    </td>
                                    @foreach($sundays as $sunday)
                                        @php
                                            $dateStr = $sunday->format('Y-m-d');
                                            $memberContributions = $contributions->get($member->id);
                                            $contrib = $memberContributions ? $memberContributions->firstWhere('date', clone $sunday) : null;
                                            $amount = $contrib ? $contrib->amount : '';
                                            $isVersed = $contrib && $contrib->remittance_id;
                                            $isDisabled = !$member->weekly_contribution;
                                        @endphp
                                        <td class="text-center p-2">
                                            <div class="position-relative">
                                                <input type="number" 
                                                    name="contributions[{{ $member->id }}][{{ $dateStr }}]" 
                                                    class="form-control form-control-sm text-center {{ $isVersed ? 'bg-light border-light-subtle text-muted' : 'border-primary-subtle' }}" 
                                                    style="border-radius: 6px; font-weight: 500;"
                                                    value="{{ $amount }}" 
                                                    min="0"
                                                    step="50"
                                                    placeholder="{{ $member->weekly_contribution ?? '-' }}"
                                                    {{ $isVersed ? 'readonly' : '' }}
                                                    {{ $isDisabled ? 'disabled' : '' }}>
                                                @if($isVersed)
                                                    <i class="mdi mdi-check-decagram text-success position-absolute" style="top: -6px; right: -4px; font-size: 14px;" title="Déjà versé à la trésorerie"></i>
                                                @endif
                                            </div>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-transparent border-top border-light-subtle p-3 p-md-4 text-end">
                    <button type="submit" class="btn btn-primary px-4 rounded-pill shadow-sm">
                        <i class="mdi mdi-content-save-outline me-1"></i> Enregistrer les cotisations
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- =================== GRAPHIQUE =================== --}}
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header border-0 py-3 px-4 d-flex align-items-center justify-content-between" style="background: rgba(var(--vz-info-rgb), 0.03); border-bottom: 1px solid rgba(var(--vz-info-rgb), 0.1) !important;">
            <div class="d-flex align-items-center gap-2">
                <i class="mdi mdi-chart-bar fs-20 text-info"></i>
                <h5 class="mb-0 fw-bold fs-15 text-info text-uppercase tracking-wider">Évolution - {{ $year }}</h5>
            </div>
        </div>
        <div class="card-body p-3 p-md-4">
            <div id="evolutionChart" style="min-height: 300px;"></div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de versement -->
<div class="modal fade" id="remittanceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-4 pt-0">
                <div class="mb-4">
                    <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center mx-auto" style="width: 80px; height: 80px;">
                        <i class="mdi mdi-cash-fast text-success fs-36"></i>
                    </div>
                </div>
                <h4 class="fw-bold mb-3">Déclarer un versement</h4>
                <p class="text-muted fs-14 mb-4">
                    Vous êtes sur le point de déclarer un versement de <br>
                    <strong class="fs-20 text-dark">{{ number_format($pendingAmount, 0, ',', ' ') }} FCFA</strong> <br>
                    à la trésorerie générale pour <strong>{{ $group->name }}</strong> ({{ \Carbon\Carbon::create()->month((int)$month)->translatedFormat('F') }} {{ $year }}).
                </p>
                <form action="{{ route('admin.finance.remittances.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="group_id" value="{{ encode_id($group->id) }}">
                    <input type="hidden" name="year" value="{{ $year }}">
                    <input type="hidden" name="month" value="{{ $month }}">
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-success rounded-pill px-4 shadow-sm">Confirmer le versement</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var chartData = @json($monthlyChartData);
        var months = chartData.map(function(d) { return d.month; });
        var expected = chartData.map(function(d) { return d.expected; });
        var collected = chartData.map(function(d) { return d.collected; });

        var isMobile = window.innerWidth < 768;
        var options = {
            chart: {
                type: 'bar',
                height: isMobile ? 280 : 350,
                toolbar: { show: false },
                fontFamily: 'inherit'
            },
            series: [
                { name: 'Attendu', data: expected },
                { name: 'Collecté', data: collected }
            ],
            xaxis: {
                categories: months,
                labels: { rotate: isMobile ? -45 : 0, style: { cssClass: 'text-muted fw-medium' } },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                labels: {
                    formatter: function (val) {
                        return val >= 1000 ? (val / 1000).toFixed(0) + 'k' : val;
                    },
                    style: { cssClass: 'text-muted fw-medium' }
                }
            },
            colors: ['var(--vz-primary)', 'var(--vz-success)'],
            grid: {
                borderColor: 'var(--vz-border-color)',
                strokeDashArray: 4,
                yaxis: { lines: { show: true } }
            },
            dataLabels: {
                enabled: !isMobile,
                formatter: function (val) {
                    return val >= 1000 ? (val / 1000).toFixed(0) + 'k' : (val > 0 ? val : '');
                },
                style: { fontSize: '10px', fontWeight: 600 }
            },
            plotOptions: {
                bar: {
                    columnWidth: isMobile ? '70%' : '45%',
                    borderRadius: 4
                }
            },
            legend: {
                position: isMobile ? 'bottom' : 'top',
                horizontalAlign: isMobile ? 'center' : 'right',
                markers: { radius: 12 }
            },
            tooltip: {
                theme: 'light',
                y: {
                    formatter: function (val) {
                        return val.toLocaleString('fr-FR') + ' FCFA';
                    }
                }
            }
        };

        var chart = new ApexCharts(document.querySelector('#evolutionChart'), options);
        chart.render();
    });
</script>
@endpush