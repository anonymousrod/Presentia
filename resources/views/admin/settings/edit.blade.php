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

                    @php
                        $inSupportMode = session()->has('tenant_church_id');
                        $isSuperAdmin = (auth()->user()?->isSuperAdmin() ?? false) && !$inSupportMode;
                    @endphp

                    @if($inSupportMode)
                        <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center mb-4">
                            <i class="ri-shield-flash-line fs-24 me-3 text-warning"></i>
                            <div>
                                <h6 class="alert-heading fw-bold mb-1">Mode Support Actif sur « {{ $church->name ?? 'cette église' }} »</h6>
                                <p class="mb-0 fs-13">Vous personnalisez actuellement les paramètres et les documents de cette paroisse locale. Les logos globaux de la plateforme MeVoici sont masqués en mode support.</p>
                            </div>
                        </div>
                    @endif

                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs nav-tabs-custom nav-success mb-3" role="tablist">
                        @if($isSuperAdmin)
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#logos" role="tab">
                                    <i class="ri-shield-user-line me-1 align-bottom"></i> Logos Plateforme MeVoici
                                </a>
                            </li>
                        @endif
                        <li class="nav-item">
                            <a class="nav-link {{ !$isSuperAdmin ? 'active' : '' }}" data-bs-toggle="tab" href="#pdfs" role="tab">
                                <i class="ri-file-pdf-line me-1 align-bottom"></i> Logos Documents & PDF
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
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#home" role="tab">
                                <i class="ri-home-4-line me-1 align-bottom"></i> Page d'Accueil
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#socials" role="tab">
                                <i class="ri-share-line me-1 align-bottom"></i> Réseaux Sociaux
                            </a>
                        </li>
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content text-muted">
                        @if($isSuperAdmin)
                            {{-- TAB: Logos Plateforme MeVoici (Super Admin Only) --}}
                            <div class="tab-pane active" id="logos" role="tabpanel">
                                <div class="alert alert-info border-0 shadow-sm d-flex align-items-center mb-4">
                                    <i class="ri-information-line fs-20 me-2 text-info"></i>
                                    <div>
                                        <strong>Zone réservée au Super Administrateur :</strong> Ces visuels définissent l'identité globale de la plateforme MeVoici (Favicon, logos officiels sur fonds sombre et clair, logo réduit).
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label fw-bold">Favicon MeVoici</label>
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="me-3 bg-light p-2 rounded text-center" style="min-width: 80px;">
                                                <img src="{{ $appSettings->favicon_url }}" alt="Favicon" style="max-height: 40px; max-width: 100%;">
                                            </div>
                                            <input type="file" name="favicon" class="form-control" accept="image/*">
                                        </div>
                                        <small class="text-muted d-block">L'icône de l'application affichée dans l'onglet du navigateur.</small>
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <label class="form-label fw-bold">Logo Réduit (SM)</label>
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="me-3 bg-light p-2 rounded text-center" style="min-width: 80px;">
                                                <img src="{{ $appSettings->logo_sm_url }}" alt="Logo SM" style="max-height: 40px; max-width: 100%;">
                                            </div>
                                            <input type="file" name="logo_sm" class="form-control" accept="image/*">
                                        </div>
                                        <small class="text-muted d-block">Logo carré ou réduit pour le menu rétracté et l'en-tête mobile.</small>
                                    </div>
                                    
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label fw-bold">Logo Principal (Pour Thème Sombre)</label>
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="me-3 bg-dark p-2 rounded text-center" style="min-width: 150px;">
                                                <img src="{{ $appSettings->logo_light_url }}" alt="Logo Thème Sombre" style="max-height: 40px; max-width: 100%;">
                                            </div>
                                            <input type="file" name="logo_light" class="form-control" accept="image/*">
                                        </div>
                                        <small class="text-muted d-block">Logo blanc/clair affiché sur les arrière-plans foncés et dans la barre de navigation d'accueil.</small>
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <label class="form-label fw-bold">Logo Principal (Pour Thème Clair)</label>
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="me-3 bg-light p-2 rounded text-center" style="min-width: 150px;">
                                                <img src="{{ $appSettings->logo_dark_url }}" alt="Logo Thème Clair" style="max-height: 40px; max-width: 100%;">
                                            </div>
                                            <input type="file" name="logo_dark" class="form-control" accept="image/*">
                                        </div>
                                        <small class="text-muted d-block">Logo sombre affiché quand l'application ou le menu est en thème clair.</small>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- TAB: Logos Documents & PDF --}}
                        <div class="tab-pane {{ !$isSuperAdmin ? 'active' : '' }}" id="pdfs" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-bold">Logo de l'Église locale (En-tête Gauche PDF)</label>
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="me-3 bg-light p-2 rounded text-center border" style="min-width: 100px;">
                                            @php
                                                $logo1Preview = $setting->pdf_logo_1 
                                                    ? asset('storage/' . $setting->pdf_logo_1) 
                                                    : ($church?->logo_url ?? $appSettings->logo_sm_url);
                                            @endphp
                                            <img src="{{ $logo1Preview }}" alt="Logo Église locale" style="max-height: 60px; max-width: 100%;">
                                        </div>
                                        <input type="file" name="pdf_logo_1" class="form-control" accept="image/*">
                                    </div>
                                    <small class="text-muted d-block">Logo de votre église locale imprimé en haut à gauche de toutes les fiches PDF (présences, cultes, cartes de membres). Par défaut : le logo de l'église.</small>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-bold">Logo MeVoici / Logo de la jeunesse (En-tête Droit PDF)</label>
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="me-3 bg-light p-2 rounded text-center border" style="min-width: 100px;">
                                            @php
                                                $logo2Preview = $setting->pdf_logo_2 
                                                    ? asset('storage/' . $setting->pdf_logo_2) 
                                                    : ($appSettings->logo_dark_url ?? asset('assets/images/logo-dark.png'));
                                            @endphp
                                            <img src="{{ $logo2Preview }}" alt="Logo MeVoici / Jeunesse" style="max-height: 60px; max-width: 100%;">
                                        </div>
                                        <input type="file" name="pdf_logo_2" class="form-control" accept="image/*">
                                    </div>
                                    <small class="text-muted d-block">Logo de la jeunesse ou de la plateforme MeVoici imprimé en haut à droite des exports PDF. Par défaut : logo officiel MeVoici (logo_dark).</small>
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

                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Image de fond (Connexion / Mot de passe oublié)</label>
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="me-3 bg-dark p-2 rounded text-center" style="min-width: 120px;">
                                            <img src="{{ $appSettings->auth_bg_url }}" alt="Auth BG" class="img-fluid rounded" style="max-height: 60px;">
                                        </div>
                                        <input type="file" name="auth_bg" class="form-control" accept="image/*">
                                    </div>
                                    <small class="text-muted d-block">Image d'arrière-plan affichée sur les pages de connexion, mot de passe oublié et réinitialisation.</small>
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
                        
                        {{-- TAB: Home Page Settings --}}
                        <div class="tab-pane" id="home" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Titre principal (Hero)</label>
                                    <input type="text" name="hero_title" class="form-control" value="{{ old('hero_title', $setting->hero_title ?? '') }}">
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Sous-titre (Hero)</label>
                                    <textarea name="hero_subtitle" class="form-control" rows="2">{{ old('hero_subtitle', $setting->hero_subtitle ?? '') }}</textarea>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Image de fond (Hero)</label>
                                    <div class="d-flex align-items-center mb-2">
                                        @if($setting->hero_image)
                                            <div class="me-3 bg-dark p-2 rounded text-center" style="min-width: 120px;">
                                                <img src="{{ asset('storage/' . $setting->hero_image) }}" alt="Hero Image" class="img-fluid rounded" style="max-height: 60px;">
                                            </div>
                                        @endif
                                        <input type="file" name="hero_image" class="form-control" accept="image/*">
                                    </div>
                                    <small class="text-muted d-block">L'image principale tout en haut de la page d'accueil.</small>
                                </div>
                                <div class="col-md-12 mb-4">
                                    <hr>
                                    <h5 class="mb-3">Section: Qui sommes-nous ?</h5>
                                </div>
                                <div class="col-md-4 mb-4">
                                    <label class="form-label">Notre Histoire</label>
                                    <textarea name="about_history" class="form-control" rows="4">{{ old('about_history', $setting->about_history ?? '') }}</textarea>
                                </div>
                                <div class="col-md-4 mb-4">
                                    <label class="form-label">Notre Mission</label>
                                    <textarea name="about_mission" class="form-control" rows="4">{{ old('about_mission', $setting->about_mission ?? '') }}</textarea>
                                </div>
                                <div class="col-md-4 mb-4">
                                    <label class="form-label">Notre Vision</label>
                                    <textarea name="about_vision" class="form-control" rows="4">{{ old('about_vision', $setting->about_vision ?? '') }}</textarea>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Image de Présentation (About)</label>
                                    <div class="d-flex align-items-center mb-2">
                                        @if($setting->about_image)
                                            <div class="me-3 bg-light p-2 rounded text-center" style="width: 120px;">
                                                <img src="{{ asset('storage/' . $setting->about_image) }}" alt="About Image" class="img-fluid rounded" style="max-height: 60px;">
                                            </div>
                                        @endif
                                        <input type="file" name="about_image" class="form-control" accept="image/*">
                                    </div>
                                    <small class="text-muted d-block">Image affichée à côté du texte "Qui sommes-nous".</small>
                                </div>
                            </div>
                        </div>
                        
                        {{-- TAB: Social Networks --}}
                        <div class="tab-pane" id="socials" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Lien Facebook de la jeunesse</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-primary text-white border-primary"><i class="ri-facebook-circle-fill"></i></span>
                                        <input type="url" name="facebook_link" class="form-control" placeholder="https://www.facebook.com/..." value="{{ old('facebook_link', $setting->facebook_link ?? '') }}">
                                    </div>
                                    <small class="text-muted d-block mt-1">Ex: https://www.facebook.com/jeuneseber</small>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Lien TikTok de la jeunesse</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-dark text-white border-dark"><i class="ri-tiktok-fill"></i></span>
                                        <input type="url" name="tiktok_link" class="form-control" placeholder="https://www.tiktok.com/@..." value="{{ old('tiktok_link', $setting->tiktok_link ?? '') }}">
                                    </div>
                                    <small class="text-muted d-block mt-1">Ex: https://www.tiktok.com/@jeuneseber</small>
                                </div>
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

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Restore active tab from localStorage
    var activeTab = localStorage.getItem('activeSettingTab');
    if(activeTab){
        var tabElement = document.querySelector('a[href="' + activeTab + '"]');
        if(tabElement) {
            var tab = new bootstrap.Tab(tabElement);
            tab.show();
        }
    }

    // Save active tab on change
    var tabLinks = document.querySelectorAll('a[data-bs-toggle="tab"]');
    tabLinks.forEach(function(link) {
        link.addEventListener('shown.bs.tab', function (e) {
            var target = e.target.getAttribute('href');
            localStorage.setItem('activeSettingTab', target);
        });
    });
});
</script>
@endsection
