@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Détails de l'activité</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('activities.index') }}">Activités</a></li>
                    <li class="breadcrumb-item active">Détails</li>
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

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@php
    $now = now();
    $isLocked = $activity->start_time->copy()->subHours(2)->lt($now);
    $hasStarted = $activity->start_time->lte($now);
    $hasFinished = $activity->end_time->lte($now);
    $isOngoing = $hasStarted && !$hasFinished;
    
    // Determine the route to call
    $formRoute = $myRegistration 
        ? route('activities.register.update', $activity)
        : route('activities.register', $activity);
        
    $formMethod = $myRegistration ? 'PUT' : 'POST';
    
    $currentStatusVal = $myRegistration 
        ? ($myRegistration->status instanceof \App\Enums\RegistrationStatus ? $myRegistration->status->value : $myRegistration->status)
        : '';
        
    $isWaitlisted = $myRegistration ? $myRegistration->is_waitlisted : false;
@endphp

<div class="row pb-5 pb-lg-0 mb-4 mb-lg-0">
    <!-- Left Column: Activity Details -->
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-3 p-md-4">
                <div class="d-flex align-items-center mb-4">
                    <div class="flex-grow-1">
                        <span class="badge fs-12 uppercase p-2 px-3 rounded-pill" style="background-color: {{ $activity->activityType?->color ?? '#17a2b8' }}; color: white;">{{ $activity->activityType?->name ?? 'N/A' }}</span>
                    </div>
                    <div class="flex-shrink-0">
                        @if($hasFinished)
                            <span class="badge bg-soft-danger text-danger fs-13 p-2 rounded-pill"><i class="mdi mdi-check-circle-outline me-1"></i> Terminée</span>
                        @elseif($isOngoing)
                            <span class="badge bg-soft-success text-success fs-13 p-2 rounded-pill"><i class="mdi mdi-circle me-1"></i> En cours</span>
                        @elseif($isLocked)
                            <span class="badge bg-soft-warning text-warning fs-13 p-2 rounded-pill"><i class="mdi mdi-lock-outline me-1"></i> Inscriptions verrouillées</span>
                        @else
                            <span class="badge bg-soft-success text-success fs-13 p-2 rounded-pill"><i class="mdi mdi-clock-outline me-1"></i> Inscriptions ouvertes</span>
                        @endif
                    </div>
                </div>

                <h2 class="fw-bold mb-3 fs-20 fs-md-24">{{ $activity->title }}</h2>

                <!-- Premium colored details cards grid (2x2 on mobile, 4 in row on XL) -->
                <div class="row g-2 g-sm-3 my-4">
                    <!-- Date de début -->
                    <div class="col-6 col-xl-3">
                        <div class="card border-0 h-100 rounded-3 info-card-premium bg-primary-subtle border-start border-primary border-3 shadow-none">
                            <div class="card-body p-2 p-sm-3 d-flex align-items-center">
                                <div class="avatar-xs avatar-sm-md flex-shrink-0 me-2 me-sm-3">
                                    <span class="avatar-title bg-primary text-white rounded-circle fs-16 fs-sm-20 shadow-sm">
                                        <i class="ri-calendar-event-line"></i>
                                    </span>
                                </div>
                                <div class="overflow-hidden">
                                    <small class="text-primary d-block uppercase tracking-wider fs-9 fs-sm-10 fw-semibold">Début</small>
                                    <span class="fw-bold text-body fs-12 fs-sm-14">{{ $activity->start_time->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Date de fin -->
                    <div class="col-6 col-xl-3">
                        <div class="card border-0 h-100 rounded-3 info-card-premium bg-info-subtle border-start border-info border-3 shadow-none">
                            <div class="card-body p-2 p-sm-3 d-flex align-items-center">
                                <div class="avatar-xs avatar-sm-md flex-shrink-0 me-2 me-sm-3">
                                    <span class="avatar-title bg-info text-white rounded-circle fs-16 fs-sm-20 shadow-sm">
                                        <i class="ri-calendar-check-line"></i>
                                    </span>
                                </div>
                                <div class="overflow-hidden">
                                    <small class="text-info d-block uppercase tracking-wider fs-9 fs-sm-10 fw-semibold">Fin</small>
                                    <span class="fw-bold text-body fs-12 fs-sm-14">{{ $activity->end_time->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Lieu -->
                    <div class="col-6 col-xl-3">
                        <div class="card border-0 h-100 rounded-3 info-card-premium bg-success-subtle border-start border-success border-3 shadow-none">
                            <div class="card-body p-2 p-sm-3 d-flex align-items-center">
                                <div class="avatar-xs avatar-sm-md flex-shrink-0 me-2 me-sm-3">
                                    <span class="avatar-title bg-success text-white rounded-circle fs-16 fs-sm-20 shadow-sm">
                                        <i class="ri-map-pin-line"></i>
                                    </span>
                                </div>
                                <div class="overflow-hidden">
                                    <small class="text-success d-block uppercase tracking-wider fs-9 fs-sm-10 fw-semibold">Lieu</small>
                                    <span class="fw-bold text-body fs-12 fs-sm-14 text-truncate d-block" title="{{ $activity->location ?: 'Non spécifié' }}">{{ $activity->location ?: 'Non spécifié' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Capacité -->
                    <div class="col-6 col-xl-3">
                        <div class="card border-0 h-100 rounded-3 info-card-premium bg-warning-subtle border-start border-warning border-3 shadow-none">
                            <div class="card-body p-2 p-sm-3 d-flex align-items-center">
                                <div class="avatar-xs avatar-sm-md flex-shrink-0 me-2 me-sm-3">
                                    <span class="avatar-title bg-warning text-white rounded-circle fs-16 fs-sm-20 shadow-sm">
                                        <i class="ri-team-line"></i>
                                    </span>
                                </div>
                                <div class="overflow-hidden">
                                    <small class="text-warning d-block uppercase tracking-wider fs-9 fs-sm-10 fw-semibold">Capacité</small>
                                    <span class="fw-bold text-body fs-12 fs-sm-14 d-block">
                                        @if($activity->capacity)
                                            {{ $activeRegistrationsCount }} / {{ $activity->capacity }}
                                        @else
                                            Illimitée
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <h5 class="fw-bold mb-3">Description</h5>
                    <div class="text-muted fs-14 fs-md-15 leading-relaxed">
                        {!! nl2br(e($activity->description ?: 'Aucune description fournie.')) !!}
                    </div>
                </div>

                <div class="mt-5 pt-4 border-top border-top-dashed d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm flex-shrink-0 me-3">
                            <div class="avatar-title bg-soft-info text-info rounded-circle fs-18">
                                <i class="mdi mdi-account-star-outline"></i>
                            </div>
                        </div>
                        <div>
                            <small class="text-muted d-block">Responsable de l'activité</small>
                            <span class="fw-bold text-body fs-14">
                                @if($activity->responsible)
                                    {{ $activity->responsible->first_name }} {{ $activity->responsible->name }}
                                @else
                                    Non assigné
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Registration Status Cockpit (Desktop & Mobile view) -->
    <div class="col-lg-4">
        @can('manage', $activity)
            <div class="card shadow-sm border-0 border-start border-success border-3 mb-4 animate__animated animate__fadeIn">
                <div class="card-header bg-soft-success border-0 py-3">
                    <h5 class="card-title mb-0 fw-bold text-success">
                        <i class="mdi mdi-clipboard-check-outline me-2"></i>Gestion des Présences
                    </h5>
                </div>
                <div class="card-body p-3 p-md-4">
                    <p class="text-muted fs-13">
                        En tant que chef de groupe ou administrateur, vous pouvez gérer les présences en temps réel pour cette activité.
                    </p>
                    <a href="{{ route('activities.attendance.index', $activity) }}" class="btn btn-success w-100 shadow-sm">
                        <i class="mdi mdi-open-in-new me-1"></i> Ouvrir la feuille d'émargement
                    </a>
                </div>
            </div>
        @endcan

        @if(!$activity->is_registration_required)
            <div class="card shadow-sm border-0 animate__animated animate__fadeIn">
                <div class="card-header bg-soft-info border-0 py-3">
                    <h5 class="card-title mb-0 fw-bold text-info"><i class="mdi mdi-door-open me-2"></i>Entrée libre</h5>
                </div>
                <div class="card-body p-3 p-md-4 text-center">
                    <div class="mb-3">
                        <i class="mdi mdi-information-outline text-info" style="font-size: 3rem;"></i>
                    </div>
                    <p class="text-muted fs-14 mb-0">
                        L'inscription préalable n'est pas requise pour cette activité. Vous pourrez directement scanner le QR Code sur place pour valider votre présence.
                    </p>
                </div>
            </div>
        @else
        <div class="card shadow-sm border-0" id="registrationCardDesktop">
            <div class="card-header bg-soft-primary border-0 py-3 d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0 fw-bold text-primary"><i class="mdi mdi-cog-outline me-2"></i>Mon Inscription</h5>
            </div>
            
            <div class="card-body p-3 p-md-4">
                <!-- Current status card indicator -->
                <div class="mb-4 text-center p-3 rounded-3 
                    @if(!$myRegistration) bg-soft-secondary text-secondary
                    @elseif($isWaitlisted) bg-soft-warning text-warning
                    @elseif($currentStatusVal === 'PRESENT') bg-soft-success text-success
                    @elseif($currentStatusVal === 'UNCERTAIN') bg-soft-info text-info
                    @elseif($currentStatusVal === 'ABSENT_JUSTIFIED') bg-soft-danger text-danger
                    @endif">
                    <small class="text-uppercase fw-semibold tracking-wider d-block mb-1">Statut actuel</small>
                    <h4 class="fw-bold mb-0">
                        @if(!$myRegistration)
                            Non Inscrit(e)
                        @elseif($isWaitlisted)
                            <i class="mdi mdi-clock-outline me-1"></i> Liste d'attente
                        @elseif($currentStatusVal === 'PRESENT')
                            <i class="mdi mdi-checkbox-marked-circle-outline me-1"></i> Inscrit(e) (Présent)
                        @elseif($currentStatusVal === 'UNCERTAIN')
                            <i class="mdi mdi-help-circle-outline me-1"></i> Incertain(e)
                        @elseif($currentStatusVal === 'ABSENT_JUSTIFIED')
                            <i class="mdi mdi-close-circle-outline me-1"></i> Absent(e) / Désinscrit(e)
                        @endif
                    </h4>
                </div>

                @if($isLocked)
                    <div class="alert alert-warning border-0 p-3" role="alert">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <i class="mdi mdi-alert-circle text-warning fs-18"></i>
                            </div>
                            <div class="flex-grow-1 ms-2">
                                <h6 class="alert-heading fw-bold mb-1">Inscriptions verrouillées</h6>
                                <p class="text-muted mb-0 fs-13">L'activité commence dans moins de 2 heures (ou a déjà commencé). Vous ne pouvez plus vous inscrire ou modifier votre statut.</p>
                            </div>
                        </div>
                    </div>
                @endif

                <form action="{{ $formRoute }}" method="POST" 
                      x-data="{ 
                          status: '{{ old('status', $currentStatusVal ?: 'PRESENT') }}', 
                          isLocked: {{ $isLocked ? 'true' : 'false' }} 
                      }">
                    @csrf
                    @if($formMethod === 'PUT')
                        @method('PUT')
                    @endif

                    <div class="mb-4">
                        <label class="form-label fw-bold mb-3">Sélectionnez votre statut :</label>

                        <!-- PRESENT option -->
                        <div class="mb-2">
                            <input class="status-option-input d-none" 
                                   type="radio" 
                                   name="status" 
                                   id="status-present" 
                                   value="PRESENT" 
                                   x-model="status"
                                   :disabled="isLocked">
                            <label class="status-option-card d-block w-100" for="status-present">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h6 class="fw-bold mb-1 text-success"><i class="mdi mdi-checkbox-marked-circle-outline me-2"></i>Je participe</h6>
                                        <small class="text-muted">Je serai présent à cette activité</small>
                                    </div>
                                    <div class="flex-shrink-0 ms-2" x-show="status === 'PRESENT'">
                                        <span class="badge bg-success rounded-circle p-1"><i class="mdi mdi-check"></i></span>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <!-- UNCERTAIN option -->
                        <div class="mb-2">
                            <input class="status-option-input d-none" 
                                   type="radio" 
                                   name="status" 
                                   id="status-uncertain" 
                                   value="UNCERTAIN" 
                                   x-model="status"
                                   :disabled="isLocked">
                            <label class="status-option-card d-block w-100" for="status-uncertain">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h6 class="fw-bold mb-1 text-warning"><i class="mdi mdi-help-circle-outline me-2"></i>Incertain(e)</h6>
                                        <small class="text-muted">Je ne suis pas encore sûr(e) d'y assister</small>
                                    </div>
                                    <div class="flex-shrink-0 ms-2" x-show="status === 'UNCERTAIN'">
                                        <span class="badge bg-warning rounded-circle p-1"><i class="mdi mdi-check"></i></span>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <!-- ABSENT_JUSTIFIED option -->
                        <div class="mb-2">
                            <input class="status-option-input d-none" 
                                   type="radio" 
                                   name="status" 
                                   id="status-absent" 
                                   value="ABSENT_JUSTIFIED" 
                                   x-model="status"
                                   :disabled="isLocked">
                            <label class="status-option-card d-block w-100" for="status-absent">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h6 class="fw-bold mb-1 text-danger"><i class="mdi mdi-close-circle-outline me-2"></i>Je n'y vais pas / Absent</h6>
                                        <small class="text-muted">Se désinscrire ou déclarer son absence</small>
                                    </div>
                                    <div class="flex-shrink-0 ms-2" x-show="status === 'ABSENT_JUSTIFIED'">
                                        <span class="badge bg-danger rounded-circle p-1"><i class="mdi mdi-check"></i></span>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Justification block, animated using Alpine -->
                    <div class="mb-4" x-show="status === 'ABSENT_JUSTIFIED'" x-transition:enter="fade-in">
                        <label for="justification" class="form-label fw-bold">Motif d'absence / désinscription <span class="text-danger">*</span></label>
                        <textarea class="form-control" 
                                  name="justification" 
                                  id="justification" 
                                  rows="3" 
                                  placeholder="Veuillez renseigner le motif de votre absence (min. 5 caractères)"
                                  :required="status === 'ABSENT_JUSTIFIED'"
                                  :disabled="isLocked">{{ old('justification', $myRegistration ? $myRegistration->justification : '') }}</textarea>
                    </div>

                    @if(!$isLocked)
                        <button type="submit" class="btn btn-primary w-100 btn-lg shadow-sm">
                            <i class="mdi mdi-content-save-outline me-1"></i> Enregistrer mon choix
                        </button>
                    @else
                        <button type="button" class="btn btn-secondary w-100 btn-lg shadow-sm" disabled>
                            <i class="mdi mdi-lock-outline me-1"></i> Inscriptions fermées
                        </button>
                    @endif
                </form>
            </div>
        </div>
        @endif
    </div>
</div>

{{-- 📱 MOBILE APP NATIVE EXPERIENCE (Sticky Bottom Bar & Bottom Sheet) --}}
@if($activity->is_registration_required)
<div class="mobile-sticky-bar d-lg-none fixed-bottom p-3 shadow-lg">
    <div class="d-flex align-items-center justify-content-between gap-2">
        <div>
            <small class="text-muted d-block text-uppercase fs-10 tracking-wider fw-semibold">Statut actuel</small>
            <span class="fw-bold fs-13 d-flex align-items-center gap-1">
                @if(!$myRegistration)
                    <span class="text-secondary"><i class="mdi mdi-circle-medium"></i> Non Inscrit(e)</span>
                @elseif($isWaitlisted)
                    <span class="text-warning"><i class="mdi mdi-clock-outline"></i> En attente</span>
                @elseif($currentStatusVal === 'PRESENT')
                    <span class="text-success"><i class="mdi mdi-check-circle-outline"></i> Inscrit(e)</span>
                @elseif($currentStatusVal === 'UNCERTAIN')
                    <span class="text-info"><i class="mdi mdi-help-circle-outline"></i> Incertain(e)</span>
                @elseif($currentStatusVal === 'ABSENT_JUSTIFIED')
                    <span class="text-danger"><i class="mdi mdi-close-circle-outline"></i> Absent(e)</span>
                @endif
            </span>
        </div>
        <div>
            @if(!$isLocked)
                <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm fw-semibold d-flex align-items-center gap-1 text-nowrap" data-bs-toggle="offcanvas" data-bs-target="#mobileRegistrationSheet">
                    <i class="mdi mdi-pencil-outline"></i>
                    <span>{{ $myRegistration ? 'Modifier' : 'S\'inscrire' }}</span>
                </button>
            @else
                <button type="button" class="btn btn-secondary rounded-pill px-3 shadow-sm fw-semibold" disabled>
                    <i class="mdi mdi-lock-outline me-1"></i> Fermé
                </button>
            @endif
        </div>
    </div>
</div>

<!-- Mobile Bottom Sheet Offcanvas -->
<div class="offcanvas offcanvas-bottom rounded-top-4 d-lg-none h-auto" tabindex="-1" id="mobileRegistrationSheet" aria-labelledby="mobileRegistrationSheetLabel" style="max-height: 85vh;">
    <div class="bottom-sheet-handle mx-auto mt-2"></div>
    <div class="offcanvas-header border-bottom py-3">
        <div class="d-flex align-items-center gap-2">
            <div class="avatar-xs flex-shrink-0">
                <span class="avatar-title bg-primary-subtle text-primary rounded-circle"><i class="mdi mdi-cog-outline"></i></span>
            </div>
            <h5 class="offcanvas-title fw-bold fs-16" id="mobileRegistrationSheetLabel">Mon Inscription</h5>
        </div>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-3 p-sm-4">
        <form action="{{ $formRoute }}" method="POST" 
              x-data="{ 
                  statusMobile: '{{ old('status', $currentStatusVal ?: 'PRESENT') }}', 
                  isLockedMobile: {{ $isLocked ? 'true' : 'false' }} 
              }">
            @csrf
            @if($formMethod === 'PUT')
                @method('PUT')
            @endif

            <div class="mb-4">
                <label class="form-label fw-bold mb-3">Sélectionnez votre statut :</label>

                <!-- PRESENT option -->
                <div class="mb-2">
                    <input class="status-option-input d-none" 
                           type="radio" 
                           name="status" 
                           id="mob-status-present" 
                           value="PRESENT" 
                           x-model="statusMobile"
                           :disabled="isLockedMobile">
                    <label class="status-option-card d-block w-100" for="mob-status-present">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="fw-bold mb-1 text-success"><i class="mdi mdi-checkbox-marked-circle-outline me-2"></i>Je participe</h6>
                                <small class="text-muted">Je serai présent à cette activité</small>
                            </div>
                            <div class="flex-shrink-0 ms-2" x-show="statusMobile === 'PRESENT'">
                                <span class="badge bg-success rounded-circle p-1"><i class="mdi mdi-check"></i></span>
                            </div>
                        </div>
                    </label>
                </div>

                <!-- UNCERTAIN option -->
                <div class="mb-2">
                    <input class="status-option-input d-none" 
                           type="radio" 
                           name="status" 
                           id="mob-status-uncertain" 
                           value="UNCERTAIN" 
                           x-model="statusMobile"
                           :disabled="isLockedMobile">
                    <label class="status-option-card d-block w-100" for="mob-status-uncertain">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="fw-bold mb-1 text-warning"><i class="mdi mdi-help-circle-outline me-2"></i>Incertain(e)</h6>
                                <small class="text-muted">Je ne suis pas encore sûr(e) d'y assister</small>
                            </div>
                            <div class="flex-shrink-0 ms-2" x-show="statusMobile === 'UNCERTAIN'">
                                <span class="badge bg-warning rounded-circle p-1"><i class="mdi mdi-check"></i></span>
                            </div>
                        </div>
                    </label>
                </div>

                <!-- ABSENT_JUSTIFIED option -->
                <div class="mb-2">
                    <input class="status-option-input d-none" 
                           type="radio" 
                           name="status" 
                           id="mob-status-absent" 
                           value="ABSENT_JUSTIFIED" 
                           x-model="statusMobile"
                           :disabled="isLockedMobile">
                    <label class="status-option-card d-block w-100" for="mob-status-absent">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="fw-bold mb-1 text-danger"><i class="mdi mdi-close-circle-outline me-2"></i>Je n'y vais pas / Absent</h6>
                                <small class="text-muted">Se désinscrire ou déclarer son absence</small>
                            </div>
                            <div class="flex-shrink-0 ms-2" x-show="statusMobile === 'ABSENT_JUSTIFIED'">
                                <span class="badge bg-danger rounded-circle p-1"><i class="mdi mdi-check"></i></span>
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Justification block -->
            <div class="mb-4" x-show="statusMobile === 'ABSENT_JUSTIFIED'" x-transition:enter="fade-in">
                <label for="mob_justification" class="form-label fw-bold">Motif d'absence / désinscription <span class="text-danger">*</span></label>
                <textarea class="form-control" 
                          name="justification" 
                          id="mob_justification" 
                          rows="3" 
                          placeholder="Veuillez renseigner le motif de votre absence (min. 5 caractères)"
                          :required="statusMobile === 'ABSENT_JUSTIFIED'"
                          :disabled="isLockedMobile">{{ old('justification', $myRegistration ? $myRegistration->justification : '') }}</textarea>
            </div>

            @if(!$isLocked)
                <button type="submit" class="btn btn-primary w-100 btn-lg shadow-sm rounded-pill">
                    <i class="mdi mdi-content-save-outline me-1"></i> Enregistrer mon choix
                </button>
            @else
                <button type="button" class="btn btn-secondary w-100 btn-lg shadow-sm rounded-pill" disabled>
                    <i class="mdi mdi-lock-outline me-1"></i> Inscriptions fermées
                </button>
            @endif
        </form>
    </div>
</div>
@endif

<style>
    /* Radio Option Cards */
    .status-option-card {
        border: 2px solid var(--vz-border-color);
        border-radius: 10px;
        padding: 16px;
        cursor: pointer;
        transition: all 0.2s ease-in-out;
        background-color: var(--vz-card-bg, #fff);
    }
    .status-option-card:hover {
        border-color: var(--vz-primary);
        background-color: var(--vz-light);
        transform: translateY(-1px);
    }
    
    /* Checked styles */
    .status-option-input[value="PRESENT"]:checked + .status-option-card {
        border-color: #0ab39c !important;
        background-color: rgba(10, 179, 156, 0.08) !important;
        box-shadow: 0 4px 12px rgba(10, 179, 156, 0.08);
    }
    
    .status-option-input[value="UNCERTAIN"]:checked + .status-option-card {
        border-color: #f7b84b !important;
        background-color: rgba(247, 184, 75, 0.08) !important;
        box-shadow: 0 4px 12px rgba(247, 184, 75, 0.08);
    }
    
    .status-option-input[value="ABSENT_JUSTIFIED"]:checked + .status-option-card {
        border-color: #f06548 !important;
        background-color: rgba(240, 101, 72, 0.08) !important;
        box-shadow: 0 4px 12px rgba(240, 101, 72, 0.08);
    }
    
    /* Disabled cards */
    .status-option-input:disabled + .status-option-card {
        cursor: not-allowed;
        opacity: 0.6;
        background-color: var(--vz-light);
        transform: none !important;
        box-shadow: none !important;
    }
    
    /* Dark mode explicit overrides */
    [data-bs-theme="dark"] .status-option-card {
        background-color: var(--vz-choices-bg);
    }
    [data-bs-theme="dark"] .status-option-card:hover {
        background-color: var(--vz-light);
    }
    
    /* Premium details cards hover micro-animation */
    .info-card-premium {
        transition: all 0.25s ease-in-out;
    }
    .info-card-premium:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    }
    
    /* Mobile App UX Specific Styles */
    .mobile-sticky-bar {
        background: var(--vz-card-bg, #ffffff);
        border-top: 1px solid var(--vz-border-color);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        z-index: 1040;
        box-shadow: 0 -8px 25px rgba(0, 0, 0, 0.08) !important;
    }
    
    [data-bs-theme="dark"] .mobile-sticky-bar {
        background: rgba(33, 37, 41, 0.95);
        border-top-color: var(--vz-border-color);
    }
    
    .bottom-sheet-handle {
        width: 38px;
        height: 4px;
        background-color: var(--vz-border-color);
        border-radius: 2px;
    }
    
    .rounded-top-4 {
        border-top-left-radius: 1.25rem !important;
        border-top-right-radius: 1.25rem !important;
    }
    
    .fs-9 { font-size: 0.6875rem; }
    
    /* Fade animation */
    .fade-in {
        animation: fadeIn 0.3s ease;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(5px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection
