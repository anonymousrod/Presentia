@extends('layouts.app')

@section('title', 'Gestion de la Galerie')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Gestion de la Galerie</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Tableau de bord</a></li>
                    <li class="breadcrumb-item active">Galerie</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Formulaire d'ajout -->
    <div class="col-lg-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Ajouter une nouvelle photo</h5>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                <form action="{{ route('admin.galleries.store') }}" method="POST" enctype="multipart/form-data" class="row g-3 align-items-end">
                    @csrf
                    <div class="col-md-5">
                        <label class="form-label">Titre / Légende (Optionnel)</label>
                        <input type="text" name="title" class="form-control" placeholder="Ex: Culte des jeunes - Mai 2026">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Image (Max 2MB)</label>
                        <input type="file" name="image" class="form-control" accept="image/*" required>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100"><i class="ri-upload-2-line me-1"></i> Uploader</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Grille des photos -->
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Photos de la Galerie</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @forelse($galleries as $gallery)
                        <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                            <div class="card border shadow-none h-100 mb-0">
                                <img src="{{ asset('storage/' . $gallery->image_path) }}" class="card-img-top" alt="{{ $gallery->title }}" style="height: 200px; object-fit: cover;">
                                <div class="card-body p-3 text-center">
                                    <h6 class="text-truncate mb-2" title="{{ $gallery->title }}">{{ $gallery->title ?? 'Sans titre' }}</h6>
                                    
                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <!-- Formulaire Activer/Désactiver -->
                                        <form action="{{ route('admin.galleries.toggle', $gallery->id) }}" method="POST">
                                            @csrf
                                            <div class="form-check form-switch form-switch-success" title="Afficher sur l'accueil">
                                                <input class="form-check-input" type="checkbox" role="switch" onchange="this.form.submit()" {{ $gallery->is_active ? 'checked' : '' }}>
                                                <label class="form-check-label"><small>{{ $gallery->is_active ? 'Visible' : 'Cachée' }}</small></label>
                                            </div>
                                        </form>

                                        <!-- Formulaire de suppression -->
                                        <form action="{{ route('admin.galleries.destroy', $gallery->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette image ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-soft-danger"><i class="ri-delete-bin-line"></i></button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-4">
                            <i class="ri-image-line fs-50 text-muted"></i>
                            <h5 class="mt-2">Aucune photo dans la galerie</h5>
                            <p class="text-muted">Utilisez le formulaire ci-dessus pour ajouter des souvenirs.</p>
                        </div>
                    @endforelse
                </div>
                
                <div class="mt-4">
                    {{ $galleries->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
