        <!-- ========== App Menu ========== -->
        <div class="app-menu navbar-menu">
            <!-- LOGO -->
            <div class="navbar-brand-box">
                <!-- Dark Logo-->
                <a href="{{ route('dashboard') }}" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="{{ $appSettings->logo_sm_url ?? asset('assets/images/logo-sm.png') }}" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ $appSettings->logo_dark_url ?? asset('assets/images/logo-dark.png') }}" alt="" height="17">
                    </span>
                </a>
                <!-- Light Logo-->
                <a href="{{ route('dashboard') }}" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="{{ $appSettings->logo_sm_url ?? asset('assets/images/logo-sm.png') }}" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ $appSettings->logo_light_url ?? asset('assets/images/logo-light.png') }}" alt="" height="17">
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
                        {{-- SUPER ADMIN SAAS NAVIGATION --}}
                        @if(auth()->check() && auth()->user()->isSuperAdmin())
                            <li class="menu-title"><span class="text-warning fw-bold"><i class="mdi mdi-shield-crown me-1"></i>Plateforme SaaS</span></li>

                            @if(session()->has('tenant_church_id'))
                                <li class="nav-item px-3 mb-2">
                                    <div class="alert alert-warning border-0 p-2 mb-0 rounded-3 fs-11">
                                        <i class="mdi mdi-information-outline me-1"></i>Mode Support Église
                                        <a href="{{ route('super-admin.leave-impersonation') }}" class="btn btn-xs btn-dark d-block mt-1 rounded-pill w-100 fs-10">Quitter le support</a>
                                    </div>
                                </li>
                            @endif

                            <li class="nav-item">
                                <a class="nav-link menu-link {{ request()->routeIs('super-admin.dashboard') ? 'active' : '' }}" href="{{ route('super-admin.dashboard') }}">
                                    <i class="mdi mdi-view-dashboard-outline text-warning"></i> <span>Supervision SaaS</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link menu-link {{ request()->routeIs('super-admin.churches.*') ? 'active' : '' }}" href="#sidebarSuperAdmin" data-bs-toggle="collapse"
                                    role="button" aria-expanded="{{ request()->routeIs('super-admin.churches.*') ? 'true' : 'false' }}" aria-controls="sidebarSuperAdmin">
                                    <i class="mdi mdi-church text-warning"></i> <span>Gestion des Églises</span>
                                </a>
                                <div class="collapse menu-dropdown {{ request()->routeIs('super-admin.churches.*') ? 'show' : '' }}" id="sidebarSuperAdmin">
                                    <ul class="nav nav-sm flex-column">
                                        <li class="nav-item">
                                            <a href="{{ route('super-admin.churches.index') }}" class="nav-link {{ request()->routeIs('super-admin.churches.index', 'super-admin.churches.show') ? 'active' : '' }}">
                                                Toutes les églises
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('super-admin.churches.create') }}" class="nav-link {{ request()->routeIs('super-admin.churches.create') ? 'active' : '' }}">
                                                Inscrire une église
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                        @endif

                        {{-- Avertissement d'expiration pour l'Admin d'Église --}}
                        @if(auth()->check() && !auth()->user()->isSuperAdmin() && auth()->user()->church && auth()->user()->church->expiresInLessThan30Days())
                            <li class="nav-item px-3 mb-2">
                                <div class="alert alert-warning border-0 p-2 mb-0 rounded-3 fs-11">
                                    <i class="mdi mdi-alert me-1"></i>Abonnement expire dans {{ auth()->user()->church->daysLeftInSubscription() }}j !
                                </div>
                            </li>
                        @endif

                        <li class="menu-title"><span data-key="t-menu">Menu Église</span></li>
                        
                        {{-- 1. Tableau de bord --}}
                        @if(auth()->check())
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                <i class="mdi mdi-speedometer"></i> <span>Tableau de bord</span>
                            </a>
                        </li>
                        @endif

                        {{-- 2. Scanner QR --}}
                        @can('attendance.scan_qr')
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->routeIs('attendance.scan') ? 'active' : '' }}" href="{{ route('attendance.scan') }}">
                                <i class="mdi mdi-qrcode-scan"></i> <span>Scanner QR</span>
                            </a>
                        </li>
                        @endcan

                        {{-- 3. Gestion des activités --}}
                        @if(auth()->check())
                            @canany(['activity.create', 'activity.edit', 'activity.view', 'attendance.view_own', 'attendance.validate_manual_own', 'attendance.validate_manual_all'])
                            <li class="nav-item">
                                <a class="nav-link menu-link {{ request()->routeIs('activities.*', 'admin.activities.*', 'admin.activity-types.*') ? 'active' : '' }}" href="#sidebarActivities" data-bs-toggle="collapse"
                                    role="button" aria-expanded="{{ request()->routeIs('activities.*', 'admin.activities.*', 'admin.activity-types.*') ? 'true' : 'false' }}" aria-controls="sidebarActivities">
                                    <i class="mdi mdi-calendar-text"></i> <span>Gestion des activités</span>
                                </a>
                                <div class="collapse menu-dropdown {{ request()->routeIs('activities.*', 'admin.activities.*', 'admin.activity-types.*') ? 'show' : '' }}" id="sidebarActivities">
                                    <ul class="nav nav-sm flex-column">
                                        @can('activity.create')
                                        <li class="nav-item">
                                            <a href="{{ route('admin.activities.create') }}" class="nav-link {{ request()->routeIs('admin.activities.create') ? 'active' : '' }}">
                                                Créer une activité
                                            </a>
                                        </li>
                                        @endcan
                                        <li class="nav-item">
                                            <a href="{{ route('activities.index') }}" class="nav-link {{ request()->routeIs('activities.index') && !request()->has('manageable') ? 'active' : '' }}">
                                                Activités publiées
                                            </a>
                                        </li>
                                        @can('activity.view')
                                        <li class="nav-item">
                                            <a href="{{ route('admin.activities.index') }}" class="nav-link {{ request()->routeIs('admin.activities.*') && !request()->routeIs('admin.activities.create') ? 'active' : '' }}">
                                                Toutes les activités
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('admin.activity-types.index') }}" class="nav-link {{ request()->routeIs('admin.activity-types.*') ? 'active' : '' }}">
                                                Types d'activités
                                            </a>
                                        </li>
                                        @endcan
                                        @canany(['attendance.validate_manual_own', 'attendance.validate_manual_all'])
                                        <li class="nav-item">
                                            <a href="{{ route('activities.index', ['manageable' => 1]) }}" class="nav-link {{ request()->routeIs('activities.index') && request()->has('manageable') ? 'active' : '' }}">
                                                Émargement Groupe
                                            </a>
                                        </li>
                                        @endcanany
                                    </ul>
                                </div>
                            </li>
                            @else
                            <li class="nav-item">
                                <a class="nav-link menu-link {{ request()->routeIs('activities.*') ? 'active' : '' }}" href="{{ route('activities.index') }}">
                                    <i class="mdi mdi-calendar-text"></i> <span>Activités</span>
                                </a>
                            </li>
                            @endcanany
                        @endif

                        {{-- 4. Gestion des groupes --}}
                        @can('group.view')
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->routeIs('admin.groups.*') ? 'active' : '' }}" href="#sidebarGroups" data-bs-toggle="collapse"
                                role="button" aria-expanded="{{ request()->routeIs('admin.groups.*') ? 'true' : 'false' }}" aria-controls="sidebarGroups">
                                <i class="mdi mdi-account-group-outline"></i> <span>Gestion des groupes</span>
                            </a>
                            <div class="collapse menu-dropdown {{ request()->routeIs('admin.groups.*') ? 'show' : '' }}" id="sidebarGroups">
                                <ul class="nav nav-sm flex-column">
                                    @can('group.create')
                                    <li class="nav-item">
                                        <a href="{{ route('admin.groups.create') }}" class="nav-link {{ request()->routeIs('admin.groups.create') ? 'active' : '' }}">
                                            Créer un groupe
                                        </a>
                                    </li>
                                    @endcan
                                    <li class="nav-item">
                                        <a href="{{ route('admin.groups.index') }}" class="nav-link {{ request()->routeIs('admin.groups.index') ? 'active' : '' }}">
                                            Liste des groupes
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        @endcan

                        {{-- Mes Groupes (Profil) --}}
                        @if(auth()->check())
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}" href="{{ route('profile.edit') }}#groups">
                                <i class="mdi mdi-account-group-outline"></i> <span>Mes Groupes</span>
                            </a>
                        </li>
                        @endif

                        {{-- 5. Gestion des membres --}}
                        @can('member.view')
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="#sidebarMembers" data-bs-toggle="collapse"
                                role="button" aria-expanded="{{ request()->routeIs('admin.users.*') ? 'true' : 'false' }}" aria-controls="sidebarMembers">
                                <i class="mdi mdi-account-multiple-outline"></i> <span>Gestion des membres</span>
                            </a>
                            <div class="collapse menu-dropdown {{ request()->routeIs('admin.users.*') ? 'show' : '' }}" id="sidebarMembers">
                                <ul class="nav nav-sm flex-column">
                                    @can('member.create')
                                    <li class="nav-item">
                                        <a href="{{ route('admin.users.create') }}" class="nav-link {{ request()->routeIs('admin.users.create') ? 'active' : '' }}">
                                            Nouveau membre
                                        </a>
                                    </li>
                                    @endcan
                                    <li class="nav-item">
                                        <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.index') ? 'active' : '' }}">
                                            Liste des membres
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        @endcan

                        {{-- Notifications --}}
                        @canany(['notification.send_all', 'notification.send_group', 'notification.send_role', 'notification.send_individual'])
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}" href="#sidebarNotifications" data-bs-toggle="collapse"
                                role="button" aria-expanded="{{ request()->routeIs('admin.notifications.*') ? 'true' : 'false' }}" aria-controls="sidebarNotifications">
                                <i class="mdi mdi-bell-outline"></i> <span>Notifications</span>
                            </a>
                            <div class="collapse menu-dropdown {{ request()->routeIs('admin.notifications.*') ? 'show' : '' }}" id="sidebarNotifications">
                                <ul class="nav nav-sm flex-column">
                                    @can('notification.send_all')
                                    <li class="nav-item">
                                        <a href="{{ route('admin.notifications.send-all') }}" class="nav-link {{ request()->routeIs('admin.notifications.send-all') ? 'active' : '' }}">Envoyer Globale</a>
                                    </li>
                                    @endcan
                                    @can('notification.send_group')
                                    <li class="nav-item">
                                        <a href="{{ route('admin.notifications.send-group') }}" class="nav-link {{ request()->routeIs('admin.notifications.send-group') ? 'active' : '' }}">Envoyer par Groupe</a>
                                    </li>
                                    @endcan
                                    @can('notification.send_role')
                                    <li class="nav-item">
                                        <a href="{{ route('admin.notifications.send-role') }}" class="nav-link {{ request()->routeIs('admin.notifications.send-role') ? 'active' : '' }}">Envoyer par Rôle</a>
                                    </li>
                                    @endcan
                                    @can('notification.send_individual')
                                    <li class="nav-item">
                                        <a href="{{ route('admin.notifications.send-individual') }}" class="nav-link {{ request()->routeIs('admin.notifications.send-individual') ? 'active' : '' }}">Envoyer Individuelle</a>
                                    </li>
                                    @endcan
                                </ul>
                            </div>
                        </li>
                        @endcanany

                        {{-- Finances (Cotisations et Trésorerie) --}}
                        @canany(['finance.collect_own_group', 'finance.view_all'])
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->routeIs('admin.finance.*') ? 'active' : '' }}" href="#sidebarFinance" data-bs-toggle="collapse"
                                role="button" aria-expanded="{{ request()->routeIs('admin.finance.*') ? 'true' : 'false' }}" aria-controls="sidebarFinance">
                                <i class="mdi mdi-cash-multiple"></i> <span>Finances</span>
                            </a>
                            <div class="collapse menu-dropdown {{ request()->routeIs('admin.finance.*') ? 'show' : '' }}" id="sidebarFinance">
                                <ul class="nav nav-sm flex-column">
                                    @can('finance.collect_own_group')
                                    <li class="nav-item">
                                        <a href="{{ route('admin.finance.contributions.index') }}" class="nav-link {{ request()->routeIs('admin.finance.contributions.index') ? 'active' : '' }}">Suivi des contributions</a>
                                    </li>
                                    @endcan
                                    @can('finance.view_all')
                                    <li class="nav-item">
                                        <a href="{{ route('admin.finance.treasury.index') }}" class="nav-link {{ request()->routeIs('admin.finance.treasury.index') ? 'active' : '' }}">Trésorerie Générale</a>
                                    </li>
                                    @endcan
                                </ul>
                            </div>
                        </li>
                        @endcanany

                        {{-- Statistiques --}}
                        @canany(['stats.view_global', 'stats.view_own_group'])
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->routeIs('admin.statistics.*') ? 'active' : '' }}" href="#sidebarStats" data-bs-toggle="collapse"
                                role="button" aria-expanded="{{ request()->routeIs('admin.statistics.*') ? 'true' : 'false' }}" aria-controls="sidebarStats">
                                <i class="mdi mdi-chart-bar"></i> <span>Statistiques</span>
                            </a>
                            <div class="collapse menu-dropdown {{ request()->routeIs('admin.statistics.*') ? 'show' : '' }}" id="sidebarStats">
                                <ul class="nav nav-sm flex-column">
                                    @can('stats.view_global')
                                    <li class="nav-item">
                                        <a href="{{ route('admin.statistics.index') }}" class="nav-link {{ request()->routeIs('admin.statistics.index') ? 'active' : '' }}">Stats Globales</a>
                                    </li>
                                    @endcan
                                    @can('stats.view_own_group')
                                    <li class="nav-item">
                                        <a href="{{ route('admin.statistics.group.index') }}" class="nav-link {{ request()->routeIs('admin.statistics.group.*') ? 'active' : '' }}">Stats de mon Groupe</a>
                                    </li>
                                    @endcan
                                </ul>
                            </div>
                        </li>
                        @endcanany

                        {{-- Administration --}}
                        @canany(['role.manage', 'permission.manage', 'audit.view'])
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->routeIs('admin.roles.*', 'admin.audit-logs.*') ? 'active' : '' }}" href="#sidebarAdmin" data-bs-toggle="collapse"
                                role="button" aria-expanded="{{ request()->routeIs('admin.roles.*', 'admin.audit-logs.*') ? 'true' : 'false' }}" aria-controls="sidebarAdmin">
                                <i class="mdi mdi-account-cog-outline"></i> <span data-key="t-admin">Administration</span>
                            </a>
                            <div class="collapse menu-dropdown {{ request()->routeIs('admin.roles.*', 'admin.audit-logs.*') ? 'show' : '' }}" id="sidebarAdmin">
                                <ul class="nav nav-sm flex-column">
                                    @canany(['role.manage', 'permission.manage'])
                                    <li class="nav-item">
                                        <a href="{{ route('admin.roles.index') }}" class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                                            Rôles & Permissions
                                        </a>
                                    </li>
                                    @endcanany
                                    @can('audit.view')
                                    <li class="nav-item">
                                        <a href="{{ route('admin.audit-logs.index') }}" class="nav-link {{ request()->routeIs('admin.audit-logs.*') ? 'active' : '' }}">
                                            Logs d'Audit
                                        </a>
                                    </li>
                                    @endcan
                                </ul>
                            </div>
                        </li>
                        @endcanany

                        {{-- Site & Paramètres --}}
                        @can('manage-users')
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->routeIs('admin.settings.*', 'admin.galleries.*') ? 'active' : '' }}" href="#sidebarSettings" data-bs-toggle="collapse"
                                role="button" aria-expanded="{{ request()->routeIs('admin.settings.*', 'admin.galleries.*') ? 'true' : 'false' }}" aria-controls="sidebarSettings">
                                <i class="mdi mdi-web"></i> <span data-key="t-site">Site & Paramètres</span>
                            </a>
                            <div class="collapse menu-dropdown {{ request()->routeIs('admin.settings.*', 'admin.galleries.*') ? 'show' : '' }}" id="sidebarSettings">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="{{ route('admin.galleries.index') }}" class="nav-link {{ request()->routeIs('admin.galleries.*') ? 'active' : '' }}">
                                            Galerie Photos
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.settings.edit') }}" class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                                            Paramètres de l'App
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        @endcan

                    </ul>
                </div>
                <!-- Sidebar -->
            </div>

            <div class="sidebar-background"></div>
        </div>
        <!-- Left Sidebar End -->
        <!-- Vertical Overlay-->
        <div class="vertical-overlay"></div>