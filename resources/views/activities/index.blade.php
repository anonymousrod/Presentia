@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Liste des activités</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Activités</a></li>
                    <li class="breadcrumb-item active">Liste</li>
                </ol>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('activities.index') }}" method="GET" class="row g-3" id="filter-form">
            <div class="col-md-5">
                <label class="form-label fw-bold">Recherche</label>
                <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Rechercher par titre...">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Type d'activité</label>
                <select name="type" class="form-select">
                    <option value="">Tous les types</option>
                    @foreach(\App\Enums\ActivityType::cases() as $type)
                        <option value="{{ $type->value }}" {{ request('type') === $type->value ? 'selected' : '' }}>{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100 me-2"><i class="mdi mdi-filter"></i> Filtrer</button>
                <button type="button" class="btn btn-light" id="btn-reset">Réinitialiser</button>
            </div>
        </form>
    </div>
</div>

<!-- Activities Grid -->
<div class="row">
    @forelse($activities as $activity)
        @php
            $regStatus = $myRegistrations[$activity->id] ?? null;
            $isWaitlisted = $myWaitlists[$activity->id] ?? false;
        @endphp
        <div class="col-xl-4 col-md-6">
            <div class="card card-height-100 border-start border-primary border-3">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-grow-1">
                            <span class="badge bg-soft-info text-info fs-11 uppercase">{{ $activity->type->value }}</span>
                        </div>
                        <div class="flex-shrink-0">
                            @if(!$regStatus)
                                <span class="badge bg-soft-secondary text-secondary fs-12">Non inscrit</span>
                            @elseif($regStatus === 'ABSENT_JUSTIFIED')
                                <span class="badge bg-soft-danger text-danger fs-12">Annulé</span>
                            @elseif($isWaitlisted)
                                <span class="badge bg-soft-warning text-warning fs-12">Liste d'attente</span>
                            @else
                                <span class="badge bg-soft-success text-success fs-12">Inscrit</span>
                            @endif
                        </div>
                    </div>
                    
                    <h5 class="card-title mb-2 text-dark fs-16">{{ $activity->title }}</h5>
                    <p class="text-muted text-truncate-two-lines mb-3 fs-13" style="min-height: 40px;">
                        {{ Str::limit($activity->description, 120) ?: 'Aucune description disponible.' }}
                    </p>

                    <div class="d-flex flex-column gap-2 mb-3">
                        <div class="d-flex align-items-center text-muted fs-13">
                            <i class="mdi mdi-calendar-clock text-primary fs-16 me-2"></i>
                            <div>
                                <strong>Début:</strong> {{ $activity->start_time->format('d/m/Y H:i') }}<br>
                                <strong>Fin:</strong> {{ $activity->end_time->format('d/m/Y H:i') }}
                            </div>
                        </div>
                        @if($activity->location)
                            <div class="d-flex align-items-center text-muted fs-13">
                                <i class="mdi mdi-map-marker text-primary fs-16 me-2"></i>
                                <span>{{ $activity->location }}</span>
                            </div>
                        @endif
                        <div class="d-flex align-items-center text-muted fs-13">
                            <i class="mdi mdi-account-group text-primary fs-16 me-2"></i>
                            <span>
                                Capacité : 
                                @if($activity->capacity)
                                    {{ $activity->registrations()->where('is_waitlisted', false)->where('status', '!=', 'ABSENT_JUSTIFIED')->count() }} / {{ $activity->capacity }}
                                @else
                                    Illimitée
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="mt-4 pt-2 border-top border-top-dashed d-flex justify-content-between align-items-center">
                        <div>
                            @if($activity->responsible)
                                <small class="text-muted">Resp: {{ $activity->responsible->first_name }} {{ $activity->responsible->name }}</small>
                            @endif
                        </div>
                        <div>
                            @if($activity->start_time->lte(now()))
                                @if($regStatus && $regStatus !== 'ABSENT_JUSTIFIED')
                                    <span class="text-success fw-semibold"><i class="mdi mdi-check-circle-outline"></i> Inscrit</span>
                                @else
                                    <span class="text-muted fw-semibold"><i class="mdi mdi-close-circle-outline"></i> Non inscrit</span>
                                @endif
                            @else
                                @if(!$regStatus || $regStatus === 'ABSENT_JUSTIFIED')
                                    <form action="{{ route('activities.register', $activity) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-primary">
                                            <i class="mdi mdi-plus-circle-outline"></i> S'inscrire
                                        </button>
                                    </form>
                                @else
                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                            onclick="openUnregisterModal('{{ route('activities.unregister', $activity) }}')">
                                        <i class="mdi mdi-minus-circle-outline"></i> Se désinscrire
                                    </button>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="card p-5 text-center">
                <div class="avatar-lg mx-auto mb-4">
                    <div class="avatar-title bg-soft-primary text-primary rounded-circle fs-24">
                        <i class="mdi mdi-calendar-remove"></i>
                    </div>
                </div>
                <h5>Aucune activité disponible</h5>
                <p class="text-muted">Il n'y a pas d'activité programmée pour le moment qui corresponde à vos critères.</p>
            </div>
        </div>
    @endforelse
</div>

<div class="row">
    <div class="col-12 mt-4">
        {{ $activities->links() }}
    </div>
</div>

<!-- Modale de Désinscription -->
<div class="modal fade" id="unregisterModal" tabindex="-1" aria-labelledby="unregisterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form id="unregister-form" action="" method="POST">
                @csrf
                <div class="modal-header border-bottom p-3">
                    <h5 class="modal-title text-danger d-flex align-items-center" id="unregisterModalLabel">
                        <i class="ri-error-warning-fill me-2 fs-20"></i> Désinscription d'une activité
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="text-center mb-3">
                        <lord-icon src="https://cdn.lordicon.com/tdrtiskw.json" trigger="loop" colors="primary:#f06548,secondary:#f7b84b" style="width:70px;height:70px"></lord-icon>
                    </div>
                    <p class="text-muted fs-14">
                        Êtes-vous sûr de vouloir vous désinscrire de cette activité ? Pour finaliser cette action, veuillez renseigner le motif de votre absence.
                    </p>
                    <div class="mb-3">
                        <label for="justification-input" class="form-label fw-bold">Motif de désinscription <span class="text-danger">*</span></label>
                        <textarea class="form-control" 
                                  id="justification-input" 
                                  name="justification" 
                                  rows="3" 
                                  required 
                                  placeholder="Ex: Contretemps professionnel, Maladie, etc. (min. 5 caractères)"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light p-3 border-0">
                    <button type="button" class="btn btn-light w-sm" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger w-sm">Confirmer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('btn-reset').addEventListener('click', function() {
        const form = document.getElementById('filter-form');
        form.querySelectorAll('input, select').forEach(el => el.value = '');
        if (window.location.search) {
            window.location.href = "{{ route('activities.index') }}";
        }
    });

    function openUnregisterModal(actionUrl) {
        const form = document.getElementById('unregister-form');
        form.action = actionUrl;
        const input = document.getElementById('justification-input');
        input.value = ''; // Reset
        
        const modalEl = document.getElementById('unregisterModal');
        const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        modal.show();
    }
</script>
@endpush
