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

<!-- Filtre du mois & Déclaration de versement -->
<div class="row mb-3">
    <div class="col-md-7">
        <form action="{{ route('admin.finance.contributions.index') }}" method="GET" class="d-flex align-items-center">
            @if(auth()->user()->can('finance.view_all') && isset($allGroups))
                <label class="me-2 fw-bold">Groupe :</label>
                <select name="group_id" class="form-select w-auto me-3" onchange="this.form.submit()">
                    @foreach($allGroups as $g)
                        <option value="{{ $g->id }}" {{ $g->id == $group->id ? 'selected' : '' }}>{{ $g->name }}</option>
                    @endforeach
                </select>
            @endif
            <label class="me-2 fw-bold">Période :</label>
            <select name="year" class="form-select w-auto me-2" onchange="this.form.submit()">
                @for($y = date('Y') - 1; $y <= date('Y') + 1; $y++)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
            <select name="month" class="form-select w-auto me-2" onchange="this.form.submit()">
                @foreach(range(2, 11) as $m)
                    @php $mStr = str_pad($m, 2, '0', STR_PAD_LEFT); @endphp
                    <option value="{{ $mStr }}" {{ $month == $mStr ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary d-none">Filtrer</button>
        </form>
    </div>
    <div class="col-md-5 text-end">
        <div class="card bg-info-subtle border-0 mb-0 d-inline-block p-2 me-3">
            <strong>À verser : </strong> <span class="fs-18 text-info">{{ number_format($pendingAmount, 0, ',', ' ') }} FCFA</span>
        </div>
        @if($pendingAmount > 0)
            @can('remittance.create')
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#remittanceModal">
                <i class="mdi mdi-cash-fast me-1"></i> Verser à la trésorerie
            </button>
            @endcan
        @endif
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
                    <input type="hidden" name="group_id" value="{{ $group->id }}">
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
                        <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                    </div>
                </form>
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
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                <form action="{{ route('admin.finance.remittances.store') }}" method="POST" class="d-inline-block">
                    @csrf
                    <input type="hidden" name="group_id" value="{{ $group->id }}">
                    <button type="submit" class="btn btn-success">Oui, déclarer le versement</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
