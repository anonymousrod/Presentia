@extends('layouts.app')

@section('title', 'Modifier le Rôle — ' . $role->name)

@section('content')
@php
    use App\Enums\PermissionEnum;
    $resourceTranslations = [
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

    $allPermNames     = collect($groupedPermissions)->flatten()->pluck('name')->toArray();
    $totalPermissions = count($allPermNames);
@endphp

{{-- Page Title --}}
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Modifier le Rôle</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">Rôles</a></li>
                    <li class="breadcrumb-item active">{{ $role->name }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>

{{-- Single Alpine root --}}
<div x-data="{
    selected: {{ json_encode($rolePermissionNames) }},

    isSelected(perm) { return this.selected.includes(perm); },

    toggle(perm) {
        if (this.isSelected(perm)) {
            this.selected = this.selected.filter(p => p !== perm);
        } else {
            this.selected = [...this.selected, perm];
        }
    },

    selectAll() { this.selected = {{ json_encode($allPermNames) }}; },
    deselectAll() { this.selected = []; },

    groupToggle(perms) {
        const allChecked = perms.every(p => this.isSelected(p));
        if (allChecked) {
            this.selected = this.selected.filter(p => !perms.includes(p));
        } else {
            const toAdd = perms.filter(p => !this.isSelected(p));
            this.selected = [...this.selected, ...toAdd];
        }
    },

    groupAllSelected(perms) { return perms.length > 0 && perms.every(p => this.isSelected(p)); },
    groupSomeSelected(perms) { return perms.some(p => this.isSelected(p)) && !this.groupAllSelected(perms); },
    groupCount(perms) { return perms.filter(p => this.isSelected(p)).length; }
}">

<form action="{{ route('admin.roles.update', $role) }}" method="POST">
    @csrf
    @method('PUT')

    {{-- Hidden inputs built from reactive selected array --}}
    <template x-for="perm in selected" :key="perm">
        <input type="hidden" name="permissions[]" :value="perm">
    </template>

    <div class="row g-3">

        {{-- LEFT: Sidebar --}}
        <div class="col-xl-3 col-lg-4">
            <div class="card sticky-sidebar">
                {{-- Role Name --}}
                <div class="card-body p-3 border-bottom border-dashed">
                    <label class="form-label fw-semibold text-uppercase text-muted fs-10 letter-spacing-1">Nom du rôle</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent">
                            <i class="ri-shield-user-line text-primary"></i>
                        </span>
                        <input type="text" name="name" id="name"
                               class="form-control @error('name') is-invalid @enderror"
                               placeholder="ex: Modérateur..."
                               value="{{ old('name', $role->name) }}" required
                               {{ $role->code === 'admin' ? 'readonly' : '' }}>
                    </div>
                    @error('name')
                        <div class="text-danger mt-1 fs-12">{{ $message }}</div>
                    @enderror
                    @if($role->is_system)
                        <span class="badge bg-secondary-subtle text-secondary mt-2">SYSTÈME</span>
                    @else
                        <span class="badge bg-info-subtle text-info mt-2">PERSONNALISÉ</span>
                    @endif
                </div>

                {{-- Stats --}}
                <div class="card-body p-3 border-bottom border-dashed">
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="p-2 rounded bg-primary-subtle text-center">
                                <div class="fs-18 fw-bold text-primary">{{ $role->users()->count() }}</div>
                                <div class="fs-11 text-muted">Utilisateurs</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 rounded bg-success-subtle text-center">
                                <div class="fs-18 fw-bold text-success" x-text="selected.length"></div>
                                <div class="fs-11 text-muted">/ {{ $totalPermissions }} perms</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Group navigation --}}
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach($groupedPermissions as $resource => $perms)
                            @php
                                $meta = $resourceTranslations[$resource] ?? ['label' => ucfirst($resource), 'icon' => 'ri-key-2-line', 'color' => 'primary'];
                                $permNamesJs = json_encode($perms->pluck('name')->toArray());
                            @endphp
                            <a href="#section-{{ $resource }}"
                               class="list-group-item list-group-item-action d-flex align-items-center gap-2 py-2 px-3 border-0 group-nav-link">
                                <i class="{{ $meta['icon'] }} text-{{ $meta['color'] }} fs-15"></i>
                                <span class="flex-grow-1 fs-13">{{ $meta['label'] }}</span>
                                <span class="badge bg-{{ $meta['color'] }}-subtle text-{{ $meta['color'] }} fs-10"
                                      x-text="groupCount({{ $permNamesJs }})"></span>
                            </a>
                        @endforeach
                    </div>
                </div>


            </div>
        </div>

        {{-- RIGHT: Permissions --}}
        <div class="col-xl-9 col-lg-8">

            {{-- Global header --}}
            <div class="card mb-3">
                <div class="card-body p-3 d-flex flex-wrap align-items-center gap-2">
                    <div class="flex-grow-1">
                        <h5 class="mb-0 fw-bold">Permissions — <span class="text-primary">{{ $role->name }}</span></h5>
                        <small class="text-muted">
                            <span x-text="selected.length"></span> sur {{ $totalPermissions }} sélectionnées
                        </small>
                    </div>
                    <button type="button" class="btn btn-sm btn-success" @click="selectAll()">
                        <i class="ri-check-double-line me-1"></i>Tout sélectionner
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger" @click="deselectAll()">
                        <i class="ri-close-line me-1"></i>Tout décocher
                    </button>
                </div>
            </div>

            {{-- Permission groups --}}
            @foreach($groupedPermissions as $resource => $permissions)
                @php
                    $meta = $resourceTranslations[$resource] ?? ['label' => ucfirst($resource), 'icon' => 'ri-key-2-line', 'color' => 'primary'];
                    $permNamesJs = json_encode($permissions->pluck('name')->toArray());
                @endphp
                <div class="card mb-3" id="section-{{ $resource }}">
                    {{-- Group Header --}}
                    <div class="card-header p-0 border-0">
                        <div class="d-flex align-items-center px-3 py-2 group-header rounded-top">
                            <div class="me-3">
                                <input type="checkbox"
                                       class="square-checkbox"
                                       :checked="groupAllSelected({{ $permNamesJs }})"
                                       @change="groupToggle({{ $permNamesJs }})"
                                       id="group-chk-{{ $resource }}"
                                       x-effect="$el.indeterminate = groupSomeSelected({{ $permNamesJs }})">
                            </div>
                            <i class="{{ $meta['icon'] }} text-{{ $meta['color'] }} fs-16 me-2"></i>
                            <label for="group-chk-{{ $resource }}"
                                   class="fw-semibold fs-13 flex-grow-1 mb-0 cursor-pointer text-uppercase letter-spacing-1">
                                {{ $meta['label'] }}
                            </label>
                            <span class="badge bg-{{ $meta['color'] }} rounded-pill">
                                <span x-text="groupCount({{ $permNamesJs }})"></span>/{{ $permissions->count() }}
                            </span>
                        </div>
                    </div>

                    {{-- Permission items --}}
                    <div class="card-body p-3">
                        <div class="row g-2">
                            @foreach($permissions as $permission)
                                <div class="col-xl-3 col-lg-4 col-md-4 col-sm-6 col-6">
                                    <label for="perm-{{ $permission->id }}"
                                           class="perm-label d-flex align-items-start gap-2 p-2 rounded border cursor-pointer w-100 h-100"
                                           :class="isSelected('{{ $permission->name }}') ? 'perm-active' : 'perm-inactive'">
                                        <input type="checkbox"
                                               id="perm-{{ $permission->id }}"
                                               class="square-checkbox"
                                               :checked="isSelected('{{ $permission->name }}')"
                                               @change="toggle('{{ $permission->name }}')">
                                        <div class="overflow-hidden">
                                            <div class="fs-12 fw-semibold lh-sm">
                                                {{ PermissionEnum::tryFrom($permission->name)?->label() ?? $permission->name }}
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach

            {{-- Bottom Actions --}}
            <div class="card mb-3">
                <div class="card-body p-3 d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.roles.index') }}" class="btn btn-light">Annuler</a>
                    <button type="submit" class="btn btn-primary px-5">
                        <i class="ri-save-line me-1"></i> Enregistrer les modifications
                    </button>
                </div>
            </div>

        </div>
    </div>
</form>
</div>

<style>
    .sticky-sidebar { position: sticky; top: 70px; }
    .letter-spacing-1 { letter-spacing: 1px; }
    .cursor-pointer { cursor: pointer; }

    /* Square checkboxes */
    .square-checkbox {
        width: 1.1rem;
        height: 1.1rem;
        border-radius: 3px !important;
        cursor: pointer;
        flex-shrink: 0;
        margin-top: 2px;
    }

    /* Permission label cards */
    .perm-label { transition: border-color 0.15s, background-color 0.15s; min-height: 54px; }
    .perm-inactive { border-color: var(--vz-border-color) !important; }
    .perm-inactive:hover { border-color: var(--vz-primary) !important; background-color: var(--vz-primary-bg-subtle); }
    .perm-active { border-color: var(--vz-primary) !important; background-color: var(--vz-primary-bg-subtle); }

    /* Group header */
    .group-header { background-color: var(--vz-light); }

    /* Dark mode */
    [data-layout-mode="dark"] .perm-inactive { border-color: rgba(255,255,255,0.1) !important; }
    [data-layout-mode="dark"] .perm-inactive:hover,
    [data-layout-mode="dark"] .perm-active { border-color: var(--vz-primary) !important; background-color: rgba(105,148,255,0.15); }
    [data-layout-mode="dark"] .group-header { background-color: rgba(255,255,255,0.05) !important; }

    /* Sidebar nav */
    .group-nav-link { transition: background 0.15s; }
    .group-nav-link:hover { background-color: var(--vz-primary-bg-subtle); }
    .group-nav-link.active { background-color: var(--vz-primary-bg-subtle); font-weight: 600; }
    [x-cloak] { display: none !important; }

        .perm-label { min-height: 52px; }
    }
</style>

<script>
    // Alpine global store for selected permissions
    document.addEventListener('alpine:init', () => {
        const initial = new Set([
            @foreach(collect($groupedPermissions)->flatten() as $perm)
                @if(in_array($perm->name, $rolePermissionNames))
                    '{{ $perm->name }}',
                @endif
            @endforeach
        ]);

        Alpine.store('selected', initial);
    });

    function roleEditor() {
        return {
            get selectedTotal() {
                return Alpine.store('selected').size;
            },

            get groupCounts() {
                const groups = {};
                @foreach($groupedPermissions as $resource => $perms)
                    groups['{{ $resource }}'] = [{{ collect($perms)->pluck('name')->map(fn($n) => "'$n'")->implode(',') }}]
                        .filter(p => Alpine.store('selected').has(p)).length;
                @endforeach
                return groups;
            },

            selectAll() {
                @foreach($groupedPermissions as $resource => $perms)
                    @foreach($perms as $perm)
                        Alpine.store('selected').add('{{ $perm->name }}');
                    @endforeach
                @endforeach
                Alpine.store('selected', new Set(Alpine.store('selected')));
            },

            deselectAll() {
                Alpine.store('selected', new Set());
            }
        }
    }
</script>

@endsection
