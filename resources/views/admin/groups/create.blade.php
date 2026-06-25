@extends('layouts.app')

@push('css')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<style>
    /* Thematic Section Headers */
    .section-header {
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        color: var(--vz-primary);
        font-weight: 700;
        display: flex;
        align-items: center;
        margin-bottom: 1.5rem;
    }
    .section-header i {
        margin-right: 0.5rem;
        font-size: 1.2rem;
    }
    .section-header::after {
        content: '';
        flex: 1;
        height: 1px;
        background: rgba(var(--vz-primary-rgb), 0.15);
        margin-left: 1rem;
    }

    /* Custom Inputs */
    .premium-input {
        border-radius: 0.5rem;
        padding: 0.65rem 1rem;
        transition: all 0.3s ease;
    }
    .premium-input:focus {
        border-color: var(--vz-primary);
        box-shadow: 0 0 0 0.25rem rgba(var(--vz-primary-rgb), 0.15);
    }
    
    /* Save Button */
    .btn-save {
        padding: 0.8rem 2rem;
        font-weight: 600;
        letter-spacing: 0.5px;
        border-radius: 0.5rem;
        box-shadow: 0 4px 10px rgba(var(--vz-primary-rgb), 0.3);
        transition: all 0.3s;
    }
    .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(var(--vz-primary-rgb), 0.4);
    }

    .ts-wrapper.form-select {
        padding: 0;
        border: none;
        height: auto;
    }
    .ts-control {
        border-radius: 0.5rem;
        border: 1px solid var(--vz-border-color);
        padding: 0.65rem 1rem;
        transition: all 0.3s ease;
    }
    .ts-control.focus {
        border-color: var(--vz-primary);
        box-shadow: 0 0 0 0.25rem rgba(var(--vz-primary-rgb), 0.15);
    }
    .ts-dropdown {
        border-radius: 0.5rem;
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
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

    <form action="{{ route('admin.groups.store') }}" method="POST">
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

                        <div class="mb-4">
                            <label for="leader_id" class="form-label fw-medium text-body">Chef de groupe</label>
                            <select id="leader_id" name="leader_id" class="form-select @error('leader_id') is-invalid @enderror">
                                <option value="">— Aucun chef désigné —</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('leader_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->first_name }} {{ $user->name }}
                                        @if($user->phone) ({{ $user->phone }}) @endif
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text text-info mt-2" style="font-size: 0.8rem;">
                                <i class="mdi mdi-information"></i>
                                Attribue automatiquement le rôle "Chef de groupe".
                            </div>
                            @error('leader_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        new TomSelect('#leader_id', {
            create: false,
            placeholder: '— Aucun chef désigné —',
            allowEmptyOption: true
        });
    });
</script>
@endpush
