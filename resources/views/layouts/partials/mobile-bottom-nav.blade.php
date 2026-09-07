{{-- Mobile Bottom Navigation Bar (Visible uniquement sur mobile / PWA) --}}
@if(auth()->check())
<nav class="mobile-bottom-nav d-block d-md-none" id="mobileBottomNav" aria-label="Navigation mobile">
    <div class="mobile-bottom-nav-inner">
        @if(auth()->user()->isSuperAdmin())
            {{-- CONFIGURATION SPÉCIFIQUE SUPER-ADMIN SAAS --}}

            {{-- 1. SaaS Dashboard --}}
            <a href="{{ route('super-admin.dashboard') }}" class="mobile-nav-item {{ request()->routeIs('super-admin.dashboard') ? 'active' : '' }}">
                <div class="mobile-nav-icon-box">
                    <i class="ri-dashboard-2-line"></i>
                </div>
                <span class="mobile-nav-label">SaaS</span>
            </a>

            {{-- 2. Liste des Églises --}}
            <a href="{{ route('super-admin.churches.index') }}" class="mobile-nav-item {{ request()->routeIs('super-admin.churches.index', 'super-admin.churches.show') ? 'active' : '' }}">
                <div class="mobile-nav-icon-box">
                    <i class="ri-building-line"></i>
                </div>
                <span class="mobile-nav-label">Églises</span>
            </a>

            {{-- 3. Inscrire / Nouvelle Église (Bouton central surélevé) --}}
            <a href="{{ route('super-admin.churches.create') }}" class="mobile-nav-item mobile-nav-item-center {{ request()->routeIs('super-admin.churches.create') ? 'active' : '' }}" title="Inscrire une église">
                <div class="mobile-nav-center-btn super-admin-btn">
                    <i class="ri-add-line"></i>
                </div>
                <span class="mobile-nav-label">Inscrire</span>
            </a>

            {{-- 4. Dashboard Général Église --}}
            <a href="{{ route('dashboard') }}" class="mobile-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <div class="mobile-nav-icon-box">
                    <i class="ri-apps-line"></i>
                </div>
                <span class="mobile-nav-label">Aperçu</span>
            </a>

            {{-- 5. Menu Complet --}}
            <button type="button" class="mobile-nav-item mobile-menu-trigger border-0 bg-transparent" id="mobileMenuTrigger" aria-label="Ouvrir le menu">
                <div class="mobile-nav-icon-box">
                    <i class="ri-menu-4-line"></i>
                </div>
                <span class="mobile-nav-label">Menu</span>
            </button>

        @else
            {{-- CONFIGURATION UTILISATEURS ÉGLISE (Admin d'église, Responsable, Membre) --}}

            {{-- 1. Accueil / Dashboard --}}
            <a href="{{ route('dashboard') }}" class="mobile-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <div class="mobile-nav-icon-box">
                    <i class="ri-home-5-line"></i>
                </div>
                <span class="mobile-nav-label">Accueil</span>
            </a>

            {{-- 2. Activités / Cultes --}}
            <a href="{{ route('activities.index') }}" class="mobile-nav-item {{ request()->routeIs('activities.*', 'admin.activities.*') ? 'active' : '' }}">
                <div class="mobile-nav-icon-box">
                    <i class="ri-calendar-event-line"></i>
                </div>
                <span class="mobile-nav-label">Activités</span>
            </a>

            {{-- 3. Action Principale : Scanner QR (Bouton central surélevé) --}}
            @if(auth()->user()->can('attendance.scan_qr'))
                <a href="{{ route('attendance.scan') }}" class="mobile-nav-item mobile-nav-item-center {{ request()->routeIs('attendance.scan') ? 'active' : '' }}" title="Scanner Présence">
                    <div class="mobile-nav-center-btn">
                        <i class="ri-qr-scan-2-line"></i>
                    </div>
                    <span class="mobile-nav-label">Scanner</span>
                </a>
            @else
                <a href="{{ route('dashboard') }}" class="mobile-nav-item mobile-nav-item-center" title="Présence">
                    <div class="mobile-nav-center-btn">
                        <i class="ri-check-double-line"></i>
                    </div>
                    <span class="mobile-nav-label">Présence</span>
                </a>
            @endif

            {{-- 4. Membres (si admin) ou Groupes (si fidèle / jeune) --}}
            @if(auth()->user()->can('member.view'))
                <a href="{{ route('admin.users.index') }}" class="mobile-nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <div class="mobile-nav-icon-box">
                        <i class="ri-team-line"></i>
                    </div>
                    <span class="mobile-nav-label">Membres</span>
                </a>
            @else
                <a href="{{ route('admin.groups.index') }}" class="mobile-nav-item {{ request()->routeIs('admin.groups.*') ? 'active' : '' }}">
                    <div class="mobile-nav-icon-box">
                        <i class="ri-group-line"></i>
                    </div>
                    <span class="mobile-nav-label">Groupes</span>
                </a>
            @endif

            {{-- 5. Menu Complet (Déclenche / Referme le tiroir latéral Sidebar) --}}
            <button type="button" class="mobile-nav-item mobile-menu-trigger border-0 bg-transparent" id="mobileMenuTrigger" aria-label="Ouvrir le menu">
                <div class="mobile-nav-icon-box">
                    <i class="ri-menu-4-line"></i>
                </div>
                <span class="mobile-nav-label">Menu</span>
            </button>
        @endif
    </div>
</nav>

{{-- Script d'activation pour le bouton Menu complet (ouverture/fermeture sans altérer l'apparence du bouton) --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const triggers = document.querySelectorAll('.mobile-menu-trigger, #mobileMenuTrigger');

        function toggleSidebar(e) {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }
            const isCurrentlyOpen = document.body.classList.contains('vertical-sidebar-enable');
            if (isCurrentlyOpen) {
                document.body.classList.remove('vertical-sidebar-enable');
            } else {
                document.body.classList.add('vertical-sidebar-enable');
                document.documentElement.setAttribute('data-sidebar-size', 'lg');
            }
        }

        triggers.forEach(function(trigger) {
            trigger.addEventListener('click', toggleSidebar);
        });
    });
</script>
@endif
