@extends('layouts.app')

@section('content')

{{-- =================== BREADCRUMBS =================== --}}
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 fw-bold">Gestion des présences</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('activities.index') }}">Activités</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('activities.show', $activity) }}">Détails</a></li>
                    <li class="breadcrumb-item active">Présences</li>
                </ol>
            </div>
        </div>
    </div>
</div>

{{-- =================== CARTE INFO ACTIVITÉ =================== --}}
<div class="card border-0 shadow-sm mb-4" style="border-left: 4px solid var(--vz-primary) !important;">
    <div class="card-body p-4">
        <div class="row align-items-center g-3">
            <div class="col-md-8">
                <div class="d-flex align-items-center gap-2 flex-wrap mb-2">
                    <span class="badge rounded-pill px-3 py-2 fs-11"
                          style="background: rgba(var(--vz-primary-rgb), 0.15); color: var(--vz-primary);">
                        <i class="mdi mdi-tag-outline me-1"></i>{{ $activity->activityType?->name ?? 'N/A' }}
                    </span>
                    @if($isClosed)
                        <span class="badge bg-danger rounded-pill px-3 py-2 fs-11">
                            <i class="mdi mdi-lock me-1"></i>Clôturée
                        </span>
                    @elseif(now() < $activity->start_time)
                        <span class="badge bg-primary rounded-pill px-3 py-2 fs-11">
                            <i class="mdi mdi-clock-outline me-1"></i>À venir
                        </span>
                    @else
                        <span class="badge bg-success rounded-pill px-3 py-2 fs-11">
                            <i class="mdi mdi-check-circle me-1"></i>En cours
                        </span>
                    @endif
                </div>
                <h3 class="fw-bold mb-1">{{ $activity->title }}</h3>
                <p class="text-muted mb-0">
                    <i class="mdi mdi-account-group me-1" style="color: var(--vz-primary);"></i>
                    Groupe associé : <strong>{{ $activity->group?->name ?? 'Membres Inscrits' }}</strong>
                </p>
            </div>
            <div class="col-md-4">
                <div class="d-flex flex-column gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded p-2" style="background: rgba(var(--vz-success-rgb), 0.15); width:36px; height:36px; display:flex; align-items:center; justify-content:center;">
                            <i class="mdi mdi-clock-start" style="color: var(--vz-success);"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block" style="font-size: 0.72rem; text-transform:uppercase; letter-spacing:0.05em;">Début</small>
                            <span class="fw-semibold" style="font-size: 0.92rem;">{{ $activity->start_time->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded p-2" style="background: rgba(var(--vz-danger-rgb), 0.15); width:36px; height:36px; display:flex; align-items:center; justify-content:center;">
                            <i class="mdi mdi-clock-end" style="color: var(--vz-danger);"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block" style="font-size: 0.72rem; text-transform:uppercase; letter-spacing:0.05em;">Fin</small>
                            <span class="fw-semibold" style="font-size: 0.92rem;">{{ $activity->end_time->format('d/m/Y H:i') }}</span>
                        </div>
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
        <div class="alert alert-{{ $validationScopeType }} border-0 mb-4 shadow-sm d-flex align-items-center gap-3 p-3" style="border-radius: 0.5rem;">
            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                 style="width:40px; height:40px; background: rgba(var(--vz-{{ $validationScopeType }}-rgb), 0.2);">
                <i class="mdi mdi-information-outline fs-20" style="color: var(--vz-{{ $validationScopeType }});"></i>
            </div>
            <div>
                <p class="mb-0" style="font-size: 0.9rem;">
                    {!! $validationScopeMessage !!}
                </p>
            </div>
        </div>
        @endif

        {{-- Bannière activité clôturée --}}
        <template x-if="isClosed">
            <div class="alert border-0 mb-4 shadow-sm d-flex align-items-center gap-3 p-4"
                 style="background: rgba(var(--vz-danger-rgb), 0.12); border-left: 4px solid var(--vz-danger) !important; border-radius: 0.5rem;">
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:44px; height:44px; background: rgba(var(--vz-danger-rgb), 0.2);">
                    <i class="mdi mdi-lock-outline fs-20" style="color: var(--vz-danger);"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-1" style="color: var(--vz-danger);">Activité Clôturée</h6>
                    <p class="mb-0 text-muted" style="font-size: 0.88rem;">
                        Cette activité est terminée depuis plus d'une heure. Les modifications de présence sont verrouillées.
                    </p>
                </div>
            </div>
        </template>

        {{-- ============ CARTE PRINCIPALE ============ --}}
        <div class="card border-0 shadow-sm">

            {{-- En-tête de la carte --}}
            <div class="card-header border-0 py-3 px-4">
                <div class="d-flex flex-column flex-md-row align-items-md-center gap-3">

                    {{-- Titre + compteur --}}
                    <div class="flex-grow-1 d-flex align-items-center gap-2">
                        <div class="rounded p-2 flex-shrink-0"
                             style="background: rgba(var(--vz-primary-rgb), 0.15); width:40px; height:40px; display:flex; align-items:center; justify-content:center;">
                            <i class="mdi mdi-format-list-checks fs-20" style="color: var(--vz-primary);"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0">{{ $activity->group ? 'Membres du Groupe' : 'Membres Inscrits' }}</h5>
                            <small class="text-muted">Gérer les statuts de présence</small>
                        </div>
                    </div>

                    {{-- Sélecteur ajout membre --}}
                    @if(count($otherEligibleUsers) > 0)
                    <div class="d-flex align-items-center gap-2" style="min-width: 320px;">
                        <select class="form-select form-select-sm" x-model="selectedUserId" style="border-radius: 0.5rem;">
                            <option value="">— Ajouter un membre à la volée —</option>
                            @foreach($otherEligibleUsers as $u)
                                <option value="{{ $u->id }}">{{ $u->first_name }} {{ $u->name }}</option>
                            @endforeach
                        </select>
                        <button class="btn btn-sm btn-primary text-nowrap px-3" type="button"
                                @click="addUnregisteredMember()"
                                :disabled="!selectedUserId || isClosed"
                                style="border-radius: 0.5rem;">
                            <i class="mdi mdi-plus me-1"></i>Ajouter
                        </button>
                    </div>
                    @endif

                    {{-- Recherche --}}
                    <div style="min-width: 220px; position: relative;">
                        <i class="mdi mdi-magnify" style="position:absolute; left:10px; top:50%; transform:translateY(-50%); color: var(--vz-secondary-color); pointer-events:none;"></i>
                        <input type="text"
                               x-model="searchQuery"
                               class="form-control form-control-sm ps-4"
                               placeholder="Rechercher un membre..."
                               style="border-radius: 0.5rem; padding-left: 2rem;">
                    </div>
                </div>
            </div>

            {{-- Liste des membres (Desktop: Tableau, Mobile: Cartes) --}}
            <div class="card-body p-0 bg-light bg-md-white">
                
                {{-- VUE DESKTOP (Tableau classique) --}}
                <div class="table-responsive d-none d-md-block">
                    <table class="table align-middle mb-0 bg-white" id="attendance-table">
                        <thead style="background: rgba(var(--vz-primary-rgb), 0.08);">
                            <tr>
                                <th class="ps-4 py-3 fw-semibold" style="font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.05em; width: 22%;">
                                    <i class="mdi mdi-account me-1 text-muted"></i>Membre
                                </th>
                                <th class="py-3 fw-semibold text-center" style="font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.05em; width: 38%;">
                                    <i class="mdi mdi-check-decagram me-1 text-muted"></i>Statut de présence
                                </th>
                                <th class="py-3 fw-semibold text-center" style="font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.05em; width: 13%;">
                                    <i class="mdi mdi-qrcode-scan me-1 text-muted"></i>Source
                                </th>
                                <th class="py-3 fw-semibold" style="font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.05em; width: 20%;">
                                    <i class="mdi mdi-note-text-outline me-1 text-muted"></i>Note
                                </th>
                                <th class="py-3 fw-semibold text-center pe-4" style="font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.05em; width: 7%;">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="member in filteredMembers()" :key="member.user_id">
                                <tr class="border-bottom attendance-row" style="transition: background 0.15s;">

                                    {{-- Membre --}}
                                    <td class="ps-4 py-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="avatar-xs flex-shrink-0">
                                                <div class="avatar-title rounded-circle fw-bold"
                                                     style="background: rgba(var(--vz-primary-rgb), 0.15); color: var(--vz-primary); font-size: 0.9rem;"
                                                     x-text="member.full_name.charAt(0).toUpperCase()">
                                                </div>
                                            </div>
                                            <div>
                                                <div class="fw-semibold" style="font-size: 0.9rem;" x-text="member.full_name"></div>
                                                <small class="text-muted" x-text="member.email"></small>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Boutons statut --}}
                                    <td class="py-3 text-center">
                                        <div class="d-flex justify-content-center gap-2 flex-wrap">
                                            {{-- PRÉSENT --}}
                                            <button type="button"
                                                    class="btn btn-sm px-3 status-btn"
                                                    :class="member.status === 'PRESENT'
                                                        ? 'btn-success shadow-sm'
                                                        : 'btn-outline-success'"
                                                    :disabled="isClosed || member.isUpdating"
                                                    @click="setStatus(member, 'PRESENT')"
                                                    style="border-radius: 2rem; font-size: 0.8rem; min-width: 80px;">
                                                <i class="mdi mdi-check me-1"></i>Présent
                                            </button>
                                            {{-- EN RETARD --}}
                                            <button type="button"
                                                    class="btn btn-sm px-3 status-btn"
                                                    :class="member.status === 'LATE'
                                                        ? 'btn-warning shadow-sm'
                                                        : 'btn-outline-warning'"
                                                    :disabled="isClosed || member.isUpdating"
                                                    @click="setStatus(member, 'LATE')"
                                                    style="border-radius: 2rem; font-size: 0.8rem; min-width: 90px;">
                                                <i class="mdi mdi-clock-alert me-1"></i>En retard
                                            </button>
                                            {{-- ABSENT --}}
                                            <button type="button"
                                                    class="btn btn-sm px-3 status-btn"
                                                    :class="member.status === 'ABSENT'
                                                        ? 'btn-danger shadow-sm'
                                                        : 'btn-outline-danger'"
                                                    :disabled="isClosed || member.isUpdating"
                                                    @click="setStatus(member, 'ABSENT')"
                                                    style="border-radius: 2rem; font-size: 0.8rem; min-width: 80px;">
                                                <i class="mdi mdi-close me-1"></i>Absent
                                            </button>
                                            {{-- EXCUSÉ --}}
                                            <button type="button"
                                                    class="btn btn-sm px-3 status-btn"
                                                    :class="member.status === 'EXCUSED'
                                                        ? 'btn-info shadow-sm'
                                                        : 'btn-outline-info'"
                                                    :disabled="isClosed || member.isUpdating"
                                                    @click="setStatus(member, 'EXCUSED')"
                                                    style="border-radius: 2rem; font-size: 0.8rem; min-width: 80px;">
                                                <i class="mdi mdi-shield-check me-1"></i>Excusé
                                            </button>
                                        </div>
                                    </td>

                                    {{-- Source (badge) --}}
                                    <td class="py-3 text-center">
                                        <template x-if="member.scan_source === 'qr_code'">
                                            <span class="badge rounded-pill px-3 py-2"
                                                  style="background: rgba(var(--vz-success-rgb), 0.15); color: var(--vz-success); font-size: 0.78rem; border: 1px solid rgba(var(--vz-success-rgb), 0.3);">
                                                <i class="mdi mdi-qrcode me-1"></i>QR Code
                                            </span>
                                        </template>
                                        <template x-if="member.scan_source === 'manual'">
                                            <span class="badge rounded-pill px-3 py-2"
                                                  style="background: rgba(var(--vz-primary-rgb), 0.15); color: var(--vz-primary); font-size: 0.78rem; border: 1px solid rgba(var(--vz-primary-rgb), 0.3);">
                                                <i class="mdi mdi-hand-pointing-right me-1"></i>Manuel
                                            </span>
                                        </template>
                                        <template x-if="!member.scan_source">
                                            <span class="badge rounded-pill px-3 py-2 text-muted"
                                                  style="font-size: 0.78rem; border: 1px solid rgba(var(--vz-secondary-rgb), 0.3);">
                                                —
                                            </span>
                                        </template>
                                    </td>

                                    {{-- Note --}}
                                    <td class="py-3">
                                        <input type="text"
                                               x-model="member.note"
                                               class="form-control form-control-sm"
                                               placeholder="Ajouter une note..."
                                               :disabled="isClosed || member.isUpdating"
                                               @blur="saveNote(member)"
                                               @keyup.enter="saveNote(member)"
                                               style="border-radius: 0.5rem; font-size: 0.85rem;">
                                    </td>

                                    {{-- Supprimer --}}
                                    <td class="py-3 text-center pe-4">
                                        <button type="button"
                                                class="btn btn-sm btn-icon"
                                                :disabled="isClosed || member.isUpdating"
                                                @click="removeMember(member)"
                                                title="Retirer ce membre"
                                                style="width:34px; height:34px; border-radius:50%; background: rgba(var(--vz-danger-rgb), 0.1); color: var(--vz-danger); border: 1px solid rgba(var(--vz-danger-rgb), 0.2); transition: all 0.2s;">
                                            <i class="mdi mdi-delete-outline fs-16"></i>
                                        </button>
                                    </td>
                                </tr>
                            </template>

                            {{-- État vide --}}
                            <template x-if="filteredMembers().length === 0">
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="d-flex flex-column align-items-center gap-2">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                                 style="width:64px; height:64px; background: rgba(var(--vz-secondary-rgb), 0.1);">
                                                <i class="mdi mdi-account-question-outline fs-30 text-muted"></i>
                                            </div>
                                            <p class="text-muted mb-0">Aucun membre trouvé.</p>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                {{-- VUE MOBILE (Cartes) --}}
                <div class="d-md-none p-3">
                    <div class="d-flex flex-column gap-3">
                        <template x-for="member in filteredMembers()" :key="member.user_id + '-mob'">
                            <div class="card border-0 shadow-sm" style="border-radius: 0.75rem;">
                                <div class="card-body p-3">
                                    {{-- Ligne 1: Avatar + Nom + Source --}}
                                    <div class="d-flex align-items-start justify-content-between mb-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="avatar-sm flex-shrink-0">
                                                <div class="avatar-title rounded-circle fw-bold"
                                                     style="background: rgba(var(--vz-primary-rgb), 0.15); color: var(--vz-primary); font-size: 1rem;"
                                                     x-text="member.full_name.charAt(0).toUpperCase()">
                                                </div>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark" style="font-size: 0.95rem; line-height: 1.2;" x-text="member.full_name"></div>
                                                <small class="text-muted" x-text="member.email"></small>
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

                                    {{-- Ligne 2: Boutons de statut --}}
                                    <div class="row g-2 mb-3">
                                        <div class="col-6">
                                            <button type="button" class="btn btn-sm w-100 h-100 py-2 d-flex flex-column align-items-center justify-content-center gap-1"
                                                    :class="member.status === 'PRESENT' ? 'btn-success shadow-sm' : 'btn-outline-success'"
                                                    :disabled="isClosed || member.isUpdating"
                                                    @click="setStatus(member, 'PRESENT')"
                                                    style="border-radius: 0.5rem;">
                                                <i class="mdi mdi-check fs-18 lh-1"></i>
                                                <span style="font-size: 0.7rem; font-weight: 600;">Présent</span>
                                            </button>
                                        </div>
                                        <div class="col-6">
                                            <button type="button" class="btn btn-sm w-100 h-100 py-2 d-flex flex-column align-items-center justify-content-center gap-1"
                                                    :class="member.status === 'LATE' ? 'btn-warning shadow-sm' : 'btn-outline-warning'"
                                                    :disabled="isClosed || member.isUpdating"
                                                    @click="setStatus(member, 'LATE')"
                                                    style="border-radius: 0.5rem;">
                                                <i class="mdi mdi-clock-alert fs-18 lh-1"></i>
                                                <span style="font-size: 0.7rem; font-weight: 600;">En retard</span>
                                            </button>
                                        </div>
                                        <div class="col-6">
                                            <button type="button" class="btn btn-sm w-100 h-100 py-2 d-flex flex-column align-items-center justify-content-center gap-1"
                                                    :class="member.status === 'ABSENT' ? 'btn-danger shadow-sm' : 'btn-outline-danger'"
                                                    :disabled="isClosed || member.isUpdating"
                                                    @click="setStatus(member, 'ABSENT')"
                                                    style="border-radius: 0.5rem;">
                                                <i class="mdi mdi-close fs-18 lh-1"></i>
                                                <span style="font-size: 0.7rem; font-weight: 600;">Absent</span>
                                            </button>
                                        </div>
                                        <div class="col-6">
                                            <button type="button" class="btn btn-sm w-100 h-100 py-2 d-flex flex-column align-items-center justify-content-center gap-1"
                                                    :class="member.status === 'EXCUSED' ? 'btn-info shadow-sm' : 'btn-outline-info'"
                                                    :disabled="isClosed || member.isUpdating"
                                                    @click="setStatus(member, 'EXCUSED')"
                                                    style="border-radius: 0.5rem;">
                                                <i class="mdi mdi-shield-check fs-18 lh-1"></i>
                                                <span style="font-size: 0.7rem; font-weight: 600;">Excusé</span>
                                            </button>
                                        </div>
                                    </div>

                                    {{-- Ligne 3: Note et suppression --}}
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="flex-grow-1 position-relative">
                                            <i class="mdi mdi-note-text-outline position-absolute text-muted" style="left: 10px; top: 50%; transform: translateY(-50%);"></i>
                                            <input type="text"
                                                   x-model="member.note"
                                                   class="form-control form-control-sm ps-4 bg-light border-0"
                                                   placeholder="Ajouter une note..."
                                                   :disabled="isClosed || member.isUpdating"
                                                   @blur="saveNote(member)"
                                                   @keyup.enter="saveNote(member)"
                                                   style="border-radius: 0.5rem; font-size: 0.85rem;">
                                        </div>
                                        <button type="button" class="btn btn-sm flex-shrink-0"
                                                :disabled="isClosed || member.isUpdating"
                                                @click="removeMember(member)"
                                                style="width:36px; height:36px; border-radius: 0.5rem; background: rgba(var(--vz-danger-rgb), 0.1); color: var(--vz-danger);">
                                            <i class="mdi mdi-delete-outline"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>

                        {{-- État vide mobile --}}
                        <template x-if="filteredMembers().length === 0">
                            <div class="text-center py-5">
                                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                     style="width:64px; height:64px; background: rgba(var(--vz-secondary-rgb), 0.1);">
                                    <i class="mdi mdi-account-question-outline fs-30 text-muted"></i>
                                </div>
                                <p class="text-muted mb-0">Aucun membre trouvé.</p>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Pied de carte : statistiques rapides --}}
            <div class="card-footer border-0 px-4 py-3" style="background: rgba(var(--vz-primary-rgb), 0.04);">
                <div class="d-flex flex-wrap gap-3 align-items-center">
                    <span class="text-muted" style="font-size: 0.82rem;">
                        <i class="mdi mdi-account-multiple me-1"></i>
                        <span x-text="filteredMembers().length"></span> membre(s) affiché(s)
                    </span>
                    <span class="badge px-3 py-2" style="background: rgba(var(--vz-success-rgb), 0.15); color: var(--vz-success); font-size: 0.78rem;">
                        <i class="mdi mdi-check me-1"></i>
                        Présents : <span x-text="members.filter(m => m.status === 'PRESENT').length"></span>
                    </span>
                    <span class="badge px-3 py-2" style="background: rgba(var(--vz-warning-rgb), 0.15); color: var(--vz-warning); font-size: 0.78rem;">
                        <i class="mdi mdi-clock-alert me-1"></i>
                        En retard : <span x-text="members.filter(m => m.status === 'LATE').length"></span>
                    </span>
                    <span class="badge px-3 py-2" style="background: rgba(var(--vz-danger-rgb), 0.15); color: var(--vz-danger); font-size: 0.78rem;">
                        <i class="mdi mdi-close me-1"></i>
                        Absents : <span x-text="members.filter(m => m.status === 'ABSENT').length"></span>
                    </span>
                    <span class="badge px-3 py-2" style="background: rgba(var(--vz-info-rgb), 0.15); color: var(--vz-info); font-size: 0.78rem;">
                        <i class="mdi mdi-shield-check me-1"></i>
                        Excusés : <span x-text="members.filter(m => m.status === 'EXCUSED').length"></span>
                    </span>
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
                return this.members.filter(m =>
                    m.full_name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                    m.email.toLowerCase().includes(this.searchQuery.toLowerCase())
                );
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
                                    local.scan_source = updated.scan_source || '';
                                }
                            } else {
                                this.members.push({
                                    user_id: updated.user_id,
                                    full_name: updated.full_name,
                                    email: updated.email,
                                    status: updated.status || '',
                                    note: updated.note || '',
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
                if (this.isClosed || member.isUpdating) return;
                member.isUpdating = true;
                let oldStatus = member.status;
                member.status = newStatus;
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
                if (this.isClosed || member.isUpdating) return;
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
                if (this.isClosed || member.isUpdating) return;
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
