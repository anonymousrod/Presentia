        <!-- ========== App Menu ========== -->
        <div class="app-menu navbar-menu">
            <!-- LOGO -->
            <div class="navbar-brand-box">
                <!-- Dark Logo-->
                <a href="{{ route('dashboard') }}" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="{{ asset('assets/images/logo-sm.png') }}" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset('assets/images/logo-dark.png') }}" alt="" height="17">
                    </span>
                </a>
                <!-- Light Logo-->
                <a href="{{ route('dashboard') }}" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="{{ asset('assets/images/logo-sm.png') }}" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset('assets/images/logo-light.png') }}" alt="" height="17">
                    </span>
                </a>
                <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover"
                    id="vertical-hover">
                    <i class="ri-record-circle-line"></i>
                </button>
            </div>

            <div id="scrollbar">
                <div class="container-fluid">

                    <div id="two-column-menu">
                    </div>
                    <ul class="navbar-nav" id="navbar-nav">
                        <li class="menu-title"><span data-key="t-menu">Menu</span></li>
                        
                        {{-- 1. Tableau de bord --}}
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="{{ route('dashboard') }}">
                                <i class="mdi mdi-speedometer"></i> <span>Tableau de bord</span>
                            </a>
                        </li>

                        {{-- 2. Scanner QR --}}
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="{{ route('attendance.scan') }}">
                                <i class="mdi mdi-qrcode-scan"></i> <span>Scanner QR</span>
                            </a>
                        </li>

                        {{-- 3. Gestion des activités --}}
                        @if(auth()->check() && (auth()->user()->hasRole('Administrateur') || auth()->user()->hasRole('Chef de groupe') || auth()->user()->can('manage-users')))
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#sidebarActivities" data-bs-toggle="collapse"
                                role="button" aria-expanded="false" aria-controls="sidebarActivities">
                                <i class="mdi mdi-calendar-text"></i> <span>Gestion des activités</span>
                            </a>
                            <div class="collapse menu-dropdown" id="sidebarActivities">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="{{ route('activities.index') }}" class="nav-link">
                                            Activités publiées
                                        </a>
                                    </li>
                                    @if(auth()->user()->hasRole('Administrateur'))
                                    <li class="nav-item">
                                        <a href="{{ route('admin.activities.index') }}" class="nav-link">
                                            Toutes les activités
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.activities.create') }}" class="nav-link">
                                            Créer une activité
                                        </a>
                                    </li>
                                    @endif
                                    @if(auth()->user()->ledGroups()->exists() || auth()->user()->hasRole('Administrateur'))
                                    <li class="nav-item">
                                        <a href="{{ route('activities.index', ['manageable' => 1]) }}" class="nav-link">
                                            Émargement Groupe
                                        </a>
                                    </li>
                                    @endif
                                </ul>
                            </div>
                        </li>
                        @else
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="{{ route('activities.index') }}">
                                <i class="mdi mdi-calendar-text"></i> <span>Activités</span>
                            </a>
                        </li>
                        @endif

                        {{-- 4. Gestion des groupes --}}
                        @if(auth()->check() && auth()->user()->hasRole('Administrateur'))
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#sidebarGroups" data-bs-toggle="collapse"
                                role="button" aria-expanded="false" aria-controls="sidebarGroups">
                                <i class="mdi mdi-account-group-outline"></i> <span>Gestion des groupes</span>
                            </a>
                            <div class="collapse menu-dropdown" id="sidebarGroups">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="{{ route('admin.groups.index') }}" class="nav-link">
                                            Liste des groupes
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.groups.create') }}" class="nav-link">
                                            Créer un groupe
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        @elseif(auth()->check())
                            @if(auth()->user()->hasRole('Chef de groupe'))
                                @php
                                    $firstLedGroup = auth()->user()->ledGroups()->first();
                                @endphp
                                @if($firstLedGroup)
                                <li class="nav-item">
                                    <a class="nav-link menu-link" href="{{ route('admin.groups.show', $firstLedGroup) }}">
                                        <i class="mdi mdi-account-group-outline"></i> <span>Mon Groupe</span>
                                    </a>
                                </li>
                                @endif
                            @elseif(auth()->user()->hasRole('Jeune'))
                                @php
                                    $firstGroup = auth()->user()->groups()->wherePivotNull('left_at')->first();
                                @endphp
                                @if($firstGroup)
                                <li class="nav-item">
                                    <a class="nav-link menu-link" href="{{ route('admin.groups.show', $firstGroup) }}">
                                        <i class="mdi mdi-account-group-outline"></i> <span>Mon Groupe</span>
                                    </a>
                                </li>
                                @endif
                            @endif
                        @endif

                        {{-- 5. Gestion des membres --}}
                        @if(auth()->check() && auth()->user()->can('manage-users'))
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#sidebarMembers" data-bs-toggle="collapse"
                                role="button" aria-expanded="false" aria-controls="sidebarMembers">
                                <i class="mdi mdi-account-multiple-outline"></i> <span>Gestion des membres</span>
                            </a>
                            <div class="collapse menu-dropdown" id="sidebarMembers">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="{{ route('admin.users.index') }}" class="nav-link">
                                            Liste des membres
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.users.create') }}" class="nav-link">
                                            Nouveau membre
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        @endif

                        {{-- 6. Administration --}}
                        @if(auth()->check() && (auth()->user()->can('manage-users') || auth()->user()->can('audit.view')))
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#sidebarAdmin" data-bs-toggle="collapse"
                                role="button" aria-expanded="false" aria-controls="sidebarAdmin">
                                <i class="mdi mdi-account-cog-outline"></i> <span data-key="t-admin">Administration</span>
                            </a>
                            <div class="collapse menu-dropdown" id="sidebarAdmin">
                                <ul class="nav nav-sm flex-column">
                                    @can('manage-users')
                                    <li class="nav-item">
                                        <a href="{{ route('admin.roles.index') }}" class="nav-link">
                                            Rôles & Permissions
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.password-requests.index') }}" class="nav-link">
                                            Demandes de Reset
                                        </a>
                                    </li>
                                    @endcan
                                    @can('audit.view')
                                    <li class="nav-item">
                                        <a href="{{ route('admin.audit-logs.index') }}" class="nav-link">
                                            Logs d'Audit
                                        </a>
                                    </li>
                                    @endcan
                                </ul>
                            </div>
                        </li>
                        @endif

                    </ul>
                </div>
                <!-- Sidebar -->
            </div>

            <div class="sidebar-background"></div>
        </div>
        <!-- Left Sidebar End -->
        <!-- Vertical Overlay-->
        <div class="vertical-overlay"></div>