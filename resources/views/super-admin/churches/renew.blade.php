@extends('layouts.app')

@section('title', 'Renouveler Abonnement : ' . $church->name)

@push('css')
<style>
    /* =========================================================================
       HERO COMPACT (STYLE UNIFORMISÉ PLATEFORME SAAS)
    ========================================================================= */
    .renew-hero {
        background-color: #0b0f19;
        background-image:
            radial-gradient(circle at 10% 30%, rgba(var(--vz-primary-rgb), 0.32) 0%, transparent 45%),
            radial-gradient(circle at 90% 70%, rgba(var(--vz-success-rgb), 0.2) 0%, transparent 45%);
        padding: 2rem 0 4.5rem 0;
        position: relative;
        overflow: hidden;
        margin: -1.5rem -1.5rem 0 -1.5rem;
        border-bottom-left-radius: 2rem;
        border-bottom-right-radius: 2rem;
    }
    .renew-hero .hero-grid {
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        background-image:
            linear-gradient(to right, rgba(255,255,255,0.03) 1px, transparent 1px),
            linear-gradient(to bottom, rgba(255,255,255,0.03) 1px, transparent 1px);
        background-size: 40px 40px;
        pointer-events: none;
        z-index: 1;
    }
    .renew-hero .orb {
        position: absolute;
        border-radius: 50%;
        filter: blur(80px);
        opacity: 0.45;
        z-index: 0;
    }
    .renew-hero .orb-blue  { width: 300px; height: 300px; background: var(--vz-primary); top: -120px; left: -80px; }
    .renew-hero .orb-green { width: 240px; height: 240px; background: var(--vz-success); bottom: -60px; right: 8%; }
    .renew-hero .hero-content { position: relative; z-index: 5; }

    .hero-badge {
        background: rgba(var(--vz-success-rgb), 0.15);
        border: 1px solid rgba(var(--vz-success-rgb), 0.35);
        color: #10b981;
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

    .form-wrap-overlap {
        margin-top: -2.5rem;
        position: relative;
        z-index: 10;
    }

    .renew-card {
        border-radius: 1.25rem;
        background: #ffffff;
        border: 1px solid rgba(226, 232, 240, 0.85);
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
    }

    @media (max-width: 991.98px) {
        .renew-hero { padding: 1.75rem 0 4.5rem 0; text-align: center; }
        .hero-back-wrap { justify-content: center !important; margin-top: 1rem; }
    }
    @media (max-width: 767.98px) {
        .form-wrap-overlap { margin-top: -1.75rem; }
    }
    @media (max-width: 575.98px) {
        .renew-hero { padding: 1.5rem 0 3.5rem 0; }
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">

        {{-- =====================================================================
             HERO COMPACT
        ===================================================================== --}}
        <div class="renew-hero px-3 px-md-4">
            <div class="hero-grid"></div>
            <div class="orb orb-blue"></div>
            <div class="orb orb-green"></div>

            <div class="container-fluid hero-content">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <div class="mb-2 d-flex justify-content-center justify-content-lg-start align-items-center gap-2">
                            <span class="badge hero-badge px-3 py-2 rounded-pill">
                                <i class="ri-refresh-line me-1"></i> RENOUVELLEMENT D'ABONNEMENT
                            </span>
                            <span class="badge bg-white bg-opacity-10 text-white border border-white border-opacity-25 px-2 py-1 fs-11 font-monospace rounded-pill">
                                {{ $church->code }}
                            </span>
                        </div>
                        <h1 class="fs-22 fs-md-28 fw-bold text-white mb-1 text-center text-lg-start" style="line-height:1.25;">
                            Renouveler : <span style="color:#10b981;">{{ $church->name }}</span>
                        </h1>
                        <p class="fs-13 mb-0 text-center text-lg-start" style="color:rgba(255,255,255,0.7); max-width:560px;">
                            Prolongez l'accès de l'église pour une durée d'une année supplémentaire (365 jours) et enregistrez le règlement.
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

        {{-- =====================================================================
             FORMULAIRE DE RENOUVELLEMENT
        ===================================================================== --}}
        <div class="container-fluid max-w-900 px-1 px-md-3 form-wrap-overlap mb-4">
            
            {{-- État Actuel --}}
            <div class="card renew-card mb-4 overflow-hidden">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar-sm rounded-3 bg-success-subtle text-success d-flex align-items-center justify-content-center flex-shrink-0" style="width:48px; height:48px;">
                                <i class="ri-building-line fs-22"></i>
                            </div>
                            <div>
                                <span class="text-muted fs-11 fw-bold text-uppercase d-block mb-0">Église Bénéficiaire</span>
                                <h5 class="fw-bold text-body mb-0 fs-16">{{ $church->name }}</h5>
                                <span class="fs-12 text-muted">{{ $church->city ?? 'Bénin' }}</span>
                            </div>
                        </div>

                        <div>
                            <span class="text-muted fs-11 fw-bold text-uppercase d-block mb-1">Expiration Actuelle</span>
                            <div class="d-flex align-items-center gap-2">
                                <strong class="text-body fs-14">
                                    {{ $church->subscription_expires_at ? $church->subscription_expires_at->format('d/m/Y') : 'Non définie' }}
                                </strong>
                                @if($church->isSubscriptionActive())
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fs-11">
                                        Actif ({{ $church->daysLeftInSubscription() }}j restants)
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 fs-11">
                                        Expiré
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Formulaire --}}
            <div class="card renew-card overflow-hidden">
                <div class="card-header bg-transparent border-0 py-3 px-4 d-flex align-items-center gap-2" style="border-bottom: 1px solid rgba(var(--vz-dark-rgb), 0.05) !important;">
                    <div class="avatar-xs bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center">
                        <i class="ri-money-dollar-circle-line fs-16"></i>
                    </div>
                    <h5 class="card-title mb-0 fw-bold fs-15 text-body">Détails du Renouvellement Annuel (1 An)</h5>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('super-admin.churches.renew', $church) }}" method="POST">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fs-12 fw-bold text-uppercase text-muted">Montant du renouvellement (FCFA) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" name="amount" class="form-control" placeholder="150000" value="{{ old('amount', $church->subscription_amount ?? 150000) }}" required min="0" step="5000" style="border-radius:10px 0 0 10px;">
                                    <span class="input-group-text bg-body-tertiary fs-12 fw-semibold" style="border-radius:0 10px 10px 0;">FCFA</span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fs-12 fw-bold text-uppercase text-muted">Mode de paiement reçu <span class="text-danger">*</span></label>
                                <select name="payment_method" class="form-select" required style="border-radius:10px;">
                                    <option value="Espèces" {{ old('payment_method') === 'Espèces' ? 'selected' : '' }}>Espèces (Main à main)</option>
                                    <option value="Orange Money" {{ old('payment_method') === 'Orange Money' ? 'selected' : '' }}>Orange Money</option>
                                    <option value="Wave" {{ old('payment_method') === 'Wave' ? 'selected' : '' }}>Wave</option>
                                    <option value="MTN Mobile Money" {{ old('payment_method') === 'MTN Mobile Money' ? 'selected' : '' }}>MTN MoMo</option>
                                    <option value="Moov Money" {{ old('payment_method') === 'Moov Money' ? 'selected' : '' }}>Moov Money</option>
                                    <option value="Virement Bancaire" {{ old('payment_method') === 'Virement Bancaire' ? 'selected' : '' }}>Virement Bancaire</option>
                                    <option value="Chèque" {{ old('payment_method') === 'Chèque' ? 'selected' : '' }}>Chèque</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label fs-12 fw-bold text-uppercase text-muted">Référence / Reçu de paiement (Optionnel)</label>
                                <input type="text" name="payment_reference" class="form-control" placeholder="Ex: RENEW-202608-412" value="{{ old('payment_reference') }}" style="border-radius:10px;">
                            </div>

                            <div class="col-12">
                                <label class="form-label fs-12 fw-bold text-uppercase text-muted">Notes / Commentaires de renouvellement</label>
                                <textarea name="notes" class="form-control" rows="3" placeholder="Informations relatives au paiement ou conditions particulières..." style="border-radius:10px;">{{ old('notes') }}</textarea>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top border-light-subtle flex-wrap">
                            <a href="{{ route('super-admin.churches.show', $church) }}" class="btn btn-light rounded-pill px-4 fs-13">Annuler</a>
                            <button type="submit" class="btn btn-success rounded-pill px-4 shadow-sm fs-13 fw-semibold">
                                <i class="ri-check-line me-1"></i> Valider le Renouvellement de 1 An
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>

    </div>
</div>
@endsection
