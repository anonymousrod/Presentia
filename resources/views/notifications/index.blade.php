@extends('layouts.app')

@push('css')
<style>
    /* ── Notification Page ── */
    .notif-header {
        background: linear-gradient(135deg, var(--vz-primary) 0%, #6366f1 100%);
        border-radius: 0 0 24px 24px;
        padding: 24px 20px;
        color: #fff;
    }
    .notif-header h4 { color: #fff; }
    .notif-header p  { color: rgba(255,255,255,.7); }

    .notif-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }
    .notif-actions .btn {
        font-size: 13px;
        padding: 6px 14px;
        border-radius: 20px;
        border: none;
    }
    .notif-actions .btn-mark-read {
        background: rgba(255,255,255,.2);
        color: #fff;
        backdrop-filter: blur(4px);
    }
    .notif-actions .btn-mark-read:hover { background: rgba(255,255,255,.35); }
    .notif-actions .btn-delete-all {
        background: rgba(255,80,80,.25);
        color: #fff;
        backdrop-filter: blur(4px);
    }
    .notif-actions .btn-delete-all:hover { background: rgba(255,80,80,.45); }

    /* ── Liste ── */
    .notif-list {
        padding: 16px;
    }
    .notif-item {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        padding: 16px;
        border-radius: 14px;
        margin-bottom: 10px;
        transition: all .25s ease;
        position: relative;
        background: var(--vz-card-bg);
        border: 1px solid var(--vz-border-color);
    }
    .notif-item:hover {
        box-shadow: 0 4px 20px rgba(0,0,0,.06);
        transform: translateY(-1px);
    }
    .notif-item.unread {
        background: rgba(var(--vz-primary-rgb), .04);
        border-left: 3px solid var(--vz-primary);
    }

    .notif-icon {
        width: 44px;
        height: 44px;
        min-width: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }

    .notif-body {
        flex: 1;
        min-width: 0; /* empêche le débordement */
    }
    .notif-title {
        font-size: 14px;
        margin-bottom: 4px;
        line-height: 1.4;
        word-break: break-word;
    }
    .notif-title a { color: inherit; text-decoration: none; }
    .notif-title a:hover { color: var(--vz-primary); }

    .notif-message {
        font-size: 13px;
        color: var(--vz-secondary-color);
        margin-bottom: 6px;
        line-height: 1.5;
        word-break: break-word;
    }

    .notif-meta {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }
    .notif-time {
        font-size: 12px;
        color: var(--vz-secondary-color);
        white-space: nowrap;
    }
    .notif-badge-new {
        font-size: 10px;
        padding: 2px 8px;
        border-radius: 10px;
        background: var(--vz-danger);
        color: #fff;
        font-weight: 600;
    }

    /* ── Bouton d'actions (3 points) ── */
    .notif-menu {
        position: absolute;
        top: 12px;
        right: 12px;
    }

    /* ── Empty state ── */
    .notif-empty {
        text-align: center;
        padding: 60px 20px;
    }
    .notif-empty-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: rgba(var(--vz-primary-rgb), .08);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 16px;
    }

    /* ── Mobile fixes ── */
    @media (max-width: 575.98px) {
        .notif-header { padding: 20px 16px; border-radius: 0 0 16px 16px; }
        .notif-list   { padding: 12px; }
        .notif-item   { padding: 12px; gap: 10px; }
        .notif-icon   { width: 38px; height: 38px; min-width: 38px; font-size: 17px; border-radius: 10px; }
        .notif-title  { font-size: 13px; }
        .notif-message { font-size: 12px; }
        .notif-actions { width: 100%; }
        .notif-actions .btn { flex: 1; text-align: center; }
    }
</style>
@endpush

@section('content')

{{-- ── Header gradient ── --}}
<div class="notif-header">
    <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between gap-3">
        <div>
            <h4 class="fs-18 fw-semibold mb-1">
                <i class="mdi mdi-bell-ring-outline me-1"></i> Notifications
            </h4>
            <p class="mb-0 fs-13">
                @if(auth()->user()->unreadNotifications->count() > 0)
                    Vous avez <strong>{{ auth()->user()->unreadNotifications->count() }}</strong> notification{{ auth()->user()->unreadNotifications->count() > 1 ? 's' : '' }} non lue{{ auth()->user()->unreadNotifications->count() > 1 ? 's' : '' }}.
                @else
                    Vous êtes à jour ! Aucune nouvelle notification.
                @endif
            </p>
        </div>
        <div class="notif-actions">
            @if(auth()->user()->unreadNotifications->count() > 0)
            <form method="POST" action="{{ route('notifications.read-all') }}">
                @csrf
                <button type="submit" class="btn btn-mark-read">
                    <i class="ri-check-double-line me-1"></i> Tout marquer lu
                </button>
            </form>
            @endif
            @if(auth()->user()->notifications()->count() > 0)
            <button type="button" class="btn btn-delete-all" data-bs-toggle="modal" data-bs-target="#deleteAllModal">
                <i class="mdi mdi-trash-can-outline me-1"></i> Tout supprimer
            </button>
            @endif
        </div>
    </div>
</div>

{{-- ── Liste des notifications ── --}}
<div class="notif-list">
    @forelse($notifications as $notification)
        @php
            $nData   = $notification->data;
            $icon    = $nData['icon']    ?? 'mdi mdi-bell-outline';
            $color   = $nData['color']   ?? 'primary';
            $title   = $nData['title']   ?? 'Notification';
            $message = $nData['message'] ?? '';
            $url     = $nData['url']     ?? route('dashboard');
            $isUnread = is_null($notification->read_at);
        @endphp

        <div class="notif-item {{ $isUnread ? 'unread' : '' }}">
            {{-- Icône --}}
            <div class="notif-icon bg-{{ $color }}-subtle text-{{ $color }}">
                <i class="{{ $icon }}"></i>
            </div>

            {{-- Contenu --}}
            <div class="notif-body">
                <div class="notif-title {{ $isUnread ? 'fw-semibold' : '' }}">
                    <a href="{{ route('notifications.read', $notification->id) }}">{{ $title }}</a>
                </div>
                @if($message)
                <div class="notif-message">{{ Str::limit($message, 120) }}</div>
                @endif
                <div class="notif-meta">
                    <span class="notif-time">
                        <i class="mdi mdi-clock-outline me-1"></i>{{ $notification->created_at->diffForHumans() }}
                    </span>
                    @if($isUnread)
                        <span class="notif-badge-new">Nouveau</span>
                    @endif
                    @if($url && $url !== route('dashboard'))
                    <a href="{{ route('notifications.read', $notification->id) }}" class="text-primary fs-12 fw-medium text-decoration-none">
                        Voir détails <i class="mdi mdi-arrow-right"></i>
                    </a>
                    @endif
                </div>
            </div>

            {{-- Menu actions --}}
            <div class="notif-menu">
                <div class="dropdown">
                    <button class="btn btn-sm btn-icon btn-ghost-secondary rounded-circle" type="button" data-bs-toggle="dropdown">
                        <i class="mdi mdi-dots-vertical fs-18"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                        @if($isUnread)
                        <li>
                            <a class="dropdown-item fs-13" href="{{ route('notifications.read', $notification->id) }}">
                                <i class="mdi mdi-check me-2 text-success"></i> Marquer comme lu
                            </a>
                        </li>
                        @endif
                        <li>
                            <form method="POST" action="{{ route('notifications.destroy', $notification->id) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="dropdown-item fs-13 text-danger">
                                    <i class="mdi mdi-trash-can-outline me-2"></i> Supprimer
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    @empty
        <div class="notif-empty">
            <div class="notif-empty-icon">
                <i class="mdi mdi-bell-check-outline fs-36 text-primary"></i>
            </div>
            <h5 class="fw-semibold mb-2">Tout est en ordre !</h5>
            <p class="text-muted fs-14 mb-0">Vous n'avez reçu aucune notification pour le moment.</p>
        </div>
    @endforelse

    {{-- Pagination --}}
    @if($notifications->hasPages())
    <div class="d-flex justify-content-center mt-3">
        {{ $notifications->links() }}
    </div>
    @endif
</div>

{{-- Delete All Confirmation Modal --}}
<div class="modal fade" id="deleteAllModal" tabindex="-1" aria-labelledby="deleteAllModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteAllModalLabel">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Êtes-vous sûr de vouloir supprimer <strong>toutes</strong> vos notifications ? Cette action est irréversible.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                <form method="POST" action="{{ route('notifications.destroy-all') }}" class="m-0">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Oui, tout supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
