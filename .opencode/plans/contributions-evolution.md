# Plan: Ajout de l'évolution des contributions

## 1. `app/Http/Controllers/Admin/Finance/ContributionController.php`

### Ajouter dans `index()` APRÈS `$yearlyContributions` et AVANT `return view(...)` :

```php
        // Total attendu pour le mois sélectionné
        $membersWeeklySum = $members->sum(function ($m) {
            return $m->weekly_contribution ?? 0;
        });
        $monthlyExpectedTotal = $membersWeeklySum * count($sundays);

        // Données pour le graphique d'évolution mensuelle (Février à Novembre)
        $monthlyChartData = [];
        $yearlyExpectedTotal = 0;
        $yearlyCollectedTotal = 0;
        for ($m = 2; $m <= 11; $m++) {
            $mStr = str_pad($m, 2, '0', STR_PAD_LEFT);
            $monthStart = Carbon::parse("$year-$mStr-01")->startOfMonth();
            $monthEnd = Carbon::parse("$year-$mStr-01")->endOfMonth();

            $sundaysCount = 0;
            $d = $monthStart->copy()->next(Carbon::SUNDAY);
            if ($monthStart->isSunday()) {
                $d = $monthStart->copy();
            }
            while ($d->lte($monthEnd)) {
                $sundaysCount++;
                $d->addWeek();
            }

            $expected = $membersWeeklySum * $sundaysCount;
            $collected = Contribution::whereIn('user_id', $members->pluck('id'))
                ->whereBetween('date', [$monthStart, $monthEnd])
                ->sum('amount');

            $monthlyChartData[] = [
                'month' => Carbon::create()->month($m)->translatedFormat('F'),
                'expected' => $expected,
                'collected' => $collected,
            ];

            $yearlyExpectedTotal += $expected;
            $yearlyCollectedTotal += $collected;
        }

        $yearlyProgressPercent = $yearlyExpectedTotal > 0
            ? min(100, round(($yearlyCollectedTotal / $yearlyExpectedTotal) * 100))
            : 0;
```

### Modifier le `compact(...)` dans `return view(...)` :

Ajouter à la fin : `'monthlyExpectedTotal', 'monthlyChartData', 'yearlyExpectedTotal', 'yearlyCollectedTotal', 'yearlyProgressPercent'`

---

## 2. `resources/views/admin/finance/contributions/index.blade.php`

### a) Ajouter les cartes récapitulatives APRÈS la ligne `<div class="row mb-3">` (celle du filtre) et AVANT `<div class="row">` (celle du tableau) :

```blade
<!-- Cartes récapitulatives -->
<div class="row mb-3">
    <div class="col-md-3">
        <div class="card bg-primary-subtle border-0 mb-0">
            <div class="card-body py-2 px-3">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="fw-bold fs-13">Attendu ce mois</span>
                    <span class="fs-16 fw-bold text-primary">{{ number_format($monthlyExpectedTotal, 0, ',', ' ') }} F</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info-subtle border-0 mb-0">
            <div class="card-body py-2 px-3">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="fw-bold fs-13">Attendu (année)</span>
                    <span class="fs-16 fw-bold text-info">{{ number_format($yearlyExpectedTotal, 0, ',', ' ') }} F</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success-subtle border-0 mb-0">
            <div class="card-body py-2 px-3">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="fw-bold fs-13">Collecté (année)</span>
                    <span class="fs-16 fw-bold text-success">{{ number_format($yearlyCollectedTotal, 0, ',', ' ') }} F</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning-subtle border-0 mb-0">
            <div class="card-body py-2 px-3">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="fw-bold fs-13">Progression</span>
                    <span class="fs-14 fw-bold text-warning">{{ $yearlyProgressPercent }}%</span>
                </div>
                <div class="progress progress-sm" style="height: 6px;">
                    <div class="progress-bar bg-warning" role="progressbar"
                         style="width: {{ $yearlyProgressPercent }}%"
                         aria-valuenow="{{ $yearlyProgressPercent }}" aria-valuemin="0" aria-valuemax="100">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
```

### b) Ajouter le graphique d'évolution APRÈS la grille de saisie `</form>` (avant le modal de versement) :

```blade
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
```

### c) Ajouter le script ApexCharts À LA FIN du template (AVANT `@endsection`) :

```blade
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var chartData = @json($monthlyChartData);
        var months = chartData.map(function(d) { return d.month; });
        var expected = chartData.map(function(d) { return d.expected; });
        var collected = chartData.map(function(d) { return d.collected; });

        var options = {
            chart: {
                type: 'bar',
                height: 350,
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
                title: { text: 'Mois' }
            },
            yaxis: {
                title: { text: 'Montant (FCFA)' },
                labels: {
                    formatter: function (val) {
                        return val.toLocaleString('fr-FR');
                    }
                }
            },
            colors: ['#3B7DD8', '#28a745'],
            dataLabels: {
                enabled: true,
                formatter: function (val) {
                    return val >= 1000 ? (val / 1000).toFixed(0) + 'k' : val;
                },
                style: { fontSize: '10px' }
            },
            plotOptions: {
                bar: {
                    columnWidth: '60%',
                    borderRadius: 4
                }
            },
            legend: {
                position: 'top'
            },
            tooltip: {
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
```

---

## Résumé des changements

| Fichier | Changement |
|---------|-----------|
| `ContributionController.php` | +25 lignes pour calculer les stats annuelles et mensuelles |
| `index.blade.php` | +110 lignes : 4 cartes récap + graphique + script ApexCharts |
