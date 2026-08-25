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

<div class="container-fluid p-0">
    {{-- Page Title --}}
    <div class="row align-items-center mb-3 g-2">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between py-2">
                <h4 class="mb-sm-0 fw-bold fs-18 fs-md-22">Modifier le Rôle</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0 fs-12">
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
                <div class="card border-0 shadow-sm rounded-3 sticky-sidebar">
                    {{-- Role Name --}}
                    <div class="card-body p-3 border-bottom border-light-subtle">
                        <label class="form-label fw-bold text-uppercase text-muted fs-10 letter-spacing-1 mb-1">Nom du rôle</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-light-subtle">
                                <i class="ri-shield-user-line text-primary"></i>
                            </span>
                            <input type="text" name="name" id="name"
                                   class="form-control bg-light border-light-subtle @error('name') is-invalid @enderror"
                                   placeholder="ex: Modérateur..."
                                   value="{{ old('name', $role->name) }}" required
                                   {{ $role->code === 'admin' ? 'readonly' : '' }}>
                        </div>
                        @error('name')
                            <div class="text-danger mt-1 fs-12">{{ $message }}</div>
                        @enderror
                        @if($role->is_system)
                            <span class="badge bg-secondary-subtle text-secondary mt-2 fs-10">SYSTÈME</span>
                        @else
                            <span class="badge bg-info-subtle text-info mt-2 fs-10">PERSONNALISÉ</span>
                        @endif
                    </div>

                    {{-- Role Description --}}
                    <div class="card-body p-3 border-bottom border-light-subtle">
                        <label class="form-label fw-bold text-uppercase text-muted fs-10 letter-spacing-1 mb-1">Description <span class="text-muted text-lowercase fw-normal">(Optionnel)</span></label>
                        <textarea name="description" id="description" rows="3"
                               class="form-control bg-light border-light-subtle @error('description') is-invalid @enderror"
                               placeholder="ex: Rôle assigné aux personnes qui...">{{ old('description', $role->description) }}</textarea>
                        @error('description')
                            <div class="text-danger mt-1 fs-12">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Stats --}}
                    <div class="card-body p-3 border-bottom border-light-subtle">
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="p-2 rounded-3 bg-primary-subtle text-center">
                                    <div class="fs-18 fw-bold text-primary">{{ $role->users()->count() }}</div>
                                    <div class="fs-11 text-muted">Utilisateurs</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-2 rounded-3 bg-success-subtle text-center">
                                    <div class="fs-18 fw-bold text-success" x-text="selected.length"></div>
                                    <div class="fs-11 text-muted">/ {{ $totalPermissions }} perms</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Group navigation --}}
                    <div class="card-body p-0 d-none d-lg-block">
                        <div class="list-group list-group-flush rounded-bottom-3">
                            @foreach($groupedPermissions as $resource => $perms)
                                @php
                                    $meta = $resourceTranslations[$resource] ?? ['label' => ucfirst($resource), 'icon' => 'ri-key-2-line', 'color' => 'primary'];
                                    $permNamesJs = json_encode($perms->pluck('name')->toArray());
                                @endphp
                                <a href="#section-{{ $resource }}"
                                   class="list-group-item list-group-item-action d-flex align-items-center gap-2 py-2 px-3 border-0 group-nav-link">
                                    <i class="{{ $meta['icon'] }} text-{{ $meta['color'] }} fs-15"></i>
                                    <span class="flex-grow-1 fs-12 text-body">{{ $meta['label'] }}</span>
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
                <div class="card border-0 shadow-sm rounded-3 mb-3">
                    <div class="card-body p-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <div>
                            <h5 class="mb-0 fw-bold fs-15 text-body">Permissions — <span class="text-primary">{{ $role->name }}</span></h5>
                            <small class="text-muted fs-12">
                                <span x-text="selected.length" class="fw-bold text-primary"></span> sur {{ $totalPermissions }} sélectionnées
                            </small>
                        </div>
                        <div class="d-flex gap-2 w-100 w-sm-auto">
                            <button type="button" class="btn btn-sm btn-soft-success flex-grow-1 flex-sm-grow-0" @click="selectAll()">
                                <i class="ri-check-double-line me-1"></i>Tout sélectionner
                            </button>
                            <button type="button" class="btn btn-sm btn-soft-danger flex-grow-1 flex-sm-grow-0" @click="deselectAll()">
                                <i class="ri-close-line me-1"></i>Tout décocher
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Permission groups --}}
                @foreach($groupedPermissions as $resource => $permissions)
                    @php
                        $meta = $resourceTranslations[$resource] ?? ['label' => ucfirst($resource), 'icon' => 'ri-key-2-line', 'color' => 'primary'];
                        $permNamesJs = json_encode($permissions->pluck('name')->toArray());
                    @endphp
                    <div class="card border-0 shadow-sm rounded-3 mb-3" id="section-{{ $resource }}">
                        {{-- Group Header --}}
                        <div class="card-header p-0 border-0">
                            <div class="d-flex align-items-center px-3 py-2 bg-light bg-opacity-50 border-bottom border-light-subtle rounded-top-3">
                                <div class="me-2">
                                    <input type="checkbox"
                                           class="form-check-input square-checkbox"
                                           :checked="groupAllSelected({{ $permNamesJs }})"
                                           @change="groupToggle({{ $permNamesJs }})"
                                           id="group-chk-{{ $resource }}"
                                           x-effect="$el.indeterminate = groupSomeSelected({{ $permNamesJs }})">
                                </div>
                                <i class="{{ $meta['icon'] }} text-{{ $meta['color'] }} fs-16 me-2"></i>
                                <label for="group-chk-{{ $resource }}"
                                       class="fw-bold fs-13 flex-grow-1 mb-0 cursor-pointer text-uppercase letter-spacing-1 text-body">
                                    {{ $meta['label'] }}
                                </label>
                                <span class="badge bg-{{ $meta['color'] }}-subtle text-{{ $meta['color'] }} rounded-pill fs-11">
                                    <span x-text="groupCount({{ $permNamesJs }})"></span>/{{ $permissions->count() }}
                                </span>
                            </div>
                        </div>

                        {{-- Permission items --}}
                        <div class="card-body p-3">
                            <div class="row g-2">
                                @foreach($permissions as $permission)
                                    <div class="col-12 col-sm-6 col-md-6 col-xl-4">
                                        <label for="perm-{{ $permission->id }}"
                                               class="perm-label d-flex align-items-center gap-2 p-2 px-3 rounded-3 border border-light-subtle cursor-pointer w-100 h-100"
                                               :class="isSelected('{{ $permission->name }}') ? 'perm-active' : 'perm-inactive'">
                                            <input type="checkbox"
                                                   id="perm-{{ $permission->id }}"
                                                   class="form-check-input square-checkbox flex-shrink-0"
                                                   :checked="isSelected('{{ $permission->name }}')"
                                                   @change="toggle('{{ $permission->name }}')">
                                            <div class="overflow-hidden">
                                                <div class="fs-12 fw-semibold lh-sm text-body">
                                                    {{ PermissionEnum::tryFrom($permission->name)?->label() ?? $permission->name }}
                                                </div>
                                                <small class="text-muted fs-10 d-block text-truncate">{{ $permission->name }}</small>
                                            </div>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach

                {{-- Bottom Actions --}}
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-body p-3 d-flex flex-column flex-sm-row justify-content-end gap-2">
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-light w-100 w-sm-auto">Annuler</a>
                        <button type="submit" class="btn btn-primary w-100 w-sm-auto px-4 shadow-none">
                            <i class="ri-save-line me-1"></i> Enregistrer les modifications
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </form>
    </div>
</div>

<style>
    .sticky-sidebar { position: sticky; top: 75px; }
    .letter-spacing-1 { letter-spacing: 1px; }
    .cursor-pointer { cursor: pointer; }

    /* Square checkboxes */
    .square-checkbox {
        width: 1.1rem;
        height: 1.1rem;
        border-radius: 3px !important;
        cursor: pointer;
        flex-shrink: 0;
    }

    /* Permission label cards */
    .perm-label { transition: border-color 0.15s, background-color 0.15s; min-height: 48px; }
    .perm-inactive { border-color: var(--vz-border-color) !important; }
    .perm-inactive:hover { border-color: var(--vz-primary) !important; background-color: var(--vz-primary-bg-subtle); }
    .perm-active { border-color: var(--vz-primary) !important; background-color: var(--vz-primary-bg-subtle); }

    /* Sidebar nav */
    .group-nav-link { transition: background 0.15s; }
    .group-nav-link:hover { background-color: var(--vz-primary-bg-subtle); }
    .group-nav-link.active { background-color: var(--vz-primary-bg-subtle); font-weight: 600; }
    [x-cloak] { display: none !important; }
</style>

@endsection
