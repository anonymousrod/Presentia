<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Velzon - Admin & Dashboard Template" name="description" />
    <meta content="Themesbrand" name="author" />
    
    <title>@yield('title', 'Dashboard | Velzon')</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ $appSettings->favicon_url ?? asset('assets/images/favicon.ico') }}">

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
    <link href="{{ asset('assets/css/custom.min.css') }}" rel="stylesheet" type="text/css" />

    @stack('css')

    <!-- Paramètres d'Images Dynamiques -->
    <style>
        [data-sidebar-image="img-1"] .sidebar-background { background-image: url('{{ $appSettings->sidebar_bg_1_url ?? asset("assets/images/sidebar/img-1.jpg") }}') !important; }
        [data-sidebar-image="img-2"] .sidebar-background { background-image: url('{{ $appSettings->sidebar_bg_2_url ?? asset("assets/images/sidebar/img-2.jpg") }}') !important; }
        [data-sidebar-image="img-3"] .sidebar-background { background-image: url('{{ $appSettings->sidebar_bg_3_url ?? asset("assets/images/sidebar/img-3.jpg") }}') !important; }
        [data-sidebar-image="img-4"] .sidebar-background { background-image: url('{{ $appSettings->sidebar_bg_4_url ?? asset("assets/images/sidebar/img-4.jpg") }}') !important; }
    </style>
</head>