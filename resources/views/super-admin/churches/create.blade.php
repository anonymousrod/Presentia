@extends('layouts.app')

@section('title', 'Inscrire une Nouvelle Église Cliente')

@push('css')
<style>
    /* =========================================================================
       HERO COMPACT
    ========================================================================= */
    .create-hero {
        background-color: #0b0f19;
        background-image:
            radial-gradient(circle at 10% 30%, rgba(var(--vz-primary-rgb), 0.32) 0%, transparent 45%),
            radial-gradient(circle at 90% 70%, rgba(var(--vz-warning-rgb), 0.2) 0%, transparent 45%);
        padding: 2rem 0 5rem 0;
        position: relative;
        overflow: hidden;
        margin: -1.5rem -1.5rem 0 -1.5rem;
        border-bottom-left-radius: 2rem;
        border-bottom-right-radius: 2rem;
    }
    .create-hero .hero-grid {
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        background-image:
            linear-gradient(to right, rgba(255,255,255,0.03) 1px, transparent 1px),
            linear-gradient(to bottom, rgba(255,255,255,0.03) 1px, transparent 1px);
        background-size: 40px 40px;
        pointer-events: none;
        z-index: 1;
    }
    .create-hero .orb {
        position: absolute;
        border-radius: 50%;
        filter: blur(80px);
        opacity: 0.45;
        z-index: 0;
    }
    .create-hero .orb-blue  { width: 300px; height: 300px; background: var(--vz-primary); top: -120px; left: -80px; }
    .create-hero .orb-amber { width: 240px; height: 240px; background: var(--vz-warning); bottom: -60px; right: 8%; }
    .create-hero .hero-content { position: relative; z-index: 5; }

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
       MODERN SAAS STEPPER
    ========================================================================= */
    .steps-wrap {
        margin-top: -2.5rem;
        position: relative;
        z-index: 10;
    }
    .stepper-card {
        border-radius: 1.25rem;
        background: #ffffff;
        border: 1px solid rgba(226, 232, 240, 0.85);
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
    }
    .step-pill {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        border-radius: 14px;
        background: rgba(248, 250, 252, 0.7);
        border: 1px solid rgba(226, 232, 240, 0.7);
        transition: all 0.25s ease;
        flex: 1;
    }
    .step-pill:hover {
        background: #ffffff;
        border-color: rgba(99, 102, 241, 0.3);
        box-shadow: 0 4px 14px rgba(99, 102, 241, 0.06);
    }
    .step-pill-icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        color: #ffffff;
        flex-shrink: 0;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }
    .icon-church { background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); }
    .icon-sub    { background: linear-gradient(135deg, #10b981 0%, #047857 100%); }
    .icon-admin  { background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%); }

    .step-number {
        font-size: 0.65rem;
        font-weight: 800;
        letter-spacing: 0.8px;
        text-transform: uppercase;
        color: var(--vz-primary);
        display: block;
        margin-bottom: 2px;
    }
    .step-title {
        font-size: 0.9rem;
        font-weight: 700;
        color: var(--vz-body-color);
        line-height: 1.2;
    }
    .step-desc {
        font-size: 0.75rem;
        color: var(--vz-gray-500);
        margin-top: 2px;
    }
    .stepper-divider {
        color: var(--vz-gray-400);
        font-size: 1.25rem;
        display: flex;
        align-items: center;
    }

    @media (max-width: 991.98px) {
        .stepper-divider { display: none; }
        .step-pill { padding: 10px 12px; }
    }
    @media (max-width: 767.98px) {
        .steps-wrap { margin-top: -1.5rem; }
    }

    /* =========================================================================
       SECTION CARDS
    ========================================================================= */
    .section-card {
        border-radius: 1.25rem;
        border: none;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        transition: box-shadow 0.3s ease;
        overflow: hidden;
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
        border-radius: 0 10px 10px 0;
        border-color: var(--vz-border-color);
        background: var(--vz-light);
        font-size: 0.82rem;
        font-weight: 600;
    }
    .input-group .form-control { border-radius: 10px 0 0 10px !important; }
    .form-hint {
        font-size: 0.75rem;
        color: var(--vz-gray-500);
        margin-top: 5px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    /* Info card abonnement */
    .sub-info-card {
        border-radius: 10px;
        border: 1.5px solid rgba(34,197,94,.25);
        background: rgba(34,197,94,.06);
        padding: 12px 16px;
    }

    /* Password field */
    .password-wrap { position: relative; }
    .password-toggle {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: var(--vz-gray-500);
        cursor: pointer;
        padding: 0;
        font-size: 1rem;
        z-index: 5;
    }
    .password-toggle:hover { color: var(--vz-primary); }
    .password-wrap .form-control { padding-right: 38px; }

    /* =========================================================================
       ACTION BAR (STANDARD FORM FOOTER)
    ========================================================================= */
    .action-bar {
        background: var(--vz-card-bg);
        border: 1px solid var(--vz-border-color);
        border-radius: 14px;
        padding: 1.25rem 1.5rem;
        margin-top: 1.5rem;
        margin-bottom: 2.5rem;
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

    /* =========================================================================
       RESPONSIVE
    ========================================================================= */
    @media (max-width: 991.98px) {
        .stepper-divider { display: none; }
        .create-hero { padding: 1.75rem 0 4.5rem 0; text-align: center; }
        .hero-back-wrap { justify-content: center !important; margin-top: 1rem; }
        .section-body { padding: 1.1rem; }
    }
    @media (max-width: 767.98px) {
        .steps-wrap { margin-top: -1.75rem; }
        .section-header { padding: 0.9rem 1.1rem; }
        .section-body { padding: 1rem; }
    }
    @media (max-width: 575.98px) {
        .create-hero { padding: 1.5rem 0 3.5rem 0; }
        .stepper-card { padding: 0.75rem !important; }
        .mobile-step-pill {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding: 8px 4px;
            background: rgba(248, 250, 252, 0.9);
            border: 1px solid rgba(226, 232, 240, 0.9);
            border-radius: 12px;
            height: 100%;
        }
        .mobile-step-pill .step-pill-icon {
            width: 32px;
            height: 32px;
            font-size: 0.95rem;
            margin-bottom: 4px;
        }
        .mobile-step-pill .step-title {
            font-size: 0.75rem;
            font-weight: 700;
        }
        .mobile-step-pill .step-number {
            font-size: 0.6rem;
            margin-bottom: 1px;
        }
        .action-bar {
            padding: 1.1rem 1rem;
            margin-top: 1.25rem;
            margin-bottom: 2rem;
        }
        .action-bar .d-flex.align-items-center {
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 12px !important;
        }
        .action-bar .text-muted {
            text-align: center;
            justify-content: center;
        }
        .action-bar-actions {
            display: flex;
            flex-direction: column;
            width: 100%;
            gap: 8px;
        }
        .btn-submit-main, .btn-cancel {
            width: 100%;
            justify-content: center;
            font-size: 0.88rem;
            padding: 12px 16px;
        }
        .btn-cancel {
            order: 2;
        }
        .btn-submit-main {
            order: 1;
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
        <div class="create-hero px-3 px-md-4">
            <div class="hero-grid"></div>
            <div class="orb orb-blue"></div>
            <div class="orb orb-amber"></div>

            <div class="container-fluid hero-content">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <div class="mb-2 d-flex justify-content-center justify-content-lg-start">
                            <span class="badge hero-badge px-3 py-2 rounded-pill">
                                <i class="ri-building-line me-1"></i> INSCRIPTION NOUVELLE ÉGLISE CLIENTE
                            </span>
                        </div>
                        <h1 class="fs-22 fs-md-28 fw-bold text-white mb-1 text-center text-lg-start" style="line-height:1.25;">
                            Inscrire une <span style="color:#f7b84b;">Nouvelle Église</span>
                        </h1>
                        <p class="fs-13 mb-0 text-center text-lg-start" style="color:rgba(255,255,255,0.7); max-width:520px;">
                            Créez l'espace église, activez l'abonnement annuel (1 an) et configurez le compte administrateur principal.
                        </p>
                    </div>
                    <div class="col-lg-4 d-flex justify-content-lg-end hero-back-wrap mt-3 mt-lg-0">
                        <a href="{{ route('super-admin.churches.index') }}" class="btn-hero-back">
                            <i class="ri-arrow-left-line"></i> Retour à la liste
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- =====================================================================
             MODERN SAAS STEPPER (DESKTOP & MOBILE RESPONSIVE)
        ===================================================================== --}}
        <div class="row steps-wrap px-1 px-md-0 mb-4">
            <div class="col-12">
                <div class="stepper-card p-3 p-md-4">
                    
                    {{-- Vue Desktop / Tablette (d-none d-md-flex) --}}
                    <div class="d-none d-md-flex align-items-center justify-content-between gap-2 gap-lg-3">
                        
                        {{-- Step 1 : Église --}}
                        <div class="step-pill">
                            <div class="step-pill-icon icon-church">
                                <i class="ri-building-line"></i>
                            </div>
                            <div class="flex-grow-1">
                                <span class="step-number">Étape 01</span>
                                <div class="step-title">Église &amp; Localisation</div>
                                <div class="step-desc">Identité, contact &amp; adresse</div>
                            </div>
                        </div>

                        <div class="stepper-divider d-none d-lg-flex">
                            <i class="ri-arrow-right-s-line"></i>
                        </div>

                        {{-- Step 2 : Abonnement --}}
                        <div class="step-pill">
                            <div class="step-pill-icon icon-sub">
                                <i class="ri-calendar-check-line"></i>
                            </div>
                            <div class="flex-grow-1">
                                <span class="step-number" style="color:#10b981;">Étape 02</span>
                                <div class="step-title">Abonnement (1 An)</div>
                                <div class="step-desc">Tarif &amp; mode de règlement</div>
                            </div>
                        </div>

                        <div class="stepper-divider d-none d-lg-flex">
                            <i class="ri-arrow-right-s-line"></i>
                        </div>

                        {{-- Step 3 : Administrateur --}}
                        <div class="step-pill">
                            <div class="step-pill-icon icon-admin">
                                <i class="ri-user-star-line"></i>
                            </div>
                            <div class="flex-grow-1">
                                <span class="step-number" style="color:#8b5cf6;">Étape 03</span>
                                <div class="step-title">Administrateur Principal</div>
                                <div class="step-desc">Accès &amp; identifiants auto</div>
                            </div>
                        </div>

                    </div>

                    {{-- Vue Mobile Compacte (d-flex d-md-none) --}}
                    <div class="d-flex d-md-none row g-2">
                        <div class="col-4">
                            <div class="mobile-step-pill">
                                <div class="step-pill-icon icon-church">
                                    <i class="ri-building-line"></i>
                                </div>
                                <span class="step-number">01</span>
                                <span class="step-title text-truncate w-100">Église</span>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="mobile-step-pill">
                                <div class="step-pill-icon icon-sub">
                                    <i class="ri-calendar-check-line"></i>
                                </div>
                                <span class="step-number" style="color:#10b981;">02</span>
                                <span class="step-title text-truncate w-100">Abonnement</span>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="mobile-step-pill">
                                <div class="step-pill-icon icon-admin">
                                    <i class="ri-user-star-line"></i>
                                </div>
                                <span class="step-number" style="color:#8b5cf6;">03</span>
                                <span class="step-title text-truncate w-100">Admin</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 pt-3 border-top border-light-subtle d-flex flex-column flex-sm-row align-items-center justify-content-between gap-2">
                        <span class="text-muted fs-12 d-flex align-items-center gap-1 text-center text-sm-start">
                            <i class="ri-information-line text-primary fs-14"></i>
                            Remplissez les 3 sections ci-dessous pour créer l'église et lui délivrer immédiatement ses accès.
                        </span>
                        <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1 fs-11">
                            Configuration initiale complète
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- =====================================================================
             FORMULAIRE
        ===================================================================== --}}
        @if ($errors->any())
            <div class="row mt-3">
                <div class="col-12">
                    <div class="alert border-0 rounded-3 d-flex align-items-start gap-3 shadow-sm"
                         style="background:rgba(var(--vz-danger-rgb),.08); border-left:4px solid var(--vz-danger) !important; border-left-style:solid !important;">
                        <div class="flex-shrink-0 text-danger fs-20 mt-1"><i class="ri-error-warning-fill"></i></div>
                        <div>
                            <h6 class="fw-bold text-danger mb-1">Veuillez corriger les erreurs suivantes :</h6>
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

        <form action="{{ route('super-admin.churches.store') }}" method="POST" id="create-church-form" enctype="multipart/form-data">
            @csrf

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
                                <h5 class="mb-0 fw-bold fs-15">Informations de l'Église</h5>
                                <span class="text-muted fs-12">Identité et coordonnées de l'église cliente</span>
                            </div>
                        </div>
                        <div class="section-body">
                            <div class="row g-3">
                                {{-- Nom officiel --}}
                                <div class="col-12 col-md-6">
                                    <div class="form-floating-custom">
                                        <label>Nom officiel de l'église <span class="required-star">*</span></label>
                                        <input type="text" name="name"
                                            class="form-control @error('name') is-invalid @enderror"
                                            placeholder="Ex : Église Évangélique de la Grâce"
                                            value="{{ old('name') }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Ville --}}
                                <div class="col-12 col-md-6">
                                    <div class="form-floating-custom">
                                        <label>Ville / Localité <span class="required-star">*</span></label>
                                        <input type="text" name="city"
                                            class="form-control @error('city') is-invalid @enderror"
                                            placeholder="Ex : Cotonou, Abidjan, Lomé…"
                                            value="{{ old('city') }}" required>
                                        @error('city')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Logo officiel de l'église --}}
                                <div class="col-12">
                                    <div class="form-floating-custom">
                                        <label>Logo officiel de l'église <span class="text-muted fs-11 fw-normal">(optionnel)</span></label>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="bg-white border rounded-3 p-2 shadow-sm d-flex align-items-center justify-content-center flex-shrink-0" style="width: 64px; height: 64px;">
                                                <img id="church-logo-preview" src="{{ asset('assets/images/home/church-default.svg') }}" alt="Logo" class="img-fluid rounded" style="max-height: 50px; max-width: 50px; object-fit: contain;">
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
                                        <label>Email officiel <span class="text-muted fs-11 fw-normal">(optionnel)</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text" style="border-radius:10px 0 0 10px; border-right:none;">
                                                <i class="ri-mail-line text-muted"></i>
                                            </span>
                                            <input type="email" name="email"
                                                class="form-control @error('email') is-invalid @enderror"
                                                placeholder="contact@eglise.org"
                                                style="border-radius:0 10px 10px 0 !important; border-left:none;"
                                                value="{{ old('email') }}">
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                {{-- Téléphone --}}
                                <div class="col-12 col-md-6">
                                    <div class="form-floating-custom">
                                        <label>Numéro de téléphone <span class="text-muted fs-11 fw-normal">(optionnel)</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text" style="border-radius:10px 0 0 10px; border-right:none;">
                                                <i class="ri-phone-line text-muted"></i>
                                            </span>
                                            <input type="text" name="phone"
                                                class="form-control @error('phone') is-invalid @enderror"
                                                placeholder="+229 97 00 00 00"
                                                style="border-radius:0 10px 10px 0 !important; border-left:none;"
                                                value="{{ old('phone') }}">
                                            @error('phone')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                {{-- Adresse --}}
                                <div class="col-12">
                                    <div class="form-floating-custom">
                                        <label>Adresse géographique <span class="text-muted fs-11 fw-normal">(optionnel)</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text" style="border-radius:10px 0 0 10px; border-right:none;">
                                                <i class="ri-map-pin-line text-muted"></i>
                                            </span>
                                            <input type="text" name="address"
                                                class="form-control @error('address') is-invalid @enderror"
                                                placeholder="Quartier, Rue, Repère…"
                                                style="border-radius:0 10px 10px 0 !important; border-left:none;"
                                                value="{{ old('address') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- =================================================================
                 SECTION 2 : ABONNEMENT & PAIEMENT
            ================================================================= --}}
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card section-card">
                        <div class="section-header">
                            <div class="section-num bg-success-subtle text-success">
                                <i class="ri-calendar-check-line"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold fs-15">Abonnement Annuel &amp; Paiement</h5>
                                <span class="text-muted fs-12">Montant, mode de paiement et référence de la transaction</span>
                            </div>
                        </div>
                        <div class="section-body">
                            {{-- Info card abonnement --}}
                            <div class="sub-info-card mb-4 d-flex align-items-center gap-3">
                                <div style="width:38px;height:38px;border-radius:10px;background:rgba(34,197,94,.15);color:#16a34a;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <i class="ri-shield-check-line fs-18"></i>
                                </div>
                                <div>
                                    <div class="fw-bold fs-13 text-body">Abonnement automatique de 1 an</div>
                                    <div class="fs-12 text-muted">L'accès sera actif immédiatement et expirera dans <strong>365 jours</strong> à compter d'aujourd'hui.</div>
                                </div>
                            </div>

                            <div class="row g-3">
                                {{-- Montant --}}
                                <div class="col-12 col-md-4">
                                    <div class="form-floating-custom">
                                        <label>Montant reçu (FCFA) <span class="required-star">*</span></label>
                                        <div class="input-group">
                                            <input type="number" name="subscription_amount"
                                                class="form-control @error('subscription_amount') is-invalid @enderror"
                                                placeholder="150000"
                                                value="{{ old('subscription_amount', 150000) }}"
                                                required min="0" step="5000">
                                            <span class="input-group-text fw-bold text-success">FCFA</span>
                                        </div>
                                        @error('subscription_amount')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Mode de paiement --}}
                                <div class="col-12 col-md-4">
                                    <div class="form-floating-custom">
                                        <label>Mode de paiement <span class="required-star">*</span></label>
                                        <select name="payment_method" class="form-select @error('payment_method') is-invalid @enderror" required>
                                            <option value="Espèces"         {{ old('payment_method','Espèces') === 'Espèces'         ? 'selected' : '' }}>💵 Espèces (Main à main)</option>
                                            <option value="Orange Money"    {{ old('payment_method') === 'Orange Money'    ? 'selected' : '' }}>🟠 Orange Money</option>
                                            <option value="Wave"            {{ old('payment_method') === 'Wave'            ? 'selected' : '' }}>🌊 Wave</option>
                                            <option value="MTN Mobile Money"{{ old('payment_method') === 'MTN Mobile Money'? 'selected' : '' }}>🟡 MTN MoMo</option>
                                            <option value="Moov Money"      {{ old('payment_method') === 'Moov Money'      ? 'selected' : '' }}>🔵 Moov Money</option>
                                            <option value="Virement Bancaire"{{old('payment_method') === 'Virement Bancaire'?'selected' : '' }}>🏦 Virement Bancaire</option>
                                            <option value="Chèque"          {{ old('payment_method') === 'Chèque'          ? 'selected' : '' }}>📄 Chèque</option>
                                        </select>
                                        @error('payment_method')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Référence --}}
                                <div class="col-12 col-md-4">
                                    <div class="form-floating-custom">
                                        <label>Référence / N° reçu <span class="text-muted fs-11 fw-normal">(optionnel)</span></label>
                                        <input type="text" name="payment_reference"
                                            class="form-control font-monospace @error('payment_reference') is-invalid @enderror"
                                            placeholder="Ex : TX-9842187"
                                            value="{{ old('payment_reference') }}">
                                        <div class="form-hint"><i class="ri-information-line"></i> Auto-généré si laissé vide</div>
                                    </div>
                                </div>

                                {{-- Notes --}}
                                <div class="col-12">
                                    <div class="form-floating-custom">
                                        <label>Notes / Commentaires <span class="text-muted fs-11 fw-normal">(optionnel)</span></label>
                                        <textarea name="notes" class="form-control" rows="2"
                                            placeholder="Informations complémentaires sur le paiement ou l'accord…">{{ old('notes') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- =================================================================
                 SECTION 3 : COMPTE ADMINISTRATEUR
            ================================================================= --}}
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card section-card">
                        <div class="section-header">
                            <div class="section-num bg-warning-subtle text-warning">
                                <i class="ri-user-star-line"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold fs-15">Compte Administrateur de l'Église</h5>
                                <span class="text-muted fs-12">Identifiants du responsable qui gérera la plateforme de cette église</span>
                            </div>
                        </div>
                        <div class="section-body">
                            <div class="row g-3">
                                {{-- Prénom --}}
                                <div class="col-12 col-sm-6">
                                    <div class="form-floating-custom">
                                        <label>Prénom <span class="required-star">*</span></label>
                                        <input type="text" name="admin_first_name"
                                            class="form-control @error('admin_first_name') is-invalid @enderror"
                                            placeholder="Ex : Paul"
                                            value="{{ old('admin_first_name') }}" required>
                                        @error('admin_first_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Nom de famille --}}
                                <div class="col-12 col-sm-6">
                                    <div class="form-floating-custom">
                                        <label>Nom de famille <span class="required-star">*</span></label>
                                        <input type="text" name="admin_name"
                                            class="form-control @error('admin_name') is-invalid @enderror"
                                            placeholder="Ex : KOUAMÉ"
                                            value="{{ old('admin_name') }}" required>
                                        @error('admin_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Email de connexion --}}
                                <div class="col-12 col-md-6">
                                    <div class="form-floating-custom">
                                        <label>Adresse Email <span class="text-muted fs-11 fw-normal">(Requis si pas de tél.)</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text" style="border-radius:10px 0 0 10px; border-right:none;">
                                                <i class="ri-mail-line text-muted"></i>
                                            </span>
                                            <input type="email" name="admin_email"
                                                class="form-control @error('admin_email') is-invalid @enderror"
                                                placeholder="admin@eglise.org"
                                                style="border-radius:0 10px 10px 0 !important; border-left:none;"
                                                value="{{ old('admin_email') }}">
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
                                        <label>Numéro de Téléphone <span class="text-muted fs-11 fw-normal">(Requis si pas d'email)</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text" style="border-radius:10px 0 0 10px; border-right:none;">
                                                <i class="ri-phone-line text-muted"></i>
                                            </span>
                                            <input type="text" name="admin_phone"
                                                class="form-control @error('admin_phone') is-invalid @enderror"
                                                placeholder="Ex : +22990000000"
                                                style="border-radius:0 10px 10px 0 !important; border-left:none;"
                                                value="{{ old('admin_phone') }}">
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

                            {{-- Encadré génération automatique des identifiants --}}
                            <div class="mt-4 p-3 rounded-3 border d-flex align-items-start gap-3"
                                 style="background: linear-gradient(135deg, rgba(var(--vz-primary-rgb), 0.06) 0%, rgba(var(--vz-info-rgb), 0.08) 100%); border-color: rgba(var(--vz-primary-rgb), 0.25) !important;">
                                <div class="avatar-sm rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                     style="background: rgba(var(--vz-primary-rgb), 0.15); color: var(--vz-primary); width: 42px; height: 42px;">
                                    <i class="ri-mail-send-line fs-20"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold fs-13 text-body mb-1">
                                        <i class="ri-lock-password-line me-1 text-primary"></i> Génération et transmission automatique des identifiants
                                    </h6>
                                    <p class="mb-0 fs-12 text-muted" style="line-height: 1.5;">
                                        Un mot de passe temporaire sera généré et envoyé :<br>
                                        • Par <strong>Email</strong> si renseigné.<br>
                                        • Par <strong>WhatsApp</strong> sinon.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- =================================================================
                 BARRE D'ACTION
            ================================================================= --}}
            <div class="action-bar mt-4 mb-2 rounded-3">
                <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
                    <div class="text-muted fs-12 d-flex align-items-center gap-1">
                        <i class="ri-error-warning-line text-danger"></i>
                        Les champs marqués <span class="text-danger fw-bold mx-1">*</span> sont obligatoires.
                    </div>
                    <div class="action-bar-actions d-flex gap-2 flex-wrap justify-content-end">
                        <button type="submit" class="btn-submit-main" id="btn-create-church">
                            <i class="ri-add-circle-fill fs-16"></i>
                            <span>Créer l'église &amp; Activer l'abonnement</span>
                        </button>
                        <a href="{{ route('super-admin.churches.index') }}" class="btn-cancel">
                            <i class="ri-close-line"></i> Annuler
                        </a>
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

    // Prévenir double-soumission
    document.getElementById('create-church-form').addEventListener('submit', function () {
        const btn = document.getElementById('btn-create-church');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Création en cours…';
    });
</script>
@endpush
@endsection
