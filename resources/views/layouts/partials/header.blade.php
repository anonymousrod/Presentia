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
                    class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger shadow-none"
                    id="topnav-hamburger-icon">
                    <span class="hamburger-icon">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                </button>

                <!-- App Search-->
                <form class="app-search d-none d-md-block">
                    <div class="position-relative">
                        <input type="text" class="form-control" placeholder="Rechercher une activité, un membre..." autocomplete="off"
                            id="search-options" value="">
                        <span class="mdi mdi-magnify search-widget-icon"></span>
                        <span class="mdi mdi-close-circle search-widget-icon search-widget-icon-close d-none"
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
            </div>

            <div class="d-flex align-items-center">

                <div class="dropdown d-md-none topbar-head-dropdown header-item">
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle shadow-none position-relative"
                        id="page-header-search-dropdown" data-bs-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="false">
                        <i class="bx bx-search fs-22"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                        aria-labelledby="page-header-search-dropdown">
                        <form class="p-3">
                            <div class="form-group m-0">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Search ..."
                                        aria-label="Recipient's username">
                                    <button class="btn btn-primary" type="submit"><i
                                            class="mdi mdi-magnify"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

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

                <div class="dropdown topbar-head-dropdown ms-1 header-item" id="notificationDropdown">
                    @php
                        $headerNotifications = auth()->user()->unreadNotifications()->latest()->take(15)->get();
                        $unreadCount = auth()->user()->unreadNotifications()->count();
                    @endphp
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
                            <img class="rounded-circle header-profile-user" 
                                 src="{{ auth()->user()->avatar_url }}"
                                 alt="Header Avatar">
                            <span class="text-start ms-xl-2">
                                <span class="d-none d-xl-inline-block ms-1 fw-medium user-name-text">
                                    {{ auth()->user()->first_name }} {{ auth()->user()->name }}
                                </span>
                                <span class="d-none d-xl-block ms-1 fs-12 user-name-sub-text">
                                    {{ auth()->user()->roles->first()?->name ?? 'Membre' }}
                                </span>
                            </span>
                        </span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <!-- item-->
                        <h6 class="dropdown-header">Bienvenue {{ auth()->user()->first_name }} !</h6>
                        <a class="dropdown-item" href="{{ route('profile.edit') }}"><i
                                class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i> <span
                                class="align-middle">Profil</span></a>
                        
                        @can('manage-users')
                        <a class="dropdown-item" href="{{ route('admin.settings.edit') }}"><i
                                class="mdi mdi-cog-outline text-muted fs-16 align-middle me-1"></i> <span
                                class="align-middle">Paramètres</span></a>
                        @endcan
                        
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="javascript:void(0);" 
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i>
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
    const searchInput = document.getElementById('search-options');
    const searchDropdown = document.getElementById('search-dropdown');
    const searchResultsContainer = document.getElementById('search-results-container');
    const closeOptionsBtn = document.getElementById('search-close-options');
    
    let searchTimeout = null;

    if (searchInput && searchDropdown && searchResultsContainer) {
        searchInput.addEventListener('input', function(e) {
            const query = e.target.value.trim();
            
            if (query.length > 0) {
                closeOptionsBtn?.classList.remove('d-none');
            } else {
                closeOptionsBtn?.classList.add('d-none');
                searchDropdown.classList.remove('show');
                return;
            }

            if (query.length >= 2) {
                searchDropdown.classList.add('show');
                searchResultsContainer.innerHTML = `
                    <div class="text-center pt-3 pb-3">
                        <div class="spinner-border text-primary spinner-border-sm" role="status">
                            <span class="visually-hidden">Chargement...</span>
                        </div>
                    </div>
                `;

                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    // Use a safe approach in case route() macro isn't available in standard js
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
                }, 400); // 400ms debounce
            } else {
                searchDropdown.classList.remove('show');
            }
        });

        // Close when clicking outside
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !searchDropdown.contains(e.target)) {
                searchDropdown.classList.remove('show');
            }
        });

        // Close button click
        if (closeOptionsBtn) {
            closeOptionsBtn.addEventListener('click', function() {
                searchInput.value = '';
                closeOptionsBtn.classList.add('d-none');
                searchDropdown.classList.remove('show');
            });
        }
    }
});
</script>
@endpush