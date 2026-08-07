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
                        <label class="form-label">Images</label>
                        <input type="file" name="images[]" class="form-control" accept="image/*" multiple required>
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
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll(this)" style="cursor: pointer; transform: scale(1.2);">
                        <label class="form-check-label ms-1 fw-bold" for="selectAllCheckbox">Tout sélectionner</label>
                    </div>
                    <h5 class="card-title mb-0 border-start ps-3">Photos de la Galerie</h5>
                </div>
                <button type="button" class="btn btn-sm btn-danger d-none" id="bulkDeleteBtn" onclick="openBulkDeleteModal()">
                    <i class="ri-delete-bin-line align-middle"></i> Supprimer la sélection (<span id="selectedCount">0</span>)
                </button>
            </div>
            <div class="card-body">
                <form id="bulkDeleteForm" action="{{ route('admin.galleries.bulk-destroy') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="row">
                    @forelse($galleries as $gallery)
                        <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                            <div class="card border shadow-none h-100 mb-0 position-relative">
                                <div class="position-absolute top-0 start-0 p-2 z-1">
                                    <input type="checkbox" name="gallery_ids[]" value="{{ $gallery->id }}" class="form-check-input gallery-checkbox fs-5" onchange="toggleBulkDeleteBtn()" style="cursor: pointer; box-shadow: 0 0 10px rgba(0,0,0,0.5);">
                                </div>
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

                                        <!-- Bouton de suppression individuelle -->
                                        <button type="button" class="btn btn-sm btn-soft-danger" onclick="openDeleteModal({{ $gallery->id }})">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
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
                </form>
                
                <div class="mt-4">
                    {{ $galleries->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<!-- Modal de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmation de suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Êtes-vous sûr de vouloir supprimer la/les image(s) sélectionnée(s) ? Cette action est irréversible.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-danger" onclick="document.getElementById('bulkDeleteForm').submit();">Oui, supprimer</button>
            </div>
        </div>
    </div>
</div>

<script>
function toggleSelectAll(source) {
    let checkboxes = document.querySelectorAll('.gallery-checkbox');
    checkboxes.forEach(cb => cb.checked = source.checked);
    toggleBulkDeleteBtn();
}

function toggleBulkDeleteBtn() {
    let checkboxes = document.querySelectorAll('.gallery-checkbox');
    let count = document.querySelectorAll('.gallery-checkbox:checked').length;
    let btn = document.getElementById('bulkDeleteBtn');
    document.getElementById('selectedCount').innerText = count;
    
    // Update Select All checkbox state
    let selectAll = document.getElementById('selectAllCheckbox');
    if (selectAll) {
        selectAll.checked = (checkboxes.length > 0 && count === checkboxes.length);
        selectAll.indeterminate = (count > 0 && count < checkboxes.length);
    }
    
    if(count > 0) {
        btn.classList.remove('d-none');
    } else {
        btn.classList.add('d-none');
    }
}

function openDeleteModal(galleryId) {
    // Uncheck all first
    document.querySelectorAll('.gallery-checkbox').forEach(cb => cb.checked = false);
    // Check the selected one
    let cb = document.querySelector('input[value="'+galleryId+'"]');
    if(cb) cb.checked = true;
    toggleBulkDeleteBtn();
    
    // Show modal
    var myModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    myModal.show();
}

function openBulkDeleteModal() {
    var myModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    myModal.show();
}
</script>
@endsection
