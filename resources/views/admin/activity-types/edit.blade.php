@extends('layouts.app')

@section('title', 'Modifier le Type d\'Activité')

@section('content')
<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Modifier le Type d'Activité</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.activity-types.index') }}">Types d'Activités</a></li>
                    <li class="breadcrumb-item active">Edition</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<!-- end page title -->

<div class="row justify-content-center">
    <div class="col-xxl-6 col-lg-8">
        <div class="card">
            <div class="card-header align-items-center d-flex border-bottom-dashed">
                <h4 class="card-title mb-0 flex-grow-1">Modifier: {{ $activityType->name }}</h4>
                <div class="flex-shrink-0">
                    <a href="{{ route('admin.activity-types.index') }}" class="btn btn-soft-secondary btn-sm">
                        <i class="ri-arrow-left-line align-bottom me-1"></i> Retour
                    </a>
                </div>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.activity-types.update', $activityType) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Nom du type <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $activityType->name) }}" placeholder="Ex: École de Dimanche, Réunion de Jeunes..." required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="color" class="form-label fw-semibold">Couleur d'affichage <span class="text-danger">*</span></label>
                        <div class="d-flex align-items-center gap-2">
                            <input type="color" class="form-control form-control-color border @error('color') is-invalid @enderror" id="color" name="color" value="{{ old('color', $activityType->color) }}" title="Choisir une couleur" required style="width: 60px; height: 38px; padding: 2px;">
                            <span class="text-muted small">Cette couleur sera utilisée pour identifier le type d'activité dans les graphiques et calendriers.</span>
                        </div>
                        @error('color')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2 border-top pt-3 mt-4">
                        <a href="{{ route('admin.activity-types.index') }}" class="btn btn-light">Annuler</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-save-3-line align-bottom me-1"></i> Enregistrer les modifications
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
