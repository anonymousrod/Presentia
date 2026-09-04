<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-layout="horizontal" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $currentChurch->name ?? 'Accueil' }} | {{ config('app.name') }}</title>

    <!-- PWA Manifest & Meta Tags -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#405189">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="MeVoici">
    <link rel="apple-touch-icon" href="/assets/images/icons/apple-touch-icon.png">

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ $appSettings->favicon_url ?? asset('assets/images/favicon.ico') }}">

    <!-- PWA Service Worker & Installer -->
    <script src="{{ asset('assets/js/pwa-installer.js') }}" defer></script>

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
        /* ═══════════════════════════════════════════
           GLOBAL ENHANCEMENTS & SMOOTH BEHAVIOR
           ═══════════════════════════════════════════ */
        html {
            scroll-behavior: smooth;
        }
        body {
            overflow-x: hidden;
        }

        /* Reading / Scroll progress bar at very top */
        #scrollProgressBar {
            position: fixed;
            top: 0;
            left: 0;
            height: 3px;
            width: 0%;
            background: linear-gradient(90deg, #f1c40f 0%, #38bdf8 50%, #405189 100%);
            z-index: 9999;
            transition: width 0.1s ease-out;
        }

        /* ═══════════════════════════════════════════
           SCROLL REVEAL ANIMATIONS
           ═══════════════════════════════════════════ */
        .reveal-on-scroll {
            opacity: 0;
            transform: translateY(35px);
            transition: opacity 1.2s cubic-bezier(0.16, 1, 0.3, 1), transform 1.2s cubic-bezier(0.16, 1, 0.3, 1);
            will-change: opacity, transform;
        }
        .reveal-on-scroll.is-revealed {
            opacity: 1;
            transform: translateY(0);
        }
        .reveal-delay-1 { transition-delay: 0.15s; }
        .reveal-delay-2 { transition-delay: 0.3s; }
        .reveal-delay-3 { transition-delay: 0.45s; }
        .reveal-delay-4 { transition-delay: 0.6s; }

        /* Floating Back to Top Button */
        .back-to-top {
            position: fixed;
            bottom: 28px;
            right: 28px;
            width: 46px;
            height: 46px;
            border-radius: 50%;
            background: #2e3f7c;
            color: #ffffff;
            border: 2px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            cursor: pointer;
            z-index: 995;
            opacity: 0;
            visibility: hidden;
            transform: translateY(16px);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            text-decoration: none;
        }
        .back-to-top.is-shown {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        .back-to-top:hover {
            background: #f1c40f;
            color: #1e293b;
            transform: translateY(-4px) scale(1.05);
            box-shadow: 0 12px 30px rgba(241, 196, 15, 0.45);
        }

        /* ═══════════════════════════════════════════
           HERO SECTION
           ═══════════════════════════════════════════ */
        .hero-section {
            position: relative;
            padding: 160px 0 110px;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            color: #fff;
            overflow: hidden;
        }
        .hero-overlay {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(to right, rgba(0, 0, 0, 0.85), rgba(0, 0, 0, 0.5));
        }
        .hero-content {
            position: relative;
            z-index: 1;
        }
        .hero-text-container {
            background: rgba(0, 0, 0, 0.05);
            padding: 42px;
            border-radius: 24px;
            backdrop-filter: blur(2px);
            -webkit-backdrop-filter: blur(2px);
            border: 1px solid rgba(255, 255, 255, 0.14);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
            animation: heroFadeIn 0.9s cubic-bezier(0.16, 1, 0.3, 1);
        }
        @keyframes heroFadeIn {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .hero-badge-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(241, 196, 15, 0.2);
            color: #f1c40f;
            border: 1px solid rgba(241, 196, 15, 0.35);
            font-weight: 700;
            padding: 7px 16px;
            border-radius: 50px;
            margin-bottom: 20px;
            font-size: 13px;
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .hero-btn-primary {
            background: linear-gradient(135deg, #405189 0%, #2e3f7c 100%);
            color: #ffffff;
            border: none;
            box-shadow: 0 8px 24px rgba(46, 63, 124, 0.4);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .hero-btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 14px 32px rgba(46, 63, 124, 0.55);
            color: #ffffff;
        }
        .hero-btn-outline {
            background: rgba(255, 255, 255, 0.1);
            border: 1.5px solid rgba(255, 255, 255, 0.5);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            color: #ffffff;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .hero-btn-outline:hover {
            background: rgba(255, 255, 255, 0.25);
            border-color: #ffffff;
            color: #ffffff;
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }
        .hero-scroll-indicator {
            animation: bounceIndicator 2.2s infinite ease-in-out;
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: color 0.3s ease;
        }
        .hero-scroll-indicator:hover {
            color: #f1c40f;
        }
        @keyframes bounceIndicator {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-8px); }
            60% { transform: translateY(-4px); }
        }

        /* ═══════════════════════════════════════════
           SECTION HEADINGS DESIGN SYSTEM
           ═══════════════════════════════════════════ */
        .section-header {
            margin-bottom: 2.8rem;
        }
        .section-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(64, 81, 137, 0.08);
            color: #405189;
            border: 1px solid rgba(64, 81, 137, 0.2);
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 12px;
        }
        .section-title {
            font-weight: 800;
            font-size: clamp(1.75rem, 3.2vw, 2.35rem);
            color: #1e293b;
            letter-spacing: -0.5px;
            margin-bottom: 0.6rem;
            position: relative;
            padding-bottom: 14px;
        }
        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 55px;
            height: 4px;
            border-radius: 4px;
            background: linear-gradient(90deg, #405189, #f1c40f);
        }
        .text-center .section-title::after {
            left: 50%;
            transform: translateX(-50%);
        }
        .section-subtitle {
            font-size: clamp(0.95rem, 1.6vw, 1.05rem);
            color: #64748b;
            max-width: 620px;
            margin: 0 auto;
            line-height: 1.6;
        }
        
        /* ═══════════════════════════════════════════
           LANDING PAGE NAVBAR — RESPONSIVE PROFESSIONNEL
           ═══════════════════════════════════════════ */

        /* --- Base navbar --- */
        .navbar-landing {
            padding: 14px 0;
            transition: background-color 0.35s ease, box-shadow 0.35s ease, padding 0.3s ease;
            background-color: transparent;
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            width: 100%;
            z-index: 1000;
        }
        .navbar-landing.is-sticky {
            position: fixed;
            background-color: #2e3f7c;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            padding: 10px 0;
            box-shadow: 0 4px 20px rgba(0,0,0,0.25);
            animation: navbarSlideDown 0.4s ease;
        }
        @keyframes navbarSlideDown {
            from { transform: translateY(-100%); opacity: 0; }
            to   { transform: translateY(0);     opacity: 1; }
        }
        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-10px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* --- Brand / Logo --- */
        .navbar-landing .brand-link {
            display: flex;
            align-items: center;
            text-decoration: none;
            gap: 0;
            min-width: 0;
            flex-shrink: 1;
            overflow: hidden;
        }
        .navbar-landing .brand-logo {
            height: 34px;
            max-height: 36px;
            max-width: 130px;
            width: auto;
            object-fit: contain;
            flex-shrink: 0;
            transition: height 0.3s ease;
            display: block;
        }
        .navbar-landing .brand-text-wrapper {
            display: flex;
            flex-direction: column;
            text-align: left;
            padding-left: 10px;
            margin-left: 8px;
            border-left: 1px solid rgba(255,255,255,0.3);
            min-width: 0;
            overflow: hidden;
        }
        .navbar-landing .brand-name {
            font-size: 13px;
            font-weight: 700;
            color: #fff;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.2;
            max-width: 180px;
        }
        .navbar-landing .brand-city {
            font-size: 11px;
            color: rgba(255,255,255,0.6);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-top: 2px;
        }

        /* --- Nav links (desktop) --- */
        .navbar-landing .nav-link {
            color: rgba(255,255,255,0.88);
            font-weight: 500;
            padding: 6px 14px !important;
            border-radius: 8px;
            transition: color 0.25s ease, background-color 0.25s ease;
        }
        .navbar-landing .nav-link:hover,
        .navbar-landing .nav-link:focus,
        .navbar-landing .nav-link.active {
            color: #f1c40f !important;
            background-color: rgba(255,255,255,0.08);
        }

        /* --- Right actions zone (switcher + toggler) --- */
        .navbar-landing .nav-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-shrink: 0;
        }

        /* --- Church switcher button --- */
        .church-switcher-btn {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: rgba(255,255,255,0.14);
            border: 1px solid rgba(255,255,255,0.28);
            color: #fff;
            border-radius: 50px;
            padding: 5px 12px;
            font-size: 12px;
            font-weight: 500;
            transition: background 0.25s ease, border-color 0.25s ease;
            white-space: nowrap;
        }
        .church-switcher-btn:hover {
            background: rgba(255,255,255,0.25);
            border-color: rgba(255,255,255,0.5);
            color: #fff;
        }

        /* --- Hamburger toggler --- */
        .navbar-landing .nav-toggler {
            border: none;
            background: rgba(255,255,255,0.12);
            color: #fff;
            border-radius: 10px;
            padding: 7px 10px;
            transition: background 0.2s ease;
            cursor: pointer;
            line-height: 1;
            align-items: center;
            justify-content: center;
        }
        .navbar-landing .nav-toggler:hover {
            background: rgba(255,255,255,0.22);
        }
        .navbar-landing .nav-toggler i {
            font-size: 22px;
            display: block;
        }

        /* --- CTA Button (Se connecter / Mon Espace) --- */
        .nav-cta-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #fff;
            color: #2e3f7c;
            border: none;
            border-radius: 50px;
            padding: 8px 20px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            box-shadow: 0 3px 12px rgba(0,0,0,0.18);
        }
        .nav-cta-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.22);
            color: #2e3f7c;
        }

        /* ─── MOBILE BREAKPOINTS ─── */
        @media (max-width: 991.98px) {
            /* Navbar always fixed & filled on mobile */
            .navbar-landing {
                position: fixed !important;
                background-color: #2e3f7c !important;
                backdrop-filter: blur(10px);
                -webkit-backdrop-filter: blur(10px);
                box-shadow: 0 4px 20px rgba(0,0,0,0.2);
                padding: 10px 0;
            }
            /* Mobile dropdown panel */
            #mobileMenu {
                position: absolute;
                top: 100%;
                left: 12px;
                right: 12px;
                background: rgba(20, 30, 65, 0.97);
                backdrop-filter: blur(18px);
                -webkit-backdrop-filter: blur(18px);
                border-radius: 18px;
                padding: 12px 8px 16px;
                border: 1px solid rgba(255,255,255,0.1);
                box-shadow: 0 16px 48px rgba(0,0,0,0.45);
                z-index: 999;
                animation: fadeInDown 0.2s ease;
            }
            #mobileMenu .nav-link {
                padding: 11px 16px !important;
                font-size: 15px;
                border-radius: 10px;
                display: flex;
                align-items: center;
                gap: 8px;
            }
            #mobileMenu .nav-link:hover {
                background-color: rgba(255,255,255,0.1) !important;
            }
            .mobile-nav-divider {
                height: 1px;
                background: rgba(255,255,255,0.1);
                margin: 8px 16px;
            }
            .nav-cta-mobile {
                display: flex !important;
                width: calc(100% - 16px);
                margin: 4px 8px 0;
                text-align: center;
                justify-content: center;
                padding: 11px 20px !important;
                border-radius: 12px !important;
            }
        }
        @media (max-width: 575.98px) {
            .navbar-landing {
                padding: 8px 0;
            }
            .navbar-landing .brand-logo {
                height: 26px;
                max-width: 95px;
            }
            .navbar-landing .brand-name {
                font-size: 12px;
                max-width: 130px;
            }
            .navbar-landing .brand-city {
                font-size: 10px;
            }
            .church-switcher-btn .switcher-label,
            .church-switcher-btn .switcher-arrow {
                display: none;
            }
            .church-switcher-btn {
                padding: 6px 8px;
                border-radius: 10px;
            }
        }
        @media (max-width: 380px) {
            .navbar-landing .brand-logo {
                height: 22px;
                max-width: 75px;
            }
            .navbar-landing .brand-name {
                max-width: 100px;
                font-size: 11px;
            }
            .navbar-landing .brand-city {
                display: none;
            }
        }

        /* ═══════════════════════════════════════════
           ABOUT SECTION (QUI SOMMES-NOUS ?)
           ═══════════════════════════════════════════ */
        .about-img-container {
            position: relative;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.1);
        }
        .about-main-img {
            width: 100%;
            height: 420px;
            object-fit: cover;
            transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .about-img-container:hover .about-main-img {
            transform: scale(1.04);
        }
        .about-badge-float {
            position: absolute;
            bottom: 20px;
            right: 20px;
            background: rgba(255, 255, 255, 0.94);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            padding: 12px 18px;
            border-radius: 18px;
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.8);
            display: flex;
            align-items: center;
            gap: 12px;
            z-index: 2;
            transition: transform 0.3s ease;
        }
        .about-badge-float:hover {
            transform: translateY(-3px);
        }
        .about-badge-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: rgba(241, 196, 15, 0.2);
            color: #d4a017;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }
        .about-feature-card {
            background: #ffffff;
            border: 1px solid rgba(0, 0, 0, 0.06);
            border-radius: 18px;
            padding: 20px 22px;
            margin-bottom: 16px;
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
            display: flex;
            gap: 18px;
            align-items: flex-start;
        }
        .about-feature-card:hover {
            transform: translateX(8px);
            box-shadow: 0 14px 32px rgba(0, 0, 0, 0.07);
            border-color: rgba(64, 81, 137, 0.25);
        }
        .about-icon-box {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
            transition: transform 0.3s ease;
        }
        .about-feature-card:hover .about-icon-box {
            transform: scale(1.1);
        }

        /* ═══════════════════════════════════════════
           STATS SECTION (CHIFFRES CLES)
           ═══════════════════════════════════════════ */
        .stats-section {
            background: radial-gradient(circle at 10% 20%, #202b52 0%, #151c36 90%);
            padding: 70px 0;
            position: relative;
            overflow: hidden;
            border-radius: 28px !important;
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 20px 50px rgba(10, 15, 35, 0.35);
        }
        .stats-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(241, 196, 15, 0.12) 0%, transparent 70%);
            pointer-events: none;
        }
        .stat-box {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 20px;
            padding: 32px 18px;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            position: relative;
        }
        .stat-box:hover {
            transform: translateY(-8px);
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(255, 255, 255, 0.25);
            box-shadow: 0 16px 36px rgba(0, 0, 0, 0.4);
        }
        .stat-icon-wrap {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            color: #f1c40f;
            margin-bottom: 12px;
            transition: transform 0.3s ease;
        }
        .stat-box:hover .stat-icon-wrap {
            transform: scale(1.15) rotate(5deg);
        }
        .stat-box h2 {
            font-weight: 800;
            color: #ffffff;
            font-size: clamp(2.2rem, 3.8vw, 3.2rem);
            margin-bottom: 6px;
            line-height: 1;
            text-shadow: 0 2px 12px rgba(0, 0, 0, 0.25);
        }
        .stat-box p {
            color: rgba(255, 255, 255, 0.85);
            font-size: 0.82rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
            margin-bottom: 0;
        }

        /* ═══════════════════════════════════════════
           ORGANIZATION / LEADERS SECTION
           ═══════════════════════════════════════════ */
        .org-card {
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
            border: 1px solid rgba(0, 0, 0, 0.06);
            border-radius: 20px;
            overflow: hidden;
            background: #ffffff;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.04);
        }
        .org-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 18px 36px rgba(0, 0, 0, 0.12);
            border-color: rgba(64, 81, 137, 0.25);
        }
        .org-img-wrapper {
            height: 230px;
            position: relative;
            overflow: hidden;
            background: #f1f5f9;
        }
        .org-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .org-card:hover .org-img {
            transform: scale(1.08);
        }
        .org-overlay-gradient {
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(15, 23, 42, 0.4) 0%, transparent 60%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .org-card:hover .org-overlay-gradient {
            opacity: 1;
        }

        /* ═══════════════════════════════════════════
           GROUPS & MINISTRIES SECTION
           ═══════════════════════════════════════════ */
        .group-card {
            border-radius: 20px;
            border: 1px solid rgba(0, 0, 0, 0.06);
            background: #ffffff;
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
            height: 100%;
            overflow: hidden;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.04);
        }
        .group-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 18px 38px rgba(0, 0, 0, 0.1);
        }

        /* ═══════════════════════════════════════════
           GALLERY SECTION
           ═══════════════════════════════════════════ */
        .gallery-item {
            position: relative;
            overflow: hidden;
            border-radius: 18px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.06);
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
            height: 240px;
        }
        .gallery-item:hover {
            transform: translateY(-6px);
            box-shadow: 0 16px 36px rgba(0, 0, 0, 0.15);
        }
        .gallery-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .gallery-item:hover .gallery-img {
            transform: scale(1.09);
        }
        .gallery-overlay {
            position: absolute;
            inset: 0;
            background: rgba(15, 23, 42, 0.55);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
            backdrop-filter: blur(2px);
        }
        .gallery-item:hover .gallery-overlay {
            opacity: 1;
        }
        .gallery-zoom-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: #ffffff;
            color: #1e293b;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            transform: scale(0.7);
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
        }
        .gallery-item:hover .gallery-zoom-icon {
            transform: scale(1);
        }

        /* ═══════════════════════════════════════════
           EVENTS / ACTIVITIES SECTION
           ═══════════════════════════════════════════ */
        .event-card {
            border: 1px solid rgba(0, 0, 0, 0.06);
            border-radius: 20px;
            overflow: hidden;
            background: #ffffff;
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.04);
            height: 100%;
        }
        .event-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 18px 38px rgba(0, 0, 0, 0.1);
            border-color: rgba(64, 81, 137, 0.2);
        }
        .event-date-badge {
            background: rgba(64, 81, 137, 0.1);
            color: #405189;
            font-weight: 700;
            padding: 5px 12px;
            border-radius: 50px;
            font-size: 12px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        /* ═══════════════════════════════════════════
           CTA SECTION (REJOIGNEZ-NOUS)
           ═══════════════════════════════════════════ */
        .cta-section {
            background: radial-gradient(circle at 80% 20%, #2f407b 0%, #151c36 100%);
            color: #ffffff !important;
            padding: 85px 24px;
            text-align: center;
            border-radius: 28px;
            margin: 50px 0;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.12);
            box-shadow: 0 24px 60px rgba(10, 15, 35, 0.35);
        }
        .cta-section::before {
            content: '';
            position: absolute;
            top: -60px;
            left: -60px;
            width: 250px;
            height: 250px;
            background: radial-gradient(circle, rgba(241, 196, 15, 0.15) 0%, transparent 70%);
            pointer-events: none;
        }
        .cta-section h2 {
            color: #ffffff !important;
            font-weight: 800;
            letter-spacing: -0.5px;
        }
        .cta-section p {
            color: rgba(255, 255, 255, 0.9) !important;
        }
        .phone-contact-pill {
            background: #ffffff !important;
            border-radius: 50px;
            padding: 8px 26px 8px 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            text-decoration: none;
        }
        .phone-contact-pill:hover {
            transform: translateY(-4px) scale(1.02);
            box-shadow: 0 18px 40px rgba(0, 0, 0, 0.3);
        }

        /* ═══════════════════════════════════════════
           FOOTER
           ═══════════════════════════════════════════ */
        .footer-custom {
            background-color: #0f1423;
            color: #94a3b8;
            padding: 70px 0 24px;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
        }
        .footer-custom h5 {
            color: #ffffff;
            font-weight: 700;
            margin-bottom: 20px;
            font-size: 15px;
            letter-spacing: 0.5px;
        }
        .footer-links a {
            color: #94a3b8;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 12px;
            transition: all 0.25s ease;
            font-size: 14px;
        }
        .footer-links a:hover {
            color: #f1c40f;
            transform: translateX(4px);
        }
        .footer-social-btn {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.08);
            color: #ffffff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(255, 255, 255, 0.15);
            transition: all 0.3s ease;
            text-decoration: none;
        }
        .footer-social-btn:hover {
            background: #f1c40f;
            color: #1e293b;
            border-color: #f1c40f;
            transform: translateY(-3px);
        }

        /* ═══════════════════════════════════════════
           EMPTY STATE CARDS
           ═══════════════════════════════════════════ */
        .empty-state-card {
            background: #ffffff;
            border-radius: 24px;
            border: 1.5px dashed rgba(64, 81, 137, 0.25);
            padding: 2.75rem 1.75rem;
            transition: all 0.3s ease;
            box-shadow: 0 6px 25px rgba(0, 0, 0, 0.03);
        }
        .empty-state-card:hover {
            border-color: rgba(64, 81, 137, 0.45);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.06);
            transform: translateY(-3px);
        }
        .empty-icon-circle {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin-bottom: 1.25rem;
        }

        /* ═══════════════════════════════════════════
           RESPONSIVE REFINEMENTS
           ═══════════════════════════════════════════ */
        @media (max-width: 991.98px) {
            .hero-section {
                padding: 120px 0 60px;
            }
            .hero-text-container {
                padding: 28px 22px;
                border-radius: 18px;
            }
            .hero-text-container h1 {
                font-size: 2.15rem !important;
                margin-bottom: 16px !important;
            }
            .hero-text-container p {
                font-size: 1.05rem !important;
                margin-bottom: 22px !important;
            }
            .about-main-img {
                height: 320px;
            }
            .about-badge-float {
                padding: 10px 14px;
                border-radius: 14px;
                bottom: 14px;
                right: 14px;
            }
            .stat-box {
                padding: 24px 12px;
                border-radius: 16px;
            }
            .stat-box h2 {
                font-size: 2.25rem;
            }
            .cta-section {
                padding: 50px 20px;
                margin: 30px 0;
                border-radius: 20px;
            }
            .gallery-item {
                height: 190px;
            }
        }

        @media (max-width: 575.98px) {
            .hero-section {
                padding: 100px 0 50px;
            }
            .hero-text-container {
                padding: 22px 16px;
                border-radius: 16px;
            }
            .hero-text-container h1 {
                font-size: 1.75rem !important;
            }
            .hero-text-container .btn {
                width: 100%;
            }
            .section-header {
                margin-bottom: 2rem;
            }
            .section-title {
                font-size: 1.55rem;
            }
            .about-main-img {
                height: 250px;
            }
            .about-feature-card {
                padding: 16px;
                gap: 14px;
            }
            .about-icon-box {
                width: 40px;
                height: 40px;
                font-size: 18px;
            }
            .stat-box {
                padding: 20px 8px;
            }
            .stat-box h2 {
                font-size: 1.85rem;
            }
            .stat-box p {
                font-size: 0.72rem;
            }
            .org-img-wrapper {
                height: 190px;
            }
            .gallery-item {
                height: 160px;
            }
            .phone-contact-pill {
                width: 100%;
                justify-content: center;
                text-align: center;
            }
            .back-to-top {
                bottom: 18px;
                right: 18px;
                width: 42px;
                height: 42px;
                font-size: 18px;
            }
        }
    </style>
</head>
<body>
    <!-- Scroll Progress Bar -->
    <div id="scrollProgressBar"></div>

    <!-- NAVBAR -->
    <nav class="navbar-landing" id="navbar" role="navigation" aria-label="Navigation principale">
        <div class="container d-flex align-items-center justify-content-between">

            {{-- ── BRAND / LOGO ── --}}
            <a class="brand-link" href="{{ route('church.landing', $currentChurch->slug) }}" aria-label="{{ $currentChurch->name }}">
                <img src="{{ $appSettings->logo_light_url ?? asset('assets/images/logo-light.png') }}"
                     alt="{{ $currentChurch->name }}"
                     class="brand-logo">
                <div class="brand-text-wrapper">
                    <span class="brand-name">{{ $currentChurch->name }}</span>
                    <span class="brand-city">{{ $currentChurch->city ?: config('app.name') }}</span>
                </div>
            </a>

            {{-- ── DESKTOP NAV — visible only lg+ via d-none d-lg-flex ── --}}
            <ul class="list-unstyled d-none d-lg-flex align-items-center mb-0 mx-3" style="gap:4px;">
                <li><a class="nav-link" href="#about"><i class="ri-information-line"></i> À propos</a></li>
                <li><a class="nav-link" href="#organization"><i class="ri-team-line"></i> Dirigeants</a></li>
                <li><a class="nav-link" href="#groups"><i class="ri-group-line"></i> Groupes</a></li>
                <li><a class="nav-link" href="#events"><i class="ri-calendar-event-line"></i> Cultes & Activités</a></li>
            </ul>

            {{-- ── RIGHT ACTIONS ── --}}
            <div class="nav-actions">
                {{-- Church switcher --}}
                @if(isset($allChurches) && $allChurches->count() > 1)
                    <button type="button"
                            class="church-switcher-btn"
                            data-bs-toggle="modal"
                            data-bs-target="#switchChurchModal"
                            title="Changer d'église"
                            aria-label="Changer d'église">
                        <i class="ri-map-pin-2-line text-warning" style="font-size:14px;flex-shrink:0;"></i>
                        <span class="switcher-label">{{ Str::limit($currentChurch->city ?: "Changer d'église", 16) }}</span>
                        <i class="ri-arrow-down-s-line switcher-arrow" style="font-size:14px;opacity:0.7;"></i>
                    </button>
                @endif

                {{-- CTA — desktop only (d-none d-lg-inline-flex) --}}
                @auth
                    <a href="{{ route('dashboard') }}" class="nav-cta-btn d-none d-lg-inline-flex">
                        <i class="ri-dashboard-line"></i> Mon Espace
                    </a>
                @else
                    <a href="{{ route('login') }}" class="nav-cta-btn d-none d-lg-inline-flex">
                        <i class="ri-user-line"></i> Se connecter
                    </a>
                @endauth

                {{-- Hamburger — mobile only (d-flex d-lg-none) --}}
                <button class="nav-toggler d-flex d-lg-none"
                        id="navToggler"
                        type="button"
                        aria-expanded="false"
                        aria-controls="mobileMenu"
                        aria-label="Ouvrir le menu">
                    <i class="ri-menu-line" id="navTogglerIcon"></i>
                </button>
            </div>

        </div>

        {{-- ── MOBILE DROPDOWN PANEL — starts hidden, toggled by JS ── --}}
        <div id="mobileMenu" style="display:none;" aria-hidden="true">
            <ul class="list-unstyled mb-0" style="padding:0;margin:0;">
                <li><a class="nav-link" href="#about"><i class="ri-information-line"></i> À propos</a></li>
                <li><a class="nav-link" href="#organization"><i class="ri-team-line"></i> Dirigeants</a></li>
                <li><a class="nav-link" href="#groups"><i class="ri-group-line"></i> Groupes</a></li>
                <li><a class="nav-link" href="#events"><i class="ri-calendar-event-line"></i> Cultes & Activités</a></li>
            </ul>
            <div class="mobile-nav-divider"></div>
            @auth
                <a href="{{ route('dashboard') }}" class="nav-cta-btn nav-cta-mobile">
                    <i class="ri-dashboard-line"></i> Mon Espace
                </a>
            @else
                <a href="{{ route('login') }}" class="nav-cta-btn nav-cta-mobile">
                    <i class="ri-user-line"></i> Se connecter
                </a>
            @endauth
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
                        <div class="hero-badge-pill">
                            <i class="bx bxs-church fs-16"></i>
                            <span>{{ $currentChurch->name }} {{ $currentChurch->city ? '• ' . $currentChurch->city : '' }}</span>
                        </div>
                        <h1 class="display-4 fw-bold mb-3 text-white" style="letter-spacing: -0.5px; text-shadow: 0 2px 10px rgba(0,0,0,0.3);">{{ $heroTitle }}</h1>
                        <p class="lead mb-4 text-light" style="font-size: 1.15rem; max-width: 620px; text-shadow: 0 1px 6px rgba(0,0,0,0.4); line-height: 1.6;">
                            {{ $heroSubtitle }}
                        </p>
                        <div class="d-flex gap-3 flex-wrap align-items-center">
                            <a href="#about" class="btn hero-btn-primary btn-lg px-4 rounded-pill">
                                <i class="ri-compass-3-line me-2"></i> Découvrir l'Église
                            </a>
                            <a href="{{ route('login') }}" class="btn hero-btn-outline btn-lg px-4 rounded-pill">
                                <i class="ri-user-follow-line me-2"></i> Espace Membre
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Animated Scroll Down Indicator -->
            <div class="text-center mt-5 pt-3 d-none d-md-block">
                <a href="#about" class="hero-scroll-indicator">
                    <span class="fs-12 fw-bold text-uppercase" style="letter-spacing: 2px;">Découvrir</span>
                    <i class="ri-arrow-down-s-line fs-22"></i>
                </a>
            </div>
        </div>
    </section>

    <div class="container mt-4 mt-md-5 px-3 px-sm-4">
        
        <!-- 2. PRESENTATION DE L'EGLISE -->
        <section id="about" class="py-4 py-md-5">
            <div class="text-center mb-4 mb-md-5 reveal-on-scroll">
                <span class="section-badge"><i class="ri-sparkling-fill"></i> À DÉCOUVRIR</span>
                <h2 class="section-title">Qui sommes-nous ?</h2>
                <p class="section-subtitle">Découvrez notre vision, notre engagement spirituel et les valeurs chrétiennes qui animent notre communauté.</p>
            </div>
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0 reveal-on-scroll">
                    @php
                        $aboutImg = $settings->about_image ? asset('storage/' . $settings->about_image) : asset('assets/images/home/about.jpg');
                    @endphp
                    <div class="about-img-container">
                        <img src="{{ $aboutImg }}" alt="Présentation de {{ $currentChurch->name }}" class="about-main-img">
                        <div class="about-badge-float">
                            <div class="about-badge-icon"><i class="ri-heart-3-fill"></i></div>
                            <div>
                                <span class="d-block fw-bold fs-13 text-dark">Communauté Vivante</span>
                                <small class="text-muted fs-11">Foi, Partage &amp; Espérance</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 ps-lg-5">
                    <div class="about-feature-card reveal-on-scroll reveal-delay-1">
                        <div class="about-icon-box" style="background: rgba(64, 81, 137, 0.1); color: #405189;">
                            <i class="ri-history-line"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-dark mb-1 fs-16">Notre Histoire</h5>
                            <p class="text-muted mb-0 fs-14" style="line-height: 1.6;">{{ $settings->about_history ?? "L'église " . $currentChurch->name . " est une communauté chrétienne engagée dans la prière, le partage et l'édification de chaque fidèle." }}</p>
                        </div>
                    </div>

                    <div class="about-feature-card reveal-on-scroll reveal-delay-2">
                        <div class="about-icon-box" style="background: rgba(10, 179, 156, 0.1); color: #0ab39c;">
                            <i class="ri-flag-line"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-dark mb-1 fs-16">Notre Mission</h5>
                            <p class="text-muted mb-0 fs-14" style="line-height: 1.6;">{{ $settings->about_mission ?? "Équiper et former chaque génération pour qu'elle soit une lumière dans le monde, en cultivant l'amour, l'entraide et le leadership chrétien." }}</p>
                        </div>
                    </div>

                    <div class="about-feature-card reveal-on-scroll reveal-delay-3">
                        <div class="about-icon-box" style="background: rgba(41, 156, 219, 0.1); color: #299cdb;">
                            <i class="ri-eye-line"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-dark mb-1 fs-16">Notre Vision</h5>
                            <p class="text-muted mb-0 fs-14" style="line-height: 1.6;">{{ $settings->about_vision ?? "Voir chaque membre grandir dans sa foi, découvrir ses dons et impacter positivement sa communauté et sa nation." }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 5. CHIFFRES CLES -->
        @php
            $hasSubstantialStats = ($stats['users'] > 1) || ($stats['groups'] > 0) || ($stats['events'] > 0);
        @endphp
        <section class="stats-section my-4 my-md-5 reveal-on-scroll">
            <div class="container position-relative" style="z-index: 1;">
                @if($hasSubstantialStats)
                    <div class="row text-center g-3 g-lg-4">
                        <div class="col-6 col-lg-3">
                            <div class="stat-box">
                                <div class="stat-icon-wrap"><i class="ri-user-star-line"></i></div>
                                <h2><span class="counter-value" data-target="{{ $stats['users'] }}">0</span></h2>
                                <p>Membres enregistrés</p>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="stat-box">
                                <div class="stat-icon-wrap"><i class="ri-group-2-line"></i></div>
                                <h2><span class="counter-value" data-target="{{ $stats['groups'] }}">0</span></h2>
                                <p>Groupes &amp; Ministères</p>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="stat-box">
                                <div class="stat-icon-wrap"><i class="ri-calendar-event-line"></i></div>
                                <h2><span class="counter-value" data-target="{{ $stats['events'] }}">0</span></h2>
                                <p>Cultes &amp; Activités</p>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="stat-box">
                                <div class="stat-icon-wrap"><i class="ri-team-line"></i></div>
                                <h2><span class="counter-value" data-target="{{ $stats['leaders'] }}">0</span></h2>
                                <p>Dirigeants &amp; Responsables</p>
                            </div>
                        </div>
                    </div>
                @else
                    {{-- Quand l'église démarre et recense ses membres --}}
                    <div class="row text-center g-3 g-lg-4">
                        <div class="col-6 col-lg-3">
                            <div class="stat-box">
                                <div class="stat-icon-wrap text-primary"><i class="ri-book-open-line"></i></div>
                                <h4 class="fw-bold text-white mb-1 fs-17">Foi &amp; Parole</h4>
                                <p class="text-white-50 fs-12 mb-0">Enseignement biblique</p>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="stat-box">
                                <div class="stat-icon-wrap text-success"><i class="ri-heart-line"></i></div>
                                <h4 class="fw-bold text-white mb-1 fs-17">Fraternité</h4>
                                <p class="text-white-50 fs-12 mb-0">Accueil &amp; communion</p>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="stat-box">
                                <div class="stat-icon-wrap text-warning"><i class="ri-fire-line"></i></div>
                                <h4 class="fw-bold text-white mb-1 fs-17">Prière</h4>
                                <p class="text-white-50 fs-12 mb-0">Intercession fervente</p>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="stat-box">
                                <div class="stat-icon-wrap text-info"><i class="ri-user-smile-line"></i></div>
                                <h4 class="fw-bold text-white mb-1 fs-17">Recensement</h4>
                                <p class="text-white-50 fs-12 mb-0">Communauté en essor</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </section>

        <!-- 3. NOTRE ORGANISATION -->
        <section id="organization" class="py-4 py-md-5">
            <div class="text-center mb-4 mb-md-5 reveal-on-scroll">
                <span class="section-badge"><i class="ri-team-fill"></i> LEADERSHIP SPIRITUEL</span>
                <h2 class="section-title">Notre Organisation</h2>
                <p class="section-subtitle">Découvrez les serviteurs dévoués, pasteurs et responsables engagés au service de {{ $currentChurch->name }}.</p>
            </div>
            <div class="row justify-content-center g-3 g-md-4">
                @forelse($leaders as $leader)
                    <div class="col-6 col-md-4 col-xl-3 mb-3 reveal-on-scroll reveal-delay-{{ ($loop->index % 4) + 1 }}">
                        <div class="card org-card h-100 shadow-sm border-0 rounded-4 overflow-hidden">
                            <div class="org-img-wrapper" style="height: 230px; overflow: hidden; background: #f3f6f9;">
                                @php
                                    $photoUrl = $leader->photo ? asset('storage/' . $leader->photo) : asset('assets/images/home/user-placeholder.jpg');
                                @endphp
                                <img src="{{ $photoUrl }}" class="org-img w-100 h-100" style="object-fit: cover;" alt="{{ $leader->first_name }} {{ $leader->name }}">
                                <div class="org-overlay-gradient"></div>
                            </div>
                            <div class="card-body text-center p-3 p-md-4">
                                <h5 class="fw-bold mb-1 fs-14 fs-md-16 text-dark">{{ $leader->first_name }} {{ $leader->name }}</h5>
                                <div class="d-flex flex-wrap justify-content-center gap-1 mb-1">
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
                    <div class="col-12 col-md-8 col-lg-6 reveal-on-scroll">
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
            <div class="text-center mb-4 mb-md-5 reveal-on-scroll">
                <span class="section-badge"><i class="ri-community-fill"></i> VIE FRATERNELLE</span>
                <h2 class="section-title">Nos Groupes &amp; Ministères</h2>
                <p class="section-subtitle">Rejoignez un groupe de proximité pour partager, servir selon vos dons et grandir ensemble dans la communion fraternelle.</p>
            </div>
            <div class="row g-3 g-md-4 justify-content-center">
                @forelse($groups as $index => $group)
                    <div class="col-12 col-md-6 col-lg-4 mb-3 mb-md-0 reveal-on-scroll reveal-delay-{{ ($loop->index % 3) + 1 }}">
                        <div class="card group-card shadow-sm h-100 border-0">
                            @if($group->image_path)
                                <div class="position-relative bg-dark" style="height: 190px; width: 100%; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                                    <div class="position-absolute w-100 h-100" style="background-image: url('{{ asset('storage/' . $group->image_path) }}'); background-size: cover; background-position: center; filter: blur(10px) brightness(0.6); transform: scale(1.1);"></div>
                                    <img src="{{ asset('storage/' . $group->image_path) }}" alt="{{ $group->name }}" style="max-width: 100%; max-height: 100%; object-fit: contain; position: relative; z-index: 1;">
                                    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(to bottom, rgba(0,0,0,0.2), transparent); z-index: 2;"></div>
                                </div>
                            @else
                                <div class="card-img-top d-flex align-items-center justify-content-center" style="height: 190px; background: linear-gradient(135deg, {{ $group->color ?? '#405189' }}dd, {{ $group->color ?? '#405189' }});">
                                    <i class="ri-group-line text-white opacity-75" style="font-size: 4.5rem;"></i>
                                </div>
                            @endif
                            <div class="card-body p-3 p-md-4">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="card-title fw-bold mb-0 fs-17" style="color: {{ $group->color ?? '#405189' }}">{{ $group->name }}</h5>
                                    <span class="badge rounded-pill px-2 py-1 fs-11" style="background: rgba(64,81,137,0.08); color: #405189;">
                                        <i class="ri-user-smile-line me-1"></i> {{ $group->members_count }} {{ Str::plural('membre', $group->members_count) }}
                                    </span>
                                </div>
                                <p class="card-text text-muted small" style="line-height: 1.6; min-height: 44px;">{{ $group->description ?? 'Aucune description disponible pour ce groupe.' }}</p>
                                
                                <hr class="border-dashed my-3">
                                
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex flex-column gap-1">
                                        <span class="badge bg-secondary-subtle text-body text-start fs-11 py-1 px-2"><i class="ri-user-star-line text-warning me-1"></i> Chef : {{ $group->leader ? $group->leader->first_name : 'N/A' }}</span>
                                        <span class="badge bg-secondary-subtle text-body text-start fs-11 py-1 px-2"><i class="ri-hand-coin-line text-success me-1"></i> Col : {{ $group->collector ? $group->collector->first_name : 'N/A' }}</span>
                                    </div>
                                    <a href="{{ route('login') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fs-12">
                                        Rejoindre <i class="ri-arrow-right-line ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 col-lg-8 mx-auto reveal-on-scroll">
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
            <div class="text-center mb-4 mb-md-5 reveal-on-scroll">
                <span class="section-badge"><i class="ri-camera-lens-fill"></i> SOUVENIRS EN IMAGES</span>
                <h2 class="section-title">Notre Galerie</h2>
                <p class="section-subtitle">Revivez en images les moments forts, célébrations fraternelles et rassemblements de foi.</p>
            </div>
            <div class="row g-3 justify-content-center">
                @forelse($galleries as $index => $gallery)
                    <div class="col-6 col-md-4 col-lg-3 reveal-on-scroll reveal-delay-{{ ($loop->index % 4) + 1 }}">
                        <div class="gallery-item shadow-sm mb-2 mb-md-3">
                            <a href="{{ asset('storage/' . $gallery->image_path) }}" target="_blank" aria-label="{{ $gallery->title ?? 'Photo' }}">
                                <img src="{{ asset('storage/' . $gallery->image_path) }}" alt="{{ $gallery->title }}" class="gallery-img">
                                <div class="gallery-overlay">
                                    <div class="gallery-zoom-icon">
                                        <i class="ri-zoom-in-line"></i>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-12 col-md-8 col-lg-6 reveal-on-scroll">
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
            <div class="text-center mb-4 mb-md-5 reveal-on-scroll">
                <span class="section-badge"><i class="ri-calendar-event-fill"></i> RENCONTRES &amp; AGENDAS</span>
                <h2 class="section-title">Cultes &amp; Prochains Événements</h2>
                <p class="section-subtitle">Prenez part aux prochains rassemblements, célébrations et rendez-vous spirituels de {{ $currentChurch->name }}.</p>
            </div>
            
            <div class="row g-3 g-md-4 justify-content-center">
                @forelse($activities as $activity)
                    <div class="col-12 col-md-6 col-lg-4 mb-3 mb-md-0 reveal-on-scroll reveal-delay-{{ ($loop->index % 3) + 1 }}">
                        <div class="event-card shadow-sm h-100">
                            @if($activity->image_path)
                                <a href="{{ asset('storage/' . $activity->image_path) }}" target="_blank" class="position-relative bg-dark d-flex align-items-center justify-content-center" style="height: 190px; width: 100%; overflow: hidden; text-decoration: none;">
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
                                <div class="card-img-top d-flex align-items-center justify-content-center" style="height: 190px; background: {{ $bg }};">
                                    <i class="ri-calendar-event-line text-white opacity-75" style="font-size: 4.5rem;"></i>
                                </div>
                            @endif
                            <div class="card-body p-3 p-md-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="event-date-badge"><i class="ri-calendar-line"></i> {{ \Carbon\Carbon::parse($activity->start_time)->format('d M Y') }}</span>
                                    <span class="text-muted small"><i class="ri-map-pin-line text-primary"></i> {{ $activity->location ?? ($currentChurch->city ?? 'Église') }}</span>
                                </div>
                                <h5 class="card-title fw-bold fs-17">{{ $activity->title }}</h5>
                                <p class="card-text text-muted small text-truncate-2 mb-0" style="line-height: 1.5;">{{ Str::limit(strip_tags($activity->description), 100) }}</p>
                            </div>
                            <div class="card-footer bg-transparent border-0 pb-3 px-3 px-md-4">
                                <a href="{{ route('login') }}" class="btn btn-primary w-100 rounded-pill">
                                    Participer / S'inscrire <i class="ri-arrow-right-line ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 col-lg-10 reveal-on-scroll">
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
        <section class="cta-section shadow-lg reveal-on-scroll">
            <div class="container px-3 position-relative" style="z-index: 2;">
                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold fs-12 mb-3 shadow-sm">
                    <i class="ri-heart-pulse-line me-1"></i> VOUS ÊTES LES BIENVENUS
                </span>
                <h2 class="display-5 fw-bold mb-3 mb-md-4 text-white">Rejoignez {{ $currentChurch->name }} !</h2>
                <p class="lead mb-4 mb-md-5 mx-auto fs-16 fs-md-18 text-white-50" style="max-width: 720px; line-height: 1.7;">
                    Vous souhaitez faire partie de notre communauté, participer à nos activités, servir le Seigneur et grandir spirituellement avec nous ?
                </p>
                <div class="d-flex flex-wrap justify-content-center align-items-center gap-3">
                    <a href="tel:{{ str_replace(' ', '', $adminPhone) }}" class="phone-contact-pill">
                        <div class="avatar-sm me-2 me-md-3 flex-shrink-0">
                            <div class="avatar-title bg-primary rounded-circle text-white shadow-sm">
                                <i class="ri-phone-fill fs-18 fs-md-20"></i>
                            </div>
                        </div>
                        <div class="text-start overflow-hidden pe-2">
                            <span class="d-block text-muted small fw-semibold text-truncate">Secrétariat pastoral :</span>
                            <span class="d-block text-dark fw-bold fs-16 fs-md-18 text-truncate">{{ $adminPhone }}</span>
                        </div>
                    </a>
                    @guest
                        <a href="{{ route('login') }}" class="btn btn-warning btn-lg rounded-pill px-4 fw-bold shadow-sm" style="color: #1e293b;">
                            <i class="ri-user-add-line me-1"></i> Espace Membre
                        </a>
                    @endguest
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
                    <p class="text-muted pe-4" style="line-height: 1.6;">
                        Une communauté chrétienne dynamique et engagée, unie par la foi et le désir de grandir ensemble dans l'amour du Christ.
                    </p>
                    <div class="d-flex gap-2 mt-4">
                        @if(!empty($settings->facebook_link))
                            <a href="{{ $settings->facebook_link }}" target="_blank" class="footer-social-btn" title="Facebook" aria-label="Facebook">
                                <i class="ri-facebook-fill fs-18"></i>
                            </a>
                        @endif
                        @if(!empty($settings->tiktok_link))
                            <a href="{{ $settings->tiktok_link }}" target="_blank" class="footer-social-btn" title="TikTok" aria-label="TikTok">
                                <i class="ri-tiktok-fill fs-18"></i>
                            </a>
                        @endif
                        <a href="tel:{{ str_replace(' ', '', $adminPhone) }}" class="footer-social-btn" title="Téléphone" aria-label="Appeler l'église">
                            <i class="ri-phone-fill fs-16"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-2 col-6 mb-4">
                    <h5>Navigation</h5>
                    <div class="footer-links">
                        <a href="#about"><i class="ri-arrow-right-s-line fs-14"></i> Qui sommes-nous ?</a>
                        <a href="#organization"><i class="ri-arrow-right-s-line fs-14"></i> Organisation</a>
                        <a href="#groups"><i class="ri-arrow-right-s-line fs-14"></i> Nos Groupes</a>
                        <a href="#events"><i class="ri-arrow-right-s-line fs-14"></i> Événements</a>
                    </div>
                </div>
                <div class="col-lg-3 col-6 mb-4">
                    <h5>Liens Utiles</h5>
                    <div class="footer-links">
                        <a href="{{ route('login') }}"><i class="ri-arrow-right-s-line fs-14"></i> Connexion Espace Membre</a>
                        <a href="{{ route('privacy') }}"><i class="ri-arrow-right-s-line fs-14"></i> Politique de confidentialité</a>
                        <a href="{{ route('legal') }}"><i class="ri-arrow-right-s-line fs-14"></i> Mentions légales</a>
                    </div>
                </div>
                <div class="col-lg-3 mb-4">
                    <h5>Contact</h5>
                    <p class="text-muted mb-2"><i class="ri-map-pin-line me-2 text-warning"></i> {{ $currentChurch->address ?? ($currentChurch->city ?? 'Bénin') }}</p>
                    <p class="text-muted mb-2"><i class="ri-phone-line me-2 text-success"></i> {{ $adminPhone }}</p>
                    <p class="text-muted mb-0"><i class="ri-mail-line me-2 text-info"></i> {{ $currentChurch->email ?? 'contact@mevoici.org' }}</p>
                </div>
            </div>
            <hr class="border-secondary my-4" style="opacity: 0.15;">
            <div class="text-center text-muted">
                <p class="mb-0 fs-13">&copy; {{ date('Y') }} {{ $currentChurch->name }} • Propulsé par {{ config('app.name') }}. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <!-- Floating Back-to-Top Button -->
    <a href="#" id="backToTop" class="back-to-top" title="Retour en haut" aria-label="Retour en haut">
        <i class="ri-arrow-up-line"></i>
    </a>

    <!-- MODAL SÉLECTEUR D'ÉGLISE -->
    @if(isset($allChurches) && $allChurches->count() > 1)
        <div class="modal fade" id="switchChurchModal" tabindex="-1" aria-labelledby="switchChurchModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="modal-header border-0 bg-primary text-white p-4">
                        <div class="d-flex align-items-center gap-3">
                            <div style="width:44px;height:44px;min-width:44px;border-radius:50%;background:rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;border:1.5px solid rgba(255,255,255,0.35);flex-shrink:0;">
                                <i class="bx bxs-church" style="font-size:20px;color:#fff;line-height:1;display:block;"></i>
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
        // ── Navbar : sticky effect ──────────────────────────────────────
        (function () {
            var navbar = document.getElementById('navbar');
            if (!navbar) return;

            function handleScroll() {
                if (window.scrollY > 60) {
                    navbar.classList.add('is-sticky');
                } else {
                    navbar.classList.remove('is-sticky');
                }
            }
            window.addEventListener('scroll', handleScroll, { passive: true });
            handleScroll(); // run on load too
        })();

        // ── Navbar : mobile toggle ───────────────────────────────────────
        (function () {
            var toggler     = document.getElementById('navToggler');
            var mobilePanel = document.getElementById('mobileMenu');
            var iconEl      = document.getElementById('navTogglerIcon');
            if (!toggler || !mobilePanel) return;

            var isOpen = false;

            function openMenu() {
                isOpen = true;
                mobilePanel.style.display = 'block';
                toggler.setAttribute('aria-expanded', 'true');
                iconEl.className = 'ri-close-line';
            }
            function closeMenu() {
                isOpen = false;
                mobilePanel.style.display = 'none';
                toggler.setAttribute('aria-expanded', 'false');
                iconEl.className = 'ri-menu-line';
            }

            toggler.addEventListener('click', function (e) {
                e.stopPropagation();
                isOpen ? closeMenu() : openMenu();
            });

            // Close when clicking any nav link inside mobile panel
            mobilePanel.querySelectorAll('.nav-link').forEach(function (link) {
                link.addEventListener('click', closeMenu);
            });

            // Close when clicking outside navbar
            document.addEventListener('click', function (e) {
                var navbar = document.getElementById('navbar');
                if (isOpen && navbar && !navbar.contains(e.target)) {
                    closeMenu();
                }
            });

            // On resize to desktop: close mobile menu & reset icon
            window.addEventListener('resize', function () {
                if (window.innerWidth >= 992 && isOpen) {
                    closeMenu();
                }
            });
        })();

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

        // ── Scroll Progress & Back to Top ─────────────────────────────
        (function() {
            const progressBar = document.getElementById('scrollProgressBar');
            const backToTopBtn = document.getElementById('backToTop');

            function onScroll() {
                const scrollTop = window.scrollY || document.documentElement.scrollTop;
                const scrollHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
                const progress = scrollHeight > 0 ? (scrollTop / scrollHeight) * 100 : 0;
                
                if (progressBar) {
                    progressBar.style.width = progress + '%';
                }

                if (backToTopBtn) {
                    if (scrollTop > 350) {
                        backToTopBtn.classList.add('is-shown');
                    } else {
                        backToTopBtn.classList.remove('is-shown');
                    }
                }
            }

            window.addEventListener('scroll', onScroll, { passive: true });
            onScroll();

            if (backToTopBtn) {
                backToTopBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                });
            }
        })();

        // ── Scroll Reveal Animations ─────────────────────────────────
        (function() {
            if (!('IntersectionObserver' in window)) {
                document.querySelectorAll('.reveal-on-scroll').forEach(function(el) {
                    el.classList.add('is-revealed');
                });
                return;
            }

            const revealObserver = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-revealed');
                        revealObserver.unobserve(entry.target);
                    }
                });
            }, {
                root: null,
                rootMargin: '0px 0px -40px 0px',
                threshold: 0.08
            });

            document.querySelectorAll('.reveal-on-scroll').forEach(function(el) {
                revealObserver.observe(el);
            });
        })();

        // ── CountUp JS for Stats ─────────────────────────────────────
        document.addEventListener("DOMContentLoaded", function() {
            const counters = document.querySelectorAll('.counter-value');
            const speed = 180;

            const animateCounters = () => {
                counters.forEach(counter => {
                    const target = +counter.getAttribute('data-target');
                    const count = +counter.innerText;
                    const inc = Math.max(1, Math.ceil(target / speed));

                    if (count < target) {
                        counter.innerText = Math.min(target, count + inc);
                        setTimeout(animateCounters, 15);
                    } else {
                        counter.innerText = target;
                    }
                });
            };
            
            if ('IntersectionObserver' in window) {
                const observer = new IntersectionObserver(entries => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            animateCounters();
                            observer.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.3 });
                
                counters.forEach(counter => {
                    observer.observe(counter);
                });
            } else {
                animateCounters();
            }
        });
    </script>
</body>
</html>
