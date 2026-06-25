@extends('layouts.app')

@push('css')
<style>
    /* Thematic Section Headers */
    .section-header {
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        color: var(--vz-primary);
        font-weight: 700;
        display: flex;
        align-items: center;
        margin-bottom: 1.5rem;
    }
    .section-header i {
        margin-right: 0.5rem;
        font-size: 1.2rem;
    }
    .section-header::after {
        content: '';
        flex: 1;
        height: 1px;
        background: rgba(var(--vz-primary-rgb), 0.15);
        margin-left: 1rem;
    }

    /* Custom Inputs */
    .premium-input {
        border-radius: 0.5rem;
        padding: 0.65rem 1rem;
        transition: all 0.3s ease;
    }
    .premium-input:focus {
        border-color: var(--vz-primary);
        box-shadow: 0 0 0 0.25rem rgba(var(--vz-primary-rgb), 0.15);
    }
    .input-group-text.premium-addon {
        border-radius: 0.5rem 0 0 0.5rem;
        border-right: 0;
        background-color: transparent;
    }
    .premium-input.with-addon {
        border-left: 0;
    }
    .premium-input.with-addon:focus {
        border-left: 1px solid var(--vz-primary);
    }
    
    /* Save Button */
    .btn-save {
        padding: 0.8rem 2rem;
        font-weight: 600;
        letter-spacing: 0.5px;
        border-radius: 0.5rem;
        box-shadow: 0 4px 10px rgba(var(--vz-primary-rgb), 0.3);
        transition: all 0.3s;
    }
    .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(var(--vz-primary-rgb), 0.4);
    }
</style>
@endpush

@section('content')

<div class="row mb-3 pb-1 mt-4 px-4">
    <div class="col-12">
        <div class="d-flex align-items-lg-center flex-lg-row flex-column justify-content-between">
            <div class="flex-grow-1">
                <h4 class="fs-16 mb-1">Modifier l'Utilisateur : {{ $user->first_name }} {{ $user->name }}</h4>
                <p class="text-muted mb-0">Mettez à jour les informations de l'utilisateur ci-dessous.</p>
            </div>
            <div class="mt-3 mt-lg-0">
                <a href="{{ route('admin.users.index') }}" class="btn btn-soft-secondary d-flex align-items-center gap-1">
                    <i class="mdi mdi-arrow-left"></i> Retour
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid max-w-1200 px-4">
    @if (session('success'))
        <div class="alert alert-success alert-border-left alert-dismissible fade show mb-4" role="alert">
            <i class="ri-check-double-line me-3 align-middle fs-16"></i>
            <strong>Succès !</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-border-left alert-dismissible fade show mb-4" role="alert">
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

    <form action="{{ route('admin.users.update', $user) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="row g-4">
            <!-- Left Column: Main Information -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 1rem;">
                    <div class="card-body p-4 p-md-5">
                        <div class="section-header">
                            <i class="mdi mdi-account-circle-outline"></i> Informations personnelles
                        </div>

                        <!-- Photo de profil -->
                        <div class="col-12 mb-4">
                            <label class="form-label fw-medium text-body">Photo de profil (Max 1 Mo)</label>
                            <div class="d-flex align-items-center">
                                <div class="me-4">
                                    @if($user->photo)
                                        <img src="{{ asset('storage/' . $user->photo) }}" alt="Photo" class="rounded-circle shadow-sm" width="90" height="90" style="object-fit: cover;">
                                    @else
                                        <div class="rounded-circle bg-soft-primary d-flex align-items-center justify-content-center text-primary shadow-sm" style="width: 90px; height: 90px; font-size: 28px; font-weight: 600;">
                                            {{ strtoupper(substr($user->first_name, 0, 1) . substr($user->name, 0, 1)) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <input type="file" class="form-control premium-input @error('photo') is-invalid @enderror" id="photo" name="photo" accept="image/jpeg,image/png,image/jpg">
                                    @error('photo')
                                        <div class="text-danger mt-1 fs-12">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label for="first_name" class="form-label fw-medium text-body">Prénom <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text premium-addon"><i class="mdi mdi-account-outline"></i></span>
                                    <input type="text" class="form-control premium-input with-addon @error('first_name') is-invalid @enderror" 
                                           id="first_name" name="first_name" value="{{ old('first_name', $user->first_name) }}" required>
                                </div>
                                @error('first_name')
                                    <div class="text-danger mt-1 fs-12">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="name" class="form-label fw-medium text-body">Nom <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text premium-addon"><i class="mdi mdi-account-details-outline"></i></span>
                                    <input type="text" class="form-control premium-input with-addon @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                </div>
                                @error('name')
                                    <div class="text-danger mt-1 fs-12">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="section-header mt-5">
                            <i class="mdi mdi-card-account-phone-outline"></i> Coordonnées
                        </div>

                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label for="email" class="form-label fw-medium text-body">Adresse Email</label>
                                <div class="input-group">
                                    <span class="input-group-text premium-addon"><i class="mdi mdi-email-outline"></i></span>
                                    <input type="email" class="form-control premium-input with-addon @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email', $user->email) }}">
                                </div>
                                @error('email')
                                    <div class="text-danger mt-1 fs-12">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="phone" class="form-label fw-medium text-body">Numéro de Téléphone</label>
                                <div class="input-group">
                                    <span class="input-group-text premium-addon"><i class="mdi mdi-phone-outline"></i></span>
                                    <input type="text" class="form-control premium-input with-addon @error('phone') is-invalid @enderror" 
                                           id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                                </div>
                                @error('phone')
                                    <div class="text-danger mt-1 fs-12">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="birth_date" class="form-label fw-medium text-body">Date de Naissance</label>
                            <div class="input-group">
                                <span class="input-group-text premium-addon"><i class="mdi mdi-calendar-outline"></i></span>
                                <input type="date" class="form-control premium-input with-addon @error('birth_date') is-invalid @enderror" 
                                       id="birth_date" name="birth_date" value="{{ old('birth_date', $user->birth_date ? $user->birth_date->format('Y-m-d') : '') }}">
                            </div>
                            @error('birth_date')
                                <div class="text-danger mt-1 fs-12">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Settings & Submit -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 1rem; position: sticky; top: 100px;">
                    <div class="card-body p-4">
                        <div class="section-header">
                            <i class="mdi mdi-shield-check-outline"></i> Paramètres
                        </div>

                        <div class="mb-4">
                            <label for="status" class="form-label fw-medium text-body">Statut du compte <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text premium-addon"><i class="mdi mdi-account-cog-outline"></i></span>
                                <select name="status" id="status" class="form-select premium-input with-addon @error('status') is-invalid @enderror" required>
                                    <option value="PENDING" {{ old('status', $user->status->value) === 'PENDING' ? 'selected' : '' }}>En attente</option>
                                    <option value="ACTIVE" {{ old('status', $user->status->value) === 'ACTIVE' ? 'selected' : '' }}>Actif</option>
                                    <option value="INACTIVE" {{ old('status', $user->status->value) === 'INACTIVE' ? 'selected' : '' }}>Inactif</option>
                                    <option value="SUSPENDED" {{ old('status', $user->status->value) === 'SUSPENDED' ? 'selected' : '' }}>Suspendu</option>
                                </select>
                            </div>
                            @error('status')
                                <div class="text-danger mt-1 fs-12">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid mt-5 pt-2">
                            <button type="submit" class="btn btn-primary btn-save">
                                <i class="mdi mdi-check-all me-1"></i> Enregistrer
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
