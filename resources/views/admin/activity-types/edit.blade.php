@extends('layouts.app')

@push('css')
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
        padding: 0.65rem 2rem;
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
</style>
@endpush

@section('content')

<div class="row mb-3 pb-1 mt-4 px-4">
    <div class="col-12">
        <div class="d-flex align-items-lg-center flex-lg-row flex-column justify-content-between">
            <div class="flex-grow-1">
                <h4 class="fs-16 mb-1">Modifier le Type : {{ $activityType->name }}</h4>
                <p class="text-muted mb-0">Mettez à jour le type d'activité.</p>
            </div>
            <div class="mt-3 mt-lg-0">
                <a href="{{ route('admin.activity-types.index') }}" class="btn btn-soft-secondary d-flex align-items-center gap-1">
                    <i class="mdi mdi-arrow-left"></i> Retour
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid max-w-1200 px-4">
    <form action="{{ route('admin.activity-types.update', $activityType) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="row g-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 1rem;">
                    <div class="card-body p-4 p-md-5">
                        <div class="section-header">
                            <i class="mdi mdi-palette-swatch-outline"></i> Configuration du Type
                        </div>

                        <div class="row align-items-end g-4">
                            <!-- Input Nom -->
                            <div class="col-lg-7">
                                <label for="name" class="form-label fw-medium text-body">Nom du type <span class="text-danger">*</span></label>
                                <input type="text" class="form-control premium-input @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $activityType->name) }}" required style="height: 50px;">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Input Couleur -->
                            <div class="col-lg-2">
                                <label for="color" class="form-label fw-medium text-body">Couleur <span class="text-danger">*</span></label>
                                <input type="color" class="form-control form-control-color premium-input px-1 py-1 w-100 @error('color') is-invalid @enderror" id="color" name="color" value="{{ old('color', $activityType->color) }}" title="Choisir une couleur" required style="height: 50px;">
                                @error('color')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Bouton Enregistrer -->
                            <div class="col-lg-3">
                                <button type="submit" class="btn btn-primary btn-save w-100 d-flex align-items-center justify-content-center" style="height: 50px;">
                                    <i class="mdi mdi-check-all me-1"></i> Enregistrer
                                </button>
                            </div>
                        </div>
                        
                        <div class="row mt-2">
                            <div class="col-lg-7">
                                <span class="text-muted small">Le nom sera affiché partout dans l'application.</span>
                            </div>
                            <div class="col-lg-5">
                                <span class="text-muted small">La couleur servira de repère visuel (graphiques, calendrier).</span>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
