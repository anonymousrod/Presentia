@extends('layouts.app')

@section('content')
<style>
    .success-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: calc(80vh - 100px);
        padding: 1.25rem 0.75rem;
    }
    .scan-card {
        background: rgba(255, 255, 255, 0.92);
        backdrop-filter: blur(16px) saturate(180%);
        -webkit-backdrop-filter: blur(16px) saturate(180%);
        border: 1px solid rgba(209, 213, 219, 0.35);
        border-radius: 24px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        padding: 2.5rem 2rem;
        max-width: 520px;
        width: 100%;
        text-align: center;
        box-sizing: border-box;
        transition: all 0.3s ease;
    }
    .checkmark-wrapper {
        width: 84px;
        height: 84px;
        border-radius: 50%;
        background: linear-gradient(135deg, #10B981, #059669);
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 0 auto 1.5rem;
        box-shadow: 0 10px 20px rgba(16, 185, 129, 0.3);
        animation: scaleIn 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
    }
    .checkmark-icon {
        font-size: 42px;
        color: white;
    }
    .pulse-ring {
        position: absolute;
        width: 84px;
        height: 84px;
        border-radius: 50%;
        border: 3px solid rgba(16, 185, 129, 0.4);
        animation: pulseRing 2s cubic-bezier(0.215, 0.610, 0.355, 1) infinite;
    }
    .scan-title {
        font-family: 'Outfit', 'Inter', sans-serif;
        font-weight: 800;
        font-size: clamp(1.6rem, 5vw, 2.2rem);
        background: linear-gradient(135deg, #4F46E5, #312E81);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 0.5rem;
        line-height: 1.25;
    }
    .scan-subtitle {
        font-size: 0.98rem;
        color: #6B7280;
        margin-bottom: 1.75rem;
        line-height: 1.5;
    }
    .activity-detail-card {
        background: #F9FAFB;
        border: 1px solid #E5E7EB;
        border-radius: 16px;
        padding: 1.25rem 1.25rem;
        text-align: left;
        margin-bottom: 2rem;
    }
    .detail-row {
        display: flex;
        align-items: baseline;
        margin-bottom: 0.75rem;
        font-size: 0.93rem;
    }
    .detail-row:last-child {
        margin-bottom: 0;
    }
    .detail-label {
        font-weight: 600;
        color: #6B7280;
        width: 125px;
        flex-shrink: 0;
        font-size: 0.88rem;
    }
    .detail-value {
        color: #111827;
        font-weight: 500;
        word-break: break-word;
    }
    .btn-gradient {
        background: linear-gradient(135deg, #4F46E5, #4338CA);
        color: white !important;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 12px;
        font-weight: 600;
        letter-spacing: 0.3px;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .btn-gradient:hover {
        background: linear-gradient(135deg, #4338CA, #3730A3);
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(79, 70, 229, 0.3);
    }
    .btn-outline-responsive {
        border-radius: 12px;
        font-weight: 600;
        padding: 0.75rem 1.5rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    @media (max-width: 576px) {
        .success-container {
            padding: 1rem 0.5rem;
            min-height: auto;
        }
        .scan-card {
            padding: 1.5rem 1.15rem;
            border-radius: 20px;
        }
        .checkmark-wrapper, .pulse-ring {
            width: 72px;
            height: 72px;
        }
        .checkmark-icon {
            font-size: 36px;
        }
        .activity-detail-card {
            padding: 1rem 0.9rem;
            margin-bottom: 1.5rem;
        }
        .detail-row {
            flex-direction: column;
            align-items: flex-start;
            gap: 2px;
            margin-bottom: 0.65rem;
        }
        .detail-label {
            width: 100%;
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #9CA3AF;
        }
        .detail-value {
            font-size: 0.95rem;
        }
    }

    @keyframes scaleIn {
        0% { transform: scale(0); }
        100% { transform: scale(1); }
    }
    @keyframes pulseRing {
        0% {
            transform: scale(0.95);
            opacity: 0.8;
        }
        50% {
            opacity: 0.4;
        }
        100% {
            transform: scale(1.3);
            opacity: 0;
        }
    }
</style>

<div class="success-container">
    <div class="scan-card">
        <div class="position-relative d-inline-block">
            <div class="pulse-ring"></div>
            <div class="checkmark-wrapper">
                <i class="mdi mdi-check checkmark-icon"></i>
            </div>
        </div>

        <h1 class="scan-title" id="scan-page-title">Présence Validée !</h1>
        <p class="scan-subtitle">
            Bonjour <strong>{{ auth()->user()->first_name }} {{ auth()->user()->name }}</strong>, 
            votre participation à cette activité a été enregistrée avec succès.
        </p>

        @if(session('info'))
            <div class="alert alert-info py-2 px-3 fs-13 mb-3 text-start rounded-3">
                <i class="mdi mdi-information-outline me-1"></i> {{ session('info') }}
            </div>
        @endif

        <div class="activity-detail-card">
            <div class="detail-row">
                <span class="detail-label"><i class="mdi mdi-bookmark-outline me-1"></i>Activité</span>
                <span class="detail-value text-primary fw-bold">{{ $activity->title }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label"><i class="mdi mdi-clock-outline me-1"></i>Date / Heure</span>
                <span class="detail-value">{{ $activity->start_time->format('d/m/Y H:i') }}</span>
            </div>
            @if($activity->location)
            <div class="detail-row">
                <span class="detail-label"><i class="mdi mdi-map-marker-outline me-1"></i>Lieu</span>
                <span class="detail-value">{{ $activity->location }}</span>
            </div>
            @endif
            @if($activity->responsible)
            <div class="detail-row">
                <span class="detail-label"><i class="mdi mdi-account-outline me-1"></i>Responsable</span>
                <span class="detail-value">{{ $activity->responsible->first_name }} {{ $activity->responsible->name }}</span>
            </div>
            @endif
            <div class="detail-row">
                <span class="detail-label"><i class="mdi mdi-calendar-check-outline me-1"></i>Émargé le</span>
                <span class="detail-value text-success fw-semibold">{{ now()->format('d/m/Y \à H:i') }}</span>
            </div>
        </div>

        <div class="d-flex flex-column flex-sm-row justify-content-center gap-2 gap-sm-3 w-100">
            <a href="{{ route('dashboard') }}" class="btn btn-gradient w-100 w-sm-auto" id="btn-back-dashboard">
                <i class="mdi mdi-view-dashboard-outline me-2"></i>Tableau de bord
            </a>
            <a href="{{ route('activities.index') }}" class="btn btn-outline-secondary border-2 btn-outline-responsive w-100 w-sm-auto" id="btn-back-activities">
                <i class="mdi mdi-calendar-text me-2"></i>Mes activités
            </a>
        </div>
    </div>
</div>
@endsection
