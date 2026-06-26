@extends('layouts.app')

@section('title', 'Statistiques de mon groupe')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Statistiques du groupe : <span class="text-primary">{{ $group->name }}</span></h4>

            @if($isGlobal)
            <div class="page-title-right">
                <form action="{{ route('admin.statistics.group.index') }}" method="GET" class="d-flex align-items-center gap-2">
                    <label for="group_id" class="form-label mb-0 text-nowrap">Changer de groupe :</label>
                    <select name="group_id" id="group_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        @foreach($groups as $g)
                            <option value="{{ $g->id }}" {{ $g->id == $group->id ? 'selected' : '' }}>{{ $g->name }}</option>
                        @endforeach
                    </select>
                </form>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- KPIs rapides --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card card-animate">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="text-uppercase fw-medium text-muted mb-2">Effectif du groupe</p>
                        <h4 class="fs-22 fw-semibold ff-secondary mb-0">
                            <span class="counter-value" data-target="{{ $group->members_count }}">{{ $group->members_count }}</span>
                        </h4>
                    </div>
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-primary-subtle text-primary rounded fs-3">
                            <i class="mdi mdi-account-group"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-animate">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="text-uppercase fw-medium text-muted mb-2">Taux de présence moyen</p>
                        <h4 class="fs-22 fw-semibold ff-secondary mb-0">
                            <span class="counter-value" data-target="{{ $averagePresence }}">{{ $averagePresence }}</span>%
                        </h4>
                    </div>
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-success-subtle text-success rounded fs-3">
                            <i class="mdi mdi-percent"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-animate">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="text-uppercase fw-medium text-muted mb-2">Séances évaluées</p>
                        <h4 class="fs-22 fw-semibold ff-secondary mb-0">
                            <span class="counter-value" data-target="{{ $totalSessions }}">{{ $totalSessions }}</span>
                        </h4>
                    </div>
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-info-subtle text-info rounded fs-3">
                            <i class="mdi mdi-calendar-check"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Filtres Globaux --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="card mb-0 shadow-sm border-0">
            <div class="card-body bg-light-subtle rounded py-3">
                <div class="d-flex flex-column flex-md-row gap-3 align-items-md-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <h6 class="mb-0 fw-bold text-uppercase fs-12 text-muted me-md-3">
                            <i class="mdi mdi-filter-variant text-primary me-1 fs-15 align-middle"></i> Filtres d'analyse
                        </h6>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <div class="d-flex align-items-center gap-2">
                            <label class="form-label mb-0 fs-13 text-nowrap">Type :</label>
                            <select id="global-filter-type" class="form-select form-select-sm" style="min-width: 150px;">
                                <option value="">Tous les types</option>
                                @foreach($activityTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <label class="form-label mb-0 fs-13 text-nowrap">Du :</label>
                            <input type="date" id="global-filter-date-from" class="form-control form-control-sm">
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <label class="form-label mb-0 fs-13 text-nowrap">Au :</label>
                            <input type="date" id="global-filter-date-to" class="form-control form-control-sm">
                        </div>
                        <button type="button" class="btn btn-sm btn-primary" id="btn-apply-filters">Appliquer</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Section Graphiques --}}
<div class="row">
    {{-- Graphique 1 : Évolution des présences --}}
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header d-flex flex-column flex-md-row align-items-start align-items-md-center gap-2">
                <h5 class="card-title mb-0 flex-grow-1">
                    <i class="mdi mdi-trending-up me-1 text-primary"></i>
                    Évolution des présences
                </h5>
            </div>
            <div class="card-body">
                <div id="chart-evolution" style="min-height: 400px;"></div>
            </div>
        </div>
    </div>

    {{-- Graphique 2 : Participation interne au groupe --}}
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header d-flex flex-column flex-md-row align-items-start align-items-md-center gap-2">
                <h5 class="card-title mb-0 flex-grow-1">
                    <i class="mdi mdi-account-details me-1 text-danger"></i>
                    Palmarès d'assiduité
                </h5>
            </div>
            <div class="card-body" data-simplebar style="height: 400px;">
                <div id="chart-participation" style="min-height: 400px;"></div>
            </div>
        </div>
    </div>
</div>

{{-- Section Tableau de suivi --}}
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header border-0">
                <h5 class="card-title mb-0">
                    <i class="mdi mdi-alert-circle-outline me-1 text-warning"></i>
                    Suivi détaillé des membres
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-nowrap align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Membre</th>
                                <th scope="col">Taux d'assiduité</th>
                                <th scope="col">Présences</th>
                                <th scope="col">Absences totales</th>
                                <th scope="col">Absences consécutives récentes</th>
                                <th scope="col">Statut d'alerte</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($members as $member)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 me-2">
                                            @if($member->photo)
                                                <img src="{{ asset('storage/' . $member->photo) }}" alt="" class="avatar-xs rounded-circle">
                                            @else
                                                <div class="avatar-xs">
                                                    <span class="avatar-title rounded-circle bg-primary-subtle text-primary">
                                                        {{ strtoupper(substr($member->first_name, 0, 1)) }}
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="fs-14 mb-0">{{ mb_strtoupper($member->name) }} {{ $member->first_name }}</h5>
                                            @if($member->phone)
                                                <span class="text-muted fs-12">{{ $member->phone }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="progress flex-grow-1" style="height: 6px;">
                                            <div class="progress-bar {{ $member->attendance_rate >= 70 ? 'bg-success' : ($member->attendance_rate >= 40 ? 'bg-warning' : 'bg-danger') }}" 
                                                 role="progressbar" 
                                                 style="width: {{ $member->attendance_rate }}%;" 
                                                 aria-valuenow="{{ $member->attendance_rate }}" 
                                                 aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <span class="ms-2 fs-12 fw-medium">{{ $member->attendance_rate }}%</span>
                                    </div>
                                </td>
                                <td>{{ $member->total_presents }}</td>
                                <td>{{ $member->total_absents }}</td>
                                <td>
                                    @if($member->consecutive_absences >= 3)
                                        <span class="text-danger fw-bold"><i class="mdi mdi-alert fs-14 me-1"></i> {{ $member->consecutive_absences }} consécutives</span>
                                    @elseif($member->consecutive_absences > 0)
                                        <span class="text-warning">{{ $member->consecutive_absences }} consécutives</span>
                                    @else
                                        <span class="text-success">0 (Présent à la dernière)</span>
                                    @endif
                                </td>
                                <td>
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
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    const groupId = {{ $group->id }};
    const groupColor = '{{ $group->color ?? "#405189" }}';

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
                    chart: { type: 'line', height: 400, toolbar: { show: false } },
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
                if (chart1) chart1.destroy();
                chart1 = new ApexCharts(document.querySelector('#chart-evolution'), options);
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
                    chart: { type: 'bar', height: Math.max(400, data.length * 35), toolbar: { show: false } },
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
                        style: { fontSize: '11px', fontWeight: 'bold', colors: ['#304758'] },
                    },
                    xaxis: {
                        title: { text: 'Présences (sur ' + totalSessions + ' séances)' },
                        max: totalSessions > 0 ? totalSessions + 1 : 10,
                        min: 0,
                        labels: { formatter: function(val) { return Math.floor(val); } }
                    },
                    yaxis: { labels: { style: { fontSize: '11px' } } },
                };
                if (chart2) chart2.destroy();
                chart2 = new ApexCharts(document.querySelector('#chart-participation'), options);
                chart2.render();
            });
    }

    function loadAllFilteredCharts() {
        loadChartEvolution();
        loadChartParticipation();
    }
    
    document.getElementById('btn-apply-filters').addEventListener('click', loadAllFilteredCharts);

    loadAllFilteredCharts();
});
</script>
@endpush
