@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Modifier le groupe « {{ $group->name }} »</h2>
        <a href="{{ route('admin.groups.show', $group) }}" class="btn btn-secondary">
            <i class="mdi mdi-arrow-left"></i> Retour
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.groups.update', $group) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Nom du groupe <span class="text-danger">*</span></label>
                        <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $group->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label for="category" class="form-label">Catégorie</label>
                        <input type="text" id="category" name="category" class="form-control @error('category') is-invalid @enderror"
                            value="{{ old('category', $group->category) }}">
                        @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-2">
                        <label for="color" class="form-label">Couleur</label>
                        <input type="color" id="color" name="color" class="form-control form-control-color w-100 @error('color') is-invalid @enderror"
                            value="{{ old('color', $group->color ?? '#3B7DD8') }}" title="Choisir la couleur du groupe">
                        @error('color')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror"
                        rows="3">{{ old('description', $group->description) }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <label for="leader_id" class="form-label">Chef de groupe</label>
                    <select id="leader_id" name="leader_id" class="form-select @error('leader_id') is-invalid @enderror">
                        <option value="">— Aucun chef désigné —</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('leader_id', $group->leader_id) == $user->id ? 'selected' : '' }}>
                                {{ $user->first_name }} {{ $user->name }}
                                @if($user->phone) ({{ $user->phone }}) @endif
                            </option>
                        @endforeach
                    </select>
                    <div class="form-text text-info">
                        <i class="mdi mdi-information"></i>
                        Changer le chef lui attribuera automatiquement le rôle "Chef de groupe".
                    </div>
                    @error('leader_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="mdi mdi-check"></i> Enregistrer les modifications
                    </button>
                    <a href="{{ route('admin.groups.show', $group) }}" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
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
        border-radius: 0.25rem;
        border: 1px solid #ced4da;
        padding: 0.47rem 0.75rem;
        font-size: 0.875rem;
    }
    .ts-dropdown {
        border-radius: 0.25rem;
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
        font-size: 0.875rem;
    }
</style>
@endpush

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
@endsection
