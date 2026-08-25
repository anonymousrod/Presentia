@extends('layouts.app')

@section('title', 'Statistiques des Présences')

@section('content')
<div class="container-fluid p-0">
    <div class="row align-items-center mb-3 g-2">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between py-2">
                <h4 class="mb-sm-0 fw-bold fs-18 fs-md-22">Statistiques des Présences</h4>
            </div>
        </div>
    </div>

    {{-- KPIs rapides --}}
    <div class="row g-2 g-md-3 mb-3 mb-md-4">
        <div class="col-4">
            <div class="card card-animate border-0 shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body p-2 p-sm-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="fs-10 fs-sm-11 text-uppercase text-muted fw-semibold tracking-wider">Jeunes</span>
                        <div class="avatar-xs bg-primary-subtle text-primary rounded-circle d-none d-sm-flex align-items-center justify-content-center">
                            <i class="mdi mdi-account-group fs-14"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold mb-0 text-primary fs-15 fs-sm-20 fs-md-22">
                        <span class="counter-value" data-target="{{ $totalMembers }}">{{ $totalMembers }}</span>
                    </h4>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="card card-animate border-0 shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body p-2 p-sm-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="fs-10 fs-sm-11 text-uppercase text-muted fw-semibold tracking-wider">Groupes</span>
                        <div class="avatar-xs bg-success-subtle text-success rounded-circle d-none d-sm-flex align-items-center justify-content-center">
                            <i class="mdi mdi-account-multiple-outline fs-14"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold mb-0 text-success fs-15 fs-sm-20 fs-md-22">
                        <span class="counter-value" data-target="{{ $totalGroups }}">{{ $totalGroups }}</span>
                    </h4>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="card card-animate border-0 shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body p-2 p-sm-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="fs-10 fs-sm-11 text-uppercase text-muted fw-semibold tracking-wider">Activités</span>
                        <div class="avatar-xs bg-info-subtle text-info rounded-circle d-none d-sm-flex align-items-center justify-content-center">
                            <i class="mdi mdi-calendar-check fs-14"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold mb-0 text-info fs-15 fs-sm-20 fs-md-22">
                        <span class="counter-value" data-target="{{ $totalActivities }}">{{ $totalActivities }}</span>
                    </h4>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtres Globaux --}}
    <div class="card border-0 shadow-sm mb-3 mb-md-4 rounded-3 overflow-hidden">
        <div class="card-header border-0 bg-transparent py-3 px-3 px-md-4" data-bs-toggle="collapse" data-bs-target="#globalFilterCollapse" style="cursor: pointer;">
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
        <div id="globalFilterCollapse" class="collapse show">
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

    {{-- Graphique 1 : Répartition des jeunes par groupe --}}
    <div class="row g-3 mb-3 mb-md-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header border-0 bg-transparent py-3 px-3 px-md-4 d-flex justify-content-between align-items-center border-bottom border-light-subtle">
                    <div class="d-flex align-items-center gap-2">
                        <i class="mdi mdi-chart-bar text-primary fs-18"></i>
                        <h5 class="card-title mb-0 fw-bold fs-14 fs-md-15 text-body">Répartition des jeunes par groupe</h5>
                    </div>
                    <button type="button" class="btn btn-sm btn-soft-secondary px-2 py-1 rounded-circle" id="btn-export-chart1" title="Télécharger l'image">
                        <i class="mdi mdi-download fs-14"></i>
                    </button>
                </div>
                <div class="card-body p-2 p-md-3" data-simplebar style="overflow-x: auto;">
                    <div id="chart-members-per-group" style="min-height: 350px;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Graphique 2 : Évolution des présences par type d'activité --}}
    <div class="row g-3 mb-3 mb-md-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header border-0 bg-transparent py-3 px-3 px-md-4 d-flex justify-content-between align-items-center border-bottom border-light-subtle">
                    <div class="d-flex align-items-center gap-2">
                        <i class="mdi mdi-chart-line text-info fs-18"></i>
                        <h5 class="card-title mb-0 fw-bold fs-14 fs-md-15 text-body">Évolution des présences par séance</h5>
                    </div>
                    <button type="button" class="btn btn-sm btn-soft-secondary px-2 py-1 rounded-circle" id="btn-export-chart2" title="Télécharger l'image">
                        <i class="mdi mdi-download fs-14"></i>
                    </button>
                </div>
                <div class="card-body p-2 p-md-3" data-simplebar style="overflow-x: auto;">
                    <div id="chart-presence-evolution" style="min-height: 380px;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Graphiques 3 & 4 côte à côte --}}
    <div class="row g-3 mb-3 mb-md-4">
        {{-- Graphique 3 : Taux de présence par groupe --}}
        <div class="col-12 col-xl-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header border-0 bg-transparent py-3 px-3 px-md-4 d-flex justify-content-between align-items-center border-bottom border-light-subtle">
                    <div class="d-flex align-items-center gap-2">
                        <i class="mdi mdi-chart-bar-stacked text-warning fs-18"></i>
                        <h5 class="card-title mb-0 fw-bold fs-14 fs-md-15 text-body">Présence par groupe</h5>
                    </div>
                    <button type="button" class="btn btn-sm btn-soft-secondary px-2 py-1 rounded-circle" id="btn-export-chart3" title="Télécharger l'image">
                        <i class="mdi mdi-download fs-14"></i>
                    </button>
                </div>
                <div class="card-body p-2 p-md-3" data-simplebar style="max-height: 450px; overflow-x: auto;">
                    <div id="chart-presence-by-group" style="min-height: 380px;"></div>
                </div>
            </div>
        </div>

        {{-- Graphique 4 : Participation individuelle --}}
        <div class="col-12 col-xl-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header border-0 bg-transparent py-3 px-3 px-md-4 d-flex justify-content-between align-items-center border-bottom border-light-subtle">
                    <div class="d-flex align-items-center gap-2">
                        <i class="mdi mdi-account-details text-danger fs-18"></i>
                        <h5 class="card-title mb-0 fw-bold fs-14 fs-md-15 text-body">Participation <span id="chart4-subtitle" class="fs-12 text-muted fw-normal"></span></h5>
                    </div>
                    <button type="button" class="btn btn-sm btn-soft-secondary px-2 py-1 rounded-circle" id="btn-export-chart4" title="Télécharger l'image">
                        <i class="mdi mdi-download fs-14"></i>
                    </button>
                </div>
                <div class="card-body p-2 p-md-3" data-simplebar style="max-height: 450px; overflow-x: auto;">
                    <div id="chart-individual-participation" style="min-height: 380px;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Graphique 5 : Affluence par activité --}}
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header border-0 bg-transparent py-3 px-3 px-md-4 d-flex justify-content-between align-items-center border-bottom border-light-subtle">
                    <div class="d-flex align-items-center gap-2">
                        <i class="mdi mdi-chart-areaspline text-success fs-18"></i>
                        <h5 class="card-title mb-0 fw-bold fs-14 fs-md-15 text-body">Affluence par activité</h5>
                    </div>
                    <button type="button" class="btn btn-sm btn-soft-secondary px-2 py-1 rounded-circle" id="btn-export-chart5" title="Télécharger l'image">
                        <i class="mdi mdi-download fs-14"></i>
                    </button>
                </div>
                <div class="card-body p-2 p-md-3" data-simplebar style="overflow-x: auto;">
                    <div id="chart-affluence" style="min-height: 380px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {

    // Palette de couleurs par groupe
    const GROUP_COLORS = {
        'Béthel':   '#1B2A4A',
        'Cana':     '#3B7DD8',
        'Éden':     '#A8C8E8',
        'Galilée':  '#8B8B8B',
        'Salem':    '#BABABA',
        'Shalom':   '#D2691E',
        'Siloé':    '#E8842C',
        'Sinaï':    '#B0D4F1',
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

        return 'Type: ' + typeText + '  •  Période: ' + dateText;
    }

    // Graphique 1 : Répartition des jeunes par groupe
    let chart1;
    function loadChart1() {
        fetch('{{ route("admin.statistics.chart.members-per-group") }}')
            .then(r => r.json())
            .then(data => {
                const totalMembers = data.reduce((sum, item) => sum + item.count, 0);
                const options = {
                    chart: { type: 'bar', height: 350, toolbar: { show: false } },
                    title: {
                        text: 'Répartition des jeunes par groupe',
                        align: 'center',
                        style: { fontSize: '15px', fontWeight: 'bold', color: 'var(--vz-body-color)' }
                    },
                    subtitle: {
                        text: 'Effectif total recensé : ' + totalMembers + ' membres',
                        align: 'center',
                        style: { fontSize: '11px', color: '#878a99' }
                    },
                    plotOptions: {
                        bar: { columnWidth: '55%', distributed: true, borderRadius: 4, dataLabels: { position: 'top' } }
                    },
                    colors: data.map((d, i) => getGroupColor(d.name, i, d.color)),
                    dataLabels: {
                        enabled: true,
                        offsetY: -20,
                        style: { fontSize: '13px', fontWeight: 'bold', colors: ['var(--vz-body-color)'] },
                    },
                    series: [{ name: 'Membres', data: data.map(d => d.count) }],
                    xaxis: {
                        categories: data.map(d => d.name),
                        labels: { style: { fontSize: '11px', fontWeight: 600 } },
                    },
                    yaxis: { title: { text: 'Nombre de membres' } },
                    legend: { show: false },
                    tooltip: { y: { formatter: val => val + ' membre(s)' } },
                };

                const container = document.querySelector('#chart-members-per-group');
                const parentWidth = container.parentElement.clientWidth || 800;
                const itemsToDisplay = window.innerWidth < 768 ? 5 : 15;
                const itemWidth = parentWidth / itemsToDisplay;
                container.style.minWidth = Math.max(parentWidth, data.length * itemWidth) + 'px';

                if (chart1) chart1.destroy();
                chart1 = new ApexCharts(container, options);
                chart1.render();
            });
    }

    // Graphique 2 : Évolution des présences
    let chart2;
    function loadChart2() {
        const typeId = document.getElementById('global-filter-type').value;
        const dateFrom = document.getElementById('global-filter-date-from').value;
        const dateTo = document.getElementById('global-filter-date-to').value;
        const url = new URL('{{ route("admin.statistics.chart.presence-evolution", [], false) }}', window.location.origin);
        if (typeId) url.searchParams.set('activity_type_id', typeId);
        if (dateFrom) url.searchParams.set('date_from', dateFrom);
        if (dateTo) url.searchParams.set('date_to', dateTo);

        fetch(url)
            .then(r => r.json())
            .then(result => {
                const series = result.series;
                const avg = result.average;

                const options = {
                    chart: { type: 'line', height: 380, toolbar: { show: false }, zoom: { enabled: false } },
                    title: {
                        text: 'Évolution des présences par séance (' + result.total_sessions + ' séances)',
                        align: 'center',
                        style: { fontSize: '15px', fontWeight: 'bold', color: 'var(--vz-body-color)' }
                    },
                    subtitle: {
                        text: getFilterSubtitle(),
                        align: 'center',
                        style: { fontSize: '11px', color: '#878a99' }
                    },
                    stroke: { width: [3, 2], curve: 'straight', dashArray: [0, 8] },
                    colors: ['#3B7DD8', '#E8842C'],
                    series: [
                        { name: 'Présences', type: 'line', data: series.map(d => d.count) },
                        { name: 'Moyenne (' + avg + ')', type: 'line', data: series.map(() => avg) }
                    ],
                    markers: { size: [5, 0], colors: ['#3B7DD8'], strokeColors: '#fff', strokeWidth: 2, hover: { sizeOffset: 3 } },
                    dataLabels: {
                        enabled: true,
                        enabledOnSeries: [0],
                        offsetY: -10,
                        style: { fontSize: '12px', fontWeight: 'bold', colors: ['var(--vz-body-color)'] },
                    },
                    xaxis: { categories: series.map(d => d.date), labels: { style: { fontSize: '11px' } } },
                    yaxis: { title: { text: 'Présences totales' }, min: 0 },
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
                    legend: { show: true, position: 'top' },
                };

                const container = document.querySelector('#chart-presence-evolution');
                const parentWidth = container.parentElement.clientWidth || 800;
                const itemsToDisplay = window.innerWidth < 768 ? 5 : 15;
                const itemWidth = parentWidth / itemsToDisplay;
                container.style.minWidth = Math.max(parentWidth, series.length * itemWidth) + 'px';

                if (chart2) chart2.destroy();
                chart2 = new ApexCharts(container, options);
                chart2.render();
            });
    }

    // Graphique 3 : Présence par groupe
    let chart3;
    function loadChart3() {
        const typeId = document.getElementById('global-filter-type').value;
        const dateFrom = document.getElementById('global-filter-date-from').value;
        const dateTo = document.getElementById('global-filter-date-to').value;

        const url = new URL('{{ route("admin.statistics.chart.presence-by-group", [], false) }}', window.location.origin);
        if (typeId) url.searchParams.set('activity_type_id', typeId);
        if (dateFrom) url.searchParams.set('date_from', dateFrom);
        if (dateTo) url.searchParams.set('date_to', dateTo);

        fetch(url)
            .then(r => r.json())
            .then(data => {
                const options = {
                    chart: { type: 'bar', height: Math.max(300, data.length * 45), toolbar: { show: false } },
                    title: {
                        text: 'Présence par groupe',
                        align: 'center',
                        style: { fontSize: '15px', fontWeight: 'bold', color: 'var(--vz-body-color)' }
                    },
                    subtitle: {
                        text: getFilterSubtitle(),
                        align: 'center',
                        style: { fontSize: '11px', color: '#878a99' }
                    },
                    plotOptions: {
                        bar: { horizontal: true, barHeight: '40%', distributed: true, borderRadius: 0, dataLabels: { position: 'top' } }
                    },
                    stroke: { show: false, width: 0 },
                    colors: data.map((d, i) => getGroupColor(d.name, i, d.color)),
                    dataLabels: {
                        enabled: true,
                        textAnchor: 'start',
                        formatter: function(val, opt) { return data[opt.dataPointIndex].label; },
                        offsetX: 10,
                        style: { fontSize: '11px', fontWeight: 'bold', colors: ['var(--vz-body-color)'] },
                    },
                    series: [{ name: 'Présents', data: data.map(d => d.presents) }],
                    labels: data.map(d => d.name),
                    xaxis: {
                        title: { text: 'Nombre de présents' },
                        max: data.length > 0 ? Math.max(...data.map(d => d.effectif)) + 2 : 10,
                        min: 0,
                    },
                    yaxis: { labels: { style: { fontSize: '12px', fontWeight: 600 } } },
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

                const container = document.querySelector('#chart-presence-by-group');
                const parentWidth = container.parentElement.clientWidth || 800;
                const itemsToDisplay = window.innerWidth < 768 ? 5 : 15;
                const itemWidth = parentWidth / itemsToDisplay;
                container.style.minWidth = Math.max(parentWidth, data.length * itemWidth) + 'px';

                if (chart3) chart3.destroy();
                chart3 = new ApexCharts(container, options);
                chart3.render();
            });
    }

    // Graphique 4 : Participation individuelle
    let chart4;
    function loadChart4() {
        const typeId = document.getElementById('global-filter-type').value;
        const dateFrom = document.getElementById('global-filter-date-from').value;
        const dateTo = document.getElementById('global-filter-date-to').value;
        const url = new URL('{{ route("admin.statistics.chart.individual-participation", [], false) }}', window.location.origin);
        if (typeId) url.searchParams.set('activity_type_id', typeId);
        if (dateFrom) url.searchParams.set('date_from', dateFrom);
        if (dateTo) url.searchParams.set('date_to', dateTo);

        fetch(url)
            .then(r => r.json())
            .then(result => {
                const data = result.data;
                const totalSessions = result.total_sessions;

                const displayData = data;

                const options = {
                    chart: { type: 'bar', height: Math.max(350, displayData.length * 30), toolbar: { show: false } },
                    title: {
                        text: 'Participation individuelle',
                        align: 'center',
                        style: { fontSize: '15px', fontWeight: 'bold', color: 'var(--vz-body-color)' }
                    },
                    subtitle: {
                        text: getFilterSubtitle() + ' (' + totalSessions + ' séances)',
                        align: 'center',
                        style: { fontSize: '11px', color: '#878a99' }
                    },
                    plotOptions: {
                        bar: { horizontal: true, barHeight: '40%', distributed: true, borderRadius: 0, dataLabels: { position: 'top' } }
                    },
                    stroke: { show: false, width: 0 },
                    colors: displayData.map((d, i) => getGroupColor(d.group_name, i, d.group_color)),
                    dataLabels: {
                        enabled: true,
                        textAnchor: 'start',
                        offsetX: 10,
                        style: { fontSize: '11px', fontWeight: 'bold', colors: ['var(--vz-body-color)'] },
                    },
                    series: [{ name: 'Présences', data: displayData.map(d => d.count) }],
                    labels: displayData.map(d => d.full_name + ' (' + d.group_name.replace('Groupe ', '') + ')'),
                    xaxis: {
                        title: { text: 'Nombre de présences (sur ' + totalSessions + ' séances)' },
                        max: totalSessions > 0 ? totalSessions + 1 : 10,
                        min: 0,
                        labels: { formatter: function(val) { return Math.floor(val); } }
                    },
                    yaxis: { labels: { style: { fontSize: '11px' }, maxWidth: 200 } },
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

                const container = document.querySelector('#chart-individual-participation');
                const parentWidth = container.parentElement.clientWidth || 800;
                const itemsToDisplay = window.innerWidth < 768 ? 5 : 15;
                const itemWidth = parentWidth / itemsToDisplay;
                container.style.minWidth = Math.max(parentWidth, displayData.length * itemWidth) + 'px';

                if (chart4) chart4.destroy();
                chart4 = new ApexCharts(container, options);
                chart4.render();
            });
    }

    // Graphique 5 : Affluence par activité
    let chart5;
    function loadChart5() {
        const typeId = document.getElementById('global-filter-type').value;
        const dateFrom = document.getElementById('global-filter-date-from').value;
        const dateTo = document.getElementById('global-filter-date-to').value;
        const url = new URL('{{ route("admin.statistics.chart.affluence-by-activity", [], false) }}', window.location.origin);
        if (typeId) url.searchParams.set('activity_type_id', typeId);
        if (dateFrom) url.searchParams.set('date_from', dateFrom);
        if (dateTo) url.searchParams.set('date_to', dateTo);

        fetch(url)
            .then(r => r.json())
            .then(data => {
                const options = {
                    chart: { type: 'bar', height: 380, stacked: true, toolbar: { show: false } },
                    title: {
                        text: 'Affluence par activité',
                        align: 'center',
                        style: { fontSize: '15px', fontWeight: 'bold', color: 'var(--vz-body-color)' }
                    },
                    subtitle: {
                        text: getFilterSubtitle(),
                        align: 'center',
                        style: { fontSize: '11px', color: '#878a99' }
                    },
                    plotOptions: { bar: { columnWidth: '55%', borderRadius: 4, dataLabels: { position: 'center' } } },
                    colors: ['#3B7DD8', '#E8842C'],
                    dataLabels: {
                        enabled: true,
                        style: { fontSize: '11px', fontWeight: 'bold' },
                        formatter: function(val) { return val > 0 ? val : ''; },
                    },
                    series: [
                        { name: 'Membres recensés', data: data.map(d => d.membres_recenses) },
                        { name: 'Hors répertoire', data: data.map(d => d.hors_repertoire) }
                    ],
                    xaxis: {
                        categories: data.map(d => d.full_label),
                        labels: { rotate: -45, style: { fontSize: '11px' }, maxHeight: 120 },
                    },
                    yaxis: { title: { text: 'Nombre de présents' } },
                    legend: { show: true, position: 'top' },
                    tooltip: { y: { formatter: val => val + ' personne(s)' } },
                };

                if (data.length > 0) {
                    options.annotations = {
                        points: data.map((d, i) => ({
                            x: d.full_label,
                            y: d.total,
                            marker: { size: 0 },
                            label: {
                                text: String(d.total),
                                offsetY: -5,
                                style: { fontSize: '12px', fontWeight: 'bold', color: 'var(--vz-body-color)', background: 'transparent' },
                            }
                        }))
                    };
                }

                const container = document.querySelector('#chart-affluence');
                const parentWidth = container.parentElement.clientWidth || 800;
                const itemsToDisplay = window.innerWidth < 768 ? 5 : 15;
                const itemWidth = parentWidth / itemsToDisplay;
                container.style.minWidth = Math.max(parentWidth, data.length * itemWidth) + 'px';

                if (chart5) chart5.destroy();
                chart5 = new ApexCharts(container, options);
                chart5.render();
            });
    }

    function loadAllFilteredCharts() {
        loadChart2();
        loadChart3();
        loadChart4();
        loadChart5();
    }
    
    document.getElementById('btn-apply-filters').addEventListener('click', loadAllFilteredCharts);

    loadChart1();
    loadAllFilteredCharts();

    // Export des graphiques
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

    document.getElementById('btn-export-chart1')?.addEventListener('click', () => exportChart(chart1, 'repartition_jeunes_par_groupe'));
    document.getElementById('btn-export-chart2')?.addEventListener('click', () => exportChart(chart2, 'evolution_presences'));
    document.getElementById('btn-export-chart3')?.addEventListener('click', () => exportChart(chart3, 'presence_par_groupe'));
    document.getElementById('btn-export-chart4')?.addEventListener('click', () => exportChart(chart4, 'participation_individuelle'));
    document.getElementById('btn-export-chart5')?.addEventListener('click', () => exportChart(chart5, 'affluence_par_activite'));
});
</script>
@endpush
