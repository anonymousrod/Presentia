@extends('layouts.app')

@section('title', 'Paramètres de la Plateforme')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Paramètres de la Plateforme</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Tableau de bord</a></li>
                    <li class="breadcrumb-item active">Paramètres de la Plateforme</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Gestion des Images et Logos</h4>
            </div>
            
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs nav-tabs-custom nav-success mb-3" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#logos" role="tab">
                                <i class="ri-image-line me-1 align-bottom"></i> Logos & Favicon
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#pdfs" role="tab">
                                <i class="ri-file-pdf-line me-1 align-bottom"></i> Logos PDF
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#defaults" role="tab">
                                <i class="ri-user-settings-line me-1 align-bottom"></i> Images par défaut (Profil)
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#sidebar" role="tab">
                                <i class="ri-layout-left-2-line me-1 align-bottom"></i> Fonds de Menu (Sidebar)
                            </a>
                        </li>
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content text-muted">
                        {{-- TAB: Logos & Favicon --}}
                        <div class="tab-pane active" id="logos" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Favicon</label>
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="me-3 bg-light p-2 rounded text-center" style="min-width: 80px;">
                                            <img src="{{ $appSettings->favicon_url }}" alt="Favicon" style="max-height: 40px; max-width: 100%;">
                                        </div>
                                        <input type="file" name="favicon" class="form-control" accept="image/*">
                                    </div>
                                    <small class="text-muted d-block">L'icône du site affichée dans l'onglet du navigateur (Favicon). Valeur par défaut : Icone J-EBER.png.</small>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Logo Réduit (Logo SM)</label>
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="me-3 bg-light p-2 rounded text-center" style="min-width: 80px;">
                                            <img src="{{ $appSettings->logo_sm_url }}" alt="Logo SM" style="max-height: 40px; max-width: 100%;">
                                        </div>
                                        <input type="file" name="logo_sm" class="form-control" accept="image/*">
                                    </div>
                                    <small class="text-muted d-block">Logo réduit (pour la sidebar réduite et le header mobile).</small>
                                </div>
                                
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Logo Principal (Pour Thème Sombre)</label>
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="me-3 bg-dark p-2 rounded text-center" style="min-width: 150px;">
                                            <img src="{{ $appSettings->logo_light_url }}" alt="Logo Thème Sombre" style="max-height: 40px; max-width: 100%;">
                                        </div>
                                        <input type="file" name="logo_light" class="form-control" accept="image/*">
                                    </div>
                                    <small class="text-muted d-block">Il s'agit du logo de couleur claire (texte blanc). Il est affiché quand l'application ou le menu est en <b>thème sombre</b>.</small>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Logo Principal (Pour Thème Clair)</label>
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="me-3 bg-light p-2 rounded text-center" style="min-width: 150px;">
                                            <img src="{{ $appSettings->logo_dark_url }}" alt="Logo Thème Clair" style="max-height: 40px; max-width: 100%;">
                                        </div>
                                        <input type="file" name="logo_dark" class="form-control" accept="image/*">
                                    </div>
                                    <small class="text-muted d-block">Il s'agit du logo de couleur sombre. Il est affiché quand l'application ou le menu est en <b>thème clair</b>.</small>
                                </div>
                            </div>
                        </div>

                        {{-- TAB: PDFs --}}
                        <div class="tab-pane" id="pdfs" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Logo UEEB (Exports PDF)</label>
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="me-3 bg-light p-2 rounded text-center" style="min-width: 100px;">
                                            <img src="{{ $appSettings->pdf_logo_1_url }}" alt="Logo PDF 1" style="max-height: 60px; max-width: 100%;">
                                        </div>
                                        <input type="file" name="pdf_logo_1" class="form-control" accept="image/*">
                                    </div>
                                    <small class="text-muted d-block">Logo UEEB utilisé dans les exports PDF (Présences, Utilisateurs).</small>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Logo Jeunesse Étoile Rouge (Exports PDF)</label>
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="me-3 bg-light p-2 rounded text-center" style="min-width: 100px;">
                                            <img src="{{ $appSettings->pdf_logo_2_url }}" alt="Logo PDF 2" style="max-height: 60px; max-width: 100%;">
                                        </div>
                                        <input type="file" name="pdf_logo_2" class="form-control" accept="image/*">
                                    </div>
                                    <small class="text-muted d-block">Logo Jeunesse Étoile Rouge utilisé dans les exports PDF.</small>
                                </div>
                            </div>
                        </div>

                        {{-- TAB: Profil par défaut --}}
                        <div class="tab-pane" id="defaults" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Avatar par défaut</label>
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="me-3 bg-light p-2 rounded text-center" style="min-width: 100px;">
                                            <img src="{{ $appSettings->default_avatar_url }}" class="rounded-circle" alt="Avatar par défaut" style="width: 60px; height: 60px; object-fit: cover;">
                                        </div>
                                        <input type="file" name="default_avatar" class="form-control" accept="image/*">
                                    </div>
                                    <small class="text-muted d-block">Image de profil par défaut pour un nouvel utilisateur (au lieu de avatar-1.jpg).</small>
                                </div>


                            </div>
                        </div>

                        {{-- TAB: Sidebar Backgrounds --}}
                        <div class="tab-pane" id="sidebar" role="tabpanel">
                            <div class="alert alert-info">
                                Ces images sont proposées en tant que fond pour le menu latéral (Sidebar) lorsque l'utilisateur modifie l'apparence depuis la roue crantée en haut à droite.
                            </div>
                            <div class="row">
                                @for($i=1; $i<=4; $i++)
                                    @php 
                                        $field = 'sidebar_bg_' . $i;
                                        $fieldUrl = 'sidebar_bg_' . $i . '_url';
                                    @endphp
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label">Image d'arrière-plan Sidebar {{ $i }}</label>
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="me-3 bg-light p-2 rounded text-center" style="width: 80px;">
                                                <img src="{{ $appSettings->$fieldUrl }}" alt="Sidebar BG {{ $i }}" class="img-fluid rounded" style="max-height: 100px;">
                                            </div>
                                            <input type="file" name="{{ $field }}" class="form-control" accept="image/*">
                                        </div>
                                        <small class="text-muted d-block">Image d'arrière-plan {{ $i }} proposée pour la sidebar dans la personnalisation du thème.</small>
                                    </div>
                                @endfor
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 text-end">
                        <button type="submit" class="btn btn-primary"><i class="ri-save-line me-1"></i> Enregistrer les modifications</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection
