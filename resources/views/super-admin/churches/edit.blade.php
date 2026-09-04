@extends('layouts.app')

@section('title', 'Modifier Église : ' . $church->name)

@push('css')
<style>
    /* =========================================================================
       HERO COMPACT
    ========================================================================= */
    .edit-hero {
        background-color: #0b0f19;
        background-image:
            radial-gradient(circle at 10% 30%, rgba(var(--vz-primary-rgb), 0.32) 0%, transparent 45%),
            radial-gradient(circle at 90% 70%, rgba(var(--vz-info-rgb), 0.2) 0%, transparent 45%);
        padding: 2rem 0 3.5rem 0;
        position: relative;
        overflow: hidden;
        margin: -1.5rem -1.5rem 0 -1.5rem;
        border-bottom-left-radius: 2rem;
        border-bottom-right-radius: 2rem;
    }
    .edit-hero .hero-grid {
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        background-image:
            linear-gradient(to right, rgba(255,255,255,0.03) 1px, transparent 1px),
            linear-gradient(to bottom, rgba(255,255,255,0.03) 1px, transparent 1px);
        background-size: 40px 40px;
        pointer-events: none;
        z-index: 1;
    }
    .edit-hero .orb {
        position: absolute;
        border-radius: 50%;
        filter: blur(80px);
        opacity: 0.45;
        z-index: 0;
    }
    .edit-hero .orb-blue { width: 300px; height: 300px; background: var(--vz-primary); top: -120px; left: -80px; }
    .edit-hero .orb-cyan { width: 240px; height: 240px; background: var(--vz-info); bottom: -60px; right: 8%; }
    .edit-hero .hero-content { position: relative; z-index: 5; }

    .hero-badge {
        background: rgba(var(--vz-primary-rgb), 0.15);
        border: 1px solid rgba(var(--vz-primary-rgb), 0.35);
        color: rgba(255,255,255,0.9);
        backdrop-filter: blur(6px);
        font-size: 0.7rem;
        letter-spacing: 1px;
        font-weight: 600;
    }
    .btn-hero-back {
        padding: 8px 18px;
        font-size: 0.85rem;
        font-weight: 500;
        border-radius: 50px;
        background: rgba(255,255,255,0.1);
        border: 1px solid rgba(255,255,255,0.2);
        color: #fff;
        backdrop-filter: blur(8px);
        transition: all 0.25s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
    }
    .btn-hero-back:hover { background: rgba(255,255,255,0.2); color: #fff; transform: translateY(-1px); }

    /* =========================================================================
       SECTION CARDS
    ========================================================================= */
    .section-card {
        border-radius: 1.25rem;
        border: none;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        transition: box-shadow 0.3s ease;
        overflow: hidden;
        background: var(--vz-card-bg);
    }
    .section-card:hover { box-shadow: 0 8px 32px rgba(0,0,0,0.09); }

    .section-header {
        padding: 1.1rem 1.5rem;
        background: var(--vz-light);
        border-bottom: 1px solid var(--vz-border-color);
        display: flex;
        align-items: center;
        gap: 0.85rem;
    }
    .section-num {
        width: 34px; height: 34px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.85rem;
        font-weight: 800;
        flex-shrink: 0;
    }
    .section-body { padding: 1.5rem; }

    /* =========================================================================
       FORM CONTROLS
    ========================================================================= */
    .form-floating-custom .form-label {
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--vz-gray-600);
        margin-bottom: 6px;
    }
    .form-floating-custom .required-star { color: var(--vz-danger); margin-left: 2px; }

    .form-control, .form-select {
        border-radius: 10px;
        border-color: var(--vz-border-color);
        font-size: 0.9rem;
        transition: border-color 0.2s, box-shadow 0.2s;
        padding: 0.6rem 0.9rem;
    }
    .form-control:focus, .form-select:focus {
        border-color: var(--vz-primary);
        box-shadow: 0 0 0 3px rgba(var(--vz-primary-rgb), 0.12);
    }
    .form-control.is-invalid:focus { box-shadow: 0 0 0 3px rgba(var(--vz-danger-rgb), 0.12); }
    .input-group .input-group-text {
        border-radius: 10px 0 0 10px !important;
        border-color: var(--vz-border-color);
        background: var(--vz-light);
        font-size: 0.82rem;
        font-weight: 600;
    }
    .input-group .form-control { border-radius: 0 10px 10px 0 !important; }
    .form-hint {
        font-size: 0.75rem;
        color: var(--vz-gray-500);
        margin-top: 5px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    /* =========================================================================
       ACTION BAR STICKY
    ========================================================================= */
    .action-bar {
        position: sticky;
        bottom: 0;
        background: var(--vz-card-bg);
        border-top: 1px solid var(--vz-border-color);
        padding: 1rem 1.5rem;
        margin: 0 -0.75rem;
        z-index: 20;
        box-shadow: 0 -4px 24px rgba(0,0,0,0.07);
    }
    .btn-submit-main {
        padding: 11px 28px;
        font-size: 0.9rem;
        font-weight: 700;
        border-radius: 50px;
        background: linear-gradient(135deg, var(--vz-primary) 0%, #4338ca 100%);
        border: none;
        color: #fff;
        box-shadow: 0 6px 18px rgba(var(--vz-primary-rgb), 0.35);
        transition: all 0.25s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .btn-submit-main:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 24px rgba(var(--vz-primary-rgb), 0.45);
        color: #fff;
    }
    .btn-cancel {
        padding: 11px 22px;
        font-size: 0.9rem;
        border-radius: 50px;
        border: 1.5px solid var(--vz-border-color);
        background: transparent;
        color: var(--vz-secondary);
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .btn-cancel:hover { background: var(--vz-light); color: var(--vz-body-color); border-color: var(--vz-gray-300); }

    .form-mandatory-note {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 7px 14px;
        background: rgba(var(--vz-danger-rgb), 0.06);
        border: 1px solid rgba(var(--vz-danger-rgb), 0.18);
        border-radius: 8px;
        font-size: 0.78rem;
        color: var(--vz-secondary-color);
        line-height: 1.4;
    }
    .form-mandatory-note span {
        display: inline;
    }

    /* =========================================================================
       DANGER ZONE
    ========================================================================= */
    .danger-zone-card {
        border-radius: 1.25rem;
        border: 1.5px solid rgba(var(--vz-danger-rgb), 0.25);
        background: rgba(var(--vz-danger-rgb), 0.02);
        box-shadow: 0 4px 20px rgba(var(--vz-danger-rgb), 0.05);
        overflow: hidden;
    }
    .danger-zone-header {
        padding: 1rem 1.5rem;
        background: rgba(var(--vz-danger-rgb), 0.06);
        border-bottom: 1px solid rgba(var(--vz-danger-rgb), 0.15);
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    /* =========================================================================
       RESPONSIVE
    ========================================================================= */
    @media (max-width: 991.98px) {
        .edit-hero { padding: 1.75rem 0 3.5rem 0; text-align: center; }
        .hero-back-wrap { justify-content: center !important; margin-top: 1rem; }
        .section-body { padding: 1.1rem; }
    }
    @media (max-width: 575.98px) {
        .action-bar { padding: 1.1rem 1rem; }
        .action-bar .d-flex.align-items-center {
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 12px !important;
        }
        .form-mandatory-note {
            width: 100%;
            justify-content: center;
            text-align: center;
            padding: 7px 10px;
            font-size: 0.74rem;
        }
        .btn-submit-main, .btn-cancel {
            width: 100%;
            justify-content: center;
            font-size: 0.88rem;
            padding: 12px 16px;
        }
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">

        {{-- =====================================================================
             HERO COMPACT
        ===================================================================== --}}
        <div class="edit-hero px-3 px-md-4">
            <div class="hero-grid"></div>
            <div class="orb orb-blue"></div>
            <div class="orb orb-cyan"></div>

            <div class="container-fluid hero-content">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <div class="mb-2 d-flex justify-content-center justify-content-lg-start align-items-center gap-2">
                            <span class="badge hero-badge px-3 py-2 rounded-pill">
                                <i class="ri-edit-2-line me-1"></i> MODIFICATION ÉGLISE CLIENTE
                            </span>
                            <span class="badge bg-white bg-opacity-10 text-white border border-white border-opacity-25 px-2 py-1 fs-11 font-monospace rounded-pill">
                                {{ $church->code }}
                            </span>
                        </div>
                        <h1 class="fs-22 fs-md-28 fw-bold text-white mb-1 text-center text-lg-start" style="line-height:1.25;">
                            Modifier : <span style="color:#f7b84b;">{{ $church->name }}</span>
                        </h1>
                        <p class="fs-13 mb-0 text-center text-lg-start" style="color:rgba(255,255,255,0.7); max-width:560px;">
                            Mettez à jour les coordonnées officielles de l'église et les informations de contact de son administrateur principal.
                        </p>
                    </div>
                    <div class="col-lg-4 d-flex justify-content-lg-end hero-back-wrap mt-3 mt-lg-0">
                        <a href="{{ route('super-admin.churches.show', $church) }}" class="btn-hero-back">
                            <i class="ri-arrow-left-line"></i> Retour à la fiche
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Alertes erreurs --}}
        @if ($errors->any())
            <div class="row mt-3">
                <div class="col-12">
                    <div class="alert border-0 rounded-3 d-flex align-items-start gap-3 shadow-sm"
                         style="background:rgba(var(--vz-danger-rgb),.08); border-left:4px solid var(--vz-danger) !important; border-left-style:solid !important;">
                        <div class="flex-shrink-0 text-danger fs-20 mt-1"><i class="ri-error-warning-fill"></i></div>
                        <div>
                            <h6 class="fw-bold text-danger mb-1">Veuillez corriger les erreurs ci-dessous :</h6>
                            <ul class="mb-0 fs-13 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Formulaire de modification --}}
        <form action="{{ route('super-admin.churches.update', $church) }}" method="POST" id="edit-church-form" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- =================================================================
                 SECTION 1 : INFORMATIONS DE L'ÉGLISE
            ================================================================= --}}
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card section-card">
                        <div class="section-header">
                            <div class="section-num bg-primary-subtle text-primary">
                                <i class="ri-building-line"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold fs-15">1. Informations de l'Église</h5>
                                <span class="text-muted fs-12">Nom, ville, coordonnées officielles et adresse</span>
                            </div>
                        </div>
                        <div class="section-body">
                            <div class="row g-3">
                                {{-- Nom officiel --}}
                                <div class="col-12 col-md-6">
                                    <div class="form-floating-custom">
                                        <label class="form-label">Nom officiel de l'église <span class="required-star">*</span></label>
                                        <input type="text" name="name"
                                            class="form-control @error('name') is-invalid @enderror"
                                            placeholder="Ex : Église Évangélique de la Grâce"
                                            value="{{ old('name', $church->name) }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Ville --}}
                                <div class="col-12 col-md-6">
                                    <div class="form-floating-custom">
                                        <label class="form-label">Ville / Localité <span class="required-star">*</span></label>
                                        <input type="text" name="city"
                                            class="form-control @error('city') is-invalid @enderror"
                                            placeholder="Ex : Cotonou, Abidjan, Lomé…"
                                            value="{{ old('city', $church->city) }}" required>
                                        @error('city')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Logo officiel de l'église --}}
                                <div class="col-12">
                                    <div class="form-floating-custom">
                                        <label class="form-label">Logo officiel de l'église <span class="text-muted fs-11 fw-normal">(optionnel)</span></label>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="bg-white border rounded-3 p-2 shadow-sm d-flex align-items-center justify-content-center flex-shrink-0" style="width: 64px; height: 64px;">
                                                <img id="church-logo-preview" src="{{ $church->logo_url }}" alt="Logo" class="img-fluid rounded" style="max-height: 50px; max-width: 50px; object-fit: contain;">
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="file" name="logo" id="church-logo-input" class="form-control @error('logo') is-invalid @enderror" accept="image/*" onchange="previewChurchLogo(this)">
                                                <small class="text-muted d-block mt-1 fs-11">Formats acceptés : PNG, JPG, WEBP, SVG (max. 4 Mo). Le logo s'affichera sur les listes, les PDF et les documents officiels.</small>
                                                @error('logo')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Email officiel --}}
                                <div class="col-12 col-md-6">
                                    <div class="form-floating-custom">
                                        <label class="form-label">Email officiel <span class="text-muted fs-11 fw-normal">(optionnel)</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="ri-mail-line text-muted"></i></span>
                                            <input type="email" name="email"
                                                class="form-control @error('email') is-invalid @enderror"
                                                placeholder="contact@eglise.org"
                                                value="{{ old('email', $church->email) }}">
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                {{-- Téléphone --}}
                                <div class="col-12 col-md-6">
                                    <div class="form-floating-custom">
                                        <label class="form-label">Numéro de téléphone <span class="text-muted fs-11 fw-normal">(optionnel)</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="ri-phone-line text-muted"></i></span>
                                            <input type="text" name="phone"
                                                class="form-control @error('phone') is-invalid @enderror"
                                                placeholder="+229 97 00 00 00"
                                                value="{{ old('phone', $church->phone) }}">
                                        </div>
                                    </div>
                                </div>

                                {{-- Adresse --}}
                                <div class="col-12">
                                    <div class="form-floating-custom">
                                        <label class="form-label">Adresse géographique <span class="text-muted fs-11 fw-normal">(optionnel)</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="ri-map-pin-line text-muted"></i></span>
                                            <input type="text" name="address"
                                                class="form-control @error('address') is-invalid @enderror"
                                                placeholder="Quartier, Rue, Repère…"
                                                value="{{ old('address', $church->address) }}">
                                        </div>
                                    </div>
                                </div>

                                {{-- Notes --}}
                                <div class="col-12">
                                    <div class="form-floating-custom">
                                        <label class="form-label">Notes / Commentaires de gestion</label>
                                        <textarea name="notes" class="form-control" rows="2"
                                            placeholder="Informations complémentaires sur cette église…">{{ old('notes', $church->notes) }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- =================================================================
                 SECTION 2 : COMPTE ADMINISTRATEUR PRINCIPAL
            ================================================================= --}}
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card section-card">
                        <div class="section-header">
                            <div class="section-num bg-warning-subtle text-warning">
                                <i class="ri-user-star-line"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold fs-15">2. Compte Administrateur Principal de l'Église</h5>
                                <span class="text-muted fs-12">Coordonnées du responsable principal (sans modification de mot de passe)</span>
                            </div>
                        </div>
                        <div class="section-body">
                            @if($admin)
                                <div class="row g-3">
                                    {{-- Prénom --}}
                                    <div class="col-12 col-sm-6">
                                        <div class="form-floating-custom">
                                            <label class="form-label">Prénom de l'admin <span class="required-star">*</span></label>
                                            <input type="text" name="admin_first_name"
                                                class="form-control @error('admin_first_name') is-invalid @enderror"
                                                placeholder="Ex : Paul"
                                                value="{{ old('admin_first_name', $admin->first_name) }}" required>
                                            @error('admin_first_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Nom de famille --}}
                                    <div class="col-12 col-sm-6">
                                        <div class="form-floating-custom">
                                            <label class="form-label">Nom de famille <span class="required-star">*</span></label>
                                            <input type="text" name="admin_name"
                                                class="form-control @error('admin_name') is-invalid @enderror"
                                                placeholder="Ex : KOUAMÉ"
                                                value="{{ old('admin_name', $admin->name) }}" required>
                                            @error('admin_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Email de connexion --}}
                                    <div class="col-12 col-md-6">
                                        <div class="form-floating-custom">
                                            <label class="form-label">Adresse Email <span class="text-muted fs-11 fw-normal">(Requis si pas de tél.)</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="ri-mail-line text-muted"></i></span>
                                                <input type="email" name="admin_email"
                                                    class="form-control @error('admin_email') is-invalid @enderror"
                                                    placeholder="admin@eglise.org"
                                                    value="{{ old('admin_email', $admin->email) }}">
                                                @error('admin_email')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="form-hint text-muted fs-12 mt-1">
                                                <i class="ri-information-line"></i> Requis si pas de téléphone.
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Téléphone admin --}}
                                    <div class="col-12 col-md-6">
                                        <div class="form-floating-custom">
                                            <label class="form-label">Numéro de Téléphone <span class="text-muted fs-11 fw-normal">(Requis si pas d'email)</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="ri-phone-line text-muted"></i></span>
                                                <input type="text" name="admin_phone"
                                                    class="form-control @error('admin_phone') is-invalid @enderror"
                                                    placeholder="Ex : +22990000000"
                                                    value="{{ old('admin_phone', $admin->phone) }}">
                                                @error('admin_phone')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="form-hint text-muted fs-12 mt-1">
                                                <i class="ri-information-line"></i> Requis si pas d'email.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Info mot de passe préservé --}}
                                <div class="mt-3 p-3 rounded-3 d-flex align-items-center gap-3 border"
                                     style="background: rgba(var(--vz-info-rgb), 0.05); border-color: rgba(var(--vz-info-rgb), 0.2) !important;">
                                    <i class="ri-lock-2-line text-info fs-20 flex-shrink-0"></i>
                                    <div class="fs-12 text-muted">
                                        <strong class="text-body">Mot de passe conservé :</strong> La modification de ces coordonnées ne modifie pas le mot de passe actuel de l'administrateur et aucun email de réinitialisation n'est expédié.
                                    </div>
                                </div>
                            @else
                                <div class="p-3 text-center text-muted bg-light rounded-3">
                                    <i class="ri-information-line fs-20 d-block mb-1"></i>
                                    Aucun administrateur rattaché pour l'instant à cette église.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- =================================================================
                 BARRE D'ACTION STICKY
            ================================================================= --}}
            <div class="action-bar mt-4 mb-4 rounded-3">
                <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
                    <div class="form-mandatory-note">
                        <i class="ri-error-warning-line text-danger fs-14 flex-shrink-0"></i>
                        <span>Les champs marqués d'un <strong class="text-danger">*</strong> sont obligatoires.</span>
                    </div>
                    <div class="d-flex gap-2 flex-wrap justify-content-end">
                        <a href="{{ route('super-admin.churches.show', $church) }}" class="btn-cancel">
                            <i class="ri-close-line"></i> Annuler
                        </a>
                        <button type="submit" class="btn-submit-main" id="btn-save-church">
                            <i class="ri-save-3-line fs-16"></i>
                            <span>Enregistrer les modifications</span>
                        </button>
                    </div>
                </div>
            </div>

        </form>

    </div>
</div>

@push('scripts')
<script>
    function previewChurchLogo(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('church-logo-preview');
                if (preview) {
                    preview.src = e.target.result;
                }
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    document.getElementById('edit-church-form').addEventListener('submit', function () {
        const btn = document.getElementById('btn-save-church');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Enregistrement…';
    });
</script>
@endpush
@endsection
