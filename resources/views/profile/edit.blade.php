@extends('layouts.app')

@section('title', 'Profil & Paramètres')

@section('content')
{{-- Cover image with overlay for the Change Cover button (exactly like Velzon) --}}<div class="profile-foreground position-relative mx-n4 mt-n4">
    <div class="profile-wid-bg">
        @if($user->cover_photo)
            <img src="{{ asset('storage/' . $user->cover_photo) }}" alt="cover-img" class="profile-wid-img" style="object-fit: cover;" />
        @else
            <img src="{{ asset('assets/images/profile-bg.jpg') }}" alt="cover-img" class="profile-wid-img" style="object-fit: cover;" />
        @endif
    </div>
</div>

<div class="pt-4 mb-4 mb-lg-3 pb-lg-4 profile-wrapper">
    <div class="row g-4 align-items-center">
        <div class="col-auto">
            <img src="{{ $user->avatar_url }}" alt="user-img" class="img-thumbnail rounded-circle avatar-lg" style="object-fit: cover;" />
        </div>
        <div class="col">
            <div class="p-2">
                <h3 class="text-white mb-1">{{ $user->full_name }}</h3>
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
                    <span class="ms-2">{{ $user->roles->first()?->name ?? 'Membre' }}</span>
                </p>
                <div class="hstack text-white-50 gap-1">
                    <div class="me-2"><i class="ri-mail-line me-1 text-white text-opacity-75 fs-16 align-middle"></i>{{ $user->email ?? 'Email non renseigné' }}</div>
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
            <div class="d-flex profile-wrapper">
                <ul class="nav nav-pills animation-nav profile-nav gap-2 gap-lg-3 flex-grow-1" role="tablist">
                    <!-- Vide pour pousser le bouton à droite -->
                </ul>
                <div class="flex-shrink-0 d-flex gap-2">
                    <form id="cover-form" action="{{ route('profile.cover') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input id="profile-foreground-img-file-input" name="cover_photo" type="file" class="d-none" onchange="document.getElementById('cover-form').submit();">
                        <label for="profile-foreground-img-file-input" class="btn btn-success">
                            <i class="ri-image-edit-line align-bottom me-1"></i> Modifier la couverture
                        </label>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row pt-4">
    <div class="col-xxl-3">
        <div class="card">
            <div class="card-body p-4">
                <div class="text-center">
                    <form id="avatar-form" action="{{ route('profile.avatar') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="profile-user position-relative d-inline-block mx-auto mb-4">
                            <img src="{{ $user->avatar_url }}" class="rounded-circle avatar-xl img-thumbnail user-profile-image shadow" alt="user-profile-image" style="object-fit: cover;">
                            <div class="avatar-xs p-0 rounded-circle profile-photo-edit">
                                <input id="profile-img-file-input" name="photo" type="file" class="profile-img-file-input d-none" onchange="document.getElementById('avatar-form').submit();">
                                <label for="profile-img-file-input" class="profile-photo-edit avatar-xs">
                                    <span class="avatar-title rounded-circle bg-light text-body shadow">
                                        <i class="ri-camera-fill"></i>
                                    </span>
                                </label>
                            </div>
                        </div>
                    </form>
                    <h5 class="fs-16 mb-1">{{ $user->full_name }}</h5>
                    <p class="text-muted mb-0">{{ $user->roles->first()?->name ?? 'Membre' }}</p>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center mb-4">
                    <div class="flex-grow-1">
                        <h5 class="card-title mb-0">Complétez votre profil</h5>
                    </div>
                </div>
                <div class="progress animated-progress custom-progress progress-label">
                    <div class="progress-bar {{ $completionPercentage == 100 ? 'bg-success' : 'bg-primary' }}" role="progressbar" style="width: {{ $completionPercentage }}%" aria-valuenow="{{ $completionPercentage }}" aria-valuemin="0" aria-valuemax="100">
                        <div class="label">{{ $completionPercentage }}%</div>
                    </div>
                </div>
                <p class="text-muted mt-2 mb-0 fs-13">Complétez toutes vos informations pour atteindre 100%.</p>
            </div>
        </div>

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
    <!--end col-->
    
    <div class="col-xxl-9">
        <div class="card">
            @php
                $showPasswordTab = $errors->has('current_password') || $errors->has('password') || session('status') === 'password-updated';
            @endphp
            <div class="card-header">
                <ul class="nav nav-tabs-custom rounded card-header-tabs border-bottom-0" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link {{ !$showPasswordTab ? 'active' : '' }}" data-bs-toggle="tab" href="#personalDetails" role="tab">
                            <i class="fas fa-home"></i> Informations Personnelles
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#groups" role="tab">
                            <i class="fas fa-users"></i> Mes Groupes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#activities" role="tab">
                            <i class="fas fa-calendar-check"></i> Mes Activités
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $showPasswordTab ? 'active' : '' }}" data-bs-toggle="tab" href="#changePassword" role="tab">
                            <i class="far fa-user"></i> Changer le mot de passe
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body p-4">
                <div class="tab-content">
                    <div class="tab-pane {{ !$showPasswordTab ? 'active' : '' }}" id="personalDetails" role="tabpanel">
                        @if (session('status') === 'profile-updated')
                            <div class="alert alert-success">
                                Profil mis à jour avec succès.
                            </div>
                        @endif

                        @if (session('status') === 'avatar-updated')
                            <div class="alert alert-success">
                                Photo de profil mise à jour avec succès.
                            </div>
                        @endif

                        @if (session('status') === 'cover-updated')
                            <div class="alert alert-success">
                                Photo de couverture mise à jour avec succès.
                            </div>
                        @endif

                        <form action="{{ route('profile.update') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="first_name" class="form-label">Prénom</label>
                                        <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name" placeholder="Votre prénom" value="{{ old('first_name', $user->first_name) }}" required>
                                        @error('first_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Nom</label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="Votre nom" value="{{ old('name', $user->name) }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Adresse Email</label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="Votre email" value="{{ old('email', $user->email) }}" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Numéro de Téléphone</label>
                                        <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" placeholder="Votre numéro de téléphone" value="{{ old('phone', $user->phone) }}">
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="birth_date" class="form-label">Date de Naissance</label>
                                        <input type="date" class="form-control @error('birth_date') is-invalid @enderror" id="birth_date" name="birth_date" value="{{ old('birth_date', $user->birth_date?->format('Y-m-d')) }}">
                                        @error('birth_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12 mt-3 mb-2">
                                    <h6 class="fw-bold">Informations Démographiques et Professionnelles</h6>
                                    <hr class="mt-1 mb-3">
                                </div>

                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="education_level" class="form-label">Niveau d'études</label>
                                        <input type="text" class="form-control @error('education_level') is-invalid @enderror" id="education_level" name="education_level" placeholder="Ex: Licence, Master, etc." value="{{ old('education_level', $user->education_level) }}">
                                        @error('education_level')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="education_field" class="form-label">Formation ou domaine d’études</label>
                                        <input type="text" class="form-control @error('education_field') is-invalid @enderror" id="education_field" name="education_field" placeholder="Ex: Informatique, Droit, etc." value="{{ old('education_field', $user->education_field) }}">
                                        @error('education_field')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="professional_status" class="form-label">Situation professionnelle actuelle</label>
                                        <input type="text" class="form-control @error('professional_status') is-invalid @enderror" id="professional_status" name="professional_status" placeholder="Ex: Étudiant, Employé, Sans emploi" value="{{ old('professional_status', $user->professional_status) }}">
                                        @error('professional_status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="current_profession" class="form-label">Profession actuelle</label>
                                        <input type="text" class="form-control @error('current_profession') is-invalid @enderror" id="current_profession" name="current_profession" placeholder="Ex: Développeur, Comptable" value="{{ old('current_profession', $user->current_profession) }}">
                                        @error('current_profession')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="residence_municipality" class="form-label">Commune de résidence</label>
                                        <input type="text" class="form-control @error('residence_municipality') is-invalid @enderror" id="residence_municipality" name="residence_municipality" placeholder="Commune" value="{{ old('residence_municipality', $user->residence_municipality) }}">
                                        @error('residence_municipality')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="residence_neighborhood" class="form-label">Quartier de résidence</label>
                                        <input type="text" class="form-control @error('residence_neighborhood') is-invalid @enderror" id="residence_neighborhood" name="residence_neighborhood" placeholder="Quartier" value="{{ old('residence_neighborhood', $user->residence_neighborhood) }}">
                                        @error('residence_neighborhood')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <label for="church_service" class="form-label">Service au sein de l'église</label>
                                        <input type="text" class="form-control @error('church_service') is-invalid @enderror" id="church_service" name="church_service" placeholder="Ex: Chorale, Intercession, etc." value="{{ old('church_service', $user->church_service) }}">
                                        @error('church_service')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-12 mt-3">
                                    <div class="hstack gap-2 justify-content-end">
                                        <button type="submit" class="btn btn-primary">Mettre à jour</button>
                                        <a href="{{ route('dashboard') }}" class="btn btn-soft-success">Annuler</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!--end tab-pane-->

                    <div class="tab-pane" id="groups" role="tabpanel">
                        <h5 class="card-title mb-4">Mes Groupes d'appartenance</h5>
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
                                                Vous n'appartenez à aucun groupe pour le moment.
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!--end tab-pane-->

                    <div class="tab-pane" id="activities" role="tabpanel">
                        <h5 class="card-title mb-4">Mes Présences aux Activités</h5>
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
                    <!--end tab-pane-->
                    
                    <div class="tab-pane {{ $showPasswordTab ? 'active' : '' }}" id="changePassword" role="tabpanel">
                        @if (session('status') === 'password-updated')
                            <div class="alert alert-success">
                                Mot de passe modifié avec succès.
                            </div>
                        @endif

                        <form action="{{ route('profile.password') }}" method="POST">
                            @csrf
                            <div class="row g-2">
                                <div class="col-lg-4">
                                    <div>
                                        <label for="current_password" class="form-label">Ancien mot de passe*</label>
                                        <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" placeholder="Ancien mot de passe" required>
                                        @error('current_password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div>
                                        <label for="password" class="form-label">Nouveau mot de passe*</label>
                                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Nouveau mot de passe" required>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div>
                                        <label for="password_confirmation" class="form-label">Confirmer le mot de passe*</label>
                                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Confirmer le mot de passe" required>
                                    </div>
                                </div>
                                <div class="col-lg-12 mt-4">
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-success">Changer le mot de passe</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!--end tab-pane-->
                </div>
            </div>
        </div>
    </div>
    <!--end col-->
</div>
<!--end row-->

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var hash = window.location.hash;
        if (hash) {
            var triggerEl = document.querySelector('ul.nav a[href="' + hash + '"]');
            if (triggerEl) {
                var tab = new bootstrap.Tab(triggerEl);
                tab.show();
            }
        }
    });
</script>
@endpush

@endsection
