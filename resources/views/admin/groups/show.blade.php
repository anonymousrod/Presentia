@extends('layouts.app')

@section('content')

{{-- =================== BREADCRUMB & EN-TÊTE =================== --}}
<div class="row mb-4">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.groups.index') }}">Groupes</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $group->name }}</li>
            </ol>
        </nav>

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
            <div>
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    {{-- Avatar du groupe --}}
                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold"
                         style="width:56px; height:56px; font-size:1.4rem;
                                background: rgba(var(--vz-primary-rgb), 0.15);
                                color: var(--vz-primary); flex-shrink:0;">
                        {{ strtoupper(substr($group->name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <h1 class="h3 mb-0 fw-bold">{{ $group->name }}</h1>
                            @if($group->category)
                                <span class="badge rounded-pill px-3 py-2"
                                      style="background: rgba(var(--vz-primary-rgb), 0.15); color: var(--vz-primary); font-size: 0.78rem;">
                                    <i class="mdi mdi-tag-outline me-1"></i>{{ $group->category }}
                                </span>
                            @endif
                        </div>
                        <p class="text-muted mb-0 mt-1" style="font-size: 0.88rem;">
                            Gérer les membres, le chef et l'historique du groupe.
                        </p>
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2 flex-shrink-0">
                <a href="javascript:history.back()" class="btn btn-sm btn-outline-secondary px-3" style="border-radius: 0.5rem;">
                    <i class="mdi mdi-arrow-left me-1"></i>Retour
                </a>
                @can('update', $group)
                <a href="{{ route('admin.groups.edit', $group) }}" class="btn btn-sm btn-primary px-3" style="border-radius: 0.5rem;">
                    <i class="mdi mdi-pencil me-1"></i>Modifier
                </a>
                @endcan
            </div>
        </div>
    </div>
</div>

{{-- Alertes --}}
@if(session('success'))
    <div class="alert border-0 mb-4 d-flex align-items-center gap-3 p-3 shadow-sm"
         style="background: rgba(var(--vz-success-rgb), 0.12); border-left: 4px solid var(--vz-success) !important; border-radius: 0.5rem;">
        <i class="mdi mdi-check-circle fs-20" style="color: var(--vz-success);"></i>
        <span>{{ session('success') }}</span>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert border-0 mb-4 d-flex align-items-center gap-3 p-3 shadow-sm"
         style="background: rgba(var(--vz-danger-rgb), 0.12); border-left: 4px solid var(--vz-danger) !important; border-radius: 0.5rem;">
        <i class="mdi mdi-alert-circle fs-20" style="color: var(--vz-danger);"></i>
        <span>{{ session('error') }}</span>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- =================== CONTENU PRINCIPAL =================== --}}
<div class="row g-4">

    {{-- ======= COLONNE GAUCHE : Membres & Historique ======= --}}
    <div class="col-lg-8">

        {{-- ---- Carte Membres Actifs ---- --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header border-0 py-3 px-4 d-flex align-items-center justify-content-between"
                 style="border-bottom: 1px solid rgba(var(--vz-border-color-translucent), 1) !important;">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded p-2 flex-shrink-0"
                         style="background: rgba(var(--vz-primary-rgb), 0.15); width:40px; height:40px; display:flex; align-items:center; justify-content:center;">
                        <i class="mdi mdi-account-group fs-20" style="color: var(--vz-primary);"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold">Membres Actifs</h5>
                        <small class="text-muted">{{ $activeMembers->count() }} membre(s) dans le groupe</small>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead style="background: rgba(var(--vz-primary-rgb), 0.06);">
                            <tr>
                                <th class="ps-4 py-3 fw-semibold" style="font-size: 0.78rem; text-transform:uppercase; letter-spacing:0.05em;">
                                    <i class="mdi mdi-account me-1 text-muted"></i>Membre
                                </th>
                                <th class="py-3 fw-semibold" style="font-size: 0.78rem; text-transform:uppercase; letter-spacing:0.05em;">
                                    <i class="mdi mdi-email-outline me-1 text-muted"></i>Contact
                                </th>
                                <th class="py-3 fw-semibold" style="font-size: 0.78rem; text-transform:uppercase; letter-spacing:0.05em;">
                                    <i class="mdi mdi-calendar-check me-1 text-muted"></i>Rejoint le
                                </th>
                                @can('assignMember', $group)
                                <th class="py-3 fw-semibold text-end pe-4" style="font-size: 0.78rem; text-transform:uppercase; letter-spacing:0.05em;">
                                    Actions
                                </th>
                                @endcan
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($activeMembers as $member)
                            <tr class="border-bottom" style="transition: background 0.15s;">
                                <td class="ps-4 py-3">
                                    <div class="d-flex align-items-center gap-3">
                                        @if($member->photo)
                                            <img src="{{ asset('storage/' . $member->photo) }}"
                                                 alt="Photo" class="rounded-circle"
                                                 width="40" height="40" style="object-fit: cover; flex-shrink:0;">
                                        @else
                                            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                                                 style="width:40px; height:40px; font-size:0.85rem;
                                                        background: rgba(var(--vz-primary-rgb), 0.15);
                                                        color: var(--vz-primary);">
                                                {{ strtoupper(substr($member->first_name, 0, 1) . substr($member->name, 0, 1)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <div class="fw-semibold" style="font-size: 0.9rem;">
                                                {{ $member->first_name }} {{ $member->name }}
                                            </div>
                                            @if($group->leader_id === $member->id)
                                                <span class="badge rounded-pill px-2 py-1 mt-1"
                                                      style="background: rgba(var(--vz-warning-rgb), 0.2); color: var(--vz-warning); font-size: 0.72rem;">
                                                    <i class="mdi mdi-crown me-1"></i>Chef de groupe
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3">
                                    <div style="font-size: 0.88rem;">{{ $member->email ?? '—' }}</div>
                                    <small class="text-muted">{{ $member->phone ?? '—' }}</small>
                                </td>
                                <td class="py-3">
                                    <span style="font-size: 0.88rem;">
                                        {{ $member->pivot->joined_at ? \Carbon\Carbon::parse($member->pivot->joined_at)->format('d/m/Y H:i') : '—' }}
                                    </span>
                                </td>
                                @can('assignMember', $group)
                                <td class="py-3 text-end pe-4">
                                    <form action="{{ route('admin.groups.members.remove', [$group, $member]) }}"
                                          method="POST"
                                          class="d-inline confirm-remove-member"
                                          data-member-name="{{ $member->first_name }} {{ $member->name }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-sm btn-icon"
                                                title="Retirer du groupe"
                                                style="width:34px; height:34px; border-radius:50%;
                                                       background: rgba(var(--vz-danger-rgb), 0.1);
                                                       color: var(--vz-danger);
                                                       border: 1px solid rgba(var(--vz-danger-rgb), 0.2);
                                                       transition: all 0.2s;">
                                            <i class="mdi mdi-account-minus fs-16"></i>
                                        </button>
                                    </form>
                                </td>
                                @endcan
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ auth()->user()->can('assignMember', $group) ? 4 : 3 }}" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center gap-2">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                                             style="width:64px; height:64px; background: rgba(var(--vz-secondary-rgb), 0.1);">
                                            <i class="mdi mdi-account-off-outline fs-30 text-muted"></i>
                                        </div>
                                        <p class="text-muted mb-0">Aucun membre actif dans ce groupe.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ---- Carte Historique ---- --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header border-0 py-3 px-4 d-flex align-items-center gap-3"
                 style="border-bottom: 1px solid rgba(var(--vz-border-color-translucent), 1) !important;">
                <div class="rounded p-2 flex-shrink-0"
                     style="background: rgba(var(--vz-secondary-rgb), 0.15); width:40px; height:40px; display:flex; align-items:center; justify-content:center;">
                    <i class="mdi mdi-history fs-20 text-muted"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold">Historique des Membres</h5>
                    <small class="text-muted">Anciens membres et durées de participation</small>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead style="background: rgba(var(--vz-secondary-rgb), 0.06);">
                            <tr>
                                <th class="ps-4 py-3 fw-semibold" style="font-size: 0.78rem; text-transform:uppercase; letter-spacing:0.05em;">Membre</th>
                                <th class="py-3 fw-semibold" style="font-size: 0.78rem; text-transform:uppercase; letter-spacing:0.05em;">Rejoint le</th>
                                <th class="py-3 fw-semibold" style="font-size: 0.78rem; text-transform:uppercase; letter-spacing:0.05em;">Quitté le</th>
                                <th class="py-3 pe-4 fw-semibold" style="font-size: 0.78rem; text-transform:uppercase; letter-spacing:0.05em;">Durée</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($allMembers as $pastMember)
                            <tr class="border-bottom">
                                <td class="ps-4 py-3 fw-semibold" style="font-size: 0.88rem;">
                                    {{ $pastMember->first_name }} {{ $pastMember->name }}
                                </td>
                                <td class="py-3" style="font-size: 0.88rem;">
                                    {{ $pastMember->pivot->joined_at ? \Carbon\Carbon::parse($pastMember->pivot->joined_at)->format('d/m/Y H:i') : '—' }}
                                </td>
                                <td class="py-3">
                                    @if($pastMember->pivot->left_at)
                                        <span class="badge rounded-pill px-2 py-1"
                                              style="background: rgba(var(--vz-danger-rgb), 0.12); color: var(--vz-danger); font-size: 0.78rem;">
                                            <i class="mdi mdi-logout me-1"></i>
                                            {{ \Carbon\Carbon::parse($pastMember->pivot->left_at)->format('d/m/Y H:i') }}
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="py-3 pe-4 text-muted" style="font-size: 0.85rem;">
                                    @if($pastMember->pivot->joined_at && $pastMember->pivot->left_at)
                                        @php
                                            $joined = \Carbon\Carbon::parse($pastMember->pivot->joined_at);
                                            $left = \Carbon\Carbon::parse($pastMember->pivot->left_at);
                                        @endphp
                                        {{ $joined->diffForHumans($left, true, false, 2) }}
                                    @else
                                        —
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">Aucun historique d'ancien membre.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- ======= COLONNE DROITE : Info, Chef, Affectation ======= --}}
    <div class="col-lg-4">

        {{-- ---- Informations du groupe ---- --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header border-0 py-3 px-4 d-flex align-items-center gap-3"
                 style="border-bottom: 1px solid rgba(var(--vz-border-color-translucent), 1) !important;">
                <div class="rounded p-2 flex-shrink-0"
                     style="background: rgba(var(--vz-info-rgb), 0.15); width:40px; height:40px; display:flex; align-items:center; justify-content:center;">
                    <i class="mdi mdi-information-outline fs-20" style="color: var(--vz-info);"></i>
                </div>
                <h5 class="mb-0 fw-bold">Informations</h5>
            </div>
            <div class="card-body px-4 py-3">
                <div class="mb-3">
                    <small class="fw-bold text-muted d-block mb-1"
                           style="font-size: 0.72rem; text-transform:uppercase; letter-spacing:0.06em;">Description</small>
                    <p class="mb-0" style="font-size: 0.9rem; white-space: pre-line; line-height: 1.6;">
                        {{ $group->description ?? 'Aucune description fournie.' }}
                    </p>
                </div>
                <hr class="my-3" style="border-color: rgba(var(--vz-border-color-translucent), 1);">
                <div>
                    <small class="fw-bold text-muted d-block mb-1"
                           style="font-size: 0.72rem; text-transform:uppercase; letter-spacing:0.06em;">Créé le</small>
                    <div class="d-flex align-items-center gap-2">
                        <i class="mdi mdi-calendar-outline text-muted"></i>
                        <span style="font-size: 0.9rem;">{{ $group->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ---- Chef de groupe ---- --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header border-0 py-3 px-4 d-flex align-items-center gap-3"
                 style="border-bottom: 1px solid rgba(var(--vz-border-color-translucent), 1) !important;">
                <div class="rounded p-2 flex-shrink-0"
                     style="background: rgba(var(--vz-warning-rgb), 0.15); width:40px; height:40px; display:flex; align-items:center; justify-content:center;">
                    <i class="mdi mdi-crown fs-20" style="color: var(--vz-warning);"></i>
                </div>
                <h5 class="mb-0 fw-bold">Chef de groupe</h5>
            </div>
            <div class="card-body px-4 py-3">
                @if($group->leader)
                    <div class="d-flex align-items-center gap-3 mb-3">
                        @if($group->leader->photo)
                            <img src="{{ asset('storage/' . $group->leader->photo) }}"
                                 alt="Photo" class="rounded-circle"
                                 width="52" height="52" style="object-fit: cover; flex-shrink:0;">
                        @else
                            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                                 style="width:52px; height:52px; font-size:1.1rem;
                                        background: rgba(var(--vz-warning-rgb), 0.15);
                                        color: var(--vz-warning);">
                                {{ strtoupper(substr($group->leader->first_name, 0, 1) . substr($group->leader->name, 0, 1)) }}
                            </div>
                        @endif
                        <div>
                            <div class="fw-bold" style="font-size: 0.95rem;">
                                {{ $group->leader->first_name }} {{ $group->leader->name }}
                            </div>
                            <span class="badge rounded-pill px-2 py-1 mt-1"
                                  style="background: rgba(var(--vz-warning-rgb), 0.15); color: var(--vz-warning); font-size: 0.72rem;">
                                <i class="mdi mdi-crown me-1"></i>Chef Désigné
                            </span>
                        </div>
                    </div>
                    <hr style="border-color: rgba(var(--vz-border-color-translucent), 1);">
                    <div class="d-flex flex-column gap-2">
                        <div class="d-flex align-items-center gap-2" style="font-size: 0.88rem;">
                            <i class="mdi mdi-email-outline text-muted fs-16"></i>
                            <span>{{ $group->leader->email ?? '—' }}</span>
                        </div>
                        <div class="d-flex align-items-center gap-2" style="font-size: 0.88rem;">
                            <i class="mdi mdi-phone-outline text-muted fs-16"></i>
                            <span>{{ $group->leader->phone ?? '—' }}</span>
                        </div>
                    </div>
                    @can('update', $group)
                    <div class="mt-3">
                        <a href="{{ route('admin.groups.edit', $group) }}"
                           class="btn btn-sm btn-outline-warning w-100"
                           style="border-radius: 0.5rem; font-size: 0.82rem;">
                            <i class="mdi mdi-account-switch me-1"></i>Changer le chef
                        </a>
                    </div>
                    @endcan
                @else
                    <div class="text-center py-3">
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-2"
                             style="width:56px; height:56px; background: rgba(var(--vz-secondary-rgb), 0.1);">
                            <i class="mdi mdi-crown-outline fs-28 text-muted"></i>
                        </div>
                        <p class="text-muted mb-2" style="font-size: 0.88rem;">Aucun chef de groupe désigné.</p>
                        @can('update', $group)
                        <a href="{{ route('admin.groups.edit', $group) }}" class="btn btn-sm btn-outline-primary" style="border-radius: 0.5rem;">
                            <i class="mdi mdi-plus me-1"></i>Désigner un chef
                        </a>
                        @endcan
                    </div>
                @endif
            </div>
        </div>

        {{-- ---- Affecter un membre ---- --}}
        @can('assignMember', $group)
        <div class="card border-0 shadow-sm">
            <div class="card-header border-0 py-3 px-4 d-flex align-items-center gap-3"
                 style="border-bottom: 1px solid rgba(var(--vz-border-color-translucent), 1) !important;">
                <div class="rounded p-2 flex-shrink-0"
                     style="background: rgba(var(--vz-success-rgb), 0.15); width:40px; height:40px; display:flex; align-items:center; justify-content:center;">
                    <i class="mdi mdi-account-plus fs-20" style="color: var(--vz-success);"></i>
                </div>
                <h5 class="mb-0 fw-bold">Affecter un membre</h5>
            </div>
            <div class="card-body px-4 py-3">
                <form action="{{ route('admin.groups.members.assign', $group) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="user_id" class="form-label text-muted" style="font-size: 0.82rem;">
                            Sélectionner un utilisateur
                        </label>
                        <select id="user_id" name="user_id"
                                class="form-select @error('user_id') is-invalid @enderror"
                                required
                                style="border-radius: 0.5rem;">
                            <option value="">— Sélectionner un utilisateur —</option>
                            @foreach($availableUsers as $user)
                                <option value="{{ $user->id }}">
                                    {{ $user->first_name }} {{ $user->name }}
                                    @if($user->phone) ({{ $user->phone }}) @endif
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-success w-100" style="border-radius: 0.5rem;">
                        <i class="mdi mdi-account-plus me-1"></i>Ajouter au groupe
                    </button>
                </form>
            </div>
        </div>
        @endcan

    </div>
</div>

@push('css')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<style>
    .ts-wrapper.form-select {
        padding: 0;
        border: none;
        height: auto;
    }
    .ts-control {
        border-radius: 0.5rem;
        padding: 0.47rem 0.75rem;
        font-size: 0.875rem;
        background: var(--vz-input-bg, #fff) !important;
        border-color: var(--vz-input-border-color, #ced4da) !important;
        color: var(--vz-body-color) !important;
    }
    .ts-dropdown {
        border-radius: 0.5rem;
        box-shadow: 0 0.5rem 1.5rem rgba(0,0,0,0.18);
        font-size: 0.875rem;
        background: var(--vz-dropdown-bg, #fff) !important;
        border-color: var(--vz-dropdown-border-color, #ced4da) !important;
    }
    .ts-dropdown .option {
        color: var(--vz-body-color) !important;
    }
    .ts-dropdown .option:hover, .ts-dropdown .active {
        background: rgba(var(--vz-primary-rgb), 0.1) !important;
        color: var(--vz-primary) !important;
    }

    /* Hover sur les lignes des tableaux */
    table tbody tr:hover {
        background: rgba(var(--vz-primary-rgb), 0.04);
    }

    /* Hover sur les boutons d'action */
    .btn-icon:hover {
        background: rgba(var(--vz-danger-rgb), 0.2) !important;
        transform: scale(1.08);
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const userSelect = document.getElementById('user_id');
        if (userSelect) {
            new TomSelect('#user_id', {
                create: false,
                placeholder: '— Sélectionner un utilisateur —',
                allowEmptyOption: true
            });
        }

        document.querySelectorAll('.confirm-remove-member').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const memberName = this.getAttribute('data-member-name');
                confirmAction(
                    `Retirer ${memberName} du groupe ? Cette action sera historisée.`,
                    () => this.submit(),
                    'Retirer du groupe',
                    'Retirer',
                    'btn-danger'
                );
            });
        });
    });
</script>
@endpush

@endsection
