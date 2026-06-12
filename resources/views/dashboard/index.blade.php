@extends('layouts.app')

@section('title', 'Tableau de Bord')

@section('content')
    <div class="row">
        <div class="col">

            <div class="h-100">
                <div class="row mb-3 pb-1">
                    <div class="col-12">
                        <div class="d-flex align-items-lg-center flex-lg-row flex-column">
                            <div class="flex-grow-1">
                                <h4 class="fs-16 mb-1">Bonjour, {{ auth()->user()->first_name }} !</h4>
                                <p class="text-muted mb-0">Voici un résumé de l'activité sur Presentia aujourd'hui.</p>
                            </div>
                        </div><!-- end card header -->
                    </div>
                    <!--end col-->
                </div>
                <!--end row-->

                <div class="row">
                    <div class="col-12 col-md-6 col-xl-3">
                        <!-- card -->
                        <div class="card card-animate">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Total Membres</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value" data-target="{{ $stats['total_users'] }}">0</span></h4>
                                        <a href="{{ route('admin.users.index') }}" class="text-decoration-underline">Voir les membres</a>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-success-subtle text-success rounded fs-3">
                                            <i class="bx bx-user-circle"></i>
                                        </span>
                                    </div>
                                </div>
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div><!-- end col -->

                    <div class="col-12 col-md-6 col-xl-3">
                        <!-- card -->
                        <div class="card card-animate">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Total Activités</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value" data-target="{{ $stats['total_activities'] }}">0</span></h4>
                                        <a href="{{ route('activities.index') }}" class="text-decoration-underline">Toutes les activités</a>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-info-subtle text-info rounded fs-3">
                                            <i class="bx bx-calendar"></i>
                                        </span>
                                    </div>
                                </div>
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div><!-- end col -->

                    <div class="col-12 col-md-6 col-xl-3">
                        <!-- card -->
                        <div class="card card-animate">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Activités à venir</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value" data-target="{{ $stats['upcoming_activities'] }}">0</span></h4>
                                        <a href="{{ route('activities.index', ['status_filter' => 'upcoming']) }}" class="text-decoration-underline">Voir à venir</a>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-warning-subtle text-warning rounded fs-3">
                                            <i class="bx bx-time-five"></i>
                                        </span>
                                    </div>
                                </div>
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div><!-- end col -->

                    <div class="col-12 col-md-6 col-xl-3">
                        <!-- card -->
                        <div class="card card-animate">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Groupes</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value" data-target="{{ $stats['total_groups'] }}">0</span></h4>
                                        <a href="{{ route('admin.groups.index') }}" class="text-decoration-underline">Gérer les groupes</a>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-primary-subtle text-primary rounded fs-3">
                                            <i class="bx bx-group"></i>
                                        </span>
                                    </div>
                                </div>
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div><!-- end col -->
                </div> <!-- end row-->

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header align-items-center d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">Activités Récentes</h4>
                            </div><!-- end card header -->

                            <div class="card-body">
                                <!-- Mobile View (Cards) -->
                                <div class="d-md-none">
                                    @forelse($recent_activities as $activity)
                                        <div class="card border mb-3 shadow-none">
                                            <div class="card-body p-3">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <h5 class="fs-14 mb-0 text-truncate">{{ $activity->title }}</h5>
                                                    <span class="badge {{ $activity->status === 'En cours' ? 'bg-success' : ($activity->status === 'Terminée' ? 'bg-secondary' : 'bg-warning') }}">
                                                        {{ $activity->status }}
                                                    </span>
                                                </div>
                                                <div class="mb-2 text-muted fs-12">
                                                    <i class="ri-calendar-event-line align-bottom me-1"></i> 
                                                    {{ \Carbon\Carbon::parse($activity->start_time)->format('d/m/Y H:i') }}
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center mt-3">
                                                    <span class="text-muted fs-12"><i class="ri-map-pin-line align-bottom me-1"></i> {{ $activity->location ?? 'Non défini' }}</span>
                                                    <a href="{{ route('activities.show', $activity) }}" class="btn btn-sm btn-soft-primary">Détails</a>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center py-3 text-muted">Aucune activité récente.</div>
                                    @endforelse
                                </div>

                                <!-- Desktop View (Table) -->
                                <div class="table-responsive table-card d-none d-md-block">
                                    <table class="table table-hover table-centered align-middle table-nowrap mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Titre</th>
                                                <th>Date de début</th>
                                                <th>Date de fin</th>
                                                <th>Lieu</th>
                                                <th>Statut</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($recent_activities as $activity)
                                                <tr>
                                                    <td>
                                                        <h5 class="fs-14 my-1"><a href="{{ route('activities.show', $activity) }}" class="text-reset">{{ $activity->title }}</a></h5>
                                                        <span class="text-muted">{{ Str::limit($activity->description, 30) }}</span>
                                                    </td>
                                                    <td>
                                                        <h5 class="fs-14 my-1 fw-normal">{{ \Carbon\Carbon::parse($activity->start_time)->format('d M Y H:i') }}</h5>
                                                    </td>
                                                    <td>
                                                        <h5 class="fs-14 my-1 fw-normal">{{ \Carbon\Carbon::parse($activity->end_time)->format('d M Y H:i') }}</h5>
                                                    </td>
                                                    <td>
                                                        <h5 class="fs-14 my-1 fw-normal">{{ $activity->location ?? 'Non spécifié' }}</h5>
                                                    </td>
                                                    <td>
                                                        <span class="badge {{ $activity->status === 'En cours' ? 'bg-success' : ($activity->status === 'Terminée' ? 'bg-secondary' : 'bg-warning') }}">
                                                            {{ $activity->status }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('activities.show', $activity) }}" class="btn btn-sm btn-soft-primary">Voir détails</a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center">Aucune activité récente.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div> <!-- end .h-100-->

        </div> <!-- end col -->
    </div> <!-- end row -->
@endsection