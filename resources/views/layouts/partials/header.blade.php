<header id="page-topbar">
    <div class="layout-width">
        <div class="navbar-header">
            <div class="d-flex">
                <!-- LOGO -->
                <div class="navbar-brand-box horizontal-logo">
                    <a href="{{ route('dashboard') }}" class="logo logo-dark">
                        <span class="logo-sm">
                            <img src="{{ $appSettings->logo_sm_url ?? asset('assets/images/logo-sm.png') }}" alt="" height="22">
                        </span>
                        <span class="logo-lg">
                            <img src="{{ $appSettings->logo_dark_url ?? asset('assets/images/logo-dark.png') }}" alt="" height="17">
                        </span>
                    </a>

                    <a href="{{ route('dashboard') }}" class="logo logo-light">
                        <span class="logo-sm">
                            <img src="{{ $appSettings->logo_sm_url ?? asset('assets/images/logo-sm.png') }}" alt="" height="22">
                        </span>
                        <span class="logo-lg">
                            <img src="{{ $appSettings->logo_light_url ?? asset('assets/images/logo-light.png') }}" alt="" height="17">
                        </span>
                    </a>
                </div>

                <button type="button"
                    class="btn btn-sm px-2 px-sm-3 fs-16 header-item vertical-menu-btn topnav-hamburger shadow-none d-none d-md-inline-flex"
                    id="topnav-hamburger-icon">
                    <span class="hamburger-icon">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                </button>

                <!-- Logo Principal sur petit écran (sans le bouton flèche/hamburger) -->
                <a href="{{ route('dashboard') }}" class="d-flex d-md-none align-items-center text-decoration-none ps-1 header-item py-0">
                    <img src="{{ $appSettings->logo_dark_url ?? asset('assets/images/logo-dark.png') }}" alt="{{ config('app.name') }}" height="22">
                </a>

                @if(auth()->check() && (auth()->user()->can('member.view') || auth()->user()->can('manage-users')))
                <!-- App Search (Desktop) -->
                <form class="app-search d-none d-md-block">
                    <div class="position-relative">
                        <input type="text" class="form-control" placeholder="Rechercher une activité, un membre..." autocomplete="off"
                            id="search-options" value="">
                        <span class="mdi mdi-magnify search-widget-icon"></span>
                        <span class="mdi mdi-close-circle search-widget-icon search-widget-icon-close d-none cursor-pointer"
                            id="search-close-options"></span>
                    </div>
                    <div class="dropdown-menu dropdown-menu-lg" id="search-dropdown">
                        <div data-simplebar style="max-height: 320px;" id="search-results-container">
                            <div class="text-center pt-3 pb-3">
                                <div class="spinner-border text-primary spinner-border-sm" role="status">
                                    <span class="visually-hidden">Chargement...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                @endif
            </div>

            <div class="d-flex align-items-center">

                @if(auth()->check() && (auth()->user()->can('member.view') || auth()->user()->can('manage-users')))
                <!-- App Search (Mobile) -->
                <div class="dropdown d-md-none topbar-head-dropdown header-item">
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle shadow-none position-relative"
                        id="page-header-search-dropdown" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true"
                        aria-expanded="false" title="Rechercher">
                        <i class="bx bx-search fs-22"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0 shadow-lg border-0"
                        aria-labelledby="page-header-search-dropdown" style="min-width: 320px; max-width: 95vw;">
                        <div class="p-3 border-bottom bg-light">
                            <div class="position-relative">
                                <input type="text" class="form-control pe-4" placeholder="Rechercher une activité, un membre..." autocomplete="off"
                                    id="search-options-mobile" value="">
                                <span class="mdi mdi-magnify" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); pointer-events: none; color: #878a99;"></span>
                                <span class="mdi mdi-close-circle d-none cursor-pointer"
                                    id="search-close-options-mobile" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); z-index: 5; color: #878a99;"></span>
                            </div>
                        </div>
                        <div data-simplebar style="max-height: 320px;" id="search-results-container-mobile">
                            <div class="p-3 text-center text-muted fs-13">
                                <i class="mdi mdi-magnify fs-20 d-block mb-1 opacity-50"></i>
                                Tapez au moins 2 caractères pour rechercher...
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <div class="ms-1 header-item d-none d-sm-flex">
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle shadow-none position-relative"
                        data-toggle="fullscreen">
                        <i class='bx bx-fullscreen fs-22'></i>
                    </button>
                </div>

                <div class="ms-1 header-item d-none d-sm-flex">
                    <button type="button"
                        class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle light-dark-mode shadow-none">
                        <i class='bx bx-moon fs-22'></i>
                    </button>
                </div>

                @php
                    $isSupportMode = session()->has('tenant_church_id') && auth()->check() && auth()->user()->isSuperAdmin();
                    $displayUser = auth()->user();
                    $displayChurch = auth()->user()->church;
                    $supportChurch = null;

                    if ($isSupportMode) {
                        $supportChurch = \App\Models\Church::find(session('tenant_church_id'));
                        if ($supportChurch) {
                            $churchAdmin = \App\Models\User::withoutGlobalScopes()
                                ->where('church_id', $supportChurch->id)
                                ->whereHas('roles', fn($q) => $q->where('name', 'Administrateur'))
                                ->first() ?? \App\Models\User::withoutGlobalScopes()->where('church_id', $supportChurch->id)->first();
                            if ($churchAdmin) {
                                $displayUser = $churchAdmin;
                            }
                            $displayChurch = $supportChurch;
                        }
                    }

                    $headerNotifications = $displayUser->unreadNotifications()->latest()->take(15)->get();
                    $unreadCount = $displayUser->unreadNotifications()->count();
                @endphp

                <div class="dropdown topbar-head-dropdown ms-1 header-item" id="notificationDropdown">
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle shadow-none position-relative"
                        id="page-header-notifications-dropdown" data-bs-toggle="dropdown" data-bs-auto-close="outside"
                        aria-haspopup="true" aria-expanded="false">
                        <i class='bx bx-bell fs-22'></i>
                        @if($unreadCount > 0)
                        <span class="position-absolute badge rounded-pill bg-danger" style="top: 3px; right: 3px; font-size: 10px; padding: 0.25em 0.45em; transform: translate(25%, -25%);">
                            {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                            <span class="visually-hidden">notifications non lues</span>
                        </span>
                        @endif
                    </button>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                        aria-labelledby="page-header-notifications-dropdown">

                        <div class="dropdown-head bg-primary bg-pattern rounded-top">
                            <div class="p-3">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h6 class="m-0 fs-16 fw-semibold text-white">Notifications</h6>
                                        @if($isSupportMode && $displayChurch)
                                            <span class="badge bg-warning text-dark fs-11 mt-1">Mode Support : {{ $displayChurch->name }}</span>
                                        @endif
                                    </div>
                                    <div class="col-auto dropdown-tabs">
                                        @if($unreadCount > 0)
                                        <span class="badge bg-light-subtle text-body fs-13">{{ $unreadCount }} non lue{{ $unreadCount > 1 ? 's' : '' }}</span>
                                        @else
                                        <span class="badge bg-light-subtle text-body fs-13">Tout lu</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-content position-relative" id="notificationItemsTabContent">
                            <div class="py-2 ps-2">
                                <div data-simplebar style="max-height: 300px;" class="pe-2">
                                    @forelse($headerNotifications as $notification)
                                    @php
                                        $nData  = $notification->data;
                                        $icon   = $nData['icon']  ?? 'mdi mdi-bell-outline';
                                        $color  = $nData['color'] ?? 'primary';
                                        $title  = $nData['title'] ?? 'Notification';
                                        $message = $nData['message'] ?? '';
                                        $url    = $nData['url']   ?? route('dashboard');
                                        $isUnread = is_null($notification->read_at);
                                    @endphp
                                    <div class="text-reset notification-item d-block dropdown-item position-relative {{ $isUnread ? 'fw-semibold' : '' }}">
                                        <a href="{{ route('notifications.read', $notification->id) }}" class="d-flex text-reset text-decoration-none">
                                            <div class="avatar-xs me-3 flex-shrink-0">
                                                <span class="avatar-title bg-{{ $color }}-subtle text-{{ $color }} rounded-circle fs-16">
                                                    <i class="{{ $icon }}"></i>
                                                </span>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mt-0 mb-1 fs-13 {{ $isUnread ? 'fw-semibold' : '' }}">{{ $title }}</h6>
                                                <p class="mb-0 fs-12 text-muted lh-sm">{{ Str::limit($message, 80) }}</p>
                                                <p class="mb-0 fs-11 fw-medium text-uppercase text-muted mt-1">
                                                    <i class="mdi mdi-clock-outline"></i>
                                                    {{ $notification->created_at->diffForHumans() }}
                                                </p>
                                            </div>
                                            @if($isUnread)
                                            <div class="flex-shrink-0 ms-2">
                                                <span class="badge bg-danger rounded-pill" style="width:8px;height:8px;padding:0;"></span>
                                            </div>
                                            @endif
                                        </a>
                                    </div>
                                    @empty
                                    <div class="text-center py-4">
                                        <i class="mdi mdi-bell-sleep-outline fs-36 text-muted"></i>
                                        <p class="text-muted mt-2 mb-0">Aucune notification</p>
                                    </div>
                                    @endforelse

                                    @if($headerNotifications->count() > 0)
                                    <div class="my-3 text-center d-flex justify-content-center gap-2">
                                        <form method="POST" action="{{ route('notifications.read-all') }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-soft-success btn-sm waves-effect waves-light">
                                                Tout marquer lu <i class="ri-check-double-line align-middle"></i>
                                            </button>
                                        </form>
                                        <a href="{{ route('notifications.index') }}" class="btn btn-soft-primary btn-sm waves-effect waves-light">
                                            Voir tout <i class="ri-arrow-right-line align-middle"></i>
                                        </a>
                                    </div>
                                    @else
                                    <div class="my-3 text-center">
                                        <a href="{{ route('notifications.index') }}" class="btn btn-soft-primary btn-sm waves-effect waves-light">
                                            Historique <i class="ri-history-line align-middle"></i>
                                        </a>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="dropdown ms-sm-3 header-item topbar-user">
                    <button type="button" class="btn shadow-none" id="page-header-user-dropdown"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="d-flex align-items-center">
                            <div class="position-relative">
                                <img class="rounded-circle header-profile-user" 
                                     src="{{ $displayUser->avatar_url }}"
                                     alt="Header Avatar">
                                @if($isSupportMode)
                                    <span class="position-absolute bottom-0 end-0 bg-warning border border-white rounded-circle" style="width: 10px; height: 10px;" title="Mode Support"></span>
                                @endif
                            </div>
                            <span class="text-start ms-xl-2">
                                <span class="d-none d-xl-inline-block ms-1 fw-medium user-name-text">
                                    {{ $displayUser->first_name }} {{ $displayUser->name }}
                                </span>
                                <span class="d-none d-xl-block ms-1 fs-12 user-name-sub-text">
                                    @if($isSupportMode)
                                        <span class="badge bg-warning text-dark px-1 py-0 fs-10 fw-bold">Support</span>
                                        Administrateur
                                        @if($displayChurch)
                                            • <span class="text-primary fw-semibold">{{ $displayChurch->name }}</span>
                                        @endif
                                    @else
                                        {{ auth()->user()->roles->first()?->name ?? 'Membre' }}
                                        @if(auth()->user()->church)
                                            • <span class="text-primary">{{ auth()->user()->church->name }}</span>
                                        @endif
                                    @endif
                                </span>
                            </span>
                        </span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end shadow-lg" style="min-width: 240px;">
                        @if($isSupportMode && $supportChurch)
                            <div class="p-3 bg-warning-subtle border-bottom border-warning-subtle text-center">
                                <span class="badge bg-warning text-dark px-2 py-1 fs-11 fw-bold rounded-pill mb-1">
                                    <i class="mdi mdi-wrench me-1"></i> Mode Support Actif
                                </span>
                                <h6 class="mb-0 fw-bold text-dark fs-13 text-truncate">{{ $supportChurch->name }}</h6>
                                <p class="text-muted fs-11 mb-2">Profil Admin : {{ $displayUser->full_name }}</p>
                                <a href="{{ route('super-admin.leave-impersonation') }}" class="btn btn-danger btn-sm rounded-pill w-100 fs-11 fw-semibold">
                                    <i class="mdi mdi-logout-variant me-1"></i> Quitter le support
                                </a>
                            </div>
                        @else
                            <h6 class="dropdown-header">Bienvenue {{ auth()->user()->first_name }} !</h6>
                        @endif

                        @if(auth()->user()->isSuperAdmin())
                        <a class="dropdown-item text-warning fw-semibold" href="{{ route('super-admin.dashboard') }}">
                            <i class="mdi mdi-shield-crown text-warning fs-16 align-middle me-1"></i>
                            <span class="align-middle">Supervision SaaS</span>
                        </a>
                        @endif

                        <a class="dropdown-item" href="{{ route('profile.edit') }}"><i
                                class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i> <span
                                class="align-middle">Profil</span></a>
                        
                        @can('manage-users')
                        <a class="dropdown-item" href="{{ route('admin.settings.edit') }}"><i
                                class="mdi mdi-cog-outline text-muted fs-16 align-middle me-1"></i> <span
                                class="align-middle">Paramètres</span></a>
                        @endcan
                        
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item text-danger" href="javascript:void(0);" 
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="mdi mdi-logout text-danger fs-16 align-middle me-1"></i>
                            <span class="align-middle" data-key="t-logout">Déconnexion</span>
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    function setupSearch(inputId, dropdownId, containerId, closeBtnId, isMobile = false) {
        const searchInput = document.getElementById(inputId);
        const searchDropdown = dropdownId ? document.getElementById(dropdownId) : null;
        const searchResultsContainer = document.getElementById(containerId);
        const closeOptionsBtn = document.getElementById(closeBtnId);
        
        if (!searchInput || !searchResultsContainer) return;

        let searchTimeout = null;

        searchInput.addEventListener('input', function(e) {
            const query = e.target.value.trim();
            
            if (query.length > 0) {
                closeOptionsBtn?.classList.remove('d-none');
            } else {
                closeOptionsBtn?.classList.add('d-none');
                if (searchDropdown) searchDropdown.classList.remove('show');
                if (isMobile) {
                    searchResultsContainer.innerHTML = `
                        <div class="p-3 text-center text-muted fs-13">
                            <i class="mdi mdi-magnify fs-20 d-block mb-1 opacity-50"></i>
                            Tapez au moins 2 caractères pour rechercher...
                        </div>
                    `;
                }
                return;
            }

            if (query.length >= 2) {
                if (searchDropdown) searchDropdown.classList.add('show');
                searchResultsContainer.innerHTML = `
                    <div class="text-center pt-3 pb-3">
                        <div class="spinner-border text-primary spinner-border-sm" role="status">
                            <span class="visually-hidden">Chargement...</span>
                        </div>
                    </div>
                `;

                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    const searchUrl = `{{ route('admin.global-search') }}?q=${encodeURIComponent(query)}`;
                    
                    fetch(searchUrl, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.length === 0) {
                            searchResultsContainer.innerHTML = `
                                <div class="p-3 text-center">
                                    <h6 class="text-muted mb-0">Aucun résultat trouvé</h6>
                                </div>
                            `;
                            return;
                        }

                        // Group results by type
                        const groupedData = data.reduce((acc, item) => {
                            if (!acc[item.type]) {
                                acc[item.type] = [];
                            }
                            acc[item.type].push(item);
                            return acc;
                        }, {});

                        let html = '';
                        for (const [type, items] of Object.entries(groupedData)) {
                            html += `<div class="dropdown-header mt-2">
                                        <h6 class="text-overflow text-muted mb-1 text-uppercase">${type}s</h6>
                                     </div>`;
                            items.forEach(item => {
                                html += `
                                    <a href="${item.url}" class="dropdown-item notify-item">
                                        <i class="${item.icon} align-middle fs-18 text-muted me-2"></i>
                                        <span>${item.title}</span>
                                        ${item.subtitle ? `<span class="d-block text-muted fs-11 ms-4">${item.subtitle}</span>` : ''}
                                    </a>
                                `;
                            });
                        }

                        searchResultsContainer.innerHTML = html;
                    })
                    .catch(error => {
                        console.error('Search error:', error);
                        searchResultsContainer.innerHTML = `
                            <div class="p-3 text-center text-danger">
                                <h6 class="mb-0">Erreur lors de la recherche</h6>
                            </div>
                        `;
                    });
                }, 300); // 300ms debounce
            } else {
                if (searchDropdown) searchDropdown.classList.remove('show');
                if (isMobile) {
                    searchResultsContainer.innerHTML = `
                        <div class="p-3 text-center text-muted fs-13">
                            <i class="mdi mdi-magnify fs-20 d-block mb-1 opacity-50"></i>
                            Tapez au moins 2 caractères pour rechercher...
                        </div>
                    `;
                }
            }
        });

        // Close button click
        if (closeOptionsBtn) {
            closeOptionsBtn.addEventListener('click', function() {
                searchInput.value = '';
                closeOptionsBtn.classList.add('d-none');
                if (searchDropdown) searchDropdown.classList.remove('show');
                if (isMobile) {
                    searchResultsContainer.innerHTML = `
                        <div class="p-3 text-center text-muted fs-13">
                            <i class="mdi mdi-magnify fs-20 d-block mb-1 opacity-50"></i>
                            Tapez au moins 2 caractères pour rechercher...
                        </div>
                    `;
                }
                searchInput.focus();
            });
        }

        // Close desktop dropdown when clicking outside
        if (!isMobile && searchDropdown) {
            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !searchDropdown.contains(e.target)) {
                    searchDropdown.classList.remove('show');
                }
            });
        }
    }

    // Initialize Desktop Search
    setupSearch('search-options', 'search-dropdown', 'search-results-container', 'search-close-options', false);

    // Initialize Mobile Search
    setupSearch('search-options-mobile', null, 'search-results-container-mobile', 'search-close-options-mobile', true);

    // Auto-focus mobile input when dropdown opens
    const mobileSearchBtn = document.getElementById('page-header-search-dropdown');
    if (mobileSearchBtn) {
        mobileSearchBtn.addEventListener('shown.bs.dropdown', function () {
            const mobileInput = document.getElementById('search-options-mobile');
            if (mobileInput) {
                setTimeout(() => mobileInput.focus(), 150);
            }
        });
    }
});
</script>
@endpush