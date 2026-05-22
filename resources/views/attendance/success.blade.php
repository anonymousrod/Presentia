@extends('layouts.app')

@section('content')
<style>
    .success-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 70vh;
    }
    .scan-card {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(16px) saturate(180%);
        -webkit-backdrop-filter: blur(16px) saturate(180%);
        border: 1px solid rgba(209, 213, 219, 0.3);
        border-radius: 24px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        padding: 3rem;
        max-width: 550px;
        width: 100%;
        text-align: center;
        transition: all 0.3s ease;
    }
    .scan-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
    }
    .checkmark-wrapper {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        background: linear-gradient(135deg, #10B981, #059669);
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 0 auto 2rem;
        box-shadow: 0 10px 20px rgba(16, 185, 129, 0.3);
        animation: scaleIn 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
    }
    .checkmark-icon {
        font-size: 45px;
        color: white;
    }
    .pulse-ring {
        position: absolute;
        width: 90px;
        height: 90px;
        border-radius: 50%;
        border: 3px solid rgba(16, 185, 129, 0.4);
        animation: pulseRing 2s cubic-bezier(0.215, 0.610, 0.355, 1) infinite;
    }
    .scan-title {
        font-family: 'Outfit', 'Inter', sans-serif;
        font-weight: 800;
        font-size: 2.25rem;
        background: linear-gradient(135deg, #4F46E5, #312E81);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 0.75rem;
    }
    .scan-subtitle {
        font-size: 1.05rem;
        color: #6B7280;
        margin-bottom: 2.5rem;
        line-height: 1.5;
    }
    .activity-detail-card {
        background: #F9FAFB;
        border: 1px solid #F3F4F6;
        border-radius: 16px;
        padding: 1.5rem;
        text-align: left;
        margin-bottom: 2.5rem;
    }
    .detail-row {
        display: flex;
        margin-bottom: 0.75rem;
        font-size: 0.95rem;
    }
    .detail-row:last-child {
        margin-bottom: 0;
    }
    .detail-label {
        font-weight: 600;
        color: #4B5563;
        width: 130px;
        flex-shrink: 0;
    }
    .detail-value {
        color: #111827;
        font-weight: 500;
    }
    .btn-gradient {
        background: linear-gradient(135deg, #4F46E5, #4338CA);
        color: white;
        border: none;
        padding: 0.85rem 2rem;
        border-radius: 12px;
        font-weight: 600;
        letter-spacing: 0.5px;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);
        transition: all 0.2s ease;
    }
    .btn-gradient:hover {
        background: linear-gradient(135deg, #4338CA, #3730A3);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(79, 70, 229, 0.3);
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

        <div class="activity-detail-card">
            <div class="detail-row">
                <span class="detail-label"><i class="mdi mdi-bookmark-outline me-2"></i>Activité :</span>
                <span class="detail-value text-primary fw-bold">{{ $activity->title }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label"><i class="mdi mdi-clock-outline me-2"></i>Date / Heure :</span>
                <span class="detail-value">{{ $activity->start_time->format('d/m/Y H:i') }}</span>
            </div>
            @if($activity->location)
            <div class="detail-row">
                <span class="detail-label"><i class="mdi mdi-map-marker-outline me-2"></i>Lieu :</span>
                <span class="detail-value">{{ $activity->location }}</span>
            </div>
            @endif
            @if($activity->responsible)
            <div class="detail-row">
                <span class="detail-label"><i class="mdi mdi-account-outline me-2"></i>Responsable :</span>
                <span class="detail-value">{{ $activity->responsible->first_name }} {{ $activity->responsible->name }}</span>
            </div>
            @endif
            <div class="detail-row">
                <span class="detail-label"><i class="mdi mdi-calendar-check-outline me-2"></i>Émargé le :</span>
                <span class="detail-value text-success">{{ now()->format('d/m/Y \à H:i') }}</span>
            </div>
        </div>

        <div class="d-flex justify-content-center gap-3">
            <a href="{{ route('dashboard') }}" class="btn btn-gradient" id="btn-back-dashboard">
                <i class="mdi mdi-view-dashboard-outline me-2"></i>Tableau de bord
            </a>
            <a href="{{ route('activities.index') }}" class="btn btn-outline-secondary px-4 py-2 border-2" style="border-radius: 12px; font-weight: 600;" id="btn-back-activities">
                <i class="mdi mdi-calendar-text me-2"></i>Mes activités
            </a>
        </div>
    </div>
</div>
@endsection
