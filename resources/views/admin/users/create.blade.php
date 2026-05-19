@extends('layouts.app')

@section('title', 'Créer un compte utilisateur')

@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Création de Compte Utilisateur</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Création d'Utilisateur</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row justify-content-center">
        <div class="col-xxl-8 col-lg-10">
            <div class="card mt-2">
                <div class="card-header align-items-center d-flex border-bottom-dashed">
                    <h4 class="card-title mb-0 flex-grow-1">Informations du Nouvel Utilisateur</h4>
                    <div class="flex-shrink-0">
                        <span class="badge bg-warning-subtle text-warning">Statut par défaut : PENDING</span>
                    </div>
                </div><!-- end card header -->

                <div class="card-body p-4">
                    @if (session('success'))
                        <div class="alert alert-success alert-border-left alert-dismissible fade show" role="alert">
                            <i class="ri-check-double-line me-3 align-middle fs-16"></i>
                            <strong>Succès !</strong> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger alert-border-left alert-dismissible fade show" role="alert">
                            <i class="ri-error-warning-line me-3 align-middle fs-16"></i>
                            <strong>Veuillez corriger les erreurs suivantes :</strong>
                            <ul class="mt-2 mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('admin.users.store') }}" method="POST">
                        @csrf

                        <div class="row g-3">
                            <!-- Prénom -->
                            <div class="col-md-6">
                                <label for="first_name" class="form-label">Prénom <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ri-user-3-line"></i></span>
                                    <input type="text" class="form-control @error('first_name') is-invalid @enderror" 
                                           id="first_name" name="first_name" value="{{ old('first_name') }}" 
                                           placeholder="Ex : Exaucé" required>
                                </div>
                                @error('first_name')
                                    <div class="text-danger mt-1 fs-12">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Nom de famille -->
                            <div class="col-md-6">
                                <label for="name" class="form-label">Nom <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ri-user-fill"></i></span>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" 
                                           placeholder="Ex : NDOUNA" required>
                                </div>
                                @error('name')
                                    <div class="text-danger mt-1 fs-12">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Adresse Email (Optionnelle si Téléphone présent) -->
                            <div class="col-md-6">
                                <label for="email" class="form-label">Adresse Email</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ri-mail-line"></i></span>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email') }}" 
                                           placeholder="Ex : contact@presentia.org">
                                </div>
                                <div class="form-text text-muted">Obligatoire si le numéro de téléphone n'est pas renseigné.</div>
                                @error('email')
                                    <div class="text-danger mt-1 fs-12">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Téléphone (Optionnel si Email présent) -->
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Numéro de Téléphone (avec code pays)</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ri-phone-line"></i></span>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" name="phone" value="{{ old('phone') }}" 
                                           placeholder="Ex : +22990000000">
                                </div>
                                <div class="form-text text-muted">Obligatoire si l'adresse email n'est pas renseignée.</div>
                                @error('phone')
                                    <div class="text-danger mt-1 fs-12">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Date de naissance (Optionnelle) -->
                            <div class="col-md-12">
                                <label for="birth_date" class="form-label">Date de Naissance</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ri-calendar-event-line"></i></span>
                                    <input type="date" class="form-control @error('birth_date') is-invalid @enderror" 
                                           id="birth_date" name="birth_date" value="{{ old('birth_date') }}">
                                </div>
                                @error('birth_date')
                                    <div class="text-danger mt-1 fs-12">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Info box about passwords -->
                        <div class="alert alert-info border-0 mt-4 mb-4" role="alert">
                            <h5 class="alert-heading fs-14 fw-bold"><i class="ri-information-line me-2 align-middle"></i> Génération automatique des identifiants</h5>
                            <p class="mb-0 fs-13 mt-1">
                                Le système générera automatiquement un mot de passe temporaire fort et sécurisé (10 caractères alphanumériques).
                                <br>
                                • Si l'email est fourni, les identifiants seront envoyés par <strong>Email</strong> (prioritaire).
                                <br>
                                • Si seul le téléphone est fourni, les identifiants seront envoyés par <strong>WhatsApp</strong>.
                            </p>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('dashboard') }}" class="btn btn-light">Annuler</a>
                            <button type="submit" class="btn btn-primary btn-load">
                                <span class="d-flex align-items-center">
                                    <i class="ri-user-add-line me-1"></i> Créer le compte
                                </span>
                            </button>
                        </div>
                    </form>
                </div><!-- end card-body -->
            </div><!-- end card -->
        </div><!-- end col -->
    </div><!-- end row -->
@endsection
