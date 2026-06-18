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
                        @if(auth()->check())
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="{{ route('dashboard') }}">
                                <i class="mdi mdi-speedometer"></i> <span>Tableau de bord</span>
                            </a>
                        </li>
                        @endif

                        {{-- 2. Scanner QR --}}
                        @can('attendance.scan_qr')
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="{{ route('attendance.scan') }}">
                                <i class="mdi mdi-qrcode-scan"></i> <span>Scanner QR</span>
                            </a>
                        </li>
                        @endcan

                        {{-- 3. Gestion des activités --}}
                        @if(auth()->check())
                            @canany(['activity.create', 'activity.edit', 'activity.view', 'attendance.view_own', 'attendance.validate_manual_own', 'attendance.validate_manual_all'])
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
                                        @can('activity.view')
                                        <li class="nav-item">
                                            <a href="{{ route('admin.activities.index') }}" class="nav-link">
                                                Toutes les activités
                                            </a>
                                        </li>
                                        @endcan
                                        @can('activity.create')
                                        <li class="nav-item">
                                            <a href="{{ route('admin.activities.create') }}" class="nav-link">
                                                Créer une activité
                                            </a>
                                        </li>
                                        @endcan
                                        @canany(['attendance.validate_manual_own', 'attendance.validate_manual_all'])
                                        <li class="nav-item">
                                            <a href="{{ route('activities.index', ['manageable' => 1]) }}" class="nav-link">
                                                Émargement Groupe
                                            </a>
                                        </li>
                                        @endcanany
                                    </ul>
                                </div>
                            </li>
                            @else
                            <li class="nav-item">
                                <a class="nav-link menu-link" href="{{ route('activities.index') }}">
                                    <i class="mdi mdi-calendar-text"></i> <span>Activités</span>
                                </a>
                            </li>
                            @endcanany
                        @endif

                        {{-- 4. Gestion des groupes --}}
                        @can('group.view')
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
                                    @can('group.create')
                                    <li class="nav-item">
                                        <a href="{{ route('admin.groups.create') }}" class="nav-link">
                                            Créer un groupe
                                        </a>
                                    </li>
                                    @endcan
                                </ul>
                            </div>
                        </li>
                        @endcan

                        {{-- Mes Groupes (Profil) --}}
                        @if(auth()->check())
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="{{ route('profile.edit') }}#groups">
                                <i class="mdi mdi-account-group-outline"></i> <span>Mes Groupes</span>
                            </a>
                        </li>
                        @endif

                        {{-- 5. Gestion des membres --}}
                        @can('member.view')
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
                                    @can('member.create')
                                    <li class="nav-item">
                                        <a href="{{ route('admin.users.create') }}" class="nav-link">
                                            Nouveau membre
                                        </a>
                                    </li>
                                    @endcan
                                </ul>
                            </div>
                        </li>
                        @endcan

                        {{-- Notifications --}}
                        @canany(['notification.send_all', 'notification.send_group', 'notification.send_role', 'notification.send_individual'])
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#sidebarNotifications" data-bs-toggle="collapse"
                                role="button" aria-expanded="false" aria-controls="sidebarNotifications">
                                <i class="mdi mdi-bell-outline"></i> <span>Notifications</span>
                            </a>
                            <div class="collapse menu-dropdown" id="sidebarNotifications">
                                <ul class="nav nav-sm flex-column">
                                    @can('notification.send_all')
                                    <li class="nav-item">
                                        <a href="#" class="nav-link">Envoyer Globale</a>
                                    </li>
                                    @endcan
                                    @can('notification.send_group')
                                    <li class="nav-item">
                                        <a href="#" class="nav-link">Envoyer par Groupe</a>
                                    </li>
                                    @endcan
                                    @can('notification.send_role')
                                    <li class="nav-item">
                                        <a href="#" class="nav-link">Envoyer par Rôle</a>
                                    </li>
                                    @endcan
                                    @can('notification.send_individual')
                                    <li class="nav-item">
                                        <a href="#" class="nav-link">Envoyer Individuelle</a>
                                    </li>
                                    @endcan
                                </ul>
                            </div>
                        </li>
                        @endcanany

                        {{-- Statistiques & Rapports --}}
                        @canany(['stats.view_global', 'stats.view_own_group', 'report.export_global', 'report.export_own_group'])
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#sidebarStats" data-bs-toggle="collapse"
                                role="button" aria-expanded="false" aria-controls="sidebarStats">
                                <i class="mdi mdi-chart-bar"></i> <span>Statistiques & Rapports</span>
                            </a>
                            <div class="collapse menu-dropdown" id="sidebarStats">
                                <ul class="nav nav-sm flex-column">
                                    @can('stats.view_global')
                                    <li class="nav-item">
                                        <a href="#" class="nav-link">Stats Globales</a>
                                    </li>
                                    @endcan
                                    @can('stats.view_own_group')
                                    <li class="nav-item">
                                        <a href="#" class="nav-link">Stats de mon Groupe</a>
                                    </li>
                                    @endcan
                                    @canany(['report.export_global', 'report.export_own_group'])
                                    <li class="nav-item">
                                        <a href="#" class="nav-link">Exporter Rapports</a>
                                    </li>
                                    @endcanany
                                </ul>
                            </div>
                        </li>
                        @endcanany

                        {{-- Administration --}}
                        @canany(['role.manage', 'permission.manage', 'audit.view', 'member.edit'])
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#sidebarAdmin" data-bs-toggle="collapse"
                                role="button" aria-expanded="false" aria-controls="sidebarAdmin">
                                <i class="mdi mdi-account-cog-outline"></i> <span data-key="t-admin">Administration</span>
                            </a>
                            <div class="collapse menu-dropdown" id="sidebarAdmin">
                                <ul class="nav nav-sm flex-column">
                                    @canany(['role.manage', 'permission.manage'])
                                    <li class="nav-item">
                                        <a href="{{ route('admin.roles.index') }}" class="nav-link">
                                            Rôles & Permissions
                                        </a>
                                    </li>
                                    @endcanany
                                    @can('member.edit')
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
                        @endcanany

                    </ul>
                </div>
                <!-- Sidebar -->
            </div>

            <div class="sidebar-background"></div>
        </div>
        <!-- Left Sidebar End -->
        <!-- Vertical Overlay-->
        <div class="vertical-overlay"></div>