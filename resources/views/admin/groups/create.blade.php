@extends('layouts.app')

@push('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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

    /* ════════════════════════════════
       SELECT2  —  Professional Style
    ════════════════════════════════ */

    .select2-container { width: 100% !important; }

    .select2-container--default .select2-selection--single {
        display: flex;
        align-items: center;
        height: 42px;
        padding: 0 0.9rem;
        border-radius: 0.6rem;
        border: 1px solid var(--vz-input-border);
        background-color: var(--vz-input-bg);
        color: var(--vz-body-color);
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .select2-container--default.select2-container--focus .select2-selection--single,
    .select2-container--default.select2-container--open .select2-selection--single {
        border-color: var(--vz-primary);
        box-shadow: 0 0 0 0.2rem rgba(var(--vz-primary-rgb), 0.15);
        outline: none;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: var(--vz-body-color);
        line-height: 1;
        padding: 0;
        flex: 1;
    }
    .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: var(--vz-secondary-color);
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        position: static;
        display: flex;
        align-items: center;
        margin-left: 0.5rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow b {
        border-top-color: var(--vz-secondary-color);
    }
    /* Clear button */
    .select2-container--default .select2-selection--single .select2-selection__clear {
        font-size: 1.2rem;
        color: var(--vz-danger);
        margin-right: 0.5rem;
    }

    /* Dropdown panel */
    .select2-dropdown {
        border: 1px solid var(--vz-border-color);
        border-radius: 0.6rem;
        box-shadow: 0 8px 30px rgba(0,0,0,0.12);
        background-color: var(--vz-choices-bg);
        overflow: hidden;
        z-index: 9999;
    }
    .select2-container--open .select2-dropdown--below { border-top: none; border-radius: 0 0 0.6rem 0.6rem; }
    .select2-container--open .select2-dropdown--above { border-bottom: none; border-radius: 0.6rem 0.6rem 0 0; }

    /* Search input */
    .select2-search--dropdown {
        padding: 0.6rem 0.75rem;
        background-color: var(--vz-choices-bg);
        border-bottom: 1px solid var(--vz-border-color);
    }
    .select2-search--dropdown .select2-search__field {
        border-radius: 0.5rem;
        border: 1px solid var(--vz-input-border);
        padding: 0.45rem 0.75rem;
        width: 100%;
        background-color: var(--vz-input-bg);
        color: var(--vz-body-color);
        font-size: 0.875rem;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .select2-search--dropdown .select2-search__field:focus {
        outline: none;
        border-color: var(--vz-primary);
        box-shadow: 0 0 0 0.15rem rgba(var(--vz-primary-rgb), 0.15);
    }

    /* Results */
    .select2-results__options { max-height: 220px; overflow-y: auto; padding: 0.25rem 0; }
    .select2-results__option {
        padding: 0.55rem 1rem;
        font-size: 0.875rem;
        color: var(--vz-body-color);
        transition: background 0.15s;
    }
    .select2-results__option--highlighted[aria-selected] {
        background-color: rgba(var(--vz-primary-rgb), 0.1);
        color: var(--vz-primary);
    }
    .select2-results__option[aria-selected="true"] {
        background-color: rgba(var(--vz-primary-rgb), 0.15);
        color: var(--vz-primary);
        font-weight: 600;
    }

    /* Custom user row inside option */
    .s2-user-row { display: flex; align-items: center; gap: 0.6rem; }
    .s2-avatar {
        width: 30px; height: 30px;
        border-radius: 50%;
        background: rgba(var(--vz-primary-rgb), 0.12);
        color: var(--vz-primary);
        display: flex; align-items: center; justify-content: center;
        font-size: 0.8rem; font-weight: 700; flex-shrink: 0;
    }
    .s2-name  { font-size: 0.875rem; font-weight: 500; line-height: 1.2; }
    .s2-phone { font-size: 0.75rem; color: var(--vz-secondary-color); }
    
    /* Dark Mode Overrides (in case variables are tricky) */
    [data-bs-theme="dark"] .premium-input,
    [data-bs-theme="dark"] .select2-container--default .select2-selection--single,
    [data-bs-theme="dark"] .select2-dropdown,
    [data-bs-theme="dark"] .select2-search--dropdown,
    [data-bs-theme="dark"] .select2-search--dropdown .select2-search__field {
        background-color: var(--vz-choices-bg, #212529);
        border-color: var(--vz-border-color, #40464c);
        color: var(--vz-body-color, #ced4da);
    }
    [data-bs-theme="dark"] .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: var(--vz-body-color, #ced4da);
    }
    [data-bs-theme="dark"] .select2-results__option {
        color: var(--vz-body-color, #ced4da);
    }
    [data-bs-theme="dark"] .select2-results__option--highlighted[aria-selected],
    [data-bs-theme="dark"] .select2-results__option[aria-selected="true"] {
        background-color: rgba(var(--vz-primary-rgb), 0.2);
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
                            <label for="leader_id" class="form-label fw-medium text-body d-flex align-items-center gap-2">
                                <i class="mdi mdi-account-star-outline text-primary fs-16"></i> Chef de groupe
                            </label>
                            <select id="leader_id" name="leader_id" class="select2-person @error('leader_id') is-invalid @enderror">
                                <option value=""></option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}"
                                        data-phone="{{ $user->phone }}"
                                        {{ old('leader_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->first_name }} {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text text-info mt-2" style="font-size: 0.8rem;">
                                <i class="mdi mdi-information"></i>
                                Attribue automatiquement le rôle "Chef de groupe".
                            </div>
                            @error('leader_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
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
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
(function($) {
    function initials(name) {
        return name.trim().split(/\s+/).map(w => w[0]).join('').substring(0, 2).toUpperCase();
    }
    function formatOption(option) {
        if (!option.id) return $('<span class="text-muted">— Aucun désigné —</span>');
        var phone = $(option.element).data('phone') || '';
        return $(
            '<div class="s2-user-row">' +
              '<div class="s2-avatar">' + initials(option.text) + '</div>' +
              '<div>' +
                '<div class="s2-name">' + option.text + '</div>' +
                (phone ? '<div class="s2-phone"><i class="mdi mdi-phone-outline"></i> ' + phone + '</div>' : '') +
              '</div>' +
            '</div>'
        );
    }
    $('.select2-person').select2({
        width: '100%',
        allowClear: true,
        placeholder: '— Aucun désigné —',
        templateResult:    formatOption,
        templateSelection: function(o) { return o.id ? o.text : o.text; },
        language: {
            noResults: function() { return 'Aucun résultat trouvé'; },
            searching: function() { return 'Recherche en cours…'; }
        }
    });
})(jQuery);
</script>
@endpush
