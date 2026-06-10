@extends('layouts.app')

@section('content')

{{-- =================== EN-TÊTE =================== --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <div>
                <h4 class="mb-1 fw-bold">
                    @if(request('manageable') == 1)
                        <i class="mdi mdi-clipboard-check-outline me-2" style="color: var(--vz-primary);"></i>Émargement Groupe
                    @else
                        <i class="mdi mdi-calendar-month me-2" style="color: var(--vz-primary);"></i>Liste des activités
                    @endif
                </h4>
                @if(request('manageable') == 1)
                    <p class="text-muted mb-0" style="font-size: 0.85rem;">
                        Accédez aux feuilles d'émargement des activités de vos groupes.
                    </p>
                @endif
            </div>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Activités</a></li>
                    <li class="breadcrumb-item active">
                        @if(request('manageable') == 1) Émargement @else Liste @endif
                    </li>
                </ol>
            </div>
        </div>
    </div>
</div>

{{-- Alertes --}}
@if(session('success'))
    <div class="alert border-0 mb-4 d-flex align-items-center gap-3 p-3 shadow-sm alert-dismissible fade show"
         style="background: rgba(var(--vz-success-rgb), 0.12); border-left: 4px solid var(--vz-success) !important; border-radius: 0.5rem;">
        <i class="mdi mdi-check-circle fs-20" style="color: var(--vz-success);"></i>
        <span>{{ session('success') }}</span>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert border-0 mb-4 d-flex align-items-center gap-3 p-3 shadow-sm alert-dismissible fade show"
         style="background: rgba(var(--vz-danger-rgb), 0.12); border-left: 4px solid var(--vz-danger) !important; border-radius: 0.5rem;">
        <i class="mdi mdi-alert-circle fs-20" style="color: var(--vz-danger);"></i>
        <span>{{ session('error') }}</span>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- =================== FILTRES =================== --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-3 px-4">
        <form action="{{ route('activities.index') }}" method="GET" class="row g-3 align-items-end" id="filter-form">
            @if(request('manageable'))
                <input type="hidden" name="manageable" value="{{ request('manageable') }}">
            @endif

            {{-- Recherche --}}
            <div class="col-md-4">
                <label class="form-label fw-semibold text-muted mb-1" style="font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.05em;">
                    <i class="mdi mdi-magnify me-1"></i>Recherche
                </label>
                <div style="position: relative;">
                    <i class="mdi mdi-magnify" style="position:absolute; left:10px; top:50%; transform:translateY(-50%); color: var(--vz-secondary-color); pointer-events:none; font-size: 1rem;"></i>
                    <input type="text" name="search"
                           class="form-control ps-4"
                           value="{{ request('search') }}"
                           placeholder="Rechercher par titre..."
                           style="border-radius: 0.5rem; padding-left: 2rem;">
                </div>
            </div>

            {{-- Type --}}
            <div class="col-md-3">
                <label class="form-label fw-semibold text-muted mb-1" style="font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.05em;">
                    <i class="mdi mdi-tag-outline me-1"></i>Type d'activité
                </label>
                <select name="type" class="form-select" style="border-radius: 0.5rem;">
                    <option value="">Tous les types</option>
                    @foreach(\App\Enums\ActivityType::cases() as $type)
                        <option value="{{ $type->value }}" {{ request('type') === $type->value ? 'selected' : '' }}>
                            {{ $type->label() }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Statut --}}
            <div class="col-md-3">
                <label class="form-label fw-semibold text-muted mb-1" style="font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.05em;">
                    <i class="mdi mdi-clock-outline me-1"></i>Statut temporel
                </label>
                @php
                    $statusFilter = request()->has('status_filter') ? request('status_filter') : 'upcoming';
                @endphp
                <select name="status_filter" class="form-select" style="border-radius: 0.5rem;">
                    <option value="" {{ $statusFilter === '' || $statusFilter === null ? 'selected' : '' }}>Tous les statuts</option>
                    <option value="upcoming"  {{ $statusFilter === 'upcoming'  ? 'selected' : '' }}>🕐 À venir</option>
                    <option value="ongoing"   {{ $statusFilter === 'ongoing'   ? 'selected' : '' }}>🟢 En cours</option>
                    <option value="finished"  {{ $statusFilter === 'finished'  ? 'selected' : '' }}>✅ Terminée</option>
                </select>
            </div>

            {{-- Actions --}}
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1" style="border-radius: 0.5rem;">
                    <i class="mdi mdi-filter-outline me-1"></i>Filtrer
                </button>
                <button type="button" id="btn-reset"
                        class="btn btn-icon"
                        title="Réinitialiser les filtres"
                        style="border-radius: 0.5rem; width:42px; height:42px; background: rgba(var(--vz-secondary-rgb), 0.12); color: var(--vz-secondary-color); border: 1px solid rgba(var(--vz-secondary-rgb), 0.2);">
                    <i class="mdi mdi-refresh fs-16"></i>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Compteur de résultats --}}
<div class="d-flex align-items-center justify-content-between mb-3 px-1">
    <span class="text-muted" style="font-size: 0.85rem;">
        <i class="mdi mdi-format-list-bulleted me-1"></i>
        <strong>{{ $activities->total() }}</strong> activité(s) trouvée(s)
    </span>
    @if(request('search') || request('type') || request('status_filter'))
        <a href="{{ route('activities.index', request('manageable') ? ['manageable' => request('manageable')] : []) }}"
           class="btn btn-sm"
           style="font-size: 0.78rem; color: var(--vz-danger); background: rgba(var(--vz-danger-rgb), 0.1); border-radius: 2rem; padding: 3px 12px;">
            <i class="mdi mdi-close me-1"></i>Effacer les filtres
        </a>
    @endif
</div>

{{-- =================== GRILLE DES ACTIVITÉS =================== --}}
<div class="row g-4">
    @forelse($activities as $activity)
        @php
            $regStatus     = $myRegistrations[$activity->id] ?? null;
            $isWaitlisted  = $myWaitlists[$activity->id] ?? false;
            $now           = now();

            if ($now < $activity->start_time) {
                $temporalState    = 'upcoming';
                $stateLabel       = 'À venir';
                $stateColor       = 'var(--vz-primary)';
                $stateBg          = 'rgba(var(--vz-primary-rgb), 0.12)';
                $stateBorder      = 'rgba(var(--vz-primary-rgb), 0.3)';
                $cardAccent       = 'var(--vz-primary)';
                $stateIcon        = 'mdi-clock-outline';
                $pillDot          = '';
            } elseif ($now > $activity->end_time) {
                $temporalState    = 'finished';
                $stateLabel       = 'Terminée';
                $stateColor       = 'var(--vz-danger)';
                $stateBg          = 'rgba(var(--vz-danger-rgb), 0.1)';
                $stateBorder      = 'rgba(var(--vz-danger-rgb), 0.25)';
                $cardAccent       = 'var(--vz-danger)';
                $stateIcon        = 'mdi-check-circle-outline';
                $pillDot          = '';
            } else {
                $temporalState    = 'ongoing';
                $stateLabel       = 'En cours';
                $stateColor       = 'var(--vz-success)';
                $stateBg          = 'rgba(var(--vz-success-rgb), 0.12)';
                $stateBorder      = 'rgba(var(--vz-success-rgb), 0.3)';
                $cardAccent       = 'var(--vz-success)';
                $stateIcon        = 'mdi-circle';
                $pillDot          = '<span class="pulsing-dot me-1"></span>';
            }

            $registeredCount = $activity->registrations()
                ->where('is_waitlisted', false)
                ->where('status', '!=', 'ABSENT_JUSTIFIED')
                ->count();
            $capacityPct = $activity->capacity ? min(100, round(($registeredCount / $activity->capacity) * 100)) : null;
        @endphp

        <div class="col-xl-4 col-md-6">
            <div class="card border-0 shadow-sm h-100 activity-card {{ $temporalState === 'finished' ? 'activity-finished' : '' }}"
                 style="border-radius: 0.75rem; border-left: 4px solid {{ $cardAccent }} !important; transition: transform 0.2s, box-shadow 0.2s;">

                {{-- Accent top bar --}}
                <div style="height: 3px; background: {{ $cardAccent }}; border-radius: 0.75rem 0.75rem 0 0; margin-left: -4px;"></div>

                <div class="card-body p-4 d-flex flex-column gap-3">

                    {{-- Ligne 1 : Badges état + inscription --}}
                    <div class="d-flex align-items-start justify-content-between gap-2">
                        <div class="d-flex gap-2 flex-wrap">
                            {{-- Badge type --}}
                            <span class="badge rounded-pill px-2 py-1"
                                  style="background: rgba(var(--vz-info-rgb), 0.15); color: var(--vz-info); font-size: 0.72rem; border: 1px solid rgba(var(--vz-info-rgb), 0.25);">
                                <i class="mdi mdi-tag-outline me-1"></i>{{ $activity->type->label() }}
                            </span>
                            {{-- Badge état temporel --}}
                            <span class="badge rounded-pill px-2 py-1 d-inline-flex align-items-center"
                                  style="background: {{ $stateBg }}; color: {{ $stateColor }}; font-size: 0.72rem; border: 1px solid {{ $stateBorder }};">
                                {!! $pillDot !!}
                                <i class="mdi {{ $stateIcon }} me-1"></i>{{ $stateLabel }}
                            </span>
                        </div>
                        {{-- Badge inscription --}}
                        <div class="flex-shrink-0">
                            @if(!$regStatus)
                                <span class="badge rounded-pill px-2 py-1"
                                      style="background: rgba(var(--vz-secondary-rgb), 0.12); color: var(--vz-secondary-color); font-size: 0.72rem;">
                                    Non inscrit
                                </span>
                            @elseif($regStatus === 'ABSENT_JUSTIFIED')
                                <span class="badge rounded-pill px-2 py-1"
                                      style="background: rgba(var(--vz-danger-rgb), 0.12); color: var(--vz-danger); font-size: 0.72rem;">
                                    <i class="mdi mdi-close me-1"></i>Annulé
                                </span>
                            @elseif($isWaitlisted)
                                <span class="badge rounded-pill px-2 py-1"
                                      style="background: rgba(var(--vz-warning-rgb), 0.15); color: var(--vz-warning); font-size: 0.72rem;">
                                    <i class="mdi mdi-clock-alert me-1"></i>Liste d'attente
                                </span>
                            @else
                                <span class="badge rounded-pill px-2 py-1"
                                      style="background: rgba(var(--vz-success-rgb), 0.15); color: var(--vz-success); font-size: 0.72rem;">
                                    <i class="mdi mdi-check me-1"></i>Inscrit
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Titre + Description --}}
                    <div>
                        <h5 class="fw-bold mb-2" style="font-size: 1rem; line-height: 1.4;">{{ $activity->title }}</h5>
                        <p class="text-muted mb-0" style="font-size: 0.85rem; line-height: 1.5; min-height: 38px;
                                   display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            {{ $activity->description ?: 'Aucune description disponible.' }}
                        </p>
                    </div>

                    {{-- Métadonnées --}}
                    <div class="d-flex flex-column gap-2">
                        {{-- Dates --}}
                        <div class="d-flex align-items-start gap-2">
                            <div class="rounded flex-shrink-0 mt-1 d-flex align-items-center justify-content-center"
                                 style="width:26px; height:26px; background: rgba(var(--vz-primary-rgb), 0.12);">
                                <i class="mdi mdi-calendar-clock" style="font-size: 0.85rem; color: var(--vz-primary);"></i>
                            </div>
                            <div style="font-size: 0.82rem; line-height: 1.5;">
                                <div class="text-muted">
                                    <span class="fw-semibold">Début :</span> {{ $activity->start_time->format('d/m/Y H:i') }}
                                </div>
                                <div class="text-muted">
                                    <span class="fw-semibold">Fin :</span> {{ $activity->end_time->format('d/m/Y H:i') }}
                                </div>
                            </div>
                        </div>

                        {{-- Lieu --}}
                        @if($activity->location)
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded flex-shrink-0 d-flex align-items-center justify-content-center"
                                 style="width:26px; height:26px; background: rgba(var(--vz-primary-rgb), 0.12);">
                                <i class="mdi mdi-map-marker" style="font-size: 0.85rem; color: var(--vz-primary);"></i>
                            </div>
                            <span class="text-muted" style="font-size: 0.82rem;">{{ $activity->location }}</span>
                        </div>
                        @endif

                        {{-- Capacité --}}
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded flex-shrink-0 d-flex align-items-center justify-content-center"
                                 style="width:26px; height:26px; background: rgba(var(--vz-primary-rgb), 0.12);">
                                <i class="mdi mdi-account-group" style="font-size: 0.85rem; color: var(--vz-primary);"></i>
                            </div>
                            <div class="flex-grow-1" style="font-size: 0.82rem;">
                                @if($activity->capacity)
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="text-muted">{{ $registeredCount }} / {{ $activity->capacity }} inscrits</span>
                                        <span class="fw-semibold" style="font-size: 0.75rem; color: {{ $capacityPct >= 90 ? 'var(--vz-danger)' : ($capacityPct >= 70 ? 'var(--vz-warning)' : 'var(--vz-success)') }}">
                                            {{ $capacityPct }}%
                                        </span>
                                    </div>
                                    <div class="progress" style="height: 4px; border-radius: 2px; background: rgba(var(--vz-secondary-rgb), 0.15);">
                                        <div class="progress-bar"
                                             style="width: {{ $capacityPct }}%; border-radius: 2px;
                                                    background: {{ $capacityPct >= 90 ? 'var(--vz-danger)' : ($capacityPct >= 70 ? 'var(--vz-warning)' : 'var(--vz-success)') }};">
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted">{{ $registeredCount }} inscrit(s) — Capacité illimitée</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Séparateur --}}
                    <hr class="my-0" style="border-color: rgba(var(--vz-border-color-translucent), 1);">

                    {{-- Pied de carte : responsable + actions --}}
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            @if($activity->responsible)
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold"
                                         style="width:28px; height:28px; font-size:0.7rem; flex-shrink:0;
                                                background: rgba(var(--vz-primary-rgb), 0.15);
                                                color: var(--vz-primary);">
                                        {{ strtoupper(substr($activity->responsible->first_name, 0, 1) . substr($activity->responsible->name, 0, 1)) }}
                                    </div>
                                    <small class="text-muted" style="font-size: 0.78rem;">
                                        {{ $activity->responsible->first_name }} {{ $activity->responsible->name }}
                                    </small>
                                </div>
                            @endif
                        </div>

                        <div class="d-flex gap-2">
                            {{-- Bouton Émarger (vue gestion) --}}
                            @if(request('manageable') == 1)
                                @can('manage', $activity)
                                    <a href="{{ route('activities.attendance.index', $activity) }}"
                                       class="btn btn-sm px-3"
                                       style="border-radius: 2rem; font-size: 0.8rem;
                                              background: rgba(var(--vz-success-rgb), 0.15);
                                              color: var(--vz-success);
                                              border: 1px solid rgba(var(--vz-success-rgb), 0.3);
                                              transition: all 0.2s;">
                                        <i class="mdi mdi-clipboard-check-outline me-1"></i>Émarger
                                    </a>
                                @endcan
                            @endif

                            {{-- Bouton Inscription / Voir --}}
                            @if($activity->start_time->lte(now()))
                                @if($regStatus && $regStatus !== 'ABSENT_JUSTIFIED')
                                    <span class="d-flex align-items-center gap-1"
                                          style="font-size: 0.8rem; color: var(--vz-success);">
                                        <i class="mdi mdi-check-circle"></i>Inscrit
                                    </span>
                                @else
                                    <span class="d-flex align-items-center gap-1 text-muted"
                                          style="font-size: 0.8rem;">
                                        <i class="mdi mdi-close-circle-outline"></i>Non inscrit
                                    </span>
                                @endif
                            @else
                                @if(!$regStatus || $regStatus === \App\Enums\RegistrationStatus::ABSENT_JUSTIFIED->value)
                                    <a href="{{ route('activities.show', $activity) }}"
                                       class="btn btn-sm btn-primary px-3"
                                       style="border-radius: 2rem; font-size: 0.8rem;">
                                        <i class="mdi mdi-plus me-1"></i>S'inscrire
                                    </a>
                                @else
                                    <a href="{{ route('activities.show', $activity) }}"
                                       class="btn btn-sm btn-outline-primary px-3"
                                       style="border-radius: 2rem; font-size: 0.8rem;">
                                        <i class="mdi mdi-cog-outline me-1"></i>Gérer
                                    </a>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @empty
        <div class="col-12">
            <div class="card border-0 shadow-sm py-5 text-center">
                <div class="card-body">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                         style="width: 72px; height: 72px; background: rgba(var(--vz-primary-rgb), 0.1);">
                        <i class="mdi mdi-calendar-remove fs-30" style="color: var(--vz-primary);"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Aucune activité disponible</h5>
                    <p class="text-muted mb-0" style="font-size: 0.9rem;">
                        Il n'y a pas d'activité correspondant à vos critères de recherche.
                    </p>
                </div>
            </div>
        </div>
    @endforelse
</div>

{{-- Pagination --}}
<div class="row mt-4">
    <div class="col-12">
        {{ $activities->links() }}
    </div>
</div>

{{-- =================== MODALE DÉSINSCRIPTION =================== --}}
<div class="modal fade" id="unregisterModal" tabindex="-1" aria-labelledby="unregisterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 0.75rem;">
            <form id="unregister-form" action="" method="POST">
                @csrf
                <div class="modal-header border-0 pb-0 p-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width:44px; height:44px; background: rgba(var(--vz-danger-rgb), 0.15);">
                            <i class="mdi mdi-alert-circle-outline fs-22" style="color: var(--vz-danger);"></i>
                        </div>
                        <h5 class="modal-title fw-bold mb-0" id="unregisterModalLabel">Désinscription d'une activité</h5>
                    </div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 py-3">
                    <p class="text-muted mb-3" style="font-size: 0.9rem;">
                        Êtes-vous sûr de vouloir vous désinscrire ? Veuillez indiquer le motif de votre absence.
                    </p>
                    <div class="mb-0">
                        <label for="justification-input" class="form-label fw-semibold mb-1" style="font-size: 0.85rem;">
                            Motif de désinscription <span style="color: var(--vz-danger);">*</span>
                        </label>
                        <textarea class="form-control"
                                  id="justification-input"
                                  name="justification"
                                  rows="3"
                                  required
                                  placeholder="Ex: Contretemps professionnel, Maladie... (min. 5 caractères)"
                                  style="border-radius: 0.5rem; font-size: 0.88rem;"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 py-3 gap-2"
                     style="background: rgba(var(--vz-secondary-rgb), 0.05); border-radius: 0 0 0.75rem 0.75rem;">
                    <button type="button" class="btn btn-sm btn-outline-secondary px-4" data-bs-dismiss="modal" style="border-radius: 0.5rem;">
                        Annuler
                    </button>
                    <button type="submit" class="btn btn-sm btn-danger px-4" style="border-radius: 0.5rem;">
                        <i class="mdi mdi-logout me-1"></i>Confirmer la désinscription
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('css')
<style>
    /* Hover effect sur les cartes */
    .activity-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12) !important;
    }

    /* Activités terminées légèrement atténuées */
    .activity-finished {
        opacity: 0.78;
        transition: opacity 0.25s ease, transform 0.2s, box-shadow 0.2s;
    }
    .activity-finished:hover {
        opacity: 0.96;
    }

    /* Pulsing dot pour "En cours" */
    .pulsing-dot {
        width: 7px;
        height: 7px;
        background-color: var(--vz-success);
        border-radius: 50%;
        display: inline-block;
        animation: pulse-dot 1.6s infinite;
        vertical-align: middle;
    }
    @keyframes pulse-dot {
        0%   { box-shadow: 0 0 0 0 rgba(var(--vz-success-rgb), 0.6); }
        70%  { box-shadow: 0 0 0 6px rgba(var(--vz-success-rgb), 0); }
        100% { box-shadow: 0 0 0 0 rgba(var(--vz-success-rgb), 0); }
    }

    /* Hover sur le bouton Émarger */
    a[href*="attendance"]:hover {
        background: rgba(var(--vz-success-rgb), 0.28) !important;
        border-color: rgba(var(--vz-success-rgb), 0.6) !important;
    }
</style>
@endpush

@push('scripts')
<script>
    document.getElementById('btn-reset').addEventListener('click', function () {
        let url = new URL("{{ route('activities.index') }}");
        @if(request('manageable') == 1)
            url.searchParams.append('manageable', '1');
        @endif
        window.location.href = url.toString();
    });

    function openUnregisterModal(actionUrl) {
        const form  = document.getElementById('unregister-form');
        const input = document.getElementById('justification-input');
        form.action = actionUrl;
        input.value = '';
        const modalEl = document.getElementById('unregisterModal');
        const modal   = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        modal.show();
    }
</script>
@endpush
