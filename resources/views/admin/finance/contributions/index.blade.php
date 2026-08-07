@extends('layouts.app')

@section('title', 'Suivi des contributions - ' . $group->name)

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Contributions - {{ $group->name }}</h4>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<style>
    .toolbar-pro {
        border-radius: 14px;
        background: var(--bs-body-bg);
        border: 1px solid var(--bs-border-color);
        position: relative;
        overflow: hidden;
    }
    .toolbar-pro::before {
        content: "";
        position: absolute;
        top: 0; left: 0; bottom: 0;
        width: 4px;
        background: linear-gradient(180deg, #405189, #0dcaf0);
    }
    .toolbar-pro .toolbar-label {
        font-size: 11px;
        font-weight: 600;
        letter-spacing: .04em;
        color: var(--bs-secondary-color);
        text-transform: uppercase;
        margin-bottom: 2px;
    }
    .toolbar-pro select.form-select-sm {
        font-size: 13px;
        border-color: var(--bs-border-color);
        background-color: var(--bs-body-bg);
    }
    .toolbar-divider {
        width: 1px;
        background: var(--bs-border-color);
        align-self: stretch;
    }
    .due-panel {
        background: linear-gradient(135deg, rgba(13,202,240,0.10), rgba(64,81,137,0.08));
        border-radius: 10px;
        padding: 8px 14px;
    }
    .due-panel .due-amount {
        font-size: 17px;
        font-weight: 700;
        color: var(--bs-info-text);
    }
    .kebab-btn {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--bs-border-color);
        background: var(--bs-tertiary-bg);
        color: var(--bs-body-color);
    }
    .kebab-btn:active, .kebab-btn:focus {
        background: var(--bs-secondary-bg);
    }
</style>

<!-- Barre d'outils : Filtres + Versement (au-dessus des cartes récapitulatives) -->
<div class="toolbar-pro shadow-sm mb-3 p-3">

    {{-- En-tête mobile : montant à verser + bouton Verser + menu 3 points --}}
    <div class="d-flex d-md-none align-items-center justify-content-between gap-2">
        <div class="due-panel flex-grow-1 d-flex align-items-center justify-content-between">
            <div>
                <p class="toolbar-label mb-0">À verser</p>
                <span class="due-amount">{{ number_format($pendingAmount, 0, ',', ' ') }} <small class="fs-11 text-body-secondary">FCFA</small></span>
            </div>
            @if($pendingAmount > 0)
                @can('remittance.create')
                <button type="button" class="btn btn-success btn-sm ms-2" data-bs-toggle="modal" data-bs-target="#remittanceModal">
                    <i class="mdi mdi-cash-fast me-1"></i>Verser
                </button>
                @endcan
            @endif
        </div>
        <button type="button" class="kebab-btn flex-shrink-0" data-bs-toggle="collapse" data-bs-target="#filterCollapse" aria-expanded="false" aria-controls="filterCollapse">
            <i class="ri-more-2-fill fs-18"></i>
        </button>
    </div>

    {{-- Filtres : repliables sur mobile (via le bouton 3 points), toujours visibles en desktop --}}
    <div class="collapse d-md-block mt-3 mt-md-0" id="filterCollapse">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">

            <form action="{{ route('admin.finance.contributions.index') }}" method="GET" class="d-flex flex-wrap align-items-end gap-2 gap-md-3">
                @if(auth()->user()->can('finance.view_all') && isset($allGroups))
                    <div>
                        <p class="toolbar-label mb-1"><i class="ri-team-line me-1"></i>Groupe</p>
                        <select name="group_id" class="form-select form-select-sm" onchange="this.form.submit()" style="min-width: 160px;">
                            @foreach($allGroups as $g)
                                <option value="{{ encode_id($g->id) }}" {{ $g->id == $group->id ? 'selected' : '' }}>{{ $g->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div>
                    <p class="toolbar-label mb-1"><i class="ri-calendar-2-line me-1"></i>Année</p>
                    <select name="year" class="form-select form-select-sm" onchange="this.form.submit()">
                        @for($y = date('Y') - 1; $y <= date('Y') + 1; $y++)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <p class="toolbar-label mb-1"><i class="ri-calendar-event-line me-1"></i>Mois</p>
                    <select name="month" class="form-select form-select-sm" onchange="this.form.submit()">
                        @foreach(range(2, 11) as $m)
                            @php $mStr = str_pad($m, 2, '0', STR_PAD_LEFT); @endphp
                            <option value="{{ $mStr }}" {{ $month == $mStr ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary d-none">Filtrer</button>
            </form>

            {{-- Bloc versement : visible seulement en desktop ici (déjà affiché en haut sur mobile) --}}
            <div class="d-none d-md-flex align-items-center gap-3">
                <div class="toolbar-divider d-none d-md-block" style="height: 40px;"></div>
                <div class="due-panel d-flex align-items-center gap-3">
                    <div>
                        <p class="toolbar-label mb-0">À verser</p>
                        <span class="due-amount">{{ number_format($pendingAmount, 0, ',', ' ') }} <small class="fs-12 text-body-secondary">FCFA</small></span>
                    </div>
                    @if($pendingAmount > 0)
                        @can('remittance.create')
                        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#remittanceModal">
                            <i class="mdi mdi-cash-fast me-1"></i>Verser
                        </button>
                        @endcan
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cartes récapitulatives -->
{{-- Mobile: single card with 2x2 grid --}}
<div class="d-md-none mb-3">
    <div class="card border-0 shadow-sm">
        <div class="card-body p-3">
            <div class="row g-2">
                <div class="col-6">
                    <div class="p-2 rounded" style="background: rgba(13,110,253,0.08);">
                        <p class="text-body-secondary fs-11 mb-0 text-truncate">
                            <span class="d-inline-block rounded-circle me-1" style="width:6px;height:6px;background:#0d6efd;"></span>
                            Attendu ce mois
                        </p>
                        <p class="fw-bold fs-16 mb-0 text-primary">{{ number_format($monthlyExpectedTotal, 0, ',', ' ') }} <span class="fs-10 text-body-secondary fw-medium">F</span></p>
                    </div>
                </div>
                <div class="col-6">
                    <div class="p-2 rounded" style="background: rgba(13,202,240,0.08);">
                        <p class="text-body-secondary fs-11 mb-0 text-truncate">
                            <span class="d-inline-block rounded-circle me-1" style="width:6px;height:6px;background:#0dcaf0;"></span>
                            Attendu (année)
                        </p>
                        <p class="fw-bold fs-16 mb-0 text-info">{{ number_format($yearlyExpectedTotal, 0, ',', ' ') }} <span class="fs-10 text-body-secondary fw-medium">F</span></p>
                    </div>
                </div>
                <div class="col-6">
                    <div class="p-2 rounded" style="background: rgba(25,135,84,0.08);">
                        <p class="text-body-secondary fs-11 mb-0 text-truncate">
                            <span class="d-inline-block rounded-circle me-1" style="width:6px;height:6px;background:#198754;"></span>
                            Collecté (année)
                        </p>
                        <p class="fw-bold fs-16 mb-0 text-success">{{ number_format($yearlyCollectedTotal, 0, ',', ' ') }} <span class="fs-10 text-body-secondary fw-medium">F</span></p>
                    </div>
                </div>
                <div class="col-6">
                    <div class="p-2 rounded" style="background: rgba(255,193,7,0.08);">
                        <p class="text-body-secondary fs-11 mb-0 text-truncate">
                            <span class="d-inline-block rounded-circle me-1" style="width:6px;height:6px;background:#ffc107;"></span>
                            Progression
                        </p>
                        <p class="fw-bold fs-16 mb-1 text-warning">{{ $yearlyProgressPercent }}<span class="fs-11 text-body-secondary fw-medium">%</span></p>
                        <div class="progress" style="height:3px;">
                            <div class="progress-bar bg-warning" role="progressbar"
                                 style="width: {{ $yearlyProgressPercent }}%"
                                 aria-valuenow="{{ $yearlyProgressPercent }}" aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Desktop/tablet: 4 separate cards --}}
<div class="row mb-4 g-3 d-none d-md-flex">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 border-start border-4 border-primary shadow-sm mb-0 h-100">
            <div class="card-body p-2 p-sm-3">
                <div class="d-flex flex-row align-items-center text-start">
                    <div class="flex-shrink-0 me-3">
                        <div class="avatar-sm bg-primary bg-opacity-10 rounded-3 d-flex align-items-center justify-content-center">
                            <i class="ri-calendar-check-line text-primary fs-20"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 min-width-0">
                        <p class="text-body-secondary fs-13 text-truncate mb-0">Attendu ce mois</p>
                        <h4 class="mb-0 fw-bold text-body fs-20">{{ number_format($monthlyExpectedTotal, 0, ',', ' ') }} <span class="fs-12 fw-medium text-body-secondary">F</span></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 border-start border-4 border-info shadow-sm mb-0 h-100">
            <div class="card-body p-2 p-sm-3">
                <div class="d-flex flex-row align-items-center text-start">
                    <div class="flex-shrink-0 me-3">
                        <div class="avatar-sm bg-info bg-opacity-10 rounded-3 d-flex align-items-center justify-content-center">
                            <i class="ri-funds-box-line text-info fs-20"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 min-width-0">
                        <p class="text-body-secondary fs-13 text-truncate mb-0">Attendu (année)</p>
                        <h4 class="mb-0 fw-bold text-body fs-20">{{ number_format($yearlyExpectedTotal, 0, ',', ' ') }} <span class="fs-12 fw-medium text-body-secondary">F</span></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 border-start border-4 border-success shadow-sm mb-0 h-100">
            <div class="card-body p-2 p-sm-3">
                <div class="d-flex flex-row align-items-center text-start">
                    <div class="flex-shrink-0 me-3">
                        <div class="avatar-sm bg-success bg-opacity-10 rounded-3 d-flex align-items-center justify-content-center">
                            <i class="ri-wallet-3-line text-success fs-20"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 min-width-0">
                        <p class="text-body-secondary fs-13 text-truncate mb-0">Collecté (année)</p>
                        <h4 class="mb-0 fw-bold text-body fs-20">{{ number_format($yearlyCollectedTotal, 0, ',', ' ') }} <span class="fs-12 fw-medium text-body-secondary">F</span></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 border-start border-4 border-warning shadow-sm mb-0 h-100">
            <div class="card-body p-2 p-sm-3">
                <div class="d-flex flex-row align-items-center text-start mb-2">
                    <div class="flex-shrink-0 me-3">
                        <div class="avatar-sm bg-warning bg-opacity-10 rounded-3 d-flex align-items-center justify-content-center">
                            <i class="ri-pie-chart-2-line text-warning fs-20"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 min-width-0">
                        <p class="text-body-secondary fs-13 text-truncate mb-0">Progression</p>
                        <h4 class="mb-0 fw-bold text-body fs-20">{{ $yearlyProgressPercent }}<span class="fs-13 fw-medium text-body-secondary">%</span></h4>
                    </div>
                </div>
                <div class="progress" style="height: 4px; margin-left: 55px;">
                    <div class="progress-bar bg-warning" role="progressbar"
                         style="width: {{ $yearlyProgressPercent }}%"
                         aria-valuenow="{{ $yearlyProgressPercent }}" aria-valuemin="0" aria-valuemax="100">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Grille de saisie - {{ \Carbon\Carbon::create()->month((int)$month)->translatedFormat('F') }} {{ $year }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.finance.contributions.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="group_id" value="{{ encode_id($group->id) }}">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>N°</th>
                                    <th>Nom et prénoms</th>
                                    <th>Bilan Annuel</th>
                                    @foreach($sundays as $sunday)
                                        <th class="text-center" style="width: 100px;">{{ $sunday->format('d/m') }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($members as $index => $member)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $member->first_name }} {{ $member->name }}</td>
                                        <td>
                                            @if($member->weekly_contribution)
                                                @php
                                                    $expected = $member->weekly_contribution * $totalSundaysInYear;
                                                    $paid = $yearlyContributions->get($member->id, 0);
                                                    $percent = $expected > 0 ? min(100, round(($paid / $expected) * 100)) : 0;
                                                    $color = $percent >= 100 ? 'success' : ($percent >= 50 ? 'warning' : 'danger');
                                                @endphp
                                                <div class="d-flex flex-column">
                                                    <span class="fs-12 text-muted mb-1">Hebdo: {{ number_format($member->weekly_contribution, 0, ',', ' ') }} F</span>
                                                    <div class="d-flex justify-content-between fs-12 mb-1">
                                                        <span class="fw-bold text-{{ $color }}">{{ number_format($paid, 0, ',', ' ') }}</span>
                                                        <span class="text-muted">/ {{ number_format($expected, 0, ',', ' ') }} F</span>
                                                    </div>
                                                    <div class="progress progress-sm">
                                                        <div class="progress-bar bg-{{ $color }}" role="progressbar" style="width: {{ $percent }}%"></div>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-muted fst-italic fs-12">Cotisation non définie</span>
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
                                            <td class="text-center">
                                                <input type="number" 
                                                       name="contributions[{{ $member->id }}][{{ $dateStr }}]" 
                                                       class="form-control form-control-sm text-center {{ $isVersed ? 'bg-light' : '' }}" 
                                                       value="{{ $amount }}" 
                                                       min="0"
                                                       step="50"
                                                       placeholder="{{ $member->weekly_contribution ?? '-' }}"
                                                       {{ $isVersed ? 'readonly title="Cotisation déjà versée à la trésorerie"' : '' }}
                                                       {{ $isDisabled ? 'disabled title="Veuillez définir la cotisation hebdomadaire du membre d\'abord"' : '' }}>
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 text-end">
                        <button type="submit" class="btn btn-primary w-100 w-sm-auto">Enregistrer les modifications</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Graphique d'évolution mensuelle -->
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Évolution des contributions - {{ $year }}</h5>
            </div>
            <div class="card-body">
                <div id="evolutionChart"></div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de versement -->
<div class="modal fade" id="remittanceModal" tabindex="-1" aria-labelledby="remittanceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="remittanceModalLabel">Confirmer le versement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-4">
                <div class="mt-2">
                    <lord-icon src="https://cdn.lordicon.com/tdrtiskw.json" trigger="loop" colors="primary:#f7b84b,secondary:#405189" style="width:130px;height:130px"></lord-icon>
                    <div class="mt-4 pt-2 fs-15 mx-4 mx-sm-5">
                        <h4>Êtes-vous sûr ?</h4>
                        <p class="text-muted mx-4 mb-0">Vous êtes sur le point de déclarer un versement de <strong>{{ number_format($pendingAmount, 0, ',', ' ') }} FCFA</strong> à la trésorerie générale pour le groupe <strong>{{ $group->name }}</strong>. Confirmez-vous cette action ?</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-center flex-nowrap flex-sm-wrap">
                <button type="button" class="btn btn-light w-100 w-sm-auto" data-bs-dismiss="modal">Annuler</button>
                <form action="{{ route('admin.finance.remittances.store') }}" method="POST" class="d-inline-block w-100 w-sm-auto">
                    @csrf
                    <input type="hidden" name="group_id" value="{{ encode_id($group->id) }}">
                    <button type="submit" class="btn btn-success w-100 w-sm-auto">Oui, déclarer le versement</button>
                </form>
            </div>
        </div>
    </div>
</div>

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
                toolbar: { show: false }
            },
            series: [
                {
                    name: 'Attendu',
                    data: expected
                },
                {
                    name: 'Collecté',
                    data: collected
                }
            ],
            xaxis: {
                categories: months,
                title: { text: 'Mois' },
                labels: { rotate: isMobile ? -45 : 0 }
            },
            yaxis: {
                title: { text: 'Montant (FCFA)' },
                labels: {
                    formatter: function (val) {
                        return val >= 1000 ? (val / 1000).toFixed(0) + 'k' : val;
                    }
                }
            },
            colors: ['#3B7DD8', '#28a745'],
            dataLabels: {
                enabled: !isMobile,
                formatter: function (val) {
                    return val >= 1000 ? (val / 1000).toFixed(0) + 'k' : val;
                },
                style: { fontSize: '10px' }
            },
            plotOptions: {
                bar: {
                    columnWidth: isMobile ? '80%' : '60%',
                    borderRadius: 4
                }
            },
            legend: {
                position: isMobile ? 'bottom' : 'top'
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return val.toLocaleString('fr-FR') + ' FCFA';
                    }
                }
            },
            responsive: [{
                breakpoint: 768,
                options: {
                    chart: { height: 280 },
                    dataLabels: { enabled: false },
                    legend: { position: 'bottom' }
                }
            }]
        };

        var chart = new ApexCharts(document.querySelector('#evolutionChart'), options);
        chart.render();
    });
</script>
@endpush

@endsection