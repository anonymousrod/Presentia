@extends('layouts.app')

@section('content')

<div class="container-fluid max-w-1200 py-3 py-md-4">
    {{-- =================== BREADCRUMBS & TOP NAV =================== --}}
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('activities.index') }}" class="btn btn-sm btn-soft-secondary rounded-pill px-3">
                    <i class="mdi mdi-arrow-left me-1"></i>Activités
                </a>
                <a href="{{ route('activities.show', $activity) }}" class="btn btn-sm btn-soft-info rounded-pill px-3">
                    <i class="mdi mdi-eye me-1"></i>Détails
                </a>
            </div>
            <h3 class="fw-bold mb-0 fs-20 fs-md-24">Feuille d'Émargement</h3>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge rounded-pill px-3 py-2 fs-12 shadow-sm" style="background: rgba(var(--vz-primary-rgb), 0.12); color: var(--vz-primary);">
                <i class="mdi mdi-tag-outline me-1"></i>{{ $activity->activityType?->name ?? 'N/A' }}
            </span>
            @if($isClosed)
                <span class="badge bg-danger rounded-pill px-3 py-2 fs-12 shadow-sm">
                    <i class="mdi mdi-lock me-1"></i>Clôturée
                </span>
            @elseif(now() < $activity->start_time)
                <span class="badge bg-primary rounded-pill px-3 py-2 fs-12 shadow-sm">
                    <i class="mdi mdi-clock-outline me-1"></i>À venir
                </span>
            @else
                <span class="badge bg-success rounded-pill px-3 py-2 fs-12 shadow-sm">
                    <i class="mdi mdi-circle me-1 text-white"></i>En cours
                </span>
            @endif
        </div>
    </div>

    {{-- =================== CARTE INFO ACTIVITÉ =================== --}}
    <div class="card border-0 shadow-sm mb-4 rounded-3 overflow-hidden position-relative" style="background: linear-gradient(135deg, rgba(var(--vz-primary-rgb), 0.03), rgba(var(--vz-primary-rgb), 0.08));">
        <div class="card-body p-4">
            <div class="row align-items-center g-3">
                <div class="col-md-7 col-lg-8">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-primary text-white rounded-pill px-3 py-1 fs-11 uppercase tracking-wider">
                            <i class="mdi mdi-account-group me-1"></i>{{ $activity->group?->name ?? 'Membres Inscrits' }}
                        </span>
                        @if($activity->location)
                        <span class="text-muted fs-13">
                            <i class="mdi mdi-map-marker text-danger me-1"></i>{{ $activity->location }}
                        </span>
                        @endif
                    </div>
                    <h3 class="fw-bold mb-1 fs-18 fs-md-22 text-body">{{ $activity->title }}</h3>
                    <p class="text-muted mb-0 fs-13">
                        Gérez et validez les présences en temps réel. Mise à jour instantanée au toucher.
                    </p>
                </div>
                <div class="col-md-5 col-lg-4">
                    <div class="p-3 bg-light rounded-3 shadow-none border border-light-subtle">
                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-light-subtle">
                            <span class="text-muted fs-12 fw-semibold text-uppercase"><i class="mdi mdi-clock-start text-success me-1"></i>Début</span>
                            <span class="fw-bold fs-13 text-body">{{ $activity->start_time->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted fs-12 fw-semibold text-uppercase"><i class="mdi mdi-clock-end text-danger me-1"></i>Fin</span>
                            <span class="fw-bold fs-13 text-body">{{ $activity->end_time->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- =================== GESTIONNAIRE ALPINE =================== --}}
    <div x-data="attendanceManager()" x-init="init()" class="row">
        <div class="col-12">

            {{-- Message d'information sur la portée de validation --}}
            @if(isset($validationScopeMessage) && $validationScopeMessage)
            <div class="alert alert-{{ $validationScopeType }} border-0 mb-4 shadow-sm d-flex align-items-center gap-3 p-3 rounded-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:40px; height:40px; background: rgba(var(--vz-{{ $validationScopeType }}-rgb), 0.2);">
                    <i class="mdi mdi-information-outline fs-20" style="color: var(--vz-{{ $validationScopeType }});"></i>
                </div>
                <div>
                    <p class="mb-0 fs-13">
                        {!! $validationScopeMessage !!}
                    </p>
                </div>
            </div>
            @endif

            {{-- Bannière activité clôturée --}}
            <template x-if="isClosed">
                <div class="alert border-0 mb-4 shadow-sm d-flex align-items-center gap-3 p-3 p-md-4 rounded-3"
                     style="background: rgba(var(--vz-danger-rgb), 0.12); border-left: 4px solid var(--vz-danger) !important;">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:44px; height:44px; background: rgba(var(--vz-danger-rgb), 0.2);">
                        <i class="mdi mdi-lock-outline fs-20" style="color: var(--vz-danger);"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1 text-danger">Activité Clôturée</h6>
                        <p class="mb-0 text-muted fs-13">
                            Cette activité est terminée depuis plus d'une heure. Les modifications de présence sont verrouillées.
                        </p>
                    </div>
                </div>
            </template>

            {{-- ================= KPI METRIC CARDS ================= --}}
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm rounded-3 p-3 bg-success-subtle border-start border-4 border-success h-100">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-uppercase fs-11 text-success fw-bold tracking-wider">Présents</span>
                                <h3 class="mb-0 fw-bold text-success fs-22 fs-md-26" x-text="members.filter(m => m.status === 'PRESENT').length">0</h3>
                            </div>
                            <div class="avatar-sm flex-shrink-0">
                                <div class="avatar-title rounded-circle bg-success text-white fs-18 shadow-sm">
                                    <i class="mdi mdi-check"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm rounded-3 p-3 bg-warning-subtle border-start border-4 border-warning h-100">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-uppercase fs-11 text-warning fw-bold tracking-wider">En retard</span>
                                <h3 class="mb-0 fw-bold text-warning fs-22 fs-md-26" x-text="members.filter(m => m.status === 'LATE').length">0</h3>
                            </div>
                            <div class="avatar-sm flex-shrink-0">
                                <div class="avatar-title rounded-circle bg-warning text-white fs-18 shadow-sm">
                                    <i class="mdi mdi-clock-alert"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm rounded-3 p-3 bg-danger-subtle border-start border-4 border-danger h-100">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-uppercase fs-11 text-danger fw-bold tracking-wider">Absents</span>
                                <h3 class="mb-0 fw-bold text-danger fs-22 fs-md-26" x-text="members.filter(m => m.status === 'ABSENT').length">0</h3>
                            </div>
                            <div class="avatar-sm flex-shrink-0">
                                <div class="avatar-title rounded-circle bg-danger text-white fs-18 shadow-sm">
                                    <i class="mdi mdi-close"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm rounded-3 p-3 bg-info-subtle border-start border-4 border-info h-100">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-uppercase fs-11 text-info fw-bold tracking-wider">Excusés</span>
                                <h3 class="mb-0 fw-bold text-info fs-22 fs-md-26" x-text="members.filter(m => m.status === 'EXCUSED').length">0</h3>
                            </div>
                            <div class="avatar-sm flex-shrink-0">
                                <div class="avatar-title rounded-circle bg-info text-white fs-18 shadow-sm">
                                    <i class="mdi mdi-shield-check"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============ CARTE PRINCIPALE DES MEMBRES ============ --}}
            <div class="card border-0 shadow-sm rounded-3">

                {{-- En-tête de la carte & Filtres --}}
                <div class="card-header border-0 bg-transparent p-3 p-md-4">
                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-xs flex-shrink-0">
                                    <div class="avatar-title rounded-circle bg-primary-subtle text-primary fs-16">
                                        <i class="mdi mdi-format-list-checks"></i>
                                    </div>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-0 fs-16 fs-md-18">{{ $activity->group ? 'Membres du Groupe' : 'Membres Inscrits' }}</h5>
                                    <small class="text-muted fs-12">Validez la présence des participants en 1 clic</small>
                                </div>
                            </div>
                            <div class="text-muted fs-12">
                                <i class="mdi mdi-account-multiple me-1"></i><span x-text="filteredMembers().length"></span> participant(s)
                            </div>
                        </div>

                        {{-- Quick Status Filter Tabs (Desktop) --}}
                        <div class="d-none d-md-flex gap-1 flex-wrap pb-1">
                            <button type="button" class="btn btn-sm rounded-pill px-3 py-1 fs-12"
                                    :class="statusFilterTab === '' ? 'btn-primary' : 'btn-soft-secondary'"
                                    @click="statusFilterTab = ''">
                                Tous (<span x-text="members.length"></span>)
                            </button>
                            <button type="button" class="btn btn-sm rounded-pill px-3 py-1 fs-12"
                                    :class="statusFilterTab === 'PRESENT' ? 'btn-success' : 'btn-soft-success'"
                                    @click="statusFilterTab = 'PRESENT'">
                                Présents (<span x-text="members.filter(m => m.status === 'PRESENT').length"></span>)
                            </button>
                            <button type="button" class="btn btn-sm rounded-pill px-3 py-1 fs-12"
                                    :class="statusFilterTab === 'LATE' ? 'btn-warning' : 'btn-soft-warning'"
                                    @click="statusFilterTab = 'LATE'">
                                En retard (<span x-text="members.filter(m => m.status === 'LATE').length"></span>)
                            </button>
                            <button type="button" class="btn btn-sm rounded-pill px-3 py-1 fs-12"
                                    :class="statusFilterTab === 'ABSENT' ? 'btn-danger' : 'btn-soft-danger'"
                                    @click="statusFilterTab = 'ABSENT'">
                                Absents (<span x-text="members.filter(m => m.status === 'ABSENT').length"></span>)
                            </button>
                            <button type="button" class="btn btn-sm rounded-pill px-3 py-1 fs-12"
                                    :class="statusFilterTab === 'EXCUSED' ? 'btn-info' : 'btn-soft-info'"
                                    @click="statusFilterTab = 'EXCUSED'">
                                Excusés (<span x-text="members.filter(m => m.status === 'EXCUSED').length"></span>)
                            </button>
                            <button type="button" class="btn btn-sm rounded-pill px-3 py-1 fs-12"
                                    :class="statusFilterTab === 'UNMARKED' ? 'btn-dark' : 'btn-soft-dark'"
                                    @click="statusFilterTab = 'UNMARKED'">
                                Non marqués (<span x-text="members.filter(m => !m.status).length"></span>)
                            </button>
                        </div>

                        {{-- Quick Status Filter Dropdown (Mobile) --}}
                        <div class="d-md-none mb-2">
                            <label class="form-label fw-semibold mb-1 fs-11 uppercase tracking-wider text-muted">
                                <i class="mdi mdi-filter-variant me-1"></i>Filtrer par statut
                            </label>
                            <select class="form-select form-select-sm bg-light border-light-subtle rounded-3" x-model="statusFilterTab">
                                <option value="">Tous (<span x-text="members.length"></span>)</option>
                                <option value="PRESENT">Présents (<span x-text="members.filter(m => m.status === 'PRESENT').length"></span>)</option>
                                <option value="LATE">En retard (<span x-text="members.filter(m => m.status === 'LATE').length"></span>)</option>
                                <option value="ABSENT">Absents (<span x-text="members.filter(m => m.status === 'ABSENT').length"></span>)</option>
                                <option value="EXCUSED">Excusés (<span x-text="members.filter(m => m.status === 'EXCUSED').length"></span>)</option>
                                <option value="UNMARKED">Non marqués (<span x-text="members.filter(m => !m.status).length"></span>)</option>
                            </select>
                        </div>

                        <div class="row g-2 align-items-center">
                            @if(count($otherEligibleUsers) > 0)
                            <div class="col-12 col-md-6 col-lg-7">
                                <label class="form-label fw-semibold mb-1 fs-11 uppercase tracking-wider text-muted">
                                    <i class="mdi mdi-account-plus-outline me-1"></i>Ajouter à la volée
                                </label>
                                <div class="input-group input-group-sm">
                                    <select class="form-select rounded-start-3" x-model="selectedUserId">
                                        <option value="">— Sélectionner un membre —</option>
                                        @foreach($otherEligibleUsers as $u)
                                            <option value="{{ $u->id }}">{{ $u->first_name }} {{ $u->name }}</option>
                                        @endforeach
                                    </select>
                                    <button class="btn btn-primary rounded-end-3" type="button"
                                            @click="addUnregisteredMember()"
                                            :disabled="!selectedUserId || isClosed">
                                        <i class="mdi mdi-plus"></i>
                                        <span class="d-none d-sm-inline ms-1">Ajouter</span>
                                    </button>
                                </div>
                            </div>
                            @endif
                            <div class="col-12 col-md-{{ count($otherEligibleUsers) > 0 ? '6' : '12' }} col-lg-{{ count($otherEligibleUsers) > 0 ? '5' : '12' }}">
                                <label class="form-label fw-semibold mb-1 fs-11 uppercase tracking-wider text-muted">
                                    <i class="mdi mdi-magnify me-1"></i>Rechercher
                                </label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light border-light-subtle"><i class="mdi mdi-magnify"></i></span>
                                    <input type="text"
                                           x-model="searchQuery"
                                           class="form-control bg-light border-light-subtle"
                                           placeholder="Nom ou email...">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Liste des membres (Desktop: Tableau, Mobile: Cartes) --}}
                <div class="card-body p-0">
                    
                    {{-- VUE DESKTOP (Tableau ultra-soigné) --}}
                    <div class="table-responsive d-none d-md-block">
                        <table class="table align-middle mb-0" id="attendance-table">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4 py-3 fw-semibold fs-12 uppercase tracking-wider" style="width: 25%;">Membre</th>
                                    <th class="py-3 fw-semibold text-center fs-12 uppercase tracking-wider" style="width: 38%;">Statut de présence</th>
                                    <th class="py-3 fw-semibold text-center fs-12 uppercase tracking-wider" style="width: 12%;">Source</th>
                                    <th class="py-3 fw-semibold fs-12 uppercase tracking-wider" style="width: 18%;">Note</th>
                                    <th class="py-3 fw-semibold text-center pe-4 fs-12 uppercase tracking-wider" style="width: 7%;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="member in filteredMembers()" :key="member.user_id">
                                    <tr class="border-bottom" style="transition: background 0.15s;">

                                        {{-- Membre --}}
                                        <td class="ps-4 py-3">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="avatar-sm flex-shrink-0">
                                                    <div class="avatar-title rounded-circle fw-bold bg-primary-subtle text-primary fs-14"
                                                         x-text="member.full_name.charAt(0).toUpperCase()">
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold fs-14 text-body" x-text="member.full_name"></div>
                                                    <small class="text-muted fs-12" x-text="member.email"></small>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Boutons statut --}}
                                        <td class="py-3 text-center">
                                            <div class="d-inline-flex gap-1 p-1 bg-light rounded-pill border border-light-subtle shadow-none">
                                                {{-- PRÉSENT --}}
                                                <button type="button"
                                                        class="btn btn-sm rounded-pill px-3 py-1 fs-12 transition-all fw-semibold"
                                                        :class="member.status === 'PRESENT' ? 'btn-success shadow-sm' : 'text-success border-0 bg-transparent'"
                                                        :disabled="isClosed"
                                                        @click="setStatus(member, 'PRESENT')">
                                                    <i class="mdi mdi-check me-1"></i>Présent
                                                </button>
                                                {{-- EN RETARD --}}
                                                <button type="button"
                                                        class="btn btn-sm rounded-pill px-3 py-1 fs-12 transition-all fw-semibold"
                                                        :class="member.status === 'LATE' ? 'btn-warning shadow-sm' : 'text-warning border-0 bg-transparent'"
                                                        :disabled="isClosed"
                                                        @click="setStatus(member, 'LATE')">
                                                    <i class="mdi mdi-clock-alert me-1"></i>En retard
                                                </button>
                                                {{-- ABSENT --}}
                                                <button type="button"
                                                        class="btn btn-sm rounded-pill px-3 py-1 fs-12 transition-all fw-semibold"
                                                        :class="member.status === 'ABSENT' ? 'btn-danger shadow-sm' : 'text-danger border-0 bg-transparent'"
                                                        :disabled="isClosed"
                                                        @click="setStatus(member, 'ABSENT')">
                                                    <i class="mdi mdi-close me-1"></i>Absent
                                                </button>
                                                {{-- EXCUSÉ --}}
                                                <button type="button"
                                                        class="btn btn-sm rounded-pill px-3 py-1 fs-12 transition-all fw-semibold"
                                                        :class="member.status === 'EXCUSED' ? 'btn-info shadow-sm' : 'text-info border-0 bg-transparent'"
                                                        :disabled="isClosed"
                                                        @click="setStatus(member, 'EXCUSED')">
                                                    <i class="mdi mdi-shield-check me-1"></i>Excusé
                                                </button>
                                            </div>
                                        </td>

                                        {{-- Source --}}
                                        <td class="py-3 text-center">
                                            <template x-if="member.scan_source === 'qr_code'">
                                                <span class="badge rounded-pill px-3 py-1 bg-success-subtle text-success fs-11">
                                                    <i class="mdi mdi-qrcode me-1"></i>QR Code
                                                </span>
                                            </template>
                                            <template x-if="member.scan_source === 'manual'">
                                                <span class="badge rounded-pill px-3 py-1 bg-primary-subtle text-primary fs-11">
                                                    <i class="mdi mdi-hand-pointing-right me-1"></i>Manuel
                                                </span>
                                            </template>
                                            <template x-if="!member.scan_source">
                                                <span class="badge rounded-pill px-3 py-1 text-muted bg-light fs-11">—</span>
                                            </template>
                                        </td>

                                        {{-- Note --}}
                                        <td class="py-3">
                                            <input type="text"
                                                   x-model="member.note"
                                                   class="form-control form-control-sm rounded-3 bg-light border-0"
                                                   placeholder="Note..."
                                                   :disabled="isClosed"
                                                   @blur="saveNote(member)"
                                                   @keyup.enter="saveNote(member)">
                                        </td>

                                        {{-- Supprimer --}}
                                        <td class="py-3 text-center pe-4">
                                            <button type="button"
                                                    class="btn btn-sm btn-icon btn-soft-danger rounded-circle"
                                                    :disabled="isClosed"
                                                    @click="removeMember(member)"
                                                    title="Retirer ce membre"
                                                    style="width:32px; height:32px;">
                                                <i class="mdi mdi-delete-outline"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </template>

                                {{-- État vide --}}
                                <template x-if="filteredMembers().length === 0">
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <div class="d-flex flex-column align-items-center gap-2">
                                                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width:60px; height:60px;">
                                                    <i class="mdi mdi-account-question-outline fs-28 text-muted"></i>
                                                </div>
                                                <p class="text-muted mb-0 fs-14">Aucun membre ne correspond à vos filtres.</p>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    {{-- VUE MOBILE (Cartes Tactiles App Native) --}}
                    <div class="d-md-none p-3 bg-light bg-opacity-50">
                        <div class="d-flex flex-column gap-3">
                            <template x-for="member in filteredMembers()" :key="member.user_id + '-mob'">
                                <div class="card border border-light-subtle shadow-sm mb-0 rounded-3 overflow-hidden">
                                    <div class="card-body p-3">
                                        {{-- Ligne 1: Avatar + Nom + Source --}}
                                        <div class="d-flex align-items-start justify-content-between mb-3">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="avatar-sm flex-shrink-0">
                                                    <div class="avatar-title rounded-circle fw-bold bg-primary-subtle text-primary fs-16"
                                                         x-text="member.full_name.charAt(0).toUpperCase()">
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-body fs-15" x-text="member.full_name"></div>
                                                    <small class="text-muted fs-12" x-text="member.email"></small>
                                                </div>
                                            </div>
                                            <div class="flex-shrink-0 text-end">
                                                <template x-if="member.scan_source === 'qr_code'">
                                                    <span class="badge bg-success-subtle text-success p-2 rounded-circle" title="QR Code"><i class="mdi mdi-qrcode fs-14"></i></span>
                                                </template>
                                                <template x-if="member.scan_source === 'manual'">
                                                    <span class="badge bg-primary-subtle text-primary p-2 rounded-circle" title="Manuel"><i class="mdi mdi-hand-pointing-right fs-14"></i></span>
                                                </template>
                                            </div>
                                        </div>

                                        {{-- Ligne 2: Grille 2x2 Tactile des boutons de statut --}}
                                        <div class="row g-2 mb-3">
                                            <div class="col-6">
                                                <button type="button" class="btn btn-sm w-100 py-2 d-flex flex-column align-items-center justify-content-center gap-1 rounded-3 transition-all"
                                                        :class="member.status === 'PRESENT' ? 'btn-success shadow-sm' : 'btn-outline-success'"
                                                        :disabled="isClosed"
                                                        @click="setStatus(member, 'PRESENT')">
                                                    <i class="mdi mdi-check fs-18 lh-1"></i>
                                                    <span class="fs-11 fw-semibold">Présent</span>
                                                </button>
                                            </div>
                                            <div class="col-6">
                                                <button type="button" class="btn btn-sm w-100 py-2 d-flex flex-column align-items-center justify-content-center gap-1 rounded-3 transition-all"
                                                        :class="member.status === 'LATE' ? 'btn-warning shadow-sm' : 'btn-outline-warning'"
                                                        :disabled="isClosed"
                                                        @click="setStatus(member, 'LATE')">
                                                    <i class="mdi mdi-clock-alert fs-18 lh-1"></i>
                                                    <span class="fs-11 fw-semibold">En retard</span>
                                                </button>
                                            </div>
                                            <div class="col-6">
                                                <button type="button" class="btn btn-sm w-100 py-2 d-flex flex-column align-items-center justify-content-center gap-1 rounded-3 transition-all"
                                                        :class="member.status === 'ABSENT' ? 'btn-danger shadow-sm' : 'btn-outline-danger'"
                                                        :disabled="isClosed"
                                                        @click="setStatus(member, 'ABSENT')">
                                                    <i class="mdi mdi-close fs-18 lh-1"></i>
                                                    <span class="fs-11 fw-semibold">Absent</span>
                                                </button>
                                            </div>
                                            <div class="col-6">
                                                <button type="button" class="btn btn-sm w-100 py-2 d-flex flex-column align-items-center justify-content-center gap-1 rounded-3 transition-all"
                                                        :class="member.status === 'EXCUSED' ? 'btn-info shadow-sm' : 'btn-outline-info'"
                                                        :disabled="isClosed"
                                                        @click="setStatus(member, 'EXCUSED')">
                                                    <i class="mdi mdi-shield-check fs-18 lh-1"></i>
                                                    <span class="fs-11 fw-semibold">Excusé</span>
                                                </button>
                                            </div>
                                        </div>

                                        {{-- Ligne 3: Note & Suppression --}}
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="flex-grow-1 position-relative">
                                                <i class="mdi mdi-note-text-outline position-absolute text-muted" style="left: 10px; top: 50%; transform: translateY(-50%);"></i>
                                                <input type="text"
                                                       x-model="member.note"
                                                       class="form-control form-control-sm ps-4 bg-light border-0 rounded-3"
                                                       placeholder="Ajouter une note..."
                                                       :disabled="isClosed"
                                                       @blur="saveNote(member)"
                                                       @keyup.enter="saveNote(member)">
                                            </div>
                                            <button type="button" class="btn btn-sm btn-soft-danger rounded-3 flex-shrink-0"
                                                    :disabled="isClosed"
                                                    @click="removeMember(member)"
                                                    style="width:36px; height:36px;">
                                                <i class="mdi mdi-delete-outline"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            {{-- État vide mobile --}}
                            <template x-if="filteredMembers().length === 0">
                                <div class="text-center py-5">
                                    <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center mb-3" style="width:60px; height:60px;">
                                        <i class="mdi mdi-account-question-outline fs-28 text-muted"></i>
                                    </div>
                                    <p class="text-muted mb-0 fs-13">Aucun membre ne correspond à vos filtres.</p>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function attendanceManager() {
        return {
            activityId: {{ $activity->id }},
            isClosed: {{ $isClosed ? 'true' : 'false' }},
            updateUrl: "{{ route('activities.attendance.update', $activity) }}",
            deleteUrl: "{{ route('activities.attendance.destroy', $activity) }}",
            dataUrl: "{{ route('activities.attendance.data', $activity) }}",
            searchQuery: '',
            statusFilterTab: '',
            selectedUserId: '',
            otherEligibleUsers: [
                @foreach($otherEligibleUsers as $u)
                {
                    user_id: {{ $u->id }},
                    full_name: "{{ addslashes($u->full_name) }}",
                    email: "{{ addslashes($u->email) }}"
                },
                @endforeach
            ],
            members: [
                @foreach($members as $m)
                @php $att = $m->attendances->first(); @endphp
                {
                    user_id: {{ $m->id }},
                    full_name: "{{ addslashes($m->full_name) }}",
                    email: "{{ addslashes($m->email) }}",
                    status: "{{ $att?->status?->value ?? '' }}",
                    note: "{{ addslashes($att?->note ?? '') }}",
                    lastSavedNote: "{{ addslashes($att?->note ?? '') }}",
                    scan_source: "{{ $att?->scan_source ?? '' }}",
                    isUpdating: false
                },
                @endforeach
            ],
            init() {
                if (!this.isClosed) {
                    setInterval(() => { this.fetchUpdates(); }, 30000);
                }
            },
            filteredMembers() {
                return this.members.filter(m => {
                    const matchesSearch = m.full_name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                                          m.email.toLowerCase().includes(this.searchQuery.toLowerCase());
                    const matchesStatus = this.statusFilterTab === '' || 
                                          (this.statusFilterTab === 'UNMARKED' ? !m.status : m.status === this.statusFilterTab);
                    return matchesSearch && matchesStatus;
                });
            },
            addUnregisteredMember() {
                if (this.isClosed || !this.selectedUserId) return;
                let user = this.otherEligibleUsers.find(u => u.user_id == this.selectedUserId);
                if (user) {
                    this.members.push({
                        user_id: user.user_id,
                        full_name: user.full_name,
                        email: user.email,
                        status: '',
                        note: '',
                        lastSavedNote: '',
                        scan_source: '',
                        isUpdating: false
                    });
                    this.otherEligibleUsers = this.otherEligibleUsers.filter(u => u.user_id != this.selectedUserId);
                    this.selectedUserId = '';
                }
            },
            async fetchUpdates() {
                try {
                    let response = await fetch(this.dataUrl);
                    if (response.ok) {
                        let data = await response.json();
                        data.forEach(updated => {
                            let local = this.members.find(m => m.user_id === updated.user_id);
                            if (local) {
                                if (!local.isUpdating) {
                                    local.status = updated.status || '';
                                    local.note = updated.note || '';
                                    local.lastSavedNote = updated.note || '';
                                    local.scan_source = updated.scan_source || '';
                                }
                            } else {
                                this.members.push({
                                    user_id: updated.user_id,
                                    full_name: updated.full_name,
                                    email: updated.email,
                                    status: updated.status || '',
                                    note: updated.note || '',
                                    lastSavedNote: updated.note || '',
                                    scan_source: updated.scan_source || '',
                                    isUpdating: false
                                });
                                this.otherEligibleUsers = this.otherEligibleUsers.filter(u => u.user_id != updated.user_id);
                            }
                        });
                    }
                } catch (e) {
                    console.error("Erreur de récupération des données :", e);
                }
            },
            async setStatus(member, newStatus) {
                if (this.isClosed) return;
                let oldStatus = member.status;
                // Optimistic UI update: activation instantanée au 1er clic!
                member.status = newStatus;
                
                member.isUpdating = true;
                try {
                    let response = await fetch(this.updateUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ user_id: member.user_id, status: newStatus, note: member.note })
                    });
                    if (response.ok) {
                        let resData = await response.json();
                        member.scan_source = resData.attendance.scan_source;
                        member.lastSavedNote = member.note || '';
                    } else {
                        member.status = oldStatus;
                        let err = await response.json();
                        alert(err.error || "Erreur lors de la mise à jour");
                    }
                } catch (e) {
                    member.status = oldStatus;
                    console.error(e);
                } finally {
                    member.isUpdating = false;
                }
            },
            async saveNote(member) {
                if (this.isClosed) return;
                let currentNote = (member.note || '').trim();
                let lastNote = (member.lastSavedNote || '').trim();
                
                if (currentNote === lastNote) return;

                if (!member.status) { member.status = 'ABSENT'; }
                member.isUpdating = true;
                try {
                    let response = await fetch(this.updateUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ user_id: member.user_id, status: member.status, note: member.note })
                    });
                    if (response.ok) {
                        let resData = await response.json();
                        member.scan_source = resData.attendance.scan_source;
                        member.lastSavedNote = member.note;
                    } else {
                        let err = await response.json();
                        alert(err.error || "Erreur de sauvegarde de la note");
                    }
                } catch (e) {
                    console.error(e);
                } finally {
                    member.isUpdating = false;
                }
            },
            async removeMember(member) {
                if (this.isClosed) return;
                window.confirmAction(
                    `Êtes-vous sûr de vouloir retirer ${member.full_name} de cette activité ? Cela supprimera également son inscription et son émargement.`,
                    async () => {
                        member.isUpdating = true;
                        try {
                            let response = await fetch(this.deleteUrl, {
                                method: 'DELETE',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({ user_id: member.user_id })
                            });
                            if (response.ok) {
                                this.members = this.members.filter(m => m.user_id !== member.user_id);
                                if (!this.otherEligibleUsers.some(u => u.user_id == member.user_id)) {
                                    this.otherEligibleUsers.push({
                                        user_id: member.user_id,
                                        full_name: member.full_name,
                                        email: member.email
                                    });
                                }
                            } else {
                                let err = await response.json();
                                alert(err.error || "Erreur lors du retrait du membre");
                            }
                        } catch (e) {
                            console.error(e);
                        } finally {
                            member.isUpdating = false;
                        }
                    },
                    "Retirer un membre",
                    "Retirer",
                    "btn-danger"
                );
            }
        }
    }
</script>
@endpush
