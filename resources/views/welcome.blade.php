<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-layout="horizontal" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $currentChurch->name ?? 'Accueil' }} | {{ config('app.name') }}</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ $appSettings->favicon_url ?? asset('assets/images/favicon.ico') }}">

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
            font-size: 20px;
        }
        .navbar-custom .nav-link {
            color: rgba(255, 255, 255, 0.9);
            font-weight: 500;
            padding: 0 12px;
            transition: color 0.3s;
        }
        .navbar-custom .nav-link:hover,
        .navbar-custom .nav-link:focus,
        .navbar-custom .nav-link.active {
            color: #f1c40f !important;
        }
        .navbar-custom.is-sticky {
            background-color: var(--vz-primary);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: fixed;
            top: 0;
            animation: fadeInDown 0.5s;
        }

        /* Church badge selector */
        .church-switcher-btn {
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: #fff;
            transition: all 0.25s ease;
        }
        .church-switcher-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            color: #fff;
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

        /* Professional Empty State Cards */
        .empty-state-card {
            background: #ffffff;
            border-radius: 20px;
            border: 1.5px dashed rgba(var(--vz-primary-rgb), 0.22);
            padding: 2.25rem 1.5rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        }
        .empty-state-card:hover {
            border-color: rgba(var(--vz-primary-rgb), 0.45);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.06);
            transform: translateY(-2px);
        }
        .empty-icon-circle {
            width: 68px;
            height: 68px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.85rem;
            margin-bottom: 1rem;
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
            <a class="navbar-brand d-flex align-items-center" href="{{ route('church.landing', $currentChurch->slug) }}">
                <img src="{{ $appSettings->logo_light_url ?? asset('assets/images/logo-light.png') }}" alt="{{ config('app.name', 'MeVoici') }}" height="32" class="me-2">
                <div class="d-flex flex-column text-start ps-2 border-start border-white border-opacity-25 ms-1">
                    <span class="fs-14 fw-bold text-white text-truncate lh-1" style="max-width: 220px;">{{ $currentChurch->name }}</span>
                    <small class="text-white-50 fs-11 mt-1">{{ $currentChurch->city ?: config('app.name') }}</small>
                </div>
            </a>

            <div class="d-flex align-items-center gap-2 order-lg-3 ms-lg-3">
                @if(isset($allChurches) && $allChurches->count() > 1)
                    <button type="button" class="btn btn-sm church-switcher-btn rounded-pill px-3 py-1 d-flex align-items-center gap-1 fs-12 shadow-sm" data-bs-toggle="modal" data-bs-target="#switchChurchModal">
                        <i class="ri-map-pin-2-line text-warning"></i>
                        <span class="d-none d-sm-inline">{{ $currentChurch->city ?: 'Changer d\'église' }}</span>
                        <i class="ri-arrow-down-s-line fs-14 opacity-75"></i>
                    </button>
                @endif

                <button class="navbar-toggler border-0 p-2 text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <i class="ri-menu-line fs-22"></i>
                </button>
            </div>

            <div class="collapse navbar-collapse order-lg-2" id="navbarNav">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center">
                    <li class="nav-item"><a class="nav-link" href="#about">À propos</a></li>
                    <li class="nav-item"><a class="nav-link" href="#organization">Dirigeants</a></li>
                    <li class="nav-item"><a class="nav-link" href="#groups">Groupes</a></li>
                    <li class="nav-item"><a class="nav-link" href="#events">Cultes & Activités</a></li>
                    <li class="nav-item ms-lg-3 mt-2 mt-lg-0">
                        @auth
                            <a href="{{ route('dashboard') }}" class="btn btn-light btn-rounded shadow-sm px-4 fw-semibold">
                                <i class="ri-dashboard-line me-1"></i> Mon Espace
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-light btn-rounded shadow-sm px-4 fw-semibold">
                                <i class="ri-user-line me-1"></i> Se connecter
                            </a>
                        @endauth
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- 1. HERO SECTION -->
    @php
        $heroBg = $settings->hero_image ? asset('storage/' . $settings->hero_image) : asset('assets/images/home/hero-bg.jpg');
        $heroTitle = $settings->hero_title ?: 'Bienvenue à ' . $currentChurch->name;
        $heroSubtitle = $settings->hero_subtitle ?: "Rejoignez notre communauté vivante, participez à nos cultes et découvrez nos activités pour grandir ensemble dans la foi.";
    @endphp
    <section class="hero-section" style="background-image: url('{{ $heroBg }}');">
        <div class="hero-overlay"></div>
        <div class="container hero-content">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <div class="hero-text-container">
                        <div class="d-inline-flex align-items-center gap-2 badge bg-warning bg-opacity-75 text-dark fw-bold px-3 py-2 rounded-pill mb-3 fs-13">
                            <i class="bx bxs-church fs-14"></i>
                            <span>{{ $currentChurch->name }} {{ $currentChurch->city ? '• ' . $currentChurch->city : '' }}</span>
                        </div>
                        <h1 class="display-4 fw-bold mb-4 text-white">{{ $heroTitle }}</h1>
                        <p class="lead mb-4 text-light" style="font-size: 1.15rem; max-width: 620px;">
                            {{ $heroSubtitle }}
                        </p>
                        <div class="d-flex gap-3 flex-wrap">
                            <a href="#about" class="btn btn-primary btn-lg px-4 rounded-pill shadow-sm">
                                <i class="ri-compass-3-line me-1"></i> Découvrir l'Église
                            </a>
                            <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg px-4 rounded-pill">
                                <i class="ri-user-follow-line me-1"></i> Espace Membre
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="container mt-4 mt-md-5 px-3 px-sm-4">
        
        <!-- 2. PRESENTATION DE L'EGLISE -->
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
                        <p class="text-muted">{{ $settings->about_history ?? "L'église " . $currentChurch->name . " est une communauté chrétienne engagée dans la prière, le partage et l'édification de chaque fidèle." }}</p>
                    </div>
                    <div class="mb-4">
                        <h5 class="fw-bold text-success"><i class="ri-flag-line align-middle me-1"></i> Notre Mission</h5>
                        <p class="text-muted">{{ $settings->about_mission ?? "Équiper et former chaque génération pour qu'elle soit une lumière dans le monde, en cultivant l'amour, l'entraide et le leadership chrétien." }}</p>
                    </div>
                    <div class="mb-4">
                        <h5 class="fw-bold text-info"><i class="ri-eye-line align-middle me-1"></i> Notre Vision</h5>
                        <p class="text-muted">{{ $settings->about_vision ?? "Voir chaque membre grandir dans sa foi, découvrir ses dons et impacter positivement sa communauté et sa nation." }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- 5. CHIFFRES CLES -->
        @php
            $hasSubstantialStats = ($stats['users'] > 1) || ($stats['groups'] > 0) || ($stats['events'] > 0);
        @endphp
        <section class="stats-section rounded-4 my-4 my-md-5 shadow-lg">
            <div class="container">
                @if($hasSubstantialStats)
                    <div class="row text-center g-2 g-sm-3 g-lg-4">
                        <div class="col-6 col-lg-3">
                            <div class="stat-box">
                                <h2 class="fw-bold"><span class="counter-value" data-target="{{ $stats['users'] }}">0</span></h2>
                                <p class="mb-0">Membres enregistrés</p>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="stat-box">
                                <h2 class="fw-bold"><span class="counter-value" data-target="{{ $stats['groups'] }}">0</span></h2>
                                <p class="mb-0">Groupes & Ministères</p>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="stat-box">
                                <h2 class="fw-bold"><span class="counter-value" data-target="{{ $stats['events'] }}">0</span></h2>
                                <p class="mb-0">Cultes & Activités</p>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="stat-box">
                                <h2 class="fw-bold"><span class="counter-value" data-target="{{ $stats['leaders'] }}">0</span></h2>
                                <p class="mb-0">Dirigeants & Responsables</p>
                            </div>
                        </div>
                    </div>
                @else
                    {{-- Quand l'église démarre et recense ses membres --}}
                    <div class="row text-center g-3 g-lg-4">
                        <div class="col-6 col-lg-3">
                            <div class="stat-box">
                                <div class="fs-24 text-primary mb-1"><i class="ri-book-open-line"></i></div>
                                <h5 class="fw-bold text-dark mb-1 fs-15">Foi &amp; Parole</h5>
                                <p class="mb-0 text-muted fs-12">Enseignement biblique</p>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="stat-box">
                                <div class="fs-24 text-success mb-1"><i class="ri-heart-line"></i></div>
                                <h5 class="fw-bold text-dark mb-1 fs-15">Fraternité</h5>
                                <p class="mb-0 text-muted fs-12">Accueil &amp; communion</p>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="stat-box">
                                <div class="fs-24 text-warning mb-1"><i class="ri-fire-line"></i></div>
                                <h5 class="fw-bold text-dark mb-1 fs-15">Prière</h5>
                                <p class="mb-0 text-muted fs-12">Intercession fervente</p>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="stat-box">
                                <div class="fs-24 text-info mb-1"><i class="ri-user-smile-line"></i></div>
                                <h5 class="fw-bold text-dark mb-1 fs-15">Recensement</h5>
                                <p class="mb-0 text-muted fs-12">Communauté en essor</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </section>

        <!-- 3. NOTRE ORGANISATION -->
        <section id="organization" class="py-4 py-md-5">
            <div class="text-center mb-4 mb-md-5">
                <h2 class="section-title d-inline-block">Notre Organisation</h2>
                <p class="text-muted mt-2">Découvrez l'équipe dirigeante et pastorale de {{ $currentChurch->name }}.</p>
            </div>
            <div class="row justify-content-center g-3 g-md-4">
                @forelse($leaders as $leader)
                    <div class="col-6 col-md-4 col-xl-3 mb-3">
                        <div class="card org-card h-100 shadow-sm border-0 rounded-4 overflow-hidden">
                            <div class="org-img-wrapper" style="height: 220px; overflow: hidden; background: #f3f6f9;">
                                @php
                                    $photoUrl = $leader->photo ? asset('storage/' . $leader->photo) : asset('assets/images/home/user-placeholder.jpg');
                                @endphp
                                <img src="{{ $photoUrl }}" class="org-img w-100 h-100" style="object-fit: cover;" alt="{{ $leader->first_name }} {{ $leader->name }}">
                            </div>
                            <div class="card-body text-center p-3 p-md-4">
                                <h5 class="fw-bold mb-1 fs-14 fs-md-16">{{ $leader->first_name }} {{ $leader->name }}</h5>
                                <div class="d-flex flex-wrap justify-content-center gap-1 mb-2">
                                    @foreach($leader->roles as $role)
                                        @if(in_array($role->name, ['Super Admin', 'Administrateur', 'Président', 'Vice-président', 'Vice Président', 'Secrétaire Général', 'Trésorier Général', 'Membre du bureau', 'Pasteur']))
                                            @php
                                                $displayRoleName = match($role->name) {
                                                    'Super Admin' => 'Administrateur',
                                                    'Administrateur' => 'Président',
                                                    default => $role->name
                                                };
                                            @endphp
                                            <span class="badge bg-primary-subtle text-primary mb-1 fs-11 px-2 py-1 rounded-pill fw-semibold">{{ $displayRoleName }}</span>
                                            @if($role->description)
                                                <p class="text-muted fs-12 mb-0 text-center w-100">{{ $role->description }}</p>
                                            @endif
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 col-md-8 col-lg-6">
                        <div class="empty-state-card text-center">
                            <div class="empty-icon-circle bg-primary-subtle text-primary mx-auto">
                                <i class="ri-team-line"></i>
                            </div>
                            <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1 mb-2 fs-11 fw-semibold">Gouvernance pastorale</span>
                            <h5 class="fw-bold text-dark mb-2">Équipe dirigeante en cours de constitution</h5>
                            <p class="text-muted fs-14 mb-3">
                                Les profils des pasteurs, responsables de ministères et membres du bureau de {{ $currentChurch->name }} sont en cours de mise à jour sur MeVoici.
                            </p>
                            <a href="#contact" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                <i class="ri-contacts-line me-1"></i> Contacter le secrétariat pastoral
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>
        </section>

        <!-- 4. LES GROUPES -->
        <section id="groups" class="py-4 py-md-5 bg-light rounded-4 px-3 px-md-4 my-4 my-md-5">
            <div class="text-center mb-4 mb-md-5">
                <h2 class="section-title d-inline-block">Nos Groupes & Ministères</h2>
                <p class="text-muted mt-2">Rejoignez un groupe de proximité pour partager, servir et grandir ensemble.</p>
            </div>
            <div class="row g-3 g-md-4 justify-content-center">
                @forelse($groups as $index => $group)
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
                @empty
                    <div class="col-12 col-lg-8 mx-auto">
                        <div class="empty-state-card text-center bg-white shadow-sm p-4 p-md-5 rounded-4">
                            <div class="empty-icon-circle bg-warning-subtle text-warning mx-auto">
                                <i class="ri-community-line"></i>
                            </div>
                            <div class="d-flex justify-content-center mb-2">
                                <span class="badge bg-warning-subtle text-warning rounded-pill px-3 py-1 fs-11 fw-semibold">Vie communautaire</span>
                            </div>
                            <h4 class="fw-bold text-dark mb-2">Cellules de maison &amp; Ministères en cours d'organisation</h4>
                            <p class="text-muted fs-14 mb-4 mx-auto" style="max-width: 560px;">
                                {{ $currentChurch->name }} structure actuellement ses groupes de partage, chorales, départements de jeunesse et cellules de prière. Les inscriptions et programmes détaillés seront très bientôt ouverts !
                            </p>
                            
                            <div class="row g-2 g-sm-3 text-start justify-content-center mb-4">
                                <div class="col-12 col-sm-4">
                                    <div class="p-3 rounded-3 bg-light border border-light-subtle h-100">
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <i class="ri-music-2-line text-primary"></i>
                                            <strong class="fs-13">Louange &amp; Chorale</strong>
                                        </div>
                                        <p class="text-muted fs-11 mb-0">Animation des cultes et célébrations.</p>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="p-3 rounded-3 bg-light border border-light-subtle h-100">
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <i class="ri-book-read-line text-success"></i>
                                            <strong class="fs-13">Études &amp; Prière</strong>
                                        </div>
                                        <p class="text-muted fs-11 mb-0">Groupes de maison &amp; approfondissement.</p>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="p-3 rounded-3 bg-light border border-light-subtle h-100">
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <i class="ri-heart-3-line text-danger"></i>
                                            <strong class="fs-13">Accueil &amp; Entraide</strong>
                                        </div>
                                        <p class="text-muted fs-11 mb-0">Hospitalité, écoute &amp; actions sociales.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex flex-wrap justify-content-center gap-2">
                                <a href="#contact" class="btn btn-primary rounded-pill px-4">
                                    <i class="ri-hand-heart-line me-1"></i> Proposer ou rejoindre un ministère
                                </a>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>
        </section>

        <!-- 6. GALERIE PHOTOS -->
        <section id="gallery" class="py-4 py-md-5 bg-light rounded-4 px-3 px-md-4 my-4 my-md-5">
            <div class="text-center mb-4 mb-md-5">
                <h2 class="section-title d-inline-block">Notre Galerie</h2>
                <p class="text-muted mt-2">Quelques souvenirs de nos cultes et moments forts.</p>
            </div>
            <div class="row g-3 justify-content-center">
                @forelse($galleries as $index => $gallery)
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="gallery-item shadow-sm mb-2 mb-md-3">
                            <a href="{{ asset('storage/' . $gallery->image_path) }}" target="_blank">
                                <img src="{{ asset('storage/' . $gallery->image_path) }}" alt="{{ $gallery->title }}" class="gallery-img">
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-12 col-md-8 col-lg-6">
                        <div class="empty-state-card text-center bg-white shadow-sm p-4 p-md-5 rounded-4">
                            <div class="empty-icon-circle bg-info-subtle text-info mx-auto">
                                <i class="ri-camera-lens-line"></i>
                            </div>
                            <span class="badge bg-info-subtle text-info rounded-pill px-3 py-1 mb-2 fs-11 fw-semibold">Album photos</span>
                            <h5 class="fw-bold text-dark mb-2">Les souvenirs photographiques arrivent bientôt</h5>
                            <p class="text-muted fs-14 mb-0">
                                Les clichés des derniers cultes, cérémonies de baptêmes et célébrations de {{ $currentChurch->name }} seront bientôt partagés ici.
                            </p>
                        </div>
                    </div>
                @endforelse
            </div>
            
            @if($galleries->hasPages())
                <div class="mt-4 d-flex justify-content-center">
                    {{ $galleries->fragment('gallery')->links() }}
                </div>
            @endif
        </section>

        <!-- 7. ACTUALITES / EVENEMENTS -->
        <section id="events" class="py-4 py-md-5">
            <div class="text-center mb-4 mb-md-5">
                <h2 class="section-title d-inline-block">Cultes & Prochains Événements</h2>
                <p class="text-muted mt-2">Ne manquez pas les prochaines rencontres de {{ $currentChurch->name }}.</p>
            </div>
            
            <div class="row g-3 g-md-4 justify-content-center">
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
                                    <span class="text-muted small"><i class="ri-map-pin-line"></i> {{ $activity->location ?? ($currentChurch->city ?? 'Église') }}</span>
                                </div>
                                <h5 class="card-title fw-bold">{{ $activity->title }}</h5>
                                <p class="card-text text-muted small text-truncate-2">{{ Str::limit(strip_tags($activity->description), 100) }}</p>
                            </div>
                            <div class="card-footer bg-transparent border-0 pb-3 px-3 px-md-4">
                                <a href="{{ route('login') }}" class="btn btn-primary w-100 rounded-pill">Participer / S'inscrire</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 col-lg-10">
                        <div class="empty-state-card bg-white shadow-sm p-4 p-md-5 rounded-4 text-center">
                            <div class="empty-icon-circle bg-primary-subtle text-primary mx-auto">
                                <i class="ri-calendar-check-line"></i>
                            </div>
                            <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1 mb-2 fs-11 fw-semibold">Programme Régulier</span>
                            <h4 class="fw-bold text-dark mb-2">Rencontres &amp; Cultes Hebdomadaires</h4>
                            <p class="text-muted fs-14 mb-4 mx-auto" style="max-width: 600px;">
                                Aucun événement ponctuel spécial n'est inscrit au calendrier pour les prochains jours, mais <strong>notre communauté vous accueille chaque semaine</strong> avec joie pour ses rendez-vous d'édification.
                            </p>

                            <div class="row g-3 text-start justify-content-center mb-4">
                                <div class="col-12 col-md-4">
                                    <div class="card h-100 border border-primary-subtle rounded-3 p-3 shadow-none bg-primary-subtle bg-opacity-10">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <span class="badge bg-primary text-white rounded-pill px-2 py-1 fs-11">Dimanche</span>
                                            <span class="fw-bold fs-13 text-dark">Culte d'Adoration</span>
                                        </div>
                                        <p class="text-muted fs-12 mb-0">Louange vivante, prière &amp; enseignement de la Parole pour toute la famille.</p>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="card h-100 border border-success-subtle rounded-3 p-3 shadow-none bg-success-subtle bg-opacity-10">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <span class="badge bg-success text-white rounded-pill px-2 py-1 fs-11">En semaine</span>
                                            <span class="fw-bold fs-13 text-dark">Étude Biblique</span>
                                        </div>
                                        <p class="text-muted fs-12 mb-0">Approfondissement des Écritures et partage fraternel interactif.</p>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="card h-100 border border-warning-subtle rounded-3 p-3 shadow-none bg-warning-subtle bg-opacity-10">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <span class="badge bg-warning text-dark rounded-pill px-2 py-1 fs-11">Prière</span>
                                            <span class="fw-bold fs-13 text-dark">Réunion d'Intercession</span>
                                        </div>
                                        <p class="text-muted fs-12 mb-0">Temps fort de consécration, prière pour les familles et la nation.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex flex-wrap justify-content-center gap-2">
                                <a href="#contact" class="btn btn-primary rounded-pill px-4">
                                    <i class="ri-map-pin-line me-1"></i> Nous rendre visite / Renseignements
                                </a>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>
        </section>

        <!-- 8. APPEL A L'ACTION -->
        <section class="cta-section shadow-lg">
            <div class="container px-3">
                <h2 class="display-5 fw-bold mb-3 mb-md-4">Rejoignez {{ $currentChurch->name }} !</h2>
                <p class="lead mb-4 mb-md-5 mx-auto fs-15 fs-md-18" style="max-width: 700px;">
                    Vous souhaitez faire partie de notre communauté, participer à nos activités et grandir spirituellement avec nous ?
                </p>
                <div class="d-inline-flex align-items-center bg-body-secondary rounded-pill p-2 pe-3 pe-md-4 shadow-sm phone-contact-pill text-start" style="cursor: pointer; max-width: 100%;" onclick="window.location.href='tel:{{ str_replace(' ', '', $adminPhone) }}'">
                    <div class="avatar-sm me-2 me-md-3 flex-shrink-0">
                        <div class="avatar-title bg-primary rounded-circle">
                            <i class="ri-phone-fill fs-18 fs-md-20"></i>
                        </div>
                    </div>
                    <div class="text-start overflow-hidden">
                        <span class="d-block text-muted small fw-medium text-truncate">Contactez le secrétariat pastoral :</span>
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
                    <h4 class="text-white fw-bold mb-3">{{ $currentChurch->name }}</h4>
                    <p class="text-muted pe-4">
                        Une communauté chrétienne dynamique et engagée, unie par la foi et le désir de grandir ensemble.
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
                    <p class="text-muted mb-2"><i class="ri-map-pin-line me-2"></i> {{ $currentChurch->address ?? ($currentChurch->city ?? 'Bénin') }}</p>
                    <p class="text-muted mb-2"><i class="ri-phone-line me-2"></i> {{ $adminPhone }}</p>
                    <p class="text-muted mb-0"><i class="ri-mail-line me-2"></i> {{ $currentChurch->email ?? 'contact@mevoici.org' }}</p>
                </div>
            </div>
            <hr class="border-secondary my-4">
            <div class="text-center text-muted">
                <p class="mb-0">&copy; {{ date('Y') }} {{ $currentChurch->name }} • Propulsé par {{ config('app.name') }}. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <!-- MODAL SÉLECTEUR D'ÉGLISE -->
    @if(isset($allChurches) && $allChurches->count() > 1)
        <div class="modal fade" id="switchChurchModal" tabindex="-1" aria-labelledby="switchChurchModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="modal-header border-0 bg-primary text-white p-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar-sm bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center text-white shadow-sm">
                                <i class="bx bxs-church fs-24"></i>
                            </div>
                            <div>
                                <h5 class="modal-title fw-bold text-white mb-0" id="switchChurchModalLabel">Trouver ou changer d'église</h5>
                                <span class="fs-13 text-white-50">Sélectionnez la paroisse ou communauté que vous souhaitez visiter</span>
                            </div>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body p-4 bg-body-tertiary">
                        <!-- Recherche instantanée -->
                        <div class="position-relative mb-4">
                            <i class="ri-search-line position-absolute top-50 start-0 translate-middle-y ms-3 text-muted fs-18"></i>
                            <input type="text" id="churchSearchInput" class="form-control form-control-lg ps-5 rounded-pill border-light-subtle shadow-sm fs-14" placeholder="Rechercher une église par son nom ou sa ville (ex: Minontin, Zogbo, Cotonou)...">
                        </div>

                        <!-- Liste des églises -->
                        <div class="row g-3" id="churchGridList" style="max-height: 400px; overflow-y: auto;">
                            @foreach($allChurches as $ch)
                                <div class="col-md-6 church-item-card" data-name="{{ strtolower($ch->name) }}" data-city="{{ strtolower($ch->city ?? '') }}">
                                    <div class="card h-100 border-0 shadow-sm rounded-3 p-3 {{ $ch->id === $currentChurch->id ? 'border border-primary border-2 bg-primary-subtle' : 'bg-white' }}">
                                        <div class="d-flex align-items-center justify-content-between gap-3">
                                            <div class="d-flex align-items-center gap-3 overflow-hidden">
                                                <img src="{{ $ch->logo_url }}" alt="{{ $ch->name }}" class="rounded-circle object-fit-cover border shadow-sm flex-shrink-0" style="width: 46px; height: 46px; min-width: 46px;">
                                                <div class="overflow-hidden">
                                                    <h6 class="fw-bold mb-0 text-body text-truncate fs-14">{{ $ch->name }}</h6>
                                                    <span class="fs-12 text-muted d-block text-truncate"><i class="ri-map-pin-line me-1 text-primary"></i>{{ $ch->city ?: 'Bénin' }}</span>
                                                </div>
                                            </div>

                                            @if($ch->id === $currentChurch->id)
                                                <span class="badge bg-primary rounded-pill px-3 py-2 fs-11">Actuelle</span>
                                            @else
                                                <a href="{{ route('church.landing', $ch->slug) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fs-12 flex-shrink-0">
                                                    Visiter <i class="ri-arrow-right-line ms-1"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div id="noChurchFound" class="text-center py-4 d-none">
                            <i class="ri-search-eye-line fs-36 text-muted mb-2 opacity-50"></i>
                            <p class="text-muted fs-13 mb-0">Aucune église ne correspond à votre recherche.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

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

        // Filtrage dynamique des églises dans la modal
        const churchSearchInput = document.getElementById('churchSearchInput');
        if (churchSearchInput) {
            churchSearchInput.addEventListener('input', function() {
                const query = this.value.toLowerCase().trim();
                const cards = document.querySelectorAll('.church-item-card');
                let foundCount = 0;

                cards.forEach(card => {
                    const name = card.getAttribute('data-name') || '';
                    const city = card.getAttribute('data-city') || '';
                    if (name.includes(query) || city.includes(query)) {
                        card.classList.remove('d-none');
                        foundCount++;
                    } else {
                        card.classList.add('d-none');
                    }
                });

                const noChurchFound = document.getElementById('noChurchFound');
                if (noChurchFound) {
                    if (foundCount === 0) {
                        noChurchFound.classList.remove('d-none');
                    } else {
                        noChurchFound.classList.add('d-none');
                    }
                }
            });
        }

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
