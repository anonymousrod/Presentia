<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} | Plateforme EBER</title>

    <!-- Google Fonts: Outfit & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;800;1,400&display=swap" rel="stylesheet">
    
    <!-- FontAwesome for Premium Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Premium CSS Styles -->
    <style>
        :root {
            --bg-primary: #0a0b10;
            --text-primary: #f3f4f6;
            --text-secondary: #9ca3af;
            --accent-glow: rgba(99, 102, 241, 0.15);
            --gradient-1: linear-gradient(135deg, #6366f1 0%, #a855f7 50%, #ec4899 100%);
            --gradient-glow: linear-gradient(135deg, rgba(99, 102, 241, 0.5) 0%, rgba(168, 85, 247, 0.5) 50%, rgba(236, 72, 153, 0.5) 100%);
            --card-bg: rgba(17, 18, 28, 0.65);
            --card-border: rgba(255, 255, 255, 0.08);
            --font-display: 'Outfit', sans-serif;
            --font-sans: 'Plus Jakarta Sans', sans-serif;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: var(--bg-primary);
            color: var(--text-primary);
            font-family: var(--font-sans);
            min-height: 100vh;
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        /* Ambient Glowing Background Orbs */
        .glow-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(140px);
            z-index: 1;
            pointer-events: none;
            opacity: 0.4;
        }

        .orb-1 {
            width: 400px;
            height: 400px;
            background: #6366f1;
            top: -10%;
            left: -10%;
            animation: float 20s infinite alternate;
        }

        .orb-2 {
            width: 500px;
            height: 500px;
            background: #ec4899;
            bottom: -10%;
            right: -10%;
            animation: float 25s infinite alternate-reverse;
        }

        @keyframes float {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(50px, 50px) scale(1.1); }
        }

        /* Header Navigation Styles */
        header {
            position: relative;
            z-index: 10;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 2rem 4rem;
            max-width: 1400px;
            width: 100%;
            margin: 0 auto;
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
        }

        .logo-icon {
            font-size: 2rem;
            background: var(--gradient-1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: spin-slow 8s linear infinite;
        }

        .logo-text {
            font-family: var(--font-display);
            font-size: 1.75rem;
            font-weight: 800;
            letter-spacing: -0.05em;
            color: #fff;
        }

        .logo-text span {
            background: var(--gradient-1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        nav {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .nav-link {
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
            padding: 0.5rem 0.25rem;
        }

        .nav-link:hover {
            color: #fff;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--gradient-1);
            transition: width 0.3s ease;
        }

        .nav-link:hover::after {
            width: 100%;
        }

        /* Main Hero Section */
        main {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            position: relative;
            z-index: 5;
            padding: 4rem 2rem;
            max-width: 1200px;
            width: 100%;
            margin: 0 auto;
            text-align: center;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            padding: 0.6rem 1.2rem;
            border-radius: 9999px;
            font-size: 0.85rem;
            font-weight: 600;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: #a855f7;
            margin-bottom: 2rem;
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        .hero-badge:hover {
            border-color: rgba(168, 85, 247, 0.4);
            transform: translateY(-2px);
        }

        .hero-badge i {
            font-size: 0.75rem;
            color: #ec4899;
        }

        h1.hero-title {
            font-family: var(--font-display);
            font-size: 4.5rem;
            font-weight: 800;
            line-height: 1.1;
            letter-spacing: -0.03em;
            color: #fff;
            margin-bottom: 1.5rem;
            max-width: 900px;
        }

        h1.hero-title span {
            background: var(--gradient-1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            position: relative;
        }

        .hero-desc {
            font-size: 1.25rem;
            color: var(--text-secondary);
            max-width: 650px;
            line-height: 1.6;
            margin-bottom: 3rem;
        }

        /* Action Buttons */
        .cta-container {
            display: flex;
            gap: 1.5rem;
            margin-bottom: 4rem;
            flex-wrap: wrap;
            justify-content: center;
        }

        .btn-premium {
            text-decoration: none;
            padding: 1.1rem 2.2rem;
            font-size: 1.05rem;
            font-weight: 700;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            z-index: 1;
            cursor: pointer;
        }

        .btn-primary-gradient {
            background: var(--gradient-1);
            color: #fff;
            box-shadow: 0 4px 30px rgba(99, 102, 241, 0.4);
        }

        .btn-primary-gradient:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 40px rgba(99, 102, 241, 0.6);
        }

        .btn-primary-gradient::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--gradient-glow);
            z-index: -1;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .btn-primary-gradient:hover::before {
            opacity: 1;
        }

        .btn-secondary-outline {
            background: rgba(255, 255, 255, 0.02);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(10px);
        }

        .btn-secondary-outline:hover {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.3);
            transform: translateY(-3px);
        }

        /* Glassmorphism Feature Grid */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
            width: 100%;
            margin-top: 2rem;
        }

        .feature-card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 20px;
            padding: 2.5rem;
            text-align: left;
            backdrop-filter: blur(20px);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.03) 0%, transparent 100%);
            pointer-events: none;
        }

        .feature-card:hover {
            transform: translateY(-8px);
            border-color: rgba(99, 102, 241, 0.3);
            box-shadow: 0 20px 45px rgba(99, 102, 241, 0.15);
        }

        .feature-icon-wrapper {
            width: 60px;
            height: 60px;
            border-radius: 14px;
            background: rgba(99, 102, 241, 0.1);
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 1.75rem;
            border: 1px solid rgba(99, 102, 241, 0.2);
            transition: all 0.3s ease;
        }

        .feature-card:hover .feature-icon-wrapper {
            background: var(--gradient-1);
            border-color: transparent;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
        }

        .feature-icon-wrapper i {
            font-size: 1.5rem;
            color: #6366f1;
            transition: all 0.3s ease;
        }

        .feature-card:hover .feature-icon-wrapper i {
            color: #fff;
        }

        .feature-title {
            font-family: var(--font-display);
            font-size: 1.4rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 0.75rem;
        }

        .feature-desc {
            font-size: 0.95rem;
            color: var(--text-secondary);
            line-height: 1.5;
        }

        /* Footer */
        footer {
            position: relative;
            z-index: 10;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            padding: 2.5rem;
            text-align: center;
            color: var(--text-secondary);
            font-size: 0.9rem;
            width: 100%;
        }

        footer span {
            color: #fff;
            font-weight: 600;
        }

        footer i {
            color: #ec4899;
            margin: 0 0.25rem;
        }

        /* Animations */
        @keyframes spin-slow {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Responsive Design Rules */
        @media (max-width: 1024px) {
            .features-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            h1.hero-title {
                font-size: 3.5rem;
            }
        }

        @media (max-width: 768px) {
            header {
                padding: 1.5rem 2rem;
                flex-direction: column;
                gap: 1.5rem;
            }
            .features-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }
            h1.hero-title {
                font-size: 2.75rem;
            }
            .cta-container {
                flex-direction: column;
                width: 100%;
                max-width: 320px;
            }
            .btn-premium {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <!-- Ambient Glowing Orbs -->
    <div class="glow-orb orb-1"></div>
    <div class="glow-orb orb-2"></div>

    <!-- Header Navigation -->
    <header>
        <a href="{{ url('/') }}" class="logo-container">
            <i class="fa-solid fa-cube logo-icon"></i>
            <span class="logo-text">{{ config('app.name') }}</span>
        </a>
        <nav>
            @auth
                <a href="{{ route('dashboard') }}" class="nav-link">Tableau de bord</a>
            @else
                <a href="{{ route('login') }}" class="nav-link">Connexion</a>
            @endauth
        </nav>
    </header>

    <!-- Main Hero Content -->
    <main>
        <div class="hero-badge">
            <i class="fa-solid fa-sparkles"></i> Nouveau : Gestion EBER Simplifiée
        </div>
        <h1 class="hero-title">Optimisez le suivi et l'<span>engagement</span> de vos équipes</h1>
        <p class="hero-desc">La plateforme moderne et intuitive conçue pour la gestion des présences, la planification des activités et le suivi de l'engagement des jeunes pour le projet EBER.</p>

        <div class="cta-container">
            @auth
                <a href="{{ route('dashboard') }}" class="btn-premium btn-primary-gradient">
                    <i class="fa-solid fa-gauge-high"></i> Accéder au Tableau de Bord
                </a>
            @else
                <a href="{{ route('login') }}" class="btn-premium btn-primary-gradient">
                    <i class="fa-solid fa-right-to-bracket"></i> Se connecter
                </a>
            @endauth
            <a href="#features" class="btn-premium btn-secondary-outline">
                <i class="fa-solid fa-compass"></i> Découvrir les fonctionnalités
            </a>
        </div>

        <!-- Glassmorphism Features Grid -->
        <div class="features-grid" id="features">
            <!-- Feature 1 -->
            <div class="feature-card">
                <div class="feature-icon-wrapper">
                    <i class="fa-solid fa-users"></i>
                </div>
                <h3 class="feature-title">Gestion de Groupes</h3>
                <p class="feature-desc">Structurez vos membres par catégories et assignez des leaders de groupe pour une coordination fluide et décentralisée.</p>
            </div>

            <!-- Feature 2 -->
            <div class="feature-card">
                <div class="feature-icon-wrapper">
                    <i class="fa-solid fa-calendar-check"></i>
                </div>
                <h3 class="feature-title">Suivi des Présences</h3>
                <p class="feature-desc">Enregistrez et analysez la participation des jeunes aux différentes activités en temps réel avec des indicateurs clés d'assiduité.</p>
            </div>

            <!-- Feature 3 -->
            <div class="feature-card">
                <div class="feature-icon-wrapper">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <h3 class="feature-title">Sécurité & Rôles</h3>
                <p class="feature-desc">Contrôle d'accès robuste basé sur des rôles et permissions personnalisés (Spatie), assurant la protection totale des données.</p>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer>
        <p>&copy; <script>document.write(new Date().getFullYear())</script> <span>{{ config('app.name') }}</span>. Fait avec <i class="fa-solid fa-heart"></i> pour EBER.</p>
    </footer>
</body>
</html>
