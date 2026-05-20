@extends('layouts.app')

@section('title', 'Modifier le compte utilisateur')

@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Modifier l'Utilisateur</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Membres</a></li>
                        <li class="breadcrumb-item active">Modifier</li>
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
                    <h4 class="card-title mb-0 flex-grow-1">Informations de l'Utilisateur</h4>
                </div>

                <div class="card-body p-4">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>Succès !</strong> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Veuillez corriger les erreurs suivantes :</strong>
                            <ul class="mt-2 mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('admin.users.update', $user) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <!-- Photo de profil -->
                            <div class="col-12 mb-3">
                                <label class="form-label">Photo de profil (Max 1 Mo, jpeg, png, jpg)</label>
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        @if($user->photo)
                                            <img src="{{ asset('storage/' . $user->photo) }}" alt="Photo" class="rounded-circle border" width="80" height="80" style="object-fit: cover;">
                                        @else
                                            <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white" style="width: 80px; height: 80px; font-size: 24px;">
                                                {{ strtoupper(substr($user->first_name, 0, 1) . substr($user->name, 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1">
                                        <input type="file" class="form-control @error('photo') is-invalid @enderror" id="photo" name="photo" accept="image/jpeg,image/png,image/jpg">
                                        @error('photo')
                                            <div class="text-danger mt-1 fs-12">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Prénom -->
                            <div class="col-md-6">
                                <label for="first_name" class="form-label">Prénom <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ri-user-3-line"></i></span>
                                    <input type="text" class="form-control @error('first_name') is-invalid @enderror" 
                                           id="first_name" name="first_name" value="{{ old('first_name', $user->first_name) }}" required>
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
                                           id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                </div>
                                @error('name')
                                    <div class="text-danger mt-1 fs-12">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Adresse Email -->
                            <div class="col-md-6">
                                <label for="email" class="form-label">Adresse Email</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ri-mail-line"></i></span>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email', $user->email) }}">
                                </div>
                                @error('email')
                                    <div class="text-danger mt-1 fs-12">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Téléphone -->
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Numéro de Téléphone</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ri-phone-line"></i></span>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                                </div>
                                @error('phone')
                                    <div class="text-danger mt-1 fs-12">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Date de naissance -->
                            <div class="col-md-6">
                                <label for="birth_date" class="form-label">Date de Naissance</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ri-calendar-event-line"></i></span>
                                    <input type="date" class="form-control @error('birth_date') is-invalid @enderror" 
                                           id="birth_date" name="birth_date" value="{{ old('birth_date', $user->birth_date ? $user->birth_date->format('Y-m-d') : '') }}">
                                </div>
                                @error('birth_date')
                                    <div class="text-danger mt-1 fs-12">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Statut -->
                            <div class="col-md-6">
                                <label for="status" class="form-label">Statut <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ri-shield-check-line"></i></span>
                                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                        <option value="PENDING" {{ old('status', $user->status->value) === 'PENDING' ? 'selected' : '' }}>En attente (PENDING)</option>
                                        <option value="ACTIVE" {{ old('status', $user->status->value) === 'ACTIVE' ? 'selected' : '' }}>Actif (ACTIVE)</option>
                                        <option value="INACTIVE" {{ old('status', $user->status->value) === 'INACTIVE' ? 'selected' : '' }}>Inactif (INACTIVE)</option>
                                        <option value="SUSPENDED" {{ old('status', $user->status->value) === 'SUSPENDED' ? 'selected' : '' }}>Suspendu (SUSPENDED)</option>
                                    </select>
                                </div>
                                @error('status')
                                    <div class="text-danger mt-1 fs-12">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-light">Annuler</a>
                            <button type="submit" class="btn btn-primary">
                                <span class="d-flex align-items-center">
                                    <i class="ri-save-line me-1"></i> Enregistrer les modifications
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
