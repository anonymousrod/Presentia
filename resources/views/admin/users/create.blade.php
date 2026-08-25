@extends('layouts.app')

@section('content')
<div class="container-fluid max-w-1200 py-3 py-md-4">
    {{-- =================== EN-TÊTE =================== --}}
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-4 gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}" class="text-muted">Utilisateurs</a></li>
                    <li class="breadcrumb-item active fw-medium" aria-current="page">Nouveau compte</li>
                </ol>
            </nav>
            <h3 class="fw-bold mb-0 fs-20 fs-md-24">Création de Compte</h3>
            <p class="text-muted mb-0 fs-13 mt-1">Renseignez les informations du nouvel utilisateur ci-dessous.</p>
        </div>
        <div>
            <a href="{{ route('admin.users.index') }}" class="btn btn-soft-secondary rounded-pill px-4 shadow-sm">
                <i class="mdi mdi-arrow-left me-1"></i> Retour
            </a>
        </div>
    </div>

    {{-- Alertes --}}
    @if (session('success'))
        <div class="alert border-0 mb-4 d-flex align-items-center gap-3 p-3 shadow-sm rounded-3"
             style="background: rgba(var(--vz-success-rgb), 0.12); border-left: 4px solid var(--vz-success) !important;">
            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                 style="width:40px; height:40px; background: rgba(var(--vz-success-rgb), 0.2);">
                <i class="mdi mdi-check-circle fs-20" style="color: var(--vz-success);"></i>
            </div>
            <div class="flex-grow-1">
                <h6 class="mb-0 fw-bold text-success">Succès !</h6>
                <span class="fs-13 text-body">{{ session('success') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert border-0 mb-4 d-flex align-items-start gap-3 p-3 shadow-sm rounded-3"
             style="background: rgba(var(--vz-danger-rgb), 0.12); border-left: 4px solid var(--vz-danger) !important;">
            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 mt-1"
                 style="width:40px; height:40px; background: rgba(var(--vz-danger-rgb), 0.2);">
                <i class="mdi mdi-alert-circle fs-20" style="color: var(--vz-danger);"></i>
            </div>
            <div class="flex-grow-1">
                <h6 class="mb-1 fw-bold text-danger">Erreurs de validation</h6>
                <ul class="mb-0 ps-3 fs-13 text-body">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button type="button" class="btn-close mt-1" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- =================== FORMULAIRE =================== --}}
    <form action="{{ route('admin.users.store') }}" method="POST">
        @csrf
        <div class="row g-4">
            
            {{-- ======= COLONNE GAUCHE ======= --}}
            <div class="col-lg-8">
                
                {{-- Informations Personnelles --}}
                <div class="card border-0 shadow-sm mb-4 rounded-3">
                    <div class="card-header border-0 py-3 px-4" style="background: rgba(var(--vz-primary-rgb), 0.03); border-bottom: 1px solid rgba(var(--vz-primary-rgb), 0.1) !important;">
                        <div class="d-flex align-items-center gap-2">
                            <i class="mdi mdi-account-circle-outline fs-20 text-primary"></i>
                            <h5 class="mb-0 fw-bold fs-15 text-primary text-uppercase tracking-wider">Informations Personnelles</h5>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label for="first_name" class="form-label fw-semibold text-muted fs-12 text-uppercase tracking-wider">Prénom <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-light-subtle"><i class="mdi mdi-account-outline text-muted"></i></span>
                                    <input type="text" class="form-control bg-light border-light-subtle @error('first_name') is-invalid @enderror" 
                                           id="first_name" name="first_name" value="{{ old('first_name') }}" 
                                           placeholder="Ex : Exaucé" required>
                                </div>
                                @error('first_name') <div class="text-danger mt-1 fs-12">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="name" class="form-label fw-semibold text-muted fs-12 text-uppercase tracking-wider">Nom <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-light-subtle"><i class="mdi mdi-account-details-outline text-muted"></i></span>
                                    <input type="text" class="form-control bg-light border-light-subtle @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" 
                                           placeholder="Ex : NDOUNA" required>
                                </div>
                                @error('name') <div class="text-danger mt-1 fs-12">{{ $message }}</div> @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="birth_date" class="form-label fw-semibold text-muted fs-12 text-uppercase tracking-wider">Date de Naissance</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-light-subtle"><i class="mdi mdi-calendar-outline text-muted"></i></span>
                                    <input type="date" class="form-control bg-light border-light-subtle @error('birth_date') is-invalid @enderror" 
                                           id="birth_date" name="birth_date" value="{{ old('birth_date') }}">
                                </div>
                                @error('birth_date') <div class="text-danger mt-1 fs-12">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Coordonnées --}}
                <div class="card border-0 shadow-sm mb-4 rounded-3">
                    <div class="card-header border-0 py-3 px-4" style="background: rgba(var(--vz-info-rgb), 0.03); border-bottom: 1px solid rgba(var(--vz-info-rgb), 0.1) !important;">
                        <div class="d-flex align-items-center gap-2">
                            <i class="mdi mdi-card-account-phone-outline fs-20 text-info"></i>
                            <h5 class="mb-0 fw-bold fs-15 text-info text-uppercase tracking-wider">Coordonnées</h5>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label for="email" class="form-label fw-semibold text-muted fs-12 text-uppercase tracking-wider">Adresse Email</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-light-subtle"><i class="mdi mdi-email-outline text-muted"></i></span>
                                    <input type="email" class="form-control bg-light border-light-subtle @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email') }}" 
                                           placeholder="Ex : contact@example.com">
                                </div>
                                <div class="form-text text-muted fs-12"><i class="mdi mdi-information-outline"></i> Requis si pas de téléphone.</div>
                                @error('email') <div class="text-danger mt-1 fs-12">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="phone" class="form-label fw-semibold text-muted fs-12 text-uppercase tracking-wider">Numéro de Téléphone</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-light-subtle"><i class="mdi mdi-phone-outline text-muted"></i></span>
                                    <input type="text" class="form-control bg-light border-light-subtle @error('phone') is-invalid @enderror" 
                                           id="phone" name="phone" value="{{ old('phone') }}" 
                                           placeholder="Ex : +22990000000">
                                </div>
                                <div class="form-text text-muted fs-12"><i class="mdi mdi-information-outline"></i> Requis si pas d'email.</div>
                                @error('phone') <div class="text-danger mt-1 fs-12">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Église & Finances --}}
                <div class="card border-0 shadow-sm mb-4 rounded-3">
                    <div class="card-header border-0 py-3 px-4" style="background: rgba(var(--vz-success-rgb), 0.03); border-bottom: 1px solid rgba(var(--vz-success-rgb), 0.1) !important;">
                        <div class="d-flex align-items-center gap-2">
                            <i class="mdi mdi-church fs-20 text-success"></i>
                            <h5 class="mb-0 fw-bold fs-15 text-success text-uppercase tracking-wider">Église & Finances</h5>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label for="church_service" class="form-label fw-semibold text-muted fs-12 text-uppercase tracking-wider">Service dans l'église</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-light-subtle"><i class="mdi mdi-account-hard-hat text-muted"></i></span>
                                    <input type="text" class="form-control bg-light border-light-subtle @error('church_service') is-invalid @enderror" 
                                           id="church_service" name="church_service" value="{{ old('church_service') }}" 
                                           placeholder="Ex: Chorale, Intercession, etc.">
                                </div>
                                @error('church_service') <div class="text-danger mt-1 fs-12">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="weekly_contribution" class="form-label fw-semibold text-muted fs-12 text-uppercase tracking-wider">Cotisation Hebdo. (FCFA)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-light-subtle"><i class="mdi mdi-cash-multiple text-muted"></i></span>
                                    <input type="number" step="50" min="0" class="form-control bg-light border-light-subtle @error('weekly_contribution') is-invalid @enderror" 
                                           id="weekly_contribution" name="weekly_contribution" value="{{ old('weekly_contribution') }}" 
                                           placeholder="Ex : 250">
                                </div>
                                @error('weekly_contribution') <div class="text-danger mt-1 fs-12">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ======= COLONNE DROITE ======= --}}
            <div class="col-lg-4">
                
                {{-- Informations Complémentaires --}}
                <div class="card border-0 shadow-sm mb-4 rounded-3">
                    <div class="card-header border-0 py-3 px-4 d-flex justify-content-between align-items-center" style="background: rgba(var(--vz-warning-rgb), 0.03); border-bottom: 1px solid rgba(var(--vz-warning-rgb), 0.1) !important;">
                        <div class="d-flex align-items-center gap-2">
                            <i class="mdi mdi-text-box-plus-outline fs-20 text-warning"></i>
                            <h5 class="mb-0 fw-bold fs-15 text-warning text-uppercase tracking-wider">Notes Addit.</h5>
                        </div>
                        <button type="button" class="btn btn-sm btn-soft-warning rounded-pill px-3" onclick="addInfoField()">
                            <i class="mdi mdi-plus"></i> Ajouter
                        </button>
                    </div>
                    <div class="card-body p-4 bg-light bg-opacity-50">
                        <p class="text-muted fs-13 mb-3">Ajoutez des paires de Titre / Valeur pour toute information non couverte.</p>
                        <div id="additional-info-container" class="vstack gap-2">
                            @php
                                $additionalInfos = old('additional_info') ?? [];
                                if(!is_array($additionalInfos)) $additionalInfos = [];
                            @endphp
                            @foreach($additionalInfos as $index => $info)
                                @if(is_array($info))
                                <div class="info-item border bg-white rounded p-3 shadow-sm">
                                    <div class="d-flex gap-2">
                                        <div class="flex-grow-1">
                                            <input type="text" name="additional_info[{{ $index }}][title]" class="form-control form-control-sm mb-2 bg-light border-light-subtle" placeholder="Titre (ex: Instrument)" value="{{ $info['title'] ?? '' }}" required>
                                            <input type="text" name="additional_info[{{ $index }}][value]" class="form-control form-control-sm bg-light border-light-subtle" placeholder="Valeur (ex: Piano)" value="{{ $info['value'] ?? '' }}" required>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-soft-danger flex-shrink-0 align-self-start rounded-circle" style="width:32px; height:32px;" onclick="this.closest('.info-item').remove()">
                                            <i class="mdi mdi-delete"></i>
                                        </button>
                                    </div>
                                </div>
                                @endif
                            @endforeach
                        </div>
                        @error('additional_info')
                            <div class="text-danger mt-2 fs-12">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Paramètres de création & Bouton --}}
                <div class="card border-0 shadow-lg mb-4 rounded-3 overflow-hidden position-sticky" style="top: 100px; background: linear-gradient(135deg, var(--vz-primary), #3b82f6);">
                    <div class="card-body p-4 text-white">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <i class="mdi mdi-shield-check-outline fs-24 text-white-75"></i>
                            <h5 class="mb-0 fw-bold fs-16 text-white text-uppercase tracking-wider">Validation</h5>
                        </div>

                        <div class="p-3 bg-white bg-opacity-10 rounded-3 border border-white border-opacity-25 mb-4">
                            <h6 class="fw-bold mb-2"><i class="mdi mdi-lock-reset me-1"></i> Génération auto</h6>
                            <p class="mb-0 fs-13 text-white-75">
                                Un mot de passe temporaire sera généré et envoyé :<br>
                                • Par <strong>Email</strong> si fourni.<br>
                                • Par <strong>WhatsApp</strong> sinon.
                            </p>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-light btn-lg fw-bold shadow-sm rounded-pill py-2">
                                <i class="mdi mdi-check-all me-1"></i> Créer le compte
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    let infoIndex = {{ count(is_array(old('additional_info')) ? old('additional_info') : []) }};
    function addInfoField() {
        const container = document.getElementById('additional-info-container');
        const item = document.createElement('div');
        item.className = 'info-item border bg-white rounded p-3 shadow-sm mb-2';
        item.innerHTML = `
            <div class="d-flex gap-2">
                <div class="flex-grow-1">
                    <input type="text" name="additional_info[${infoIndex}][title]" class="form-control form-control-sm mb-2 bg-light border-light-subtle" placeholder="Titre (ex: Instrument)" required>
                    <input type="text" name="additional_info[${infoIndex}][value]" class="form-control form-control-sm bg-light border-light-subtle" placeholder="Valeur (ex: Piano)" required>
                </div>
                <button type="button" class="btn btn-sm btn-soft-danger flex-shrink-0 align-self-start rounded-circle" style="width:32px; height:32px;" onclick="this.closest('.info-item').remove()">
                    <i class="mdi mdi-delete"></i>
                </button>
            </div>
        `;
        container.appendChild(item);
        infoIndex++;
    }
</script>
@endpush
