<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Velzon - Admin & Dashboard Template" name="description" />
    <meta content="Themesbrand" name="author" />
    
    <title>@yield('title', 'Dashboard | Velzon')</title>

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

    <!-- jsvectormap -->
    <link href="{{ asset('assets/libs/jsvectormap/jsvectormap.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Swiper -->
    <link href="{{ asset('assets/libs/swiper/swiper-bundle.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- Layout config -->
    <script src="{{ asset('assets/js/layout.js') }}"></script>

    <!-- Bootstrap Css -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- App Css -->
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Custom Css -->
    <link href="{{ asset('assets/css/custom.min.css') }}?v={{ filemtime(public_path('assets/css/custom.min.css')) }}" rel="stylesheet" type="text/css" />

    @stack('css')

    <!-- Paramètres d'Images Dynamiques -->
    <style>
        [data-sidebar-image="img-1"] .sidebar-background { background-image: url('{{ $appSettings->sidebar_bg_1_url ?? asset("assets/images/sidebar/img-1.jpg") }}') !important; }
        [data-sidebar-image="img-2"] .sidebar-background { background-image: url('{{ $appSettings->sidebar_bg_2_url ?? asset("assets/images/sidebar/img-2.jpg") }}') !important; }
        [data-sidebar-image="img-3"] .sidebar-background { background-image: url('{{ $appSettings->sidebar_bg_3_url ?? asset("assets/images/sidebar/img-3.jpg") }}') !important; }
        [data-sidebar-image="img-4"] .sidebar-background { background-image: url('{{ $appSettings->sidebar_bg_4_url ?? asset("assets/images/sidebar/img-4.jpg") }}') !important; }
    </style>
</head>