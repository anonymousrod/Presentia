@extends('layouts.app')

@section('title', 'Statistiques des Présences')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Statistiques des Présences</h4>
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
                        <p class="text-uppercase fw-medium text-muted mb-2">Jeunes inscrits</p>
                        <h4 class="fs-22 fw-semibold ff-secondary mb-0">
                            <span class="counter-value" data-target="{{ $totalMembers }}">{{ $totalMembers }}</span>
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
                        <p class="text-uppercase fw-medium text-muted mb-2">Groupes</p>
                        <h4 class="fs-22 fw-semibold ff-secondary mb-0">
                            <span class="counter-value" data-target="{{ $totalGroups }}">{{ $totalGroups }}</span>
                        </h4>
                    </div>
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-success-subtle text-success rounded fs-3">
                            <i class="mdi mdi-account-multiple-outline"></i>
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
                        <p class="text-uppercase fw-medium text-muted mb-2">Activités publiées</p>
                        <h4 class="fs-22 fw-semibold ff-secondary mb-0">
                            <span class="counter-value" data-target="{{ $totalActivities }}">{{ $totalActivities }}</span>
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

{{-- Graphique 1 : Répartition des jeunes par groupe --}}
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <h5 class="card-title mb-0 flex-grow-1">
                    <i class="mdi mdi-chart-bar me-1 text-primary"></i>
                    Répartition des jeunes par groupe
                </h5>
            </div>
            <div class="card-body" data-simplebar>
                <div id="chart-members-per-group" style="min-height: 350px;"></div>
            </div>
        </div>
    </div>
</div>

{{-- Graphique 2 : Évolution des présences par type d'activité --}}
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex flex-column flex-md-row align-items-start align-items-md-center gap-2">
                <h5 class="card-title mb-0 flex-grow-1">
                    <i class="mdi mdi-chart-line me-1 text-info"></i>
                    Évolution des présences par séance
                </h5>
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <label class="form-label mb-0 text-nowrap fs-13">Type :</label>
                        <select id="filter-evolution-type" class="form-select form-select-sm w-auto">
                            <option value="">Tous les types</option>
                            @foreach($activityTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <label class="form-label mb-0 text-nowrap fs-13">Année :</label>
                        <select id="filter-evolution-year" class="form-select form-select-sm w-auto">
                            <option value="">Toutes</option>
                            @foreach($years as $y)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-body" data-simplebar>
                <div id="chart-presence-evolution" style="min-height: 380px;"></div>
            </div>
        </div>
    </div>
</div>

{{-- Graphiques 3 & 4 côte à côte --}}
<div class="row">
    {{-- Graphique 3 : Taux de présence par groupe --}}
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-3">
                    <i class="mdi mdi-chart-bar-stacked me-1 text-warning"></i>
                    Présence par groupe <span class="fs-12 text-muted fw-normal d-block d-sm-inline mt-1 mt-sm-0">(membres présents / effectif)</span>
                </h5>
                <div class="row g-2">
                    <div class="col-md-4">
                        <label class="form-label fs-12 mb-1">Type d'activité</label>
                        <select id="filter-group-type" class="form-select form-select-sm">
                            <option value="">Toutes</option>
                            @foreach($activityTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fs-12 mb-1">Date début</label>
                        <input type="date" id="filter-group-date-from" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fs-12 mb-1">Date fin</label>
                        <input type="date" id="filter-group-date-to" class="form-control form-control-sm">
                    </div>
                </div>
            </div>
            <div class="card-body" data-simplebar style="height: 450px;">
                <div id="chart-presence-by-group" style="min-height: 400px;"></div>
            </div>
        </div>
    </div>

    {{-- Graphique 4 : Participation individuelle --}}
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header d-flex flex-column flex-md-row align-items-start align-items-md-center gap-2">
                <h5 class="card-title mb-0 flex-grow-1">
                    <i class="mdi mdi-account-details me-1 text-danger"></i>
                    Participation individuelle <span id="chart4-subtitle" class="fs-12 text-muted fw-normal d-block d-sm-inline mt-1 mt-sm-0"></span>
                </h5>
                <div class="d-flex flex-wrap gap-2 align-items-center mt-2 mt-md-0">
                    <div class="d-flex align-items-center gap-2">
                        <label class="form-label mb-0 text-nowrap fs-12">Type :</label>
                        <select id="filter-individual-type" class="form-select form-select-sm w-auto">
                            <option value="">Tous</option>
                            @foreach($activityTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <label class="form-label mb-0 text-nowrap fs-12">Année :</label>
                        <select id="filter-individual-year" class="form-select form-select-sm w-auto">
                            <option value="">Toutes</option>
                            @foreach($years as $y)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-body" data-simplebar style="height: 450px;">
                <div id="chart-individual-participation" style="min-height: 400px;"></div>
            </div>
        </div>
    </div>
</div>

{{-- Graphique 5 : Affluence par activité --}}
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex flex-column flex-md-row align-items-start align-items-md-center gap-2">
                <h5 class="card-title mb-0 flex-grow-1">
                    <i class="mdi mdi-chart-areaspline me-1 text-success"></i>
                    Affluence par activité
                </h5>
                <div class="d-flex flex-wrap gap-2 align-items-center mt-2 mt-md-0">
                    <div class="d-flex align-items-center gap-2">
                        <label class="form-label mb-0 text-nowrap fs-13">Type :</label>
                        <select id="filter-affluence-type" class="form-select form-select-sm w-auto">
                            <option value="">Tous les types</option>
                            @foreach($activityTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <label class="form-label mb-0 text-nowrap fs-13">Année :</label>
                        <select id="filter-affluence-year" class="form-select form-select-sm w-auto">
                            <option value="">Toutes</option>
                            @foreach($years as $y)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-body" data-simplebar>
                <div id="chart-affluence" style="min-height: 380px;"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- ApexCharts (already included in Velzon template, but ensure it's loaded) --}}
<script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {

    // ============================================================
    // Palette de couleurs par groupe (cohérente avec les images)
    // ============================================================
    const GROUP_COLORS = {
        'Béthel':   '#1B2A4A',  // navy foncé
        'Cana':     '#3B7DD8',  // bleu
        'Éden':     '#A8C8E8',  // bleu clair
        'Galilée':  '#8B8B8B',  // gris
        'Salem':    '#BABABA',  // gris clair
        'Shalom':   '#D2691E',  // marron/orange
        'Siloé':    '#E8842C',  // orange vif
        'Sinaï':    '#B0D4F1',  // bleu pâle
    };

    const DEFAULT_COLORS = [
        '#1B2A4A', '#3B7DD8', '#A8C8E8', '#8B8B8B',
        '#BABABA', '#D2691E', '#E8842C', '#B0D4F1',
        '#4CAF50', '#9C27B0', '#FF5722', '#795548'
    ];

    function getGroupColor(name, index, colorFromDb = null) {
        if (colorFromDb) return colorFromDb;
        return GROUP_COLORS[name] || DEFAULT_COLORS[index % DEFAULT_COLORS.length];
    }

    // ============================================================
    // Graphique 1 : Répartition des jeunes par groupe
    // ============================================================
    let chart1;
    function loadChart1() {
        fetch('{{ route("admin.statistics.chart.members-per-group") }}')
            .then(r => r.json())
            .then(data => {
                const options = {
                    chart: {
                        type: 'bar',
                        height: 350,
                        toolbar: { show: true },
                    },
                    plotOptions: {
                        bar: {
                            columnWidth: '55%',
                            distributed: true,
                            borderRadius: 4,
                            dataLabels: { position: 'top' },
                        }
                    },
                    colors: data.map((d, i) => getGroupColor(d.name, i, d.color)),
                    dataLabels: {
                        enabled: true,
                        offsetY: -20,
                        style: { fontSize: '14px', fontWeight: 'bold', colors: ['#304758'] },
                    },
                    series: [{
                        name: 'Membres',
                        data: data.map(d => d.count),
                    }],
                    xaxis: {
                        categories: data.map(d => d.name),
                        labels: { style: { fontSize: '12px', fontWeight: 600 } },
                    },
                    yaxis: {
                        title: { text: 'Nombre de membres' },
                    },
                    title: {
                        text: 'Répartition des jeunes par groupe',
                        align: 'center',
                        style: { fontSize: '16px', fontWeight: 'bold', color: '#1B2A4A' },
                    },
                    legend: { show: false },
                    tooltip: {
                        y: { formatter: val => val + ' membre(s)' }
                    },
                };

                const container = document.querySelector('#chart-members-per-group');
                container.style.minWidth = Math.max(100, data.length * 60) + 'px';

                if (chart1) chart1.destroy();
                chart1 = new ApexCharts(container, options);
                chart1.render();
            });
    }

    // ============================================================
    // Graphique 2 : Évolution des présences (ligne temporelle)
    // ============================================================
    let chart2;
    function loadChart2() {
        const typeId = document.getElementById('filter-evolution-type').value;
        const year = document.getElementById('filter-evolution-year').value;
        const url = new URL('{{ route("admin.statistics.chart.presence-evolution") }}');
        if (typeId) url.searchParams.set('activity_type_id', typeId);
        if (year) url.searchParams.set('year', year);

        fetch(url)
            .then(r => r.json())
            .then(result => {
                const series = result.series;
                const avg = result.average;

                const options = {
                    chart: {
                        type: 'line',
                        height: 380,
                        toolbar: { show: true },
                        zoom: { enabled: false },
                    },
                    stroke: {
                        width: [3, 2],
                        curve: 'straight',
                        dashArray: [0, 8],
                    },
                    colors: ['#1B2A4A', '#E8842C'],
                    series: [
                        {
                            name: 'Présences',
                            type: 'line',
                            data: series.map(d => d.count),
                        },
                        {
                            name: 'Moyenne (' + avg + ')',
                            type: 'line',
                            data: series.map(() => avg),
                        }
                    ],
                    markers: {
                        size: [6, 0],
                        colors: ['#1B2A4A'],
                        strokeColors: '#fff',
                        strokeWidth: 2,
                        hover: { sizeOffset: 3 },
                    },
                    dataLabels: {
                        enabled: true,
                        enabledOnSeries: [0],
                        offsetY: -10,
                        style: { fontSize: '13px', fontWeight: 'bold', colors: ['#1B2A4A'] },
                    },
                    xaxis: {
                        categories: series.map(d => d.date),
                        labels: { style: { fontSize: '12px' } },
                    },
                    yaxis: {
                        title: { text: 'Présences totales' },
                        min: 0,
                    },
                    title: {
                        text: 'Évolution des présences par séance (' + result.total_sessions + ' séances)',
                        align: 'center',
                        style: { fontSize: '15px', fontWeight: 'bold', color: '#1B2A4A' },
                    },
                    tooltip: {
                        shared: true,
                        custom: function({ series, seriesIndex, dataPointIndex, w }) {
                            const item = result.series[dataPointIndex];
                            return '<div class="p-2">' +
                                '<strong>' + item.title + '</strong><br>' +
                                '<span class="text-muted">' + item.full_date + '</span><br>' +
                                '<span class="fw-bold text-primary">' + item.count + ' présent(s)</span>' +
                                '</div>';
                        }
                    },
                    annotations: {
                        yaxis: [{
                            y: avg,
                            borderColor: '#E8842C',
                            label: {
                                text: 'moyenne ' + avg,
                                style: { color: '#E8842C', background: 'transparent', fontSize: '12px' },
                                position: 'right',
                            }
                        }]
                    },
                    legend: {
                        show: true,
                        position: 'top',
                    },
                };

                const container = document.querySelector('#chart-presence-evolution');
                const parentWidth = container.parentElement.clientWidth || 800;
                const itemWidth = parentWidth / 15;
                container.style.minWidth = Math.max(parentWidth, series.length * itemWidth) + 'px';

                if (chart2) chart2.destroy();
                chart2 = new ApexCharts(container, options);
                chart2.render();
            });
    }

    document.getElementById('filter-evolution-type').addEventListener('change', loadChart2);
    document.getElementById('filter-evolution-year').addEventListener('change', loadChart2);

    // ============================================================
    // Graphique 3 : Présence par groupe (barres horizontales, nombre)
    // ============================================================
    let chart3;
    function loadChart3() {
        const typeId = document.getElementById('filter-group-type').value;
        const dateFrom = document.getElementById('filter-group-date-from').value;
        const dateTo = document.getElementById('filter-group-date-to').value;

        const url = new URL('{{ route("admin.statistics.chart.presence-by-group") }}');
        if (typeId) url.searchParams.set('activity_type_id', typeId);
        if (dateFrom) url.searchParams.set('date_from', dateFrom);
        if (dateTo) url.searchParams.set('date_to', dateTo);

        fetch(url)
            .then(r => r.json())
            .then(data => {
                const options = {
                    chart: {
                        type: 'bar',
                        height: Math.max(300, data.length * 50),
                        toolbar: { show: true },
                    },
                    plotOptions: {
                        bar: {
                            horizontal: true,
                            barHeight: '65%',
                            distributed: true,
                            borderRadius: 4,
                            dataLabels: { position: 'top' },
                        }
                    },
                    colors: data.map((d, i) => getGroupColor(d.name, i, d.color)),
                    dataLabels: {
                        enabled: true,
                        textAnchor: 'start',
                        formatter: function(val, opt) {
                            const item = data[opt.dataPointIndex];
                            return item.label;
                        },
                        offsetX: 10,
                        style: { fontSize: '13px', fontWeight: 'bold', colors: ['#304758'] },
                    },
                    series: [{
                        name: 'Présents',
                        data: data.map(d => d.presents),
                    }],
                    labels: data.map(d => d.name),
                    xaxis: {
                        title: { text: 'Nombre de présents' },
                        max: data.length > 0 ? Math.max(...data.map(d => d.effectif)) + 2 : 10,
                        min: 0,
                    },
                    yaxis: {
                        labels: { style: { fontSize: '13px', fontWeight: 600 } },
                    },
                    legend: { show: false },
                    tooltip: {
                        y: {
                            formatter: function(val, opt) {
                                const item = data[opt.dataPointIndex];
                                return item.presents + ' présents sur ' + item.effectif + ' membres';
                            }
                        }
                    },
                };

                if (chart3) chart3.destroy();
                chart3 = new ApexCharts(document.querySelector('#chart-presence-by-group'), options);
                chart3.render();
            });
    }

    document.getElementById('filter-group-type').addEventListener('change', loadChart3);
    document.getElementById('filter-group-date-from').addEventListener('change', loadChart3);
    document.getElementById('filter-group-date-to').addEventListener('change', loadChart3);

    // ============================================================
    // Graphique 4 : Participation individuelle (barres horizontales)
    // ============================================================
    let chart4;
    function loadChart4() {
        const typeId = document.getElementById('filter-individual-type').value;
        const year = document.getElementById('filter-individual-year').value;
        const url = new URL('{{ route("admin.statistics.chart.individual-participation") }}');
        if (typeId) url.searchParams.set('activity_type_id', typeId);
        if (year) url.searchParams.set('year', year);

        fetch(url)
            .then(r => r.json())
            .then(result => {
                const data = result.data;
                const totalSessions = result.total_sessions;

                document.getElementById('chart4-subtitle').innerText = '(sur ' + totalSessions + ' séances)';

                // Afficher toutes les données et ajuster la hauteur dynamiquement
                const displayData = data;

                const options = {
                    chart: {
                        type: 'bar',
                        height: Math.max(400, displayData.length * 30),
                        toolbar: { show: true },
                    },
                    plotOptions: {
                        bar: {
                            horizontal: true,
                            barHeight: '40%',
                            distributed: true,
                            borderRadius: 0,
                            dataLabels: { position: 'top' },
                        }
                    },
                    stroke: {
                        show: false,
                        width: 0
                    },
                    colors: displayData.map((d, i) => getGroupColor(d.group_name, i, d.group_color)),
                    dataLabels: {
                        enabled: true,
                        textAnchor: 'start',
                        offsetX: 10,
                        style: { fontSize: '11px', fontWeight: 'bold', colors: ['#304758'] },
                    },
                    series: [{
                        name: 'Présences',
                        data: displayData.map(d => d.count),
                    }],
                    labels: displayData.map(d => d.full_name + ' (' + d.group_name.replace('Groupe ', '') + ')'),
                    xaxis: {
                        title: { text: 'Nombre de présences (sur ' + totalSessions + ' séances)' },
                        max: totalSessions > 0 ? totalSessions + 1 : 10,
                        min: 0,
                        labels: {
                            formatter: function(val) { return Math.floor(val); }
                        }
                    },
                    yaxis: {
                        labels: {
                            style: { fontSize: '11px' },
                            maxWidth: 200,
                        },
                    },
                    legend: { show: false },
                    tooltip: {
                        followCursor: true,
                        intersect: false,
                        y: {
                            formatter: function(val, opt) {
                                const item = displayData[opt.dataPointIndex];
                                return val + ' séance(s) — Groupe: ' + item.group_name;
                            }
                        }
                    },
                };

                if (chart4) chart4.destroy();
                chart4 = new ApexCharts(document.querySelector('#chart-individual-participation'), options);
                chart4.render();
            });
    }

    document.getElementById('filter-individual-type').addEventListener('change', loadChart4);
    document.getElementById('filter-individual-year').addEventListener('change', loadChart4);

    // ============================================================
    // Graphique 5 : Affluence par activité (barres empilées)
    // ============================================================
    let chart5;
    function loadChart5() {
        const typeId = document.getElementById('filter-affluence-type').value;
        const year = document.getElementById('filter-affluence-year').value;
        const url = new URL('{{ route("admin.statistics.chart.affluence-by-activity") }}');
        if (typeId) url.searchParams.set('activity_type_id', typeId);
        if (year) url.searchParams.set('year', year);

        fetch(url)
            .then(r => r.json())
            .then(data => {
                const options = {
                    chart: {
                        type: 'bar',
                        height: 380,
                        stacked: true,
                        toolbar: { show: true },
                    },
                    plotOptions: {
                        bar: {
                            columnWidth: '55%',
                            borderRadius: 4,
                            dataLabels: { position: 'center' },
                        }
                    },
                    colors: ['#3B7DD8', '#E8842C'],
                    dataLabels: {
                        enabled: true,
                        style: { fontSize: '12px', fontWeight: 'bold' },
                        formatter: function(val) { return val > 0 ? val : ''; },
                    },
                    series: [
                        {
                            name: 'Membres recensés',
                            data: data.map(d => d.membres_recenses),
                        },
                        {
                            name: 'Hors répertoire',
                            data: data.map(d => d.hors_repertoire),
                        }
                    ],
                    xaxis: {
                        categories: data.map(d => d.full_label),
                        labels: {
                            rotate: -45,
                            style: { fontSize: '11px' },
                            maxHeight: 120,
                        },
                    },
                    yaxis: {
                        title: { text: 'Nombre de présents' },
                    },
                    title: {
                        text: 'Affluence par activité',
                        align: 'center',
                        style: { fontSize: '16px', fontWeight: 'bold', color: '#1B2A4A' },
                    },
                    legend: {
                        show: true,
                        position: 'top',
                    },
                    tooltip: {
                        y: { formatter: val => val + ' personne(s)' },
                    },
                };

                // Ajouter les totaux en annotation
                if (data.length > 0) {
                    options.annotations = {
                        points: data.map((d, i) => ({
                            x: d.full_label,
                            y: d.total,
                            marker: { size: 0 },
                            label: {
                                text: String(d.total),
                                offsetY: -5,
                                style: { fontSize: '13px', fontWeight: 'bold', color: '#1B2A4A', background: 'transparent' },
                            }
                        }))
                    };
                }

                const container = document.querySelector('#chart-affluence');
                const parentWidth = container.parentElement.clientWidth || 800;
                const itemWidth = parentWidth / 15;
                container.style.minWidth = Math.max(parentWidth, data.length * itemWidth) + 'px';

                if (chart5) chart5.destroy();
                chart5 = new ApexCharts(container, options);
                chart5.render();
            });
    }

    document.getElementById('filter-affluence-type').addEventListener('change', loadChart5);
    document.getElementById('filter-affluence-year').addEventListener('change', loadChart5);

    // ============================================================
    // Charger tous les graphiques au démarrage
    // ============================================================
    loadChart1();
    loadChart2();
    loadChart3();
    loadChart4();
    loadChart5();
});
</script>
@endpush
