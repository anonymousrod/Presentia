<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-layout="horizontal" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil | {{ config('app.name') }}</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ $settings->favicon_url ?? asset('assets/images/favicon.ico') }}">

    <!-- Swiper -->
    <link href="{{ asset('assets/libs/swiper/swiper-bundle.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- Bootstrap Css -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- App Css -->
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Custom Css -->
    <link href="{{ asset('assets/css/custom.min.css') }}" rel="stylesheet" type="text/css" />

    <style>
        /* Custom Styles for Home Page */
        .hero-section {
            position: relative;
            padding: 150px 0 120px;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            color: #fff;
        }
        .hero-overlay {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(to right, rgba(0,0,0,0.85), rgba(0,0,0,0.5));
        }
        .hero-content {
            position: relative;
            z-index: 1;
        }
        .hero-text-container {
            background: rgba(0, 0, 0, 0.55);
            padding: 40px;
            border-radius: 20px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        .section-title {
            font-weight: 700;
            margin-bottom: 2rem;
            position: relative;
            padding-bottom: 10px;
        }
        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 3px;
            background-color: var(--vz-primary);
        }
        .text-center .section-title::after {
            left: 50%;
            transform: translateX(-50%);
        }
        
        /* Navbar Custom */
        .navbar-custom {
            padding: 15px 0;
            transition: all 0.3s ease;
            background-color: transparent;
            position: absolute;
            width: 100%;
            z-index: 999;
        }
        .navbar-custom .navbar-brand {
            color: #fff;
            font-weight: 700;
            font-size: 24px;
        }
        .navbar-custom .nav-link {
            color: rgba(255, 255, 255, 0.9);
            font-weight: 500;
            padding: 0 15px;
            transition: color 0.3s;
        }
        .navbar-custom .nav-link:hover,
        .navbar-custom .nav-link:focus,
        .navbar-custom .nav-link.active {
            color: #f1c40f !important; /* Jaune/Or pour bien ressortir au survol */
        }
        .navbar-custom.is-sticky {
            background-color: var(--vz-primary);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: fixed;
            top: 0;
            animation: fadeInDown 0.5s;
        }

        /* Org Chart Cards */
        .org-card {
            transition: all 0.3s ease;
            border: none;
            border-radius: 15px;
            overflow: hidden;
            background: var(--vz-card-bg-custom);
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        .org-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .org-img-wrapper {
            height: 250px;
            overflow: hidden;
        }
        .org-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        .org-card:hover .org-img {
            transform: scale(1.05);
        }

        /* Group Cards */
        .group-card {
            border-radius: 15px;
            border: none;
            transition: all 0.3s;
            height: 100%;
        }
        .group-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }

        /* Stats Section */
        .stats-section {
            background: linear-gradient(135deg, #2b3964 0%, #405189 100%);
            padding: 80px 0;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        .stat-box {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 20px;
            padding: 40px 20px;
            backdrop-filter: blur(10px);
            transition: all 0.4s ease;
            height: 100%;
        }
        .stat-box:hover {
            transform: translateY(-10px);
            background: rgba(255, 255, 255, 0.06);
            border-color: rgba(255, 255, 255, 0.1);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.4);
        }
        .stat-box h2 {
            font-weight: 700;
            color: #ffffff;
            font-size: 3.5rem;
            margin-bottom: 15px;
            text-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .stat-box p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1.1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }

        /* Gallery */
        .gallery-img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            border-radius: 10px;
            transition: transform 0.3s;
            cursor: pointer;
        }
        .gallery-item {
            overflow: hidden;
            border-radius: 10px;
            margin-bottom: 24px;
        }
        .gallery-item:hover .gallery-img {
            transform: scale(1.1);
        }

        /* CTA Section */
        .cta-section {
            background: linear-gradient(135deg, var(--vz-primary), var(--vz-info));
            color: white;
            padding: 80px 0;
            text-align: center;
            border-radius: 20px;
            margin: 40px 0;
        }
        
        /* Footer */
        .footer-custom {
            background-color: #1a1d21;
            color: #a1aab2;
            padding: 60px 0 20px;
        }
        .footer-custom h5 {
            color: white;
            font-weight: 600;
            margin-bottom: 20px;
        }
        .footer-links a {
            color: #a1aab2;
            text-decoration: none;
            display: block;
            margin-bottom: 10px;
            transition: color 0.3s;
        }
        .footer-links a:hover {
            color: white;
        }

        /* Responsive Fixes for Small Screens */
        @media (max-width: 991.98px) {
            .navbar-custom {
                background-color: var(--vz-primary) !important;
                padding: 12px 0;
                position: fixed;
                top: 0;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
            }
            .navbar-collapse {
                background: rgba(33, 37, 41, 0.96);
                backdrop-filter: blur(12px);
                -webkit-backdrop-filter: blur(12px);
                border-radius: 14px;
                padding: 16px;
                margin-top: 12px;
                border: 1px solid rgba(255, 255, 255, 0.12);
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.35);
            }
            .navbar-nav .nav-link {
                padding: 10px 14px;
                border-radius: 8px;
                font-size: 1rem;
            }
            .navbar-nav .ms-3 {
                margin-left: 0 !important;
                margin-top: 12px;
                width: 100%;
            }
            .navbar-nav .btn {
                width: 100%;
                text-align: center;
            }
            .hero-section {
                padding: 110px 0 50px;
            }
            .hero-text-container {
                padding: 24px 20px;
                border-radius: 16px;
            }
            .hero-text-container h1 {
                font-size: 2rem !important;
                margin-bottom: 16px !important;
            }
            .hero-text-container p {
                font-size: 1rem !important;
                margin-bottom: 24px !important;
            }
            .org-img-wrapper {
                height: 200px;
            }
            .stat-box {
                padding: 24px 12px;
                border-radius: 16px;
            }
            .stat-box h2 {
                font-size: 2.25rem;
                margin-bottom: 8px;
            }
            .stat-box p {
                font-size: 0.8rem;
                letter-spacing: 0.5px;
            }
            .cta-section {
                padding: 40px 20px;
                margin: 24px 0;
                border-radius: 16px;
            }
            .cta-section h2 {
                font-size: 1.75rem;
            }
            .gallery-img {
                height: 180px;
            }
        }

        @media (max-width: 575.98px) {
            .hero-text-container h1 {
                font-size: 1.65rem !important;
            }
            .hero-text-container .btn {
                width: 100%;
            }
            .section-title {
                font-size: 1.5rem;
            }
            .stat-box {
                padding: 18px 8px;
            }
            .stat-box h2 {
                font-size: 1.85rem;
            }
            .stat-box p {
                font-size: 0.72rem;
            }
            .org-img-wrapper {
                height: 180px;
            }
            .phone-contact-pill {
                width: 100%;
                justify-content: center;
                text-align: center;
            }
        }
    </style>
</head>
<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-custom" id="navbar">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                @if(!empty($settings->logo_light))
                    <img src="{{ $settings->logo_light_url }}" alt="Logo" height="35" class="me-2">
                @else
                    <i class="ri-cube-fill me-2 align-middle fs-3"></i>
                @endif
                <span class="fs-4">{{ config('app.name') }}</span>
            </a>
            <button class="navbar-toggler border-0 p-2 text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <i class="ri-menu-line fs-22"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center">
                    <li class="nav-item"><a class="nav-link" href="#about">Qui sommes-nous</a></li>
                    <li class="nav-item"><a class="nav-link" href="#organization">Organisation</a></li>
                    <li class="nav-item"><a class="nav-link" href="#groups">Groupes</a></li>
                    <li class="nav-item"><a class="nav-link" href="#events">Événements</a></li>
                    <li class="nav-item ms-3">
                        @auth
                            <a href="{{ route('dashboard') }}" class="btn btn-light btn-rounded">Mon Tableau de bord</a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-light btn-rounded">Se connecter</a>
                        @endauth
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- 1. HERO SECTION -->
    @php
        $heroBg = $settings->hero_image ? asset('storage/' . $settings->hero_image) : asset('assets/images/home/hero-bg.jpg');
    @endphp
    <section class="hero-section" style="background-image: url('{{ $heroBg }}');">
        <div class="hero-overlay"></div>
        <div class="container hero-content">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <div class="hero-text-container">
                        <h1 class="display-4 fw-bold mb-4 text-white">{{ $settings->hero_title ?? 'Bienvenue dans notre Jeunesse' }}</h1>
                        <p class="lead mb-5 text-light" style="font-size: 1.2rem; max-width: 600px;">
                            {{ $settings->hero_subtitle ?? 'Découvrez notre vision, nos valeurs et participez à nos activités pour grandir ensemble dans la foi.' }}
                        </p>
                        <div class="d-flex gap-3 flex-wrap">
                            <a href="#about" class="btn btn-primary btn-lg px-4">Découvrir</a>
                            <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg px-4">Espace Membre</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="container mt-4 mt-md-5 px-3 px-sm-4">
        
        <!-- 2. PRESENTATION DE LA JEUNESSE -->
        <section id="about" class="py-4 py-md-5">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    @php
                        $aboutImg = $settings->about_image ? asset('storage/' . $settings->about_image) : asset('assets/images/home/about.jpg');
                    @endphp
                    <img src="{{ $aboutImg }}" alt="Présentation" class="img-fluid rounded-4 shadow-lg w-100" style="max-height: 380px; object-fit: cover;">
                </div>
                <div class="col-lg-6 ps-lg-5">
                    <h2 class="section-title">Qui sommes-nous ?</h2>
                    <div class="mb-4">
                        <h5 class="fw-bold text-primary"><i class="ri-history-line align-middle me-1"></i> Notre Histoire</h5>
                        <p class="text-muted">{{ $settings->about_history ?? "La jeunesse a été fondée avec pour but de rassembler les jeunes autour des valeurs chrétiennes, en leur offrant un cadre d'épanouissement spirituel et social." }}</p>
                    </div>
                    <div class="mb-4">
                        <h5 class="fw-bold text-success"><i class="ri-flag-line align-middle me-1"></i> Notre Mission</h5>
                        <p class="text-muted">{{ $settings->about_mission ?? "Équiper et former la nouvelle génération pour qu'elle soit une lumière dans le monde, en cultivant l'amour, l'entraide et le leadership." }}</p>
                    </div>
                    <div class="mb-4">
                        <h5 class="fw-bold text-info"><i class="ri-eye-line align-middle me-1"></i> Notre Vision</h5>
                        <p class="text-muted">{{ $settings->about_vision ?? "Voir chaque jeune découvrir son potentiel et s'engager activement dans la société et dans l'église." }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- 5. CHIFFRES CLES -->
        <section class="stats-section rounded-4 my-4 my-md-5 shadow-lg">
            <div class="container">
                <div class="row text-center g-2 g-sm-3 g-lg-4">
                    <div class="col-6 col-lg-3">
                        <div class="stat-box">
                            <h2 class="fw-bold"><span class="counter-value" data-target="{{ $stats['users'] }}">0</span></h2>
                            <p class="mb-0">Jeunes engagés</p>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="stat-box">
                            <h2 class="fw-bold"><span class="counter-value" data-target="{{ $stats['groups'] }}">0</span></h2>
                            <p class="mb-0">Groupes actifs</p>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="stat-box">
                            <h2 class="fw-bold"><span class="counter-value" data-target="{{ $stats['events'] }}">0</span></h2>
                            <p class="mb-0">Événements</p>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="stat-box">
                            <h2 class="fw-bold"><span class="counter-value" data-target="{{ $stats['leaders'] }}">0</span></h2>
                            <p class="mb-0">Responsables</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 3. NOTRE ORGANISATION -->
        <section id="organization" class="py-4 py-md-5">
            <div class="text-center mb-4 mb-md-5">
                <h2 class="section-title d-inline-block">Notre Organisation</h2>
                <p class="text-muted mt-2">Découvrez l'équipe dirigeante de notre jeunesse.</p>
            </div>
            <div class="row justify-content-center g-3 g-md-4">
                @foreach($leaders as $leader)
                    <div class="col-6 col-md-4 col-xl-3 mb-3">
                        <div class="card org-card h-100">
                            <div class="org-img-wrapper">
                                @php
                                    $photoUrl = $leader->photo ? asset('storage/' . $leader->photo) : asset('assets/images/home/user-placeholder.jpg');
                                @endphp
                                <img src="{{ $photoUrl }}" class="org-img" alt="{{ $leader->name }}">
                            </div>
                            <div class="card-body text-center p-3 p-md-4">
                                <h5 class="fw-bold mb-1 fs-14 fs-md-16">{{ $leader->first_name }} {{ $leader->name }}</h5>
                                <div class="d-flex flex-wrap justify-content-center gap-1 mb-2">
                                    @foreach($leader->roles as $role)
                                        @if(in_array($role->name, ['Administrateur', 'Président', 'Vice Président', 'Membre du bureau']))
                                            <span class="badge bg-primary-subtle text-primary mb-1 fs-10 fs-md-12">{{ $role->name == 'Administrateur' ? 'Président' : $role->name }}</span>
                                            @if($role->description)
                                                <p class="text-muted fs-12 fs-md-13 mb-0 text-center">{{ $role->description }}</p>
                                            @endif
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <!-- 4. LES GROUPES -->
        <section id="groups" class="py-4 py-md-5 bg-light rounded-4 px-3 px-md-4 my-4 my-md-5">
            <div class="text-center mb-4 mb-md-5">
                <h2 class="section-title d-inline-block">Nos Groupes</h2>
                <p class="text-muted mt-2">Rejoignez un groupe de proximité pour partager et grandir ensemble.</p>
            </div>
            <div class="row g-3 g-md-4">
                @foreach($groups as $index => $group)
                    <div class="col-12 col-md-6 col-lg-4 mb-3 mb-md-0">
                        <div class="card group-card shadow-sm h-100">
                            @if($group->image_path)
                                <div class="position-relative bg-dark" style="height: 180px; width: 100%; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                                    <div class="position-absolute w-100 h-100" style="background-image: url('{{ asset('storage/' . $group->image_path) }}'); background-size: cover; background-position: center; filter: blur(10px) brightness(0.6); transform: scale(1.1);"></div>
                                    <img src="{{ asset('storage/' . $group->image_path) }}" alt="{{ $group->name }}" style="max-width: 100%; max-height: 100%; object-fit: contain; position: relative; z-index: 1;">
                                    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(to bottom, rgba(0,0,0,0.2), transparent); z-index: 2;"></div>
                                </div>
                            @else
                                <div class="card-img-top d-flex align-items-center justify-content-center" style="height: 180px; background: linear-gradient(135deg, {{ $group->color ?? '#405189' }}dd, {{ $group->color ?? '#405189' }});">
                                    <i class="ri-group-line text-white opacity-75" style="font-size: 4.5rem;"></i>
                                </div>
                            @endif
                            <div class="card-body p-3 p-md-4">
                                <h5 class="card-title fw-bold" style="color: {{ $group->color ?? '#405189' }}">{{ $group->name }}</h5>
                                <p class="card-text text-muted small">{{ $group->description ?? 'Aucune description disponible pour ce groupe.' }}</p>
                                
                                <hr class="border-dashed my-3">
                                
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="badge bg-secondary-subtle text-body mb-1 d-block text-start"><i class="ri-user-star-line text-warning"></i> Chef: {{ $group->leader ? $group->leader->first_name : 'N/A' }}</span>
                                        <span class="badge bg-secondary-subtle text-body d-block text-start"><i class="ri-hand-coin-line text-success"></i> Col: {{ $group->collector ? $group->collector->first_name : 'N/A' }}</span>
                                    </div>
                                    <div class="text-center">
                                        <h4 class="mb-0 fw-bold">{{ $group->members_count }}</h4>
                                        <small class="text-muted">Membres</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <!-- 6. GALERIE PHOTOS -->
        <section id="gallery" class="py-4 py-md-5 bg-light rounded-4 px-3 px-md-4 my-4 my-md-5">
            <div class="text-center mb-4 mb-md-5">
                <h2 class="section-title d-inline-block">Notre Galerie</h2>
                <p class="text-muted mt-2">Quelques souvenirs de nos moments passés ensemble.</p>
            </div>
            <div class="row g-3">
                @forelse($galleries as $index => $gallery)
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="gallery-item shadow-sm mb-2 mb-md-3">
                            <a href="{{ asset('storage/' . $gallery->image_path) }}" target="_blank">
                                <img src="{{ asset('storage/' . $gallery->image_path) }}" alt="{{ $gallery->title }}" class="gallery-img">
                            </a>
                        </div>
                    </div>
                @empty
                    <!-- Placeholders si la galerie est vide -->
                    <div class="col-6 col-md-4"><div class="gallery-item shadow-sm"><img src="{{ asset('assets/images/home/group-1.jpg') }}" class="gallery-img" alt="Gallery 1"></div></div>
                    <div class="col-6 col-md-4"><div class="gallery-item shadow-sm"><img src="{{ asset('assets/images/home/activity.jpg') }}" class="gallery-img" alt="Gallery 2"></div></div>
                    <div class="col-6 col-md-4"><div class="gallery-item shadow-sm"><img src="{{ asset('assets/images/home/group-2.jpg') }}" class="gallery-img" alt="Gallery 3"></div></div>
                @endforelse
            </div>
            
            <div class="mt-4 d-flex justify-content-center">
                {{ $galleries->fragment('gallery')->links() }}
            </div>
        </section>

        <!-- 7. ACTUALITES / EVENEMENTS -->
        <section id="events" class="py-4 py-md-5">
            <div class="text-center mb-4 mb-md-5">
                <h2 class="section-title d-inline-block">Prochains Événements</h2>
                <p class="text-muted mt-2">Ne manquez pas nos prochaines rencontres.</p>
            </div>
            
            <div class="row g-3 g-md-4">
                @forelse($activities as $activity)
                    <div class="col-12 col-md-6 col-lg-4 mb-3 mb-md-0">
                        <div class="card h-100 border-0 shadow-sm overflow-hidden">
                            @if($activity->image_path)
                                <a href="{{ asset('storage/' . $activity->image_path) }}" target="_blank" class="position-relative bg-dark d-flex align-items-center justify-content-center" style="height: 180px; width: 100%; overflow: hidden; text-decoration: none;">
                                    <div class="position-absolute w-100 h-100" style="background-image: url('{{ asset('storage/' . $activity->image_path) }}'); background-size: cover; background-position: center; filter: blur(10px) brightness(0.6); transform: scale(1.1);"></div>
                                    <img src="{{ asset('storage/' . $activity->image_path) }}" alt="{{ $activity->title }}" style="max-width: 100%; max-height: 100%; object-fit: contain; position: relative; z-index: 1;">
                                    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(to bottom, rgba(0,0,0,0.2), transparent); z-index: 2;"></div>
                                </a>
                            @else
                                @php
                                    $gradients = [
                                        'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                                        'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
                                        'linear-gradient(135deg, #fa709a 0%, #fee140 100%)',
                                        'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)'
                                    ];
                                    $bg = $gradients[$loop->index % 4];
                                @endphp
                                <div class="card-img-top d-flex align-items-center justify-content-center" style="height: 180px; background: {{ $bg }};">
                                    <i class="ri-calendar-event-line text-white opacity-75" style="font-size: 4.5rem;"></i>
                                </div>
                            @endif
                            <div class="card-body p-3 p-md-4">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="badge bg-primary-subtle text-primary">{{ \Carbon\Carbon::parse($activity->start_time)->format('d M Y') }}</span>
                                    <span class="text-muted small"><i class="ri-map-pin-line"></i> {{ $activity->location ?? 'Non défini' }}</span>
                                </div>
                                <h5 class="card-title fw-bold">{{ $activity->title }}</h5>
                                <p class="card-text text-muted small text-truncate-2">{{ Str::limit(strip_tags($activity->description), 100) }}</p>
                            </div>
                            <div class="card-footer bg-transparent border-0 pb-3 px-3 px-md-4">
                                <a href="{{ route('login') }}" class="btn btn-primary w-100">S'inscrire à l'événement</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <div class="avatar-lg mx-auto mb-3">
                            <div class="avatar-title bg-light text-primary rounded-circle fs-24">
                                <i class="ri-calendar-event-line"></i>
                            </div>
                        </div>
                        <h5>Aucun événement à venir</h5>
                        <p class="text-muted">Restez à l'écoute, de nouvelles activités seront bientôt programmées !</p>
                    </div>
                @endforelse
            </div>
        </section>

        <!-- 8. APPEL A L'ACTION -->
        <section class="cta-section shadow-lg">
            <div class="container px-3">
                <h2 class="display-5 fw-bold mb-3 mb-md-4">Rejoignez-nous dès aujourd'hui !</h2>
                <p class="lead mb-4 mb-md-5 mx-auto fs-15 fs-md-18" style="max-width: 700px;">
                    Vous souhaitez faire partie de notre merveilleuse jeunesse, participer à nos activités et grandir avec nous ?
                </p>
                <div class="d-inline-flex align-items-center bg-body-secondary rounded-pill p-2 pe-3 pe-md-4 shadow-sm phone-contact-pill text-start" style="cursor: pointer; max-width: 100%;" onclick="window.location.href='tel:{{ str_replace(' ', '', $adminPhone) }}'">
                    <div class="avatar-sm me-2 me-md-3 flex-shrink-0">
                        <div class="avatar-title bg-primary rounded-circle">
                            <i class="ri-phone-fill fs-18 fs-md-20"></i>
                        </div>
                    </div>
                    <div class="text-start overflow-hidden">
                        <span class="d-block text-muted small fw-medium text-truncate">Contactez le Président pour être ajouté :</span>
                        <span class="d-block text-body fw-bold fs-16 fs-md-18 text-truncate">{{ $adminPhone }}</span>
                    </div>
                </div>
            </div>
        </section>

    </div>

    <!-- 9. FOOTER -->
    <footer class="footer-custom mt-auto">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h4 class="text-white fw-bold mb-3">Notre Jeunesse</h4>
                    <p class="text-muted pe-4">
                        Une communauté dynamique de jeunes engagés, unis par la foi et le désir de grandir ensemble.
                    </p>
                    <div class="d-flex gap-2 mt-4">
                        @if(!empty($settings->facebook_link))
                            <a href="{{ $settings->facebook_link }}" target="_blank" class="btn btn-sm btn-icon btn-outline-dark rounded-circle border-secondary text-white" style="transition: all 0.3s; background: rgba(0, 0, 0, 0.5);">
                                <i class="ri-facebook-fill fs-18"></i>
                            </a>
                        @endif
                        @if(!empty($settings->tiktok_link))
                            <a href="{{ $settings->tiktok_link }}" target="_blank" class="btn btn-sm btn-icon btn-outline-dark rounded-circle border-secondary text-white" style="transition: all 0.3s; background: rgba(0, 0, 0, 0.5);">
                                <i class="ri-tiktok-fill fs-18"></i>
                            </a>
                        @endif
                    </div>
                </div>
                <div class="col-lg-2 col-6 mb-4">
                    <h5>Navigation</h5>
                    <div class="footer-links">
                        <a href="#about">Qui sommes-nous ?</a>
                        <a href="#organization">Organisation</a>
                        <a href="#groups">Nos Groupes</a>
                        <a href="#events">Événements</a>
                    </div>
                </div>
                <div class="col-lg-3 col-6 mb-4">
                    <h5>Liens Utiles</h5>
                    <div class="footer-links">
                        <a href="{{ route('login') }}">Connexion Espace Membre</a>
                        <a href="{{ route('privacy') }}">Politique de confidentialité</a>
                        <a href="{{ route('legal') }}">Mentions légales</a>
                    </div>
                </div>
                <div class="col-lg-3 mb-4">
                    <h5>Contact</h5>
                    <p class="text-muted mb-2"><i class="ri-map-pin-line me-2"></i> Lome, Togo</p>
                    <p class="text-muted mb-2"><i class="ri-phone-line me-2"></i> {{ $adminPhone }}</p>
                    <p class="text-muted mb-0"><i class="ri-mail-line me-2"></i> contact@jeunesse.com</p>
                </div>
            </div>
            <hr class="border-secondary my-4">
            <div class="text-center text-muted">
                <p class="mb-0">&copy; {{ date('Y') }} Plateforme de la Jeunesse. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <!-- JAVASCRIPT -->
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/libs/swiper/swiper-bundle.min.js') }}"></script>
    <script>
        // Navbar Sticky effect
        window.addEventListener('scroll', function() {
            var navbar = document.getElementById('navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('is-sticky');
            } else {
                navbar.classList.remove('is-sticky');
            }
        });

        // CountUp JS
        document.addEventListener("DOMContentLoaded", function() {
            const counters = document.querySelectorAll('.counter-value');
            const speed = 200;

            const animateCounters = () => {
                counters.forEach(counter => {
                    const target = +counter.getAttribute('data-target');
                    const count = +counter.innerText;
                    const inc = target / speed;

                    if(count < target) {
                        counter.innerText = Math.ceil(count + inc);
                        setTimeout(animateCounters, 10);
                    } else {
                        counter.innerText = target;
                    }
                });
            };
            
            // Simple Intersection Observer to start counting when visible
            const observer = new IntersectionObserver(entries => {
                entries.forEach(entry => {
                    if(entry.isIntersecting) {
                        animateCounters();
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.5 });
            
            counters.forEach(counter => {
                observer.observe(counter);
            });
        });
    </script>
</body>
</html>
