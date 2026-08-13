@extends('layouts.app')

@push('css')
<style>
    /* ── Section Headers ── */
    .section-header {
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 2px;
        color: var(--vz-primary);
        font-weight: 700;
        display: flex;
        align-items: center;
        margin-bottom: 1.5rem;
    }
    .section-header i { margin-right: 0.5rem; font-size: 1.1rem; }
    .section-header::after {
        content: '';
        flex: 1;
        height: 1px;
        background: rgba(var(--vz-primary-rgb), 0.12);
        margin-left: 1rem;
    }

    /* ── Premium Input ── */
    .premium-input {
        border-radius: 0.6rem;
        padding: 0.65rem 1rem;
        transition: all 0.25s ease;
        background-color: var(--vz-input-bg);
        border-color: var(--vz-input-border);
        color: var(--vz-body-color);
    }
    .premium-input:focus {
        border-color: var(--vz-primary);
        box-shadow: 0 0 0 0.2rem rgba(var(--vz-primary-rgb), 0.15);
    }

    /* ── Save Button ── */
    .btn-save {
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        letter-spacing: 0.4px;
        border-radius: 0.6rem;
        box-shadow: 0 4px 12px rgba(var(--vz-primary-rgb), 0.3);
        transition: all 0.25s ease;
    }
    .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(var(--vz-primary-rgb), 0.4);
    }

    /* ── Member Picker ── */
    .member-picker { position: relative; }
    .member-picker-trigger {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        padding: 0.6rem 0.85rem;
        border-radius: 0.6rem;
        border: 1px solid var(--vz-input-border);
        background: var(--vz-input-bg);
        cursor: pointer !important;
        transition: border-color 0.2s, box-shadow 0.2s;
        min-height: 44px;
        user-select: none;
    }
    .member-picker-trigger:hover {
        border-color: var(--vz-primary);
    }
    .member-picker-trigger.open {
        border-color: var(--vz-primary);
        box-shadow: 0 0 0 0.2rem rgba(var(--vz-primary-rgb), 0.15);
    }
    .member-picker-avatar {
        width: 30px; height: 30px;
        border-radius: 50%;
        background: rgba(var(--vz-primary-rgb), 0.1);
        color: var(--vz-primary);
        font-size: 0.7rem;
        font-weight: 700;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .member-picker-avatar.empty {
        background: var(--vz-light);
        color: var(--vz-secondary-color);
    }
    .member-picker-label {
        flex: 1;
        font-size: 0.875rem;
        font-weight: 500;
        color: var(--vz-body-color);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .member-picker-label.placeholder {
        color: var(--vz-secondary-color);
        font-weight: 400;
    }
    .member-picker-label small {
        display: block;
        font-size: 0.72rem;
        color: var(--vz-secondary-color);
        font-weight: 400;
        margin-top: 1px;
    }
    .member-picker-chevron {
        margin-left: auto;
        font-size: 1rem;
        color: var(--vz-secondary-color);
        transition: transform 0.2s;
    }
    .member-picker-trigger.open .member-picker-chevron { transform: rotate(180deg); }
    .member-picker-clear {
        width: 22px; height: 22px;
        border-radius: 50%;
        background: rgba(var(--vz-danger-rgb), 0.1);
        color: var(--vz-danger);
        display: flex; align-items: center; justify-content: center;
        font-size: 0.8rem;
        cursor: pointer;
        transition: background 0.15s;
        flex-shrink: 0;
    }
    .member-picker-clear:hover { background: rgba(var(--vz-danger-rgb), 0.2); }

    /* Dropdown */
    .member-picker-dropdown {
        position: absolute;
        top: calc(100% + 4px);
        left: 0; right: 0;
        background: var(--vz-card-bg, #fff);
        border: 1px solid var(--vz-border-color);
        border-radius: 0.65rem;
        box-shadow: 0 10px 40px rgba(0,0,0,0.12);
        z-index: 1050;
        overflow: hidden;
        display: none;
        animation: mpFadeIn 0.15s ease;
    }
    .member-picker-dropdown.show { display: block; }
    @keyframes mpFadeIn {
        from { opacity: 0; transform: translateY(-6px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .member-picker-search-wrap {
        padding: 0.6rem 0.7rem;
        border-bottom: 1px solid var(--vz-border-color);
    }
    .member-picker-search {
        width: 100%;
        padding: 0.4rem 0.75rem 0.4rem 2rem;
        border: 1px solid var(--vz-input-border);
        border-radius: 0.45rem;
        background: var(--vz-input-bg);
        color: var(--vz-body-color);
        font-size: 0.84rem;
        outline: none;
        transition: border-color 0.2s;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%236c757d' stroke-width='2'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cpath d='m21 21-4.35-4.35'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: 0.55rem center;
    }
    .member-picker-search:focus { border-color: var(--vz-primary); }
    .member-picker-list { max-height: 210px; overflow-y: auto; padding: 0.3rem 0; }
    .member-picker-item {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        padding: 0.55rem 0.85rem;
        cursor: pointer;
        transition: background 0.12s;
    }
    .member-picker-item:hover { background: rgba(var(--vz-primary-rgb), 0.06); }
    .member-picker-item.selected {
        background: rgba(var(--vz-primary-rgb), 0.1);
    }
    .member-picker-item.selected .mpi-name { color: var(--vz-primary); font-weight: 600; }
    .member-picker-item .mp-avatar {
        width: 32px; height: 32px;
        border-radius: 50%;
        font-size: 0.72rem;
        font-weight: 700;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .mpi-name  { font-size: 0.84rem; font-weight: 500; color: var(--vz-body-color); line-height: 1.2; }
    .mpi-phone { font-size: 0.73rem; color: var(--vz-secondary-color); }
    .member-picker-none {
        padding: 1rem;
        text-align: center;
        font-size: 0.84rem;
        color: var(--vz-secondary-color);
        display: none;
    }
    .member-picker-none.show { display: block; }

    /* Dark mode */
    [data-bs-theme="dark"] .member-picker-dropdown {
        background: var(--vz-card-bg, #212529);
        border-color: var(--vz-border-color, #40464c);
    }
    [data-bs-theme="dark"] .member-picker-trigger {
        background: var(--vz-input-bg);
        border-color: var(--vz-input-border);
    }
</style>
@endpush

@section('content')

<div class="row mb-3 pb-1 mt-4 px-4">
    <div class="col-12">
        <div class="d-flex align-items-lg-center flex-lg-row flex-column justify-content-between">
            <div class="flex-grow-1">
                <h4 class="fs-16 mb-1">Nouveau groupe</h4>
                <p class="text-muted mb-0">Renseignez les informations du nouveau groupe ci-dessous.</p>
            </div>
            <div class="mt-3 mt-lg-0">
                <a href="{{ route('admin.groups.index') }}" class="btn btn-soft-secondary d-flex align-items-center gap-1">
                    <i class="mdi mdi-arrow-left"></i> Retour aux groupes
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid max-w-1200 px-4">
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.groups.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="row g-4">
            <!-- Left Column: Main Information -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 1rem;">
                    <div class="card-body p-4 p-md-5">
                        <div class="section-header">
                            <i class="mdi mdi-information-outline"></i> Informations principales
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="name" class="form-label fw-medium text-body">Nom du groupe <span class="text-danger">*</span></label>
                                <input type="text" id="name" name="name" class="form-control premium-input @error('name') is-invalid @enderror"
                                    value="{{ old('name') }}" placeholder="Ex: Groupe Jeunesse" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="category" class="form-label fw-medium text-body">Catégorie</label>
                                <input type="text" id="category" name="category" class="form-control premium-input @error('category') is-invalid @enderror"
                                    value="{{ old('category') }}" placeholder="Ex : Louvetisme...">
                                @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label fw-medium text-body">Description</label>
                            <textarea id="description" name="description" class="form-control premium-input @error('description') is-invalid @enderror"
                                rows="5" placeholder="Description du groupe">{{ old('description') }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-4">
                            <label for="image" class="form-label fw-medium text-body">Affiche / Image du groupe <span class="text-muted">(Optionnel)</span></label>
                            <input type="file" id="image" name="image" class="form-control premium-input @error('image') is-invalid @enderror" accept="image/*">
                            @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Settings & Submit -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 1rem; position: sticky; top: 100px;">
                    <div class="card-body p-4">
                        <div class="section-header">
                            <i class="mdi mdi-cog-outline"></i> Configuration
                        </div>

                        <div class="mb-4">
                            <label for="color" class="form-label fw-medium text-body">Couleur <span class="text-danger">*</span></label>
                            <input type="color" id="color" name="color" class="form-control form-control-color w-100 premium-input px-1 py-1 @error('color') is-invalid @enderror"
                                value="{{ old('color', '#3B7DD8') }}" title="Choisir la couleur" style="height: 50px;">
                            @error('color')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <hr class="border-dashed my-4">

                        {{-- ── Chef de groupe ── --}}
                        <div class="mb-4">
                            <label class="form-label fw-medium text-body d-flex align-items-center gap-1 mb-2">
                                <i class="mdi mdi-crown-outline text-warning"></i> Chef de groupe
                            </label>
                            <div class="member-picker" id="picker-leader">
                                <input type="hidden" name="leader_id" id="leader_id_hidden"
                                    value="{{ old('leader_id') }}">
                                <div class="member-picker-trigger" id="trigger-leader" tabindex="0">
                                    <div class="member-picker-avatar empty" id="avatar-leader">
                                        <i class="mdi mdi-account-outline"></i>
                                    </div>
                                    <div class="member-picker-label placeholder" id="label-leader">— Aucun chef désigné —</div>
                                    <span class="member-picker-clear d-none" id="clear-leader" title="Effacer">
                                        <i class="mdi mdi-close"></i>
                                    </span>
                                    <i class="mdi mdi-chevron-down member-picker-chevron"></i>
                                </div>
                                <div class="member-picker-dropdown" id="dropdown-leader">
                                    <div class="member-picker-search-wrap">
                                        <input type="text" class="member-picker-search" id="search-leader" placeholder="Rechercher un membre..." autocomplete="off">
                                    </div>
                                    <div class="member-picker-list" id="list-leader">
                                        @foreach($users as $user)
                                            @php
                                                $initials = mb_strtoupper(mb_substr($user->first_name, 0, 1) . mb_substr($user->name, 0, 1));
                                                $colors = ['#4f46e5','#0891b2','#059669','#d97706','#dc2626','#7c3aed'];
                                                $color  = $colors[abs(crc32($user->name)) % count($colors)];
                                            @endphp
                                            <div class="member-picker-item"
                                                data-id="{{ $user->id }}"
                                                data-name="{{ $user->first_name }} {{ $user->name }}"
                                                data-phone="{{ $user->phone ?? '' }}"
                                                data-initials="{{ $initials }}"
                                                data-color="{{ $color }}">
                                                <div class="mp-avatar" style="background:{{ $color }}20; color:{{ $color }};">
                                                    {{ $initials }}
                                                </div>
                                                <div>
                                                    <div class="mpi-name">{{ $user->first_name }} {{ $user->name }}</div>
                                                    @if($user->phone)
                                                        <div class="mpi-phone"><i class="mdi mdi-phone-outline"></i> {{ $user->phone }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="member-picker-none" id="none-leader">Aucun résultat</div>
                                </div>
                            </div>
                            <div class="form-text mt-2 text-muted" style="font-size:0.78rem;">
                                <i class="mdi mdi-information-outline"></i>
                                Attribue automatiquement le rôle "Chef de groupe".
                            </div>
                            @error('leader_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>

                        {{-- ── Chargé de collecte ── --}}
                        <div class="mb-4">
                            <label class="form-label fw-medium text-body d-flex align-items-center gap-1 mb-2">
                                <i class="mdi mdi-hand-coin-outline text-success"></i> Chargé de collecte
                            </label>
                            <div class="member-picker" id="picker-collector">
                                <input type="hidden" name="collector_id" id="collector_id_hidden"
                                    value="{{ old('collector_id') }}">
                                <div class="member-picker-trigger" id="trigger-collector" tabindex="0">
                                    <div class="member-picker-avatar empty" id="avatar-collector">
                                        <i class="mdi mdi-account-outline"></i>
                                    </div>
                                    <div class="member-picker-label placeholder" id="label-collector">— Aucun chargé désigné —</div>
                                    <span class="member-picker-clear d-none" id="clear-collector" title="Effacer">
                                        <i class="mdi mdi-close"></i>
                                    </span>
                                    <i class="mdi mdi-chevron-down member-picker-chevron"></i>
                                </div>
                                <div class="member-picker-dropdown" id="dropdown-collector">
                                    <div class="member-picker-search-wrap">
                                        <input type="text" class="member-picker-search" id="search-collector" placeholder="Rechercher un membre..." autocomplete="off">
                                    </div>
                                    <div class="member-picker-list" id="list-collector">
                                        @foreach($users as $user)
                                            @php
                                                $initials = mb_strtoupper(mb_substr($user->first_name, 0, 1) . mb_substr($user->name, 0, 1));
                                                $colors = ['#4f46e5','#0891b2','#059669','#d97706','#dc2626','#7c3aed'];
                                                $color  = $colors[abs(crc32($user->name)) % count($colors)];
                                            @endphp
                                            <div class="member-picker-item"
                                                data-id="{{ $user->id }}"
                                                data-name="{{ $user->first_name }} {{ $user->name }}"
                                                data-phone="{{ $user->phone ?? '' }}"
                                                data-initials="{{ $initials }}"
                                                data-color="{{ $color }}">
                                                <div class="mp-avatar" style="background:{{ $color }}20; color:{{ $color }};">
                                                    {{ $initials }}
                                                </div>
                                                <div>
                                                    <div class="mpi-name">{{ $user->first_name }} {{ $user->name }}</div>
                                                    @if($user->phone)
                                                        <div class="mpi-phone"><i class="mdi mdi-phone-outline"></i> {{ $user->phone }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="member-picker-none" id="none-collector">Aucun résultat</div>
                                </div>
                            </div>
                            <div class="form-text mt-2 text-muted" style="font-size:0.78rem;">
                                <i class="mdi mdi-information-outline"></i>
                                Attribue automatiquement le rôle "Chargé de collecte".
                            </div>
                            @error('collector_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>

                        <div class="d-grid mt-4 pt-2">
                            <button type="submit" class="btn btn-primary btn-save">
                                <i class="mdi mdi-check-all me-1"></i> Créer le groupe
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
(function() {
    function MemberPicker(id, initialValue) {
        const hidden    = document.getElementById(id + '_id_hidden');
        const trigger   = document.getElementById('trigger-' + id);
        const dropdown  = document.getElementById('dropdown-' + id);
        const avatarEl  = document.getElementById('avatar-' + id);
        const labelEl   = document.getElementById('label-' + id);
        const clearBtn  = document.getElementById('clear-' + id);
        const searchEl  = document.getElementById('search-' + id);
        const listEl    = document.getElementById('list-' + id);
        const noneEl    = document.getElementById('none-' + id);
        const items     = Array.from(listEl.querySelectorAll('.member-picker-item'));

        let selected = null;

        function select(item) {
            selected = item;
            hidden.value  = item ? item.dataset.id : '';
            const color   = item ? item.dataset.color : null;
            const initials = item ? item.dataset.initials : null;
            const name     = item ? item.dataset.name : null;
            const phone    = item ? item.dataset.phone : null;

            if (item) {
                avatarEl.className = 'member-picker-avatar';
                avatarEl.innerHTML = initials;
                avatarEl.style.background = color + '22';
                avatarEl.style.color = color;
                labelEl.className = 'member-picker-label';
                labelEl.innerHTML = name + (phone ? '<small><i class="mdi mdi-phone-outline"></i> ' + phone + '</small>' : '');
                clearBtn.classList.remove('d-none');
            } else {
                avatarEl.className = 'member-picker-avatar empty';
                avatarEl.innerHTML = '<i class="mdi mdi-account-outline"></i>';
                avatarEl.style.background = '';
                avatarEl.style.color = '';
                labelEl.className = 'member-picker-label placeholder';
                labelEl.textContent = id === 'leader' ? '— Aucun chef désigné —' : '— Aucun chargé désigné —';
                clearBtn.classList.add('d-none');
            }

            items.forEach(i => i.classList.toggle('selected', i === item));
        }

        function open() {
            dropdown.classList.add('show');
            trigger.classList.add('open');
            searchEl.value = '';
            filterItems('');
            searchEl.focus();
        }

        function close() {
            dropdown.classList.remove('show');
            trigger.classList.remove('open');
        }

        function filterItems(q) {
            let visible = 0;
            items.forEach(item => {
                const match = item.dataset.name.toLowerCase().includes(q.toLowerCase())
                           || item.dataset.phone.includes(q);
                item.style.display = match ? '' : 'none';
                if (match) visible++;
            });
            noneEl.classList.toggle('show', visible === 0);
        }

        // init pre-selected
        if (initialValue) {
            const preItem = items.find(i => i.dataset.id == initialValue);
            if (preItem) select(preItem);
        }

        trigger.addEventListener('click', () => dropdown.classList.contains('show') ? close() : open());
        trigger.addEventListener('keydown', e => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); open(); } });
        clearBtn.addEventListener('click', e => { e.stopPropagation(); select(null); });
        searchEl.addEventListener('input', e => filterItems(e.target.value));
        items.forEach(item => {
            item.addEventListener('click', () => { select(item); close(); });
        });
        document.addEventListener('click', e => {
            if (!trigger.closest('.member-picker').contains(e.target)) close();
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        MemberPicker('leader',    '{{ old('leader_id') }}');
        MemberPicker('collector', '{{ old('collector_id') }}');
    });
})();
</script>
@endpush
