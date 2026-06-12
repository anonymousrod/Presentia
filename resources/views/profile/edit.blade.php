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
            @if($user->photo)
                <img src="{{ asset('storage/' . $user->photo) }}" alt="user-img" class="img-thumbnail rounded-circle avatar-lg" style="object-fit: cover;" />
            @else
                <img src="{{ asset('assets/images/users/avatar-1.jpg') }}" alt="user-img" class="img-thumbnail rounded-circle avatar-lg" style="object-fit: cover;" />
            @endif
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
                <div class="col-lg-6 col-6">
                    <div class="p-2">
                        <h4 class="text-white mb-1">{{ $user->registrations()->count() }}</h4>
                        <p class="fs-14 mb-0">Inscriptions</p>
                    </div>
                </div>
                <div class="col-lg-6 col-6">
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
                            @if($user->photo)
                                <img src="{{ asset('storage/' . $user->photo) }}" class="rounded-circle avatar-xl img-thumbnail user-profile-image shadow" alt="user-profile-image" style="object-fit: cover;">
                            @else
                                <img src="{{ asset('assets/images/users/avatar-1.jpg') }}" class="rounded-circle avatar-xl img-thumbnail user-profile-image shadow" alt="user-profile-image" style="object-fit: cover;">
                            @endif
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
@endsection
