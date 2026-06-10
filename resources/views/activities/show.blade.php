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
    $isLocked = $activity->start_time->subHours(2)->lt(now());
    $hasStarted = $activity->start_time->lte(now());
    
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

<div class="row">
    <!-- Left Column: Activity Details -->
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-4">
                    <div class="flex-grow-1">
                        <span class="badge bg-soft-info text-info fs-12 uppercase p-2 px-3 rounded-pill">{{ $activity->type->label() }}</span>
                    </div>
                    <div class="flex-shrink-0">
                        @if($hasStarted)
                            <span class="badge bg-soft-secondary text-secondary fs-13 p-2 rounded-pill"><i class="mdi mdi-history me-1"></i> Terminée / Commencée</span>
                        @elseif($isLocked)
                            <span class="badge bg-soft-danger text-danger fs-13 p-2 rounded-pill"><i class="mdi mdi-lock-outline me-1"></i> Inscriptions verrouillées</span>
                        @else
                            <span class="badge bg-soft-success text-success fs-13 p-2 rounded-pill"><i class="mdi mdi-clock-outline me-1"></i> Inscriptions ouvertes</span>
                        @endif
                    </div>
                </div>

                <h2 class="text-dark fw-bold mb-3">{{ $activity->title }}</h2>

                <!-- Premium colored details cards grid -->
                <div class="row g-3 my-4">
                    <!-- Date de début -->
                    <div class="col-sm-6 col-xl-3">
                        <div class="card border-0 h-100 rounded-3 info-card-premium bg-primary-subtle border-start border-primary border-3 shadow-none">
                            <div class="card-body p-3 d-flex align-items-center">
                                <div class="avatar-sm flex-shrink-0 me-3">
                                    <span class="avatar-title bg-primary text-white rounded-circle fs-20 shadow-sm">
                                        <i class="ri-calendar-event-line"></i>
                                    </span>
                                </div>
                                <div class="overflow-hidden">
                                    <small class="text-primary d-block uppercase tracking-wider fs-10 fw-semibold">Date de début</small>
                                    <span class="fw-bold text-dark fs-14">{{ $activity->start_time->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Date de fin -->
                    <div class="col-sm-6 col-xl-3">
                        <div class="card border-0 h-100 rounded-3 info-card-premium bg-info-subtle border-start border-info border-3 shadow-none">
                            <div class="card-body p-3 d-flex align-items-center">
                                <div class="avatar-sm flex-shrink-0 me-3">
                                    <span class="avatar-title bg-info text-white rounded-circle fs-20 shadow-sm">
                                        <i class="ri-calendar-check-line"></i>
                                    </span>
                                </div>
                                <div class="overflow-hidden">
                                    <small class="text-info d-block uppercase tracking-wider fs-10 fw-semibold">Date de fin</small>
                                    <span class="fw-bold text-dark fs-14">{{ $activity->end_time->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Lieu -->
                    <div class="col-sm-6 col-xl-3">
                        <div class="card border-0 h-100 rounded-3 info-card-premium bg-success-subtle border-start border-success border-3 shadow-none">
                            <div class="card-body p-3 d-flex align-items-center">
                                <div class="avatar-sm flex-shrink-0 me-3">
                                    <span class="avatar-title bg-success text-white rounded-circle fs-20 shadow-sm">
                                        <i class="ri-map-pin-line"></i>
                                    </span>
                                </div>
                                <div class="overflow-hidden">
                                    <small class="text-success d-block uppercase tracking-wider fs-10 fw-semibold">Lieu</small>
                                    <span class="fw-bold text-dark fs-14 text-truncate d-block" title="{{ $activity->location ?: 'Non spécifié' }}">{{ $activity->location ?: 'Non spécifié' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Capacité -->
                    <div class="col-sm-6 col-xl-3">
                        <div class="card border-0 h-100 rounded-3 info-card-premium bg-warning-subtle border-start border-warning border-3 shadow-none">
                            <div class="card-body p-3 d-flex align-items-center">
                                <div class="avatar-sm flex-shrink-0 me-3">
                                    <span class="avatar-title bg-warning text-white rounded-circle fs-20 shadow-sm">
                                        <i class="ri-team-line"></i>
                                    </span>
                                </div>
                                <div class="overflow-hidden">
                                    <small class="text-warning d-block uppercase tracking-wider fs-10 fw-semibold">Capacité</small>
                                    <span class="fw-bold text-dark fs-14 d-block">
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
                    <h5 class="fw-bold mb-3 text-dark">Description</h5>
                    <div class="text-muted fs-15 leading-relaxed">
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
                            <span class="fw-bold text-dark fs-14">
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

    <!-- Right Column: Registration Status Cockpit -->
    <div class="col-lg-4">
        @can('manage', $activity)
            <div class="card shadow-sm border-0 border-start border-success border-3 mb-4 animate__animated animate__fadeIn">
                <div class="card-header bg-soft-success border-0 py-3">
                    <h5 class="card-title mb-0 fw-bold text-success">
                        <i class="mdi mdi-clipboard-check-outline me-2"></i>Gestion des Présences
                    </h5>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted fs-13">
                        En tant que chef de groupe ou administrateur, vous pouvez gérer les présences en temps réel pour cette activité.
                    </p>
                    <a href="{{ route('activities.attendance.index', $activity) }}" class="btn btn-success w-100 shadow-sm">
                        <i class="mdi mdi-open-in-new me-1"></i> Ouvrir la feuille d'émargement
                    </a>
                </div>
            </div>
        @endcan

        <div class="card shadow-sm border-0">
            <div class="card-header bg-soft-primary border-0 py-3">
                <h5 class="card-title mb-0 fw-bold text-primary"><i class="mdi mdi-cog-outline me-2"></i>Mon Inscription</h5>
            </div>
            
            <div class="card-body p-4">
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
    </div>
</div>

<style>
    /* Radio Option Cards */
    .status-option-card {
        border: 2px solid #eff2f7;
        border-radius: 10px;
        padding: 16px;
        cursor: pointer;
        transition: all 0.2s ease-in-out;
        background-color: #fff;
    }
    .status-option-card:hover {
        border-color: #e2e5ec;
        background-color: #f8f9fa;
        transform: translateY(-1px);
    }
    
    /* Checked styles */
    .status-option-input[value="PRESENT"]:checked + .status-option-card {
        border-color: #0ab39c !important;
        background-color: rgba(10, 179, 156, 0.04) !important;
        box-shadow: 0 4px 12px rgba(10, 179, 156, 0.08);
    }
    
    .status-option-input[value="UNCERTAIN"]:checked + .status-option-card {
        border-color: #f7b84b !important;
        background-color: rgba(247, 184, 75, 0.04) !important;
        box-shadow: 0 4px 12px rgba(247, 184, 75, 0.08);
    }
    
    .status-option-input[value="ABSENT_JUSTIFIED"]:checked + .status-option-card {
        border-color: #f06548 !important;
        background-color: rgba(240, 101, 72, 0.04) !important;
        box-shadow: 0 4px 12px rgba(240, 101, 72, 0.08);
    }
    
    /* Disabled cards */
    .status-option-input:disabled + .status-option-card {
        cursor: not-allowed;
        opacity: 0.6;
        background-color: #f8f9fa;
        transform: none !important;
        box-shadow: none !important;
    }
    
    /* Premium details cards hover micro-animation */
    .info-card-premium {
        transition: all 0.25s ease-in-out;
    }
    .info-card-premium:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    }
    
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
