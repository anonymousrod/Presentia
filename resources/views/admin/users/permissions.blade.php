@extends('layouts.app')

@section('title', 'Gérer les Permissions — ' . $user->full_name)

@section('content')
@php
    use App\Enums\PermissionEnum;

    $resourceMeta = [
        'member'       => ['label' => 'Membres',       'icon' => 'ri-user-3-line',        'color' => 'primary'],
        'group'        => ['label' => 'Groupes',        'icon' => 'ri-group-line',          'color' => 'info'],
        'activity'     => ['label' => 'Activités',     'icon' => 'ri-calendar-event-line', 'color' => 'success'],
        'attendance'   => ['label' => 'Présences',     'icon' => 'ri-checkbox-circle-line','color' => 'warning'],
        'registration' => ['label' => 'Inscriptions',  'icon' => 'ri-file-list-3-line',    'color' => 'danger'],
        'notification' => ['label' => 'Notifications', 'icon' => 'ri-notification-3-line', 'color' => 'primary'],
        'stats'        => ['label' => 'Statistiques',  'icon' => 'ri-bar-chart-line',      'color' => 'info'],
        'report'       => ['label' => 'Rapports',      'icon' => 'ri-file-chart-line',     'color' => 'success'],
        'role'         => ['label' => 'Rôles',         'icon' => 'ri-shield-star-line',    'color' => 'warning'],
        'permission'   => ['label' => 'Permissions',   'icon' => 'ri-key-2-line',          'color' => 'danger'],
        'audit'        => ['label' => 'Audit & Logs',  'icon' => 'ri-file-search-line',    'color' => 'secondary'],
        'qrcode'       => ['label' => 'Codes QR',      'icon' => 'ri-qr-code-line',        'color' => 'primary'],
    ];

    $allPermNames = collect($groupedPermissions)->flatten()->pluck('name')->toArray();
@endphp

<!-- Page Title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Gérer les Permissions</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Utilisateurs</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.users.show', $user) }}">{{ $user->full_name }}</a></li>
                    <li class="breadcrumb-item active">Permissions</li>
                </ol>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-3">
        <i class="ri-check-line me-1 align-middle fs-16"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{--
    ARCHITECTURE ALPINE.JS
    ─────────────────────
    Un seul x-data à la racine gère TOUT l'état :
    • tab           : onglet actif ('edit' | 'view')
    • selectedPerms : tableau des permissions directes cochées (réactif)
    Les rôles utilisent des checkboxes HTML standards (pas besoin d'Alpine).
    Les inputs name="permissions[]" sont générés via <template x-for>.
--}}
<div x-data="{
    tab: window.location.hash === '#effective' ? 'view' : 'edit',

    selectedPerms: {{ json_encode($directPermissionNames) }},

    has(perm)     { return this.selectedPerms.includes(perm); },

    toggle(perm) {
        if (this.has(perm)) {
            this.selectedPerms = this.selectedPerms.filter(p => p !== perm);
        } else {
            this.selectedPerms = [...this.selectedPerms, perm];
        }
    },

    selectAll()   { this.selectedPerms = {{ json_encode($allPermNames) }}; },
    clearAll()    { this.selectedPerms = []; },

    groupAll(perms)  { perms.forEach(p => { if (!this.has(p)) this.selectedPerms = [...this.selectedPerms, p]; }); },
    groupNone(perms) { this.selectedPerms = this.selectedPerms.filter(p => !perms.includes(p)); },
    groupToggle(perms) {
        perms.every(p => this.has(p)) ? this.groupNone(perms) : this.groupAll(perms);
    },
    groupAllSel(perms)  { return perms.length > 0 && perms.every(p => this.has(p)); },
    groupSomeSel(perms) { return perms.some(p => this.has(p)) && !this.groupAllSel(perms); },
    groupCount(perms)   { return perms.filter(p => this.has(p)).length; }
}">

<form action="{{ route('admin.users.permissions.update', $user) }}" method="POST">
    @csrf


    {{-- Permissions directes : générées dynamiquement depuis l'état Alpine --}}
    <template x-for="p in selectedPerms" :key="p">
        <input type="hidden" name="permissions[]" :value="p">
    </template>

    <!-- ═══════════════ LAYOUT PRINCIPAL ═══════════════ -->
    <div class="row g-0">

        <!-- ════ COLONNE GAUCHE — Sidebar sticky ════ -->
        <div class="col-xl-3 col-lg-4 pe-lg-3 mb-3 mb-lg-0">
            <div class="perm-sidebar">

                <!-- Profil utilisateur -->
                <div class="card mb-3">
                    <div class="card-body p-3 d-flex align-items-center gap-3">
                        <img src="{{ auth()->user()->photo ? asset('storage/' . auth()->user()->photo) : asset('assets/images/users/avatar-1.jpg') }}"
                             alt="{{ $user->full_name }}"
                             class="rounded-circle avatar-sm flex-shrink-0">
                        <div class="overflow-hidden">
                            <div class="fw-bold fs-14 text-truncate">{{ $user->full_name }}</div>
                            <div class="text-muted fs-11 text-truncate">{{ $user->email }}</div>
                            <div class="mt-1">
                                @forelse($user->roles as $r)
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-10">{{ $r->name }}</span>
                                @empty
                                    <span class="text-muted fs-11">Aucun rôle</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Toggle onglets -->
                <div class="card mb-3">
                    <div class="card-body p-2">
                        <div class="btn-group w-100">
                            <button type="button" class="btn btn-sm fw-medium"
                                    :class="tab==='edit' ? 'btn-primary' : 'btn-outline-secondary'"
                                    @click="tab='edit'; window.location.hash=''">
                                <i class="ri-edit-box-line me-1"></i>Modifier
                            </button>
                            <button type="button" class="btn btn-sm fw-medium"
                                    :class="tab==='view' ? 'btn-info' : 'btn-outline-secondary'"
                                    @click="tab='view'; window.location.hash='effective'">
                                <i class="ri-eye-line me-1"></i>Effectifs
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Rôles -->
                <div class="card mb-3" x-show="tab === 'edit'">
                    <div class="card-header py-2 px-3 border-bottom border-dashed">
                        <span class="fs-11 fw-semibold text-uppercase text-muted letter-spacing-1">
                            <i class="ri-shield-star-line me-1"></i>Rôles
                        </span>
                    </div>
                    <div class="card-body p-2">
                        @foreach($roles as $role)
                            @php
                                $isJeune       = $role->code === 'default_user';
                                $isGroupLeader = $role->code === 'group_leader';
                                $isDisabled    = $isJeune || $isGroupLeader;
                                $isChecked     = $isJeune || in_array($role->name, $currentUserRoleNames);
                            @endphp
                            <div class="role-item d-flex align-items-center gap-2 px-2 py-2 rounded mb-1
                                        {{ $isDisabled ? 'role-item-locked' : 'role-item-editable' }}">
                                <div class="form-check mb-0">
                                    <input type="checkbox"
                                           class="form-check-input sq-check"
                                           name="roles_checkbox[]"
                                           value="{{ $role->name }}"
                                           id="sidebar-role-{{ $role->id }}"
                                           {{ $isChecked ? 'checked' : '' }}
                                           {{ $isDisabled ? 'disabled' : '' }}
                                           @if(!$isDisabled)
                                           onchange="document.getElementById('hidden-role-{{ $role->id }}').disabled = !this.checked"
                                           @endif>
                                </div>
                                <label for="sidebar-role-{{ $role->id }}" class="flex-grow-1 cursor-pointer mb-0">
                                    <div class="fs-12 fw-semibold lh-sm">
                                        {{ $role->name }}
                                        @if($isJeune)
                                            <span class="badge bg-danger-subtle text-danger fs-9 ms-1">Requis</span>
                                        @elseif($isGroupLeader)
                                            <span class="badge bg-warning-subtle text-warning fs-9 ms-1"
                                                  title="Géré automatiquement à l'assignation d'un groupe">Auto</span>
                                        @endif
                                    </div>
                                    <div class="fs-10 text-muted">{{ $role->permissions()->count() }} permissions</div>
                                </label>
                            </div>
                            {{-- Hidden input géré par JS pour les rôles modifiables --}}
                            @if(!$isDisabled && $isChecked)
                                <input type="hidden" id="hidden-role-{{ $role->id }}" name="roles[]" value="{{ $role->name }}">
                            @elseif(!$isDisabled)
                                <input type="hidden" id="hidden-role-{{ $role->id }}" name="roles[]" value="{{ $role->name }}" disabled>
                            @endif
                        @endforeach
                    </div>
                </div>

                <!-- Stats -->
                <div class="card mb-3" x-show="tab === 'edit'">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fs-11 fw-semibold text-muted text-uppercase letter-spacing-1">Permissions directes</span>
                            <span class="badge bg-success-subtle text-success">
                                <span x-text="selectedPerms.length"></span>/{{ count($allPermNames) }}
                            </span>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-success flex-grow-1" @click="selectAll()">
                                <i class="ri-check-double-line me-1"></i>Tout
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger flex-grow-1" @click="clearAll()">
                                <i class="ri-close-line me-1"></i>Aucun
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Navigation groupes (uniquement onglet edit) -->
                <div class="card mb-3" x-show="tab === 'edit'">
                    <div class="list-group list-group-flush rounded">
                        @foreach($groupedPermissions as $resource => $perms)
                            @php $m = $resourceMeta[$resource] ?? ['label' => ucfirst($resource), 'icon' => 'ri-key-2-line', 'color' => 'primary']; @endphp
                            <a href="#sec-{{ $resource }}"
                               class="list-group-item list-group-item-action border-0 d-flex align-items-center gap-2 py-2 px-3 grp-nav-link">
                                <i class="{{ $m['icon'] }} text-{{ $m['color'] }} fs-14"></i>
                                <span class="flex-grow-1 fs-12">{{ $m['label'] }}</span>
                                <span class="badge bg-{{ $m['color'] }}-subtle text-{{ $m['color'] }} rounded-pill fs-10"
                                      x-text="groupCount({{ json_encode($perms->pluck('name')->toArray()) }})"></span>
                            </a>
                        @endforeach
                    </div>
                </div>



            </div>
        </div>

        <!-- ════ COLONNE DROITE — Contenu principal ════ -->
        <div class="col-xl-9 col-lg-8">

            <!-- ── ONGLET MODIFIER ── -->
            <div x-show="tab === 'edit'" x-transition>

                <!-- Info banner -->
                <div class="alert alert-warning border-0 rounded-3 mb-3 py-2 px-3 d-flex align-items-center gap-2">
                    <i class="ri-information-line fs-18 flex-shrink-0 text-warning"></i>
                    <div class="fs-12 text-muted">
                        <strong class="text-body">Permissions directes :</strong>
                        elles s'ajoutent à celles des rôles et accordent des droits exceptionnels à cet utilisateur uniquement.
                    </div>
                </div>

                <!-- Groupes de permissions -->
                @foreach($groupedPermissions as $resource => $permissions)
                    @php
                        $m           = $resourceMeta[$resource] ?? ['label' => ucfirst($resource), 'icon' => 'ri-key-2-line', 'color' => 'primary'];
                        $permNames   = $permissions->pluck('name')->toArray();
                        $permNamesJs = json_encode($permNames);
                    @endphp
                    <div class="card mb-3" id="sec-{{ $resource }}">
                        <!-- En-tête groupe -->
                        <div class="card-header p-0 border-0">
                            <div class="d-flex align-items-center px-3 py-2 grp-header rounded-top">
                                <div class="me-3">
                                    <input type="checkbox"
                                           class="sq-check"
                                           id="grp-{{ $resource }}"
                                           :checked="groupAllSel({{ $permNamesJs }})"
                                           x-effect="$el.indeterminate = groupSomeSel({{ $permNamesJs }})"
                                           @change="groupToggle({{ $permNamesJs }})">
                                </div>
                                <i class="{{ $m['icon'] }} text-{{ $m['color'] }} fs-15 me-2"></i>
                                <label for="grp-{{ $resource }}"
                                       class="fw-semibold fs-12 flex-grow-1 mb-0 cursor-pointer text-uppercase letter-spacing-1">
                                    {{ $m['label'] }}
                                </label>
                                <span class="badge bg-{{ $m['color'] }} rounded-pill">
                                    <span x-text="groupCount({{ $permNamesJs }})"></span>/{{ count($permNames) }}
                                </span>
                            </div>
                        </div>

                        <!-- Grille de permissions -->
                        <div class="card-body p-3">
                            <div class="row g-2">
                                @foreach($permissions as $permission)
                                    @php $inherited = in_array($permission->name, $rolePermissionNames); @endphp
                                    <div class="col-xl-3 col-lg-4 col-md-4 col-sm-6 col-6">
                                        <label for="dp-{{ $permission->id }}"
                                               class="perm-card d-flex align-items-start gap-2 p-2 rounded border w-100 h-100 cursor-pointer"
                                               :class="has('{{ $permission->name }}')
                                                       ? 'perm-card-active'
                                                       : '{{ $inherited ? 'perm-card-inherited' : 'perm-card-default' }}'">
                                            <input type="checkbox"
                                                   id="dp-{{ $permission->id }}"
                                                   class="sq-check flex-shrink-0"
                                                   :checked="has('{{ $permission->name }}')"
                                                   @change="toggle('{{ $permission->name }}')">
                                            <div class="overflow-hidden">
                                                <div class="fs-12 fw-semibold lh-sm">
                                                    {{ PermissionEnum::tryFrom($permission->name)?->label() ?? $permission->name }}
                                                </div>
                                                @if($inherited)
                                                    <span class="badge bg-primary-subtle text-primary fs-9 mt-1">HÉRITÉ</span>
                                                @endif
                                            </div>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- Actions -->
                <div class="card mb-3">
                    <div class="card-body p-3 d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-light">Annuler</a>
                        <button type="submit" class="btn btn-primary px-5 fw-semibold">
                            <i class="ri-save-line me-1"></i> Enregistrer les modifications
                        </button>
                    </div>
                </div>

            </div>

            <!-- ── ONGLET DROITS EFFECTIFS ── -->
            <div x-show="tab === 'view'" x-transition x-cloak>
                <div class="card mb-3">
                    <div class="card-body py-2 px-3 d-flex align-items-center gap-2">
                        <i class="ri-shield-check-line text-success fs-20"></i>
                        <div>
                            <div class="fw-semibold">Droits effectifs — {{ $user->full_name }}</div>
                            <div class="fs-11 text-muted">Combinaison des rôles + permissions directes accordées.</div>
                        </div>
                    </div>
                </div>

                @forelse($groupedPermissions as $resource => $permissions)
                    @php
                        $m         = $resourceMeta[$resource] ?? ['label' => ucfirst($resource), 'icon' => 'ri-key-2-line', 'color' => 'primary'];
                        $effective = collect($permissions)->filter(
                            fn($p) => in_array($p->name, $rolePermissionNames) || in_array($p->name, $directPermissionNames)
                        );
                    @endphp
                    @if($effective->isNotEmpty())
                        <div class="card mb-3">
                            <div class="card-header p-0 border-0">
                                <div class="d-flex align-items-center px-3 py-2 grp-header rounded-top">
                                    <i class="{{ $m['icon'] }} text-{{ $m['color'] }} fs-15 me-2"></i>
                                    <span class="fw-semibold fs-12 flex-grow-1 text-uppercase letter-spacing-1">{{ $m['label'] }}</span>
                                    <span class="badge bg-{{ $m['color'] }} rounded-pill">{{ $effective->count() }}</span>
                                </div>
                            </div>
                            <div class="card-body p-3">
                                <div class="row g-2">
                                    @foreach($effective as $permission)
                                        @php
                                            $hasViaRole = in_array($permission->name, $rolePermissionNames);
                                            $hasDirect  = in_array($permission->name, $directPermissionNames);
                                        @endphp
                                        <div class="col-xl-3 col-lg-4 col-md-4 col-sm-6 col-6">
                                            <div class="perm-card perm-card-effective d-flex align-items-start gap-2 p-2 rounded border h-100">
                                                <i class="ri-checkbox-circle-fill text-success fs-16 flex-shrink-0 mt-1"></i>
                                                <div class="overflow-hidden">
                                                    <div class="fs-12 fw-semibold lh-sm">
                                                        {{ PermissionEnum::tryFrom($permission->name)?->label() ?? $permission->name }}
                                                    </div>
                                                    <div class="d-flex flex-wrap gap-1 mt-1 fs-9">
                                                        @if($hasViaRole)
                                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-9">VIA RÔLE</span>
                                                        @endif
                                                        @if($hasDirect)
                                                            <span class="badge bg-info-subtle text-info border border-info-subtle fs-9">DIRECT</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                @empty
                    <div class="card">
                        <div class="card-body text-center py-5 text-muted">
                            <i class="ri-lock-line fs-48 d-block mb-3 opacity-50"></i>
                            Aucune permission effective pour cet utilisateur.
                        </div>
                    </div>
                @endforelse
            </div>

        </div>
    </div><!-- /row -->
</form>
</div><!-- /x-data -->

<style>
    /* ── Layout ── */
    .perm-sidebar { position: sticky; top: 70px; }

    /* ── Square checkboxes ── */
    .sq-check {
        width: 1.05rem;
        height: 1.05rem;
        border-radius: 3px !important;
        cursor: pointer;
        flex-shrink: 0;
        margin-top: 2px;
    }

    /* ── Group headers ── */
    .grp-header { background: var(--vz-light); }
    [data-layout-mode="dark"] .grp-header { background: rgba(255,255,255,0.04) !important; }

    /* ── Permission cards ── */
    .perm-card { transition: border-color 0.15s, background 0.15s; min-height: 56px; }

    .perm-card-default  { border-color: var(--vz-border-color) !important; }
    .perm-card-default:hover { border-color: var(--vz-primary) !important; background: var(--vz-primary-bg-subtle); }

    .perm-card-active   { border-color: var(--vz-primary) !important; background: var(--vz-primary-bg-subtle); }

    .perm-card-inherited { border-color: var(--vz-info) !important; background: var(--vz-info-bg-subtle); }

    .perm-card-effective { border-color: var(--vz-success-border-subtle) !important; background: var(--vz-success-bg-subtle); }

    /* Dark mode overrides */
    [data-layout-mode="dark"] .perm-card-default  { border-color: rgba(255,255,255,0.1) !important; }
    [data-layout-mode="dark"] .perm-card-default:hover,
    [data-layout-mode="dark"] .perm-card-active   { border-color: var(--vz-primary) !important; background: rgba(105,148,255,0.14); }
    [data-layout-mode="dark"] .perm-card-inherited { border-color: var(--vz-info) !important; background: rgba(41,156,219,0.1); }
    [data-layout-mode="dark"] .perm-card-effective { border-color: var(--vz-success) !important; background: rgba(10,179,156,0.1); }

    /* ── Role items sidebar ── */
    .role-item { border: 1px solid transparent; transition: background 0.15s, border-color 0.15s; }
    .role-item-editable:hover { border-color: var(--vz-primary) !important; background: var(--vz-primary-bg-subtle); }
    .role-item-locked { background: var(--vz-light); opacity: 0.85; }
    [data-layout-mode="dark"] .role-item-locked { background: rgba(255,255,255,0.04); }

    /* ── Group nav links ── */
    .grp-nav-link { transition: background 0.15s; }
    .grp-nav-link:hover { background: var(--vz-primary-bg-subtle); }

    /* ── Misc ── */
    .letter-spacing-1 { letter-spacing: 1px; }
    .cursor-pointer   { cursor: pointer; }
    .fs-9 { font-size: 0.65rem; }
    [x-cloak] { display: none !important; }

    @media (max-width: 991px) { .perm-sidebar { position: static; } }
</style>

{{-- Gestion des rôles via checkboxes HTML standard --}}
<script>
    document.querySelectorAll('[name="roles_checkbox[]"]').forEach(function(chk) {
        chk.addEventListener('change', function() {
            const hiddenId = 'hidden-role-' + this.id.replace('sidebar-role-', '');
            const hidden   = document.getElementById(hiddenId);
            if (hidden) hidden.disabled = !this.checked;
        });
    });
</script>
@endsection
