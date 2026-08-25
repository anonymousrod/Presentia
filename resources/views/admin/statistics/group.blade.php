@extends('layouts.app')

@section('title', 'Statistiques de mon groupe')

@section('content')
<div class="container-fluid p-0">
    {{-- En-tête de page --}}
    <div class="row align-items-center mb-3 g-2">
        <div class="col-12 col-md-6">
            <h4 class="mb-1 fw-bold fs-18 fs-md-22">Statistiques du groupe : <span class="text-primary">{{ $group->name }}</span></h4>
            <p class="text-muted mb-0 fs-12 fs-md-13">Analyse détaillée de l'assiduité des membres de ce groupe.</p>
        </div>

        @if($isGlobal)
        <div class="col-12 col-md-6 text-md-end mt-2 mt-md-0">
            <form action="{{ route('admin.statistics.group.index') }}" method="GET" class="d-inline-flex align-items-center gap-2 w-100 justify-content-md-end">
                <label for="group_id" class="form-label mb-0 text-nowrap fs-13 text-muted">Changer de groupe :</label>
                <select name="group_id" id="group_id" class="form-select form-select-sm border border-light-subtle shadow-none fw-medium" onchange="this.form.submit()" style="max-width: 220px;">
                    @foreach($groups as $g)
                        <option value="{{ encode_id($g->id) }}" {{ decode_id(request('group_id', encode_id($group->id ?? null))) == $g->id ? 'selected' : '' }}>{{ $g->name }}</option>
                    @endforeach
                </select>
            </form>
        </div>
        @endif
    </div>

    {{-- KPIs rapides --}}
    <div class="row g-2 g-md-3 mb-3 mb-md-4">
        <div class="col-4">
            <div class="card card-animate border-0 shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body p-2 p-sm-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="fs-10 fs-sm-11 text-uppercase text-muted fw-semibold tracking-wider">Effectif</span>
                        <div class="avatar-xs bg-primary-subtle text-primary rounded-circle d-none d-sm-flex align-items-center justify-content-center">
                            <i class="mdi mdi-account-group fs-14"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold mb-0 text-primary fs-15 fs-sm-20 fs-md-22">
                        <span class="counter-value" data-target="{{ $group->members_count }}">{{ $group->members_count }}</span>
                    </h4>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="card card-animate border-0 shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body p-2 p-sm-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="fs-10 fs-sm-11 text-uppercase text-muted fw-semibold tracking-wider">Présence</span>
                        <div class="avatar-xs bg-success-subtle text-success rounded-circle d-none d-sm-flex align-items-center justify-content-center">
                            <i class="mdi mdi-percent fs-14"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold mb-0 text-success fs-15 fs-sm-20 fs-md-22">
                        <span class="counter-value" data-target="{{ $averagePresence }}">{{ $averagePresence }}</span><small class="fs-11 fs-sm-13">%</small>
                    </h4>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="card card-animate border-0 shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body p-2 p-sm-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="fs-10 fs-sm-11 text-uppercase text-muted fw-semibold tracking-wider">Séances</span>
                        <div class="avatar-xs bg-info-subtle text-info rounded-circle d-none d-sm-flex align-items-center justify-content-center">
                            <i class="mdi mdi-calendar-check fs-14"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold mb-0 text-info fs-15 fs-sm-20 fs-md-22">
                        <span class="counter-value" data-target="{{ $totalSessions }}">{{ $totalSessions }}</span>
                    </h4>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtres Globaux --}}
    <div class="card border-0 shadow-sm mb-3 mb-md-4 rounded-3 overflow-hidden">
        <div class="card-header border-0 bg-transparent py-3 px-3 px-md-4" data-bs-toggle="collapse" data-bs-target="#groupFilterCollapse" style="cursor: pointer;">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <div class="avatar-xs bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center">
                        <i class="mdi mdi-filter-variant fs-16"></i>
                    </div>
                    <h6 class="mb-0 fw-bold fs-13 fs-md-14 text-body">Filtres d'analyse</h6>
                </div>
                <i class="mdi mdi-chevron-down fs-20 text-muted"></i>
            </div>
        </div>
        <div id="groupFilterCollapse" class="collapse show">
            <div class="card-body p-3 border-top border-light-subtle">
                <div class="row g-2 align-items-end">
                    <div class="col-12 col-md-4">
                        <label class="form-label mb-1 fs-12 text-muted fw-semibold text-uppercase tracking-wider">Type d'activité</label>
                        <select id="global-filter-type" class="form-select form-select-sm">
                            <option value="">Tous les types</option>
                            @foreach($activityTypes as $type)
                                <option value="{{ encode_id($type->id) }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-3">
                        <label class="form-label mb-1 fs-12 text-muted fw-semibold text-uppercase tracking-wider">Date début</label>
                        <input type="date" id="global-filter-date-from" class="form-control form-control-sm">
                    </div>
                    <div class="col-6 col-md-3">
                        <label class="form-label mb-1 fs-12 text-muted fw-semibold text-uppercase tracking-wider">Date fin</label>
                        <input type="date" id="global-filter-date-to" class="form-control form-control-sm">
                    </div>
                    <div class="col-12 col-md-2 text-end">
                        <button type="button" class="btn btn-sm btn-primary w-100 shadow-none" id="btn-apply-filters">
                            <i class="mdi mdi-magnify me-1"></i> Appliquer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Section Graphiques --}}
    <div class="row g-3 mb-4">
        {{-- Graphique 1 : Évolution des présences --}}
        <div class="col-12 col-xl-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header border-0 bg-transparent py-3 px-3 px-md-4 d-flex justify-content-between align-items-center border-bottom border-light-subtle">
                    <div class="d-flex align-items-center gap-2">
                        <i class="mdi mdi-trending-up text-primary fs-18"></i>
                        <h5 class="card-title mb-0 fw-bold fs-14 fs-md-15 text-body">Évolution des présences</h5>
                    </div>
                    <button type="button" class="btn btn-sm btn-soft-secondary px-2 py-1 rounded-circle" id="btn-export-group-chart1" title="Télécharger l'image">
                        <i class="mdi mdi-download fs-14"></i>
                    </button>
                </div>
                <div class="card-body p-2 p-md-3" data-simplebar style="overflow-x: auto;">
                    <div id="chart-evolution" style="min-height: 350px;"></div>
                </div>
            </div>
        </div>

        {{-- Graphique 2 : Palmarès d'assiduité --}}
        <div class="col-12 col-xl-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header border-0 bg-transparent py-3 px-3 px-md-4 d-flex justify-content-between align-items-center border-bottom border-light-subtle">
                    <div class="d-flex align-items-center gap-2">
                        <i class="mdi mdi-account-details text-danger fs-18"></i>
                        <h5 class="card-title mb-0 fw-bold fs-14 fs-md-15 text-body">Palmarès d'assiduité</h5>
                    </div>
                    <button type="button" class="btn btn-sm btn-soft-secondary px-2 py-1 rounded-circle" id="btn-export-group-chart2" title="Télécharger l'image">
                        <i class="mdi mdi-download fs-14"></i>
                    </button>
                </div>
                <div class="card-body p-2 p-md-3" data-simplebar style="max-height: 420px; overflow-x: auto;">
                    <div id="chart-participation" style="min-height: 350px;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Section Suivi détaillé des membres --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header border-0 bg-transparent py-3 px-3 px-md-4 border-bottom border-light-subtle">
            <div class="d-flex align-items-center gap-2">
                <i class="mdi mdi-alert-circle-outline text-warning fs-18"></i>
                <h5 class="card-title mb-0 fw-bold fs-14 fs-md-15 text-body">Suivi détaillé des membres</h5>
            </div>
        </div>
        
        <div class="card-body p-0">
            {{-- Vue Desktop : Tableau standard --}}
            <div class="table-responsive d-none d-md-block">
                <table class="table table-nowrap align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="ps-4">Membre</th>
                            <th scope="col">Taux d'assiduité</th>
                            <th scope="col" class="text-center">Présences</th>
                            <th scope="col" class="text-center">Absences totales</th>
                            <th scope="col" class="text-center">Absences consécutives récentes</th>
                            <th scope="col" class="pe-4 text-end">Statut d'alerte</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($members as $member)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="flex-shrink-0">
                                        @if($member->photo)
                                            <img src="{{ asset('storage/' . $member->photo) }}" alt="" class="avatar-xs rounded-circle object-cover">
                                        @else
                                            <div class="avatar-xs">
                                                <span class="avatar-title rounded-circle bg-primary-subtle text-primary fw-bold">
                                                    {{ strtoupper(substr($member->first_name, 0, 1)) }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1">
                                        <h5 class="fs-13 mb-0 fw-bold text-body">{{ mb_strtoupper($member->name) }} {{ $member->first_name }}</h5>
                                        @if($member->phone)
                                            <span class="text-muted fs-12"><i class="mdi mdi-phone-outline me-1"></i>{{ $member->phone }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2" style="max-width: 180px;">
                                    <div class="progress flex-grow-1 shadow-none" style="height: 6px;">
                                        <div class="progress-bar {{ $member->attendance_rate >= 70 ? 'bg-success' : ($member->attendance_rate >= 40 ? 'bg-warning' : 'bg-danger') }} rounded-pill" 
                                             role="progressbar" 
                                             style="width: {{ $member->attendance_rate }}%;" 
                                             aria-valuenow="{{ $member->attendance_rate }}" 
                                             aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <span class="fs-12 fw-bold {{ $member->attendance_rate >= 70 ? 'text-success' : ($member->attendance_rate >= 40 ? 'text-warning' : 'text-danger') }}">{{ $member->attendance_rate }}%</span>
                                </div>
                            </td>
                            <td class="text-center">{{ $member->total_presents }}</td>
                            <td class="text-center">{{ $member->total_absents }}</td>
                            <td class="text-center">
                                @if($member->consecutive_absences >= 3)
                                    <span class="text-danger fw-bold"><i class="mdi mdi-alert fs-14 me-1"></i> {{ $member->consecutive_absences }} consécutives</span>
                                @elseif($member->consecutive_absences > 0)
                                    <span class="text-warning">{{ $member->consecutive_absences }} consécutives</span>
                                @else
                                    <span class="text-success">0 (Présent à la dernière)</span>
                                @endif
                            </td>
                            <td class="pe-4 text-end">
                                @if($member->consecutive_absences >= 3)
                                    <span class="badge bg-danger-subtle text-danger">À Risque (Absence prolongée)</span>
                                @elseif($member->attendance_rate < 40)
                                    <span class="badge bg-warning-subtle text-warning">Participation faible</span>
                                @else
                                    <span class="badge bg-success-subtle text-success">Actif régulier</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Vue Mobile : Liste sous forme de cartes denses --}}
            <div class="d-block d-md-none p-2">
                @foreach($members as $member)
                <div class="card border border-light-subtle rounded-3 mb-2 p-3 shadow-none">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="d-flex align-items-center gap-2">
                            @if($member->photo)
                                <img src="{{ asset('storage/' . $member->photo) }}" alt="" class="avatar-xs rounded-circle object-cover">
                            @else
                                <div class="avatar-xs">
                                    <span class="avatar-title rounded-circle bg-primary-subtle text-primary fw-bold">
                                        {{ strtoupper(substr($member->first_name, 0, 1)) }}
                                    </span>
                                </div>
                            @endif
                            <div>
                                <h6 class="fs-13 mb-0 fw-bold text-body">{{ mb_strtoupper($member->name) }} {{ $member->first_name }}</h6>
                                @if($member->phone)
                                    <a href="tel:{{ $member->phone }}" class="text-muted fs-11"><i class="mdi mdi-phone-outline me-1"></i>{{ $member->phone }}</a>
                                @endif
                            </div>
                        </div>
                        <div>
                            @if($member->consecutive_absences >= 3)
                                <span class="badge bg-danger text-white rounded-pill px-2 py-1 fs-10"><i class="mdi mdi-alert me-1"></i>À Risque</span>
                            @elseif($member->attendance_rate < 40)
                                <span class="badge bg-warning text-white rounded-pill px-2 py-1 fs-10">Faible</span>
                            @else
                                <span class="badge bg-success-subtle text-success rounded-pill px-2 py-1 fs-10">Régulier</span>
                            @endif
                        </div>
                    </div>

                    {{-- Barre de progression du taux d'assiduité --}}
                    <div class="mb-2">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fs-11 text-muted">Assiduité</span>
                            <span class="fs-11 fw-bold {{ $member->attendance_rate >= 70 ? 'text-success' : ($member->attendance_rate >= 40 ? 'text-warning' : 'text-danger') }}">{{ $member->attendance_rate }}%</span>
                        </div>
                        <div class="progress shadow-none" style="height: 5px;">
                            <div class="progress-bar {{ $member->attendance_rate >= 70 ? 'bg-success' : ($member->attendance_rate >= 40 ? 'bg-warning' : 'bg-danger') }} rounded-pill" 
                                 role="progressbar" style="width: {{ $member->attendance_rate }}%;"></div>
                        </div>
                    </div>

                    {{-- Badges statistiques en bas de carte --}}
                    <div class="d-flex justify-content-between align-items-center pt-2 border-top border-light-subtle fs-11">
                        <div>
                            <span class="text-muted me-1">Présences:</span>
                            <span class="badge bg-success-subtle text-success px-2">{{ $member->total_presents }}</span>
                        </div>
                        <div>
                            <span class="text-muted me-1">Absences:</span>
                            <span class="badge bg-danger-subtle text-danger px-2">{{ $member->total_absents }}</span>
                        </div>
                        <div>
                            @if($member->consecutive_absences >= 3)
                                <span class="text-danger fw-bold"><i class="mdi mdi-alert me-1"></i>{{ $member->consecutive_absences }} conséc.</span>
                            @else
                                <span class="text-muted">{{ $member->consecutive_absences }} conséc.</span>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    const groupId = '{{ encode_id($group->id) }}';
    const groupColor = '{{ $group->color ?? "#405189" }}';

    function formatDateFr(dateStr) {
        if (!dateStr) return '';
        const parts = dateStr.split('-');
        if (parts.length === 3) {
            return parts[2] + '/' + parts[1] + '/' + parts[0];
        }
        return dateStr;
    }

    function getFilterSubtitle() {
        const typeSelect = document.getElementById('global-filter-type');
        const typeText = typeSelect && typeSelect.selectedIndex > 0 
            ? typeSelect.options[typeSelect.selectedIndex].text 
            : 'Tous les types';
            
        const dateFrom = document.getElementById('global-filter-date-from').value;
        const dateTo = document.getElementById('global-filter-date-to').value;
        
        let dateText = 'Toutes les dates';
        if (dateFrom && dateTo) {
            dateText = 'Du ' + formatDateFr(dateFrom) + ' au ' + formatDateFr(dateTo);
        } else if (dateFrom) {
            dateText = 'Depuis le ' + formatDateFr(dateFrom);
        } else if (dateTo) {
            dateText = "Jusqu'au " + formatDateFr(dateTo);
        }

        return 'Groupe: {{ $group->name }}  •  Type: ' + typeText + '  •  Période: ' + dateText;
    }

    // Graphique 1 : Evolution
    let chart1;
    function loadChartEvolution() {
        const typeId = document.getElementById('global-filter-type').value;
        const dateFrom = document.getElementById('global-filter-date-from').value;
        const dateTo = document.getElementById('global-filter-date-to').value;
        const url = new URL('{{ route("admin.statistics.group.chart.evolution", [], false) }}', window.location.origin);
        url.searchParams.set('group_id', groupId);
        if (typeId) url.searchParams.set('activity_type_id', typeId);
        if (dateFrom) url.searchParams.set('date_from', dateFrom);
        if (dateTo) url.searchParams.set('date_to', dateTo);

        fetch(url)
            .then(r => r.json())
            .then(data => {
                const options = {
                    chart: { type: 'line', height: 350, toolbar: { show: false } },
                    title: {
                        text: 'Évolution des présences',
                        align: 'center',
                        style: { fontSize: '15px', fontWeight: 'bold', color: 'var(--vz-body-color)' }
                    },
                    subtitle: {
                        text: getFilterSubtitle(),
                        align: 'center',
                        style: { fontSize: '11px', color: '#878a99' }
                    },
                    series: [{ name: 'Présents', data: data.series.map(d => d.count) }],
                    labels: data.series.map(d => d.date),
                    xaxis: { title: { text: 'Séances' } },
                    yaxis: { 
                        title: { text: 'Présences' },
                        labels: { formatter: function(val) { return Math.floor(val); } },
                        min: 0
                    },
                    colors: [groupColor],
                    stroke: { curve: 'smooth', width: 3 },
                    markers: { size: 5, colors: [groupColor], strokeColors: '#fff', strokeWidth: 2, hover: { size: 7 } },
                    tooltip: {
                        y: {
                            formatter: function(val, opt) {
                                return val + " présents - " + data.series[opt.dataPointIndex].title;
                            }
                        }
                    }
                };

                const container = document.querySelector('#chart-evolution');
                const parentWidth = container.parentElement.clientWidth || 800;
                const itemsToDisplay = window.innerWidth < 768 ? 5 : 15;
                const itemWidth = parentWidth / itemsToDisplay;
                container.style.minWidth = Math.max(parentWidth, data.series.length * itemWidth) + 'px';

                if (chart1) chart1.destroy();
                chart1 = new ApexCharts(container, options);
                chart1.render();
            });
    }

    // Graphique 2 : Participation
    let chart2;
    function loadChartParticipation() {
        const typeId = document.getElementById('global-filter-type').value;
        const dateFrom = document.getElementById('global-filter-date-from').value;
        const dateTo = document.getElementById('global-filter-date-to').value;
        const url = new URL('{{ route("admin.statistics.group.chart.participation", [], false) }}', window.location.origin);
        url.searchParams.set('group_id', groupId);
        if (typeId) url.searchParams.set('activity_type_id', typeId);
        if (dateFrom) url.searchParams.set('date_from', dateFrom);
        if (dateTo) url.searchParams.set('date_to', dateTo);

        fetch(url)
            .then(r => r.json())
            .then(result => {
                const data = result.data;
                const totalSessions = result.total_sessions;

                const options = {
                    chart: { type: 'bar', height: Math.max(350, data.length * 35), toolbar: { show: false } },
                    title: {
                        text: 'Palmarès d\'assiduité',
                        align: 'center',
                        style: { fontSize: '15px', fontWeight: 'bold', color: 'var(--vz-body-color)' }
                    },
                    subtitle: {
                        text: getFilterSubtitle() + ' (' + totalSessions + ' séances)',
                        align: 'center',
                        style: { fontSize: '11px', color: '#878a99' }
                    },
                    series: [{ name: 'Présences', data: data.map(d => d.count) }],
                    labels: data.map(d => d.full_name),
                    plotOptions: {
                        bar: {
                            horizontal: true,
                            barHeight: '40%',
                            borderRadius: 0,
                            dataLabels: { position: 'top' },
                        }
                    },
                    colors: [groupColor],
                    dataLabels: {
                        enabled: true,
                        textAnchor: 'start',
                        offsetX: 10,
                        style: { fontSize: '11px', fontWeight: 'bold', colors: ['var(--vz-body-color)'] },
                    },
                    xaxis: {
                        title: { text: 'Présences (sur ' + totalSessions + ' séances)' },
                        max: totalSessions > 0 ? totalSessions + 1 : 10,
                        min: 0,
                        labels: { formatter: function(val) { return Math.floor(val); } }
                    },
                    yaxis: { labels: { style: { fontSize: '11px' } } },
                };

                const container = document.querySelector('#chart-participation');
                const parentWidth = container.parentElement.clientWidth || 800;
                const itemsToDisplay = window.innerWidth < 768 ? 5 : 15;
                const itemWidth = parentWidth / itemsToDisplay;
                container.style.minWidth = Math.max(parentWidth, data.length * itemWidth) + 'px';

                if (chart2) chart2.destroy();
                chart2 = new ApexCharts(container, options);
                chart2.render();
            });
    }

    function loadAllFilteredCharts() {
        loadChartEvolution();
        loadChartParticipation();
    }
    
    document.getElementById('btn-apply-filters').addEventListener('click', loadAllFilteredCharts);

    loadAllFilteredCharts();

    // Export des graphiques en image
    function exportChart(chart, filename) {
        if (chart) {
            chart.dataURI().then(({ imgURI }) => {
                let a = document.createElement('a');
                a.href = imgURI;
                a.download = filename + '.png';
                a.click();
            });
        }
    }

    document.getElementById('btn-export-group-chart1')?.addEventListener('click', () => exportChart(chart1, 'evolution_presences_groupe'));
    document.getElementById('btn-export-group-chart2')?.addEventListener('click', () => exportChart(chart2, 'palmares_assiduite_groupe'));
});
</script>
@endpush
