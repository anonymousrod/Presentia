@extends('layouts.app')

@section('title', 'Trésorerie Générale')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Trésorerie Générale</h4>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="row">
    <div class="col-xl-4 col-md-6">
        <div class="card card-animate">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1 overflow-hidden">
                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Total Collecté (Saisi)</p>
                    </div>
                </div>
                <div class="d-flex align-items-end justify-content-between mt-4">
                    <div>
                        <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value" data-target="{{ $totalCollected }}">{{ number_format($totalCollected, 0, ',', ' ') }}</span> FCFA</h4>
                        <a href="#" class="text-decoration-underline">Voir les détails</a>
                    </div>
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-primary-subtle rounded fs-3">
                            <i class="bx bx-wallet text-primary"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-4 col-md-6">
        <div class="card card-animate">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1 overflow-hidden">
                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Total en Caisse (Validé)</p>
                    </div>
                </div>
                <div class="d-flex align-items-end justify-content-between mt-4">
                    <div>
                        <h4 class="fs-22 fw-semibold ff-secondary mb-4 text-success"><span class="counter-value" data-target="{{ $totalValidated }}">{{ number_format($totalValidated, 0, ',', ' ') }}</span> FCFA</h4>
                        <a href="#" class="text-decoration-underline">Voir l'historique</a>
                    </div>
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-success-subtle rounded fs-3">
                            <i class="bx bx-dollar-circle text-success"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-4 col-md-6">
        <div class="card card-animate">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1 overflow-hidden">
                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">En attente de versement</p>
                    </div>
                </div>
                <div class="d-flex align-items-end justify-content-between mt-4">
                    <div>
                        <h4 class="fs-22 fw-semibold ff-secondary mb-4 text-warning"><span class="counter-value" data-target="{{ $totalPending }}">{{ number_format($totalPending, 0, ',', ' ') }}</span> FCFA</h4>
                        <a href="#" class="text-decoration-underline text-warning">Voir les versements en attente</a>
                    </div>
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-warning-subtle rounded fs-3">
                            <i class="bx bx-time text-warning"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Historique des versements déclarés</h5>
                
                <!-- Filtre -->
                <form action="{{ route('admin.finance.treasury.index') }}" method="GET" class="d-flex gap-2">
                    <select name="group_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Tous les groupes</option>
                        @foreach($groups as $g)
                            <option value="{{ $g->id }}" {{ request('group_id') == $g->id ? 'selected' : '' }}>{{ $g->name }}</option>
                        @endforeach
                    </select>

                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Tous les statuts</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                        <option value="validated" {{ request('status') == 'validated' ? 'selected' : '' }}>Validés</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejetés</option>
                    </select>
                </form>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle table-nowrap mb-0">
                        <thead class="table-light">
                            <tr>
                                <th># ID</th>
                                <th>Date de déclaration</th>
                                <th>Groupe</th>
                                <th>Chargé de collecte</th>
                                <th>Montant</th>
                                <th>Statut</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($remittances as $rem)
                                <tr>
                                    <td>{{ $rem->id }}</td>
                                    <td>{{ $rem->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @if($rem->group)
                                            <span class="badge bg-info-subtle text-info">{{ $rem->group->name }}</span>
                                        @else
                                            <span class="text-muted">Inconnu</span>
                                        @endif
                                    </td>
                                    <td>{{ $rem->collector->first_name }} {{ $rem->collector->name }}</td>
                                    <td class="fw-bold">{{ number_format($rem->amount, 0, ',', ' ') }} FCFA</td>
                                    <td>
                                        @if($rem->status == 'pending')
                                            <span class="badge bg-warning">En attente</span>
                                        @elseif($rem->status == 'validated')
                                            <span class="badge bg-success">Validé le {{ $rem->validated_at->format('d/m/Y') }}</span>
                                        @else
                                            <span class="badge bg-danger">Rejeté</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($rem->status == 'pending')
                                            @can('remittance.validate')
                                            <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#validateModal{{ $rem->id }}">
                                                Valider la réception
                                            </button>

                                            <!-- Modal de validation -->
                                            <div class="modal fade" id="validateModal{{ $rem->id }}" tabindex="-1" aria-labelledby="validateModalLabel{{ $rem->id }}" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="validateModalLabel{{ $rem->id }}">Validation de fonds</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body text-center p-4">
                                                            <div class="mt-2">
                                                                <lord-icon src="https://cdn.lordicon.com/tdrtiskw.json" trigger="loop" colors="primary:#0ab39c,secondary:#405189" style="width:130px;height:130px"></lord-icon>
                                                                <div class="mt-4 pt-2 fs-15 mx-4 mx-sm-5">
                                                                    <h4>Êtes-vous sûr ?</h4>
                                                                    <p class="text-muted mx-4 mb-0">Confirmez-vous avoir physiquement reçu la somme de <strong>{{ number_format($rem->amount, 0, ',', ' ') }} FCFA</strong> du chargé de collecte <strong>{{ $rem->collector->first_name }} {{ $rem->collector->name }}</strong> ?</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer justify-content-center">
                                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                                                            <form action="{{ route('admin.finance.remittances.validate', $rem->id) }}" method="POST" class="d-inline-block">
                                                                @csrf
                                                                <button type="submit" class="btn btn-success">Oui, confirmer la réception</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @else
                                            <span class="text-muted">Aucune action possible</span>
                                            @endcan
                                        @else
                                            <span class="text-muted"><i class="mdi mdi-check-circle text-success"></i> Terminé</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">Aucun versement déclaré pour le moment.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $remittances->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
