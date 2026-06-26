@extends('layouts.app')

@section('title', 'Détails du compte utilisateur')

@section('content')
    <div class="profile-foreground position-relative mx-n4 mt-n4">
        <div class="profile-wid-bg">
            <img src="{{ asset('assets/images/profile-bg.jpg') }}" alt="" class="profile-wid-img" />
        </div>
    </div>
    <div class="pt-4 mb-4 mb-lg-3 pb-lg-4 profile-wrapper">
        <div class="row g-4">
            <div class="col-auto">
                <div class="avatar-lg">
                    @if($user->photo)
                        <img src="{{ asset('storage/' . $user->photo) }}" alt="user-img" class="img-thumbnail rounded-circle" style="width: 100%; height: 100%; object-fit: cover;" />
                    @else
                        <div class="img-thumbnail rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white" style="width: 100%; height: 100%; font-size: 32px;">
                            {{ strtoupper(substr($user->first_name, 0, 1) . substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                </div>
            </div>
            <!--end col-->
            <div class="col">
                <div class="p-2">
                    <h3 class="text-white mb-1 text-break">{{ $user->first_name }} {{ $user->name }}</h3>
                    <p class="text-white text-opacity-75">
                        <span class="badge bg-{{ match($user->status->value) {
                            'ACTIVE' => 'success',
                            'PENDING' => 'warning',
                            'INACTIVE' => 'secondary',
                            'SUSPENDED' => 'danger',
                            default => 'primary'
                        } }} fs-12">
                            {{ $user->status->label() }}
                        </span>
                    </p>
                    <div class="hstack flex-wrap text-white-50 gap-2">
                        <div><i class="ri-mail-line me-1 text-white text-opacity-75 fs-16 align-middle"></i>{{ $user->email ?? 'Email non renseigné' }}</div>
                        <div>
                            <i class="ri-phone-line me-1 text-white text-opacity-75 fs-16 align-middle"></i>{{ $user->phone ?? 'Téléphone non renseigné' }}
                        </div>
                    </div>
                </div>
            </div>
            <!--end col-->
            <div class="col-12 col-lg-auto order-last order-lg-0">
                <div class="row text text-white-50 text-center">
                    <div class="col-lg-4 col-4">
                        <div class="p-2">
                            <h4 class="text-white mb-1">{{ $user->groups()->count() }}</h4>
                            <p class="fs-14 mb-0">Groupes</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-4">
                        <div class="p-2">
                            <h4 class="text-white mb-1">{{ $user->registrations()->count() }}</h4>
                            <p class="fs-14 mb-0">Inscriptions</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-4">
                        <div class="p-2">
                            <h4 class="text-white mb-1">{{ $user->attendances()->count() }}</h4>
                            <p class="fs-14 mb-0">Présences</p>
                        </div>
                    </div>
                </div>
            </div>
            <!--end col-->
        </div>
        <!--end row-->
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div>
                <div class="d-flex flex-column flex-sm-row align-items-sm-center profile-wrapper">
                    <!-- Nav tabs -->
                    <ul class="nav nav-pills animation-nav profile-nav gap-2 gap-lg-3 flex-grow-1" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link fs-14 active" data-bs-toggle="tab" href="#overview-tab" role="tab">
                                <i class="ri-airplay-fill d-inline-block d-md-none"></i> <span class="d-none d-md-inline-block">Vue d'ensemble</span>
                            </a>
                        </li>
                    </ul>
                    <div class="flex-shrink-0 d-flex gap-2 mt-3 mt-sm-0">
                        @can('role.manage')
                        <a href="{{ route('admin.users.permissions.edit', $user) }}" class="btn btn-warning w-100 w-sm-auto">
                            <i class="ri-key-line align-bottom"></i> Permissions
                        </a>
                        @endcan
                    </div>
                </div>
                <!-- Tab panes -->
                <div class="tab-content pt-4 text-muted">
                    <div class="tab-pane active" id="overview-tab" role="tabpanel">
                        
                        <div class="row g-4">
                            <!-- Left Column: Personal, Professional, Residence info -->
                            <div class="col-xxl-4 col-xl-5">
                                
                                <!-- Card: Finances & Rôles -->
                                <div class="card ribbon-box border shadow-none mb-4">
                                    <div class="card-body">
                                        <div class="ribbon ribbon-warning round-shape">Finances & Rôles</div>
                                        <div class="mt-4">
                                            <ul class="list-unstyled vstack gap-3 mb-0">
                                                <li>
                                                    <div class="d-flex">
                                                        <div class="flex-shrink-0 avatar-xs">
                                                            <div class="avatar-title rounded bg-warning-subtle text-warning">
                                                                <i class="ri-money-dollar-circle-line"></i>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1 ms-3">
                                                            <h6 class="mb-1 fs-14">Cotisation hebdomadaire</h6>
                                                            <p class="text-muted mb-0">{{ $user->weekly_contribution ? number_format($user->weekly_contribution, 0, ',', ' ') . ' FCFA' : 'Non renseignée' }}</p>
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="d-flex">
                                                        <div class="flex-shrink-0 avatar-xs">
                                                            <div class="avatar-title rounded bg-success-subtle text-success">
                                                                <i class="ri-wallet-3-line"></i>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1 ms-3">
                                                            <h6 class="mb-1 fs-14">Avancement des cotisations</h6>
                                                            <p class="text-muted mb-1">
                                                                <strong>{{ number_format($paidContribution, 0, ',', ' ') }} FCFA</strong> / {{ number_format($expectedContribution, 0, ',', ' ') }} FCFA
                                                            </p>
                                                            @php
                                                                $percent = $expectedContribution > 0 ? min(100, round(($paidContribution / $expectedContribution) * 100)) : 0;
                                                            @endphp
                                                            <div class="progress animated-progress" style="height: 6px;">
                                                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percent }}%" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="d-flex">
                                                        <div class="flex-shrink-0 avatar-xs">
                                                            <div class="avatar-title rounded bg-info-subtle text-info">
                                                                <i class="ri-shield-user-line"></i>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1 ms-3">
                                                            <h6 class="mb-1 fs-14">Rôles attribués</h6>
                                                            <div class="d-flex flex-wrap gap-1 mt-1">
                                                                @forelse($user->getRoleNames() as $role)
                                                                    <span class="badge bg-info-subtle text-info">{{ $role }}</span>
                                                                @empty
                                                                    <span class="text-muted">Aucun rôle</span>
                                                                @endforelse
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <!-- Card: Informations Personnelles -->
                                <div class="card ribbon-box border shadow-none mb-4">
                                    <div class="card-body">
                                        <div class="ribbon ribbon-primary round-shape">Infos Personnelles</div>
                                        <div class="mt-4">
                                            <ul class="list-unstyled vstack gap-2 mb-0">
                                                <li>
                                                    <div class="d-flex">
                                                        <div class="flex-shrink-0 avatar-xs">
                                                            <div class="avatar-title rounded bg-primary-subtle text-primary">
                                                                <i class="ri-user-3-line"></i>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1 ms-3">
                                                            <h6 class="mb-1 fs-14">Nom complet</h6>
                                                            <p class="text-muted mb-0">{{ $user->full_name }}</p>
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="d-flex">
                                                        <div class="flex-shrink-0 avatar-xs">
                                                            <div class="avatar-title rounded bg-success-subtle text-success">
                                                                <i class="ri-calendar-event-line"></i>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1 ms-3">
                                                            <h6 class="mb-1 fs-14">Date de naissance</h6>
                                                            <p class="text-muted mb-0">{{ $user->birth_date?->format('d/m/Y') ?? 'Non renseignée' }}</p>
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="d-flex">
                                                        <div class="flex-shrink-0 avatar-xs">
                                                            <div class="avatar-title rounded bg-info-subtle text-info">
                                                                <i class="ri-men-line"></i>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1 ms-3">
                                                            <h6 class="mb-1 fs-14">Sexe</h6>
                                                            <p class="text-muted mb-0">
                                                                @if($user->gender === 'M')
                                                                    Masculin
                                                                @elseif($user->gender === 'F')
                                                                    Féminin
                                                                @else
                                                                    Non renseigné
                                                                @endif
                                                            </p>
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <!-- Card: Académique & Pro -->
                                <div class="card ribbon-box border shadow-none mb-4">
                                    <div class="card-body">
                                        <div class="ribbon ribbon-success round-shape">Académique & Pro</div>
                                        <div class="mt-4">
                                            <ul class="list-unstyled vstack gap-2 mb-0">
                                                <li>
                                                    <div class="d-flex">
                                                        <div class="flex-shrink-0 avatar-xs">
                                                            <div class="avatar-title rounded bg-secondary-subtle text-secondary">
                                                                <i class="ri-book-open-line"></i>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1 ms-3">
                                                            <h6 class="mb-1 fs-14">Niveau d'études</h6>
                                                            <p class="text-muted mb-0">{{ $user->education_level ?? 'Non renseigné' }}</p>
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="d-flex">
                                                        <div class="flex-shrink-0 avatar-xs">
                                                            <div class="avatar-title rounded bg-secondary-subtle text-secondary">
                                                                <i class="ri-microscope-line"></i>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1 ms-3">
                                                            <h6 class="mb-1 fs-14">Domaine d'études</h6>
                                                            <p class="text-muted mb-0">{{ $user->education_field ?? 'Non renseigné' }}</p>
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="d-flex">
                                                        <div class="flex-shrink-0 avatar-xs">
                                                            <div class="avatar-title rounded bg-warning-subtle text-warning">
                                                                <i class="ri-briefcase-line"></i>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1 ms-3">
                                                            <h6 class="mb-1 fs-14">Statut professionnel</h6>
                                                            <p class="text-muted mb-0">{{ $user->professional_status ?? 'Non renseigné' }}</p>
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="d-flex">
                                                        <div class="flex-shrink-0 avatar-xs">
                                                            <div class="avatar-title rounded bg-warning-subtle text-warning">
                                                                <i class="ri-building-4-line"></i>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1 ms-3">
                                                            <h6 class="mb-1 fs-14">Profession actuelle</h6>
                                                            <p class="text-muted mb-0">{{ $user->current_profession ?? 'Non renseigné' }}</p>
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="d-flex">
                                                        <div class="flex-shrink-0 avatar-xs">
                                                            <div class="avatar-title rounded bg-primary-subtle text-primary">
                                                                <i class="ri-church-line"></i>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1 ms-3">
                                                            <h6 class="mb-1 fs-14">Service à l'église</h6>
                                                            <p class="text-muted mb-0">{{ $user->church_service ?? 'Non renseigné' }}</p>
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <!-- Card: Résidence -->
                                <div class="card ribbon-box border shadow-none mb-4">
                                    <div class="card-body">
                                        <div class="ribbon ribbon-info round-shape">Résidence</div>
                                        <div class="mt-4">
                                            <ul class="list-unstyled vstack gap-2 mb-0">
                                                <li>
                                                    <div class="d-flex">
                                                        <div class="flex-shrink-0 avatar-xs">
                                                            <div class="avatar-title rounded bg-info-subtle text-info">
                                                                <i class="ri-map-pin-line"></i>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1 ms-3">
                                                            <h6 class="mb-1 fs-14">Commune</h6>
                                                            <p class="text-muted mb-0">{{ $user->residence_municipality ?? 'Non renseignée' }}</p>
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="d-flex">
                                                        <div class="flex-shrink-0 avatar-xs">
                                                            <div class="avatar-title rounded bg-info-subtle text-info">
                                                                <i class="ri-community-line"></i>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1 ms-3">
                                                            <h6 class="mb-1 fs-14">Quartier</h6>
                                                            <p class="text-muted mb-0">{{ $user->residence_neighborhood ?? 'Non renseigné' }}</p>
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                @php
                                    $additionalInfos = is_string($user->additional_info) ? json_decode($user->additional_info, true) : $user->additional_info;
                                @endphp
                                @if(!empty($additionalInfos) && is_array($additionalInfos))
                                <!-- Card: Informations Complémentaires -->
                                <div class="card ribbon-box border shadow-none mb-4">
                                    <div class="card-body">
                                        <div class="ribbon ribbon-dark round-shape">Notes & Remarques</div>
                                        <div class="mt-4">
                                            <ul class="list-unstyled vstack gap-2 mb-0">
                                                @foreach($additionalInfos as $info)
                                                    @if(is_array($info))
                                                    <li>
                                                        <div class="d-flex">
                                                            <div class="flex-shrink-0 avatar-xs">
                                                                <div class="avatar-title rounded bg-light text-dark border">
                                                                    <i class="ri-information-line"></i>
                                                                </div>
                                                            </div>
                                                            <div class="flex-grow-1 ms-3">
                                                                <h6 class="mb-1 fs-14">{{ $info['title'] ?? 'Info' }}</h6>
                                                                <p class="text-muted mb-0">{{ $info['value'] ?? '' }}</p>
                                                            </div>
                                                        </div>
                                                    </li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                @endif

                            </div>
                            <!--end left col-->

                            <!-- Right Column: Groups and Attendances -->
                            <div class="col-xxl-8 col-xl-7">
                                
                                <!-- Card: Groupes -->
                                <div class="card">
                                    <div class="card-header align-items-center d-flex border-bottom-dashed">
                                        <h4 class="card-title mb-0 flex-grow-1">Groupes d'appartenance</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive table-card">
                                            <table class="table table-borderless table-nowrap align-middle mb-0">
                                                <thead class="table-light text-muted">
                                                    <tr>
                                                        <th scope="col">Nom du groupe</th>
                                                        <th scope="col">Rôle</th>
                                                        <th scope="col">Date d'adhésion</th>
                                                        <th scope="col">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $memberGroups = $user->groups->keyBy('id');
                                                        $ledGroups = $user->ledGroups->keyBy('id');
                                                        $allGroups = $memberGroups->merge($ledGroups);
                                                    @endphp

                                                    @forelse($allGroups as $group)
                                                    @php
                                                        $isLeader = $ledGroups->has($group->id);
                                                        $isMember = $memberGroups->has($group->id);
                                                        
                                                        // Get pivot from memberGroups collection since merge might overwrite with ledGroups (which lacks pivot)
                                                        $originalGroup = $memberGroups->get($group->id);
                                                        $joinedAt = $originalGroup && $originalGroup->pivot && $originalGroup->pivot->joined_at 
                                                                    ? \Carbon\Carbon::parse($originalGroup->pivot->joined_at)->format('d M Y') 
                                                                    : '-';
                                                    @endphp
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div class="avatar-xs me-2">
                                                                    <div class="avatar-title bg-primary-subtle text-primary rounded-circle fs-16">
                                                                        {{ substr($group->name, 0, 1) }}
                                                                    </div>
                                                                </div>
                                                                <h6 class="fs-14 mb-0">{{ $group->name }}</h6>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            @if($isLeader)
                                                                <span class="badge bg-success-subtle text-success">Leader</span>
                                                            @endif
                                                            @if($isMember && !$isLeader)
                                                                <span class="badge bg-secondary-subtle text-secondary">Membre</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ $joinedAt }}</td>
                                                        <td>
                                                            <a href="{{ route('admin.groups.show', $group->id) }}" class="btn btn-sm btn-soft-info" data-bs-toggle="tooltip" title="Voir le groupe">
                                                                <i class="ri-eye-fill"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    @empty
                                                    <tr>
                                                        <td colspan="4" class="text-center p-4">
                                                            <div class="text-muted">
                                                                <i class="ri-group-line display-5 text-muted mb-3 d-block"></i>
                                                                L'utilisateur n'appartient à aucun groupe.
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <!-- end card groupes -->

                                <!-- Card: Activités -->
                                <div class="card">
                                    <div class="card-header align-items-center d-flex border-bottom-dashed">
                                        <h4 class="card-title mb-0 flex-grow-1">Présences aux activités</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive table-card">
                                            <table class="table table-borderless table-nowrap align-middle mb-0">
                                                <thead class="table-light text-muted">
                                                    <tr>
                                                        <th scope="col">Activité</th>
                                                        <th scope="col">Date scannée</th>
                                                        <th scope="col">Statut</th>
                                                        <th scope="col">Source</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $attendances = $user->attendances()->with('activity')->orderBy('scanned_at', 'desc')->get();
                                                    @endphp
                                                    @forelse($attendances as $attendance)
                                                    <tr>
                                                        <td class="text-wrap" style="max-width: 250px;">
                                                            <h6 class="fs-14 mb-0">{{ $attendance->activity->title ?? 'Activité inconnue' }}</h6>
                                                            <p class="text-muted fs-12 mb-0">{{ $attendance->activity?->start_date?->format('d M Y') ?? '' }}</p>
                                                        </td>
                                                        <td>{{ $attendance->scanned_at?->format('d M Y à H:i') ?? '-' }}</td>
                                                        <td>
                                                            @if($attendance->status->value === 'PRESENT')
                                                                <span class="badge bg-success-subtle text-success"><i class="ri-check-line align-bottom"></i> Présent</span>
                                                            @elseif($attendance->status->value === 'ABSENT')
                                                                <span class="badge bg-danger-subtle text-danger"><i class="ri-close-line align-bottom"></i> Absent</span>
                                                            @elseif($attendance->status->value === 'LATE')
                                                                <span class="badge bg-warning-subtle text-warning"><i class="ri-time-line align-bottom"></i> En retard</span>
                                                            @else
                                                                <span class="badge bg-secondary-subtle text-secondary">{{ $attendance->status->label() }}</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-light text-body">{{ $attendance->scan_source ?? 'N/A' }}</span>
                                                        </td>
                                                    </tr>
                                                    @empty
                                                    <tr>
                                                        <td colspan="4" class="text-center p-4">
                                                            <div class="text-muted">
                                                                <i class="ri-calendar-event-line display-5 text-muted mb-3 d-block"></i>
                                                                Aucune présence enregistrée.
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <!-- end card activités -->

                            </div>
                            <!--end right col-->
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <!--end col-->
    </div>
    <!--end row-->
@endsection
