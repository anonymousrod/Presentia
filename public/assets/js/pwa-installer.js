/**
 * PWA Registration & Add To Home Screen (A2HS) Manager
 * Presentia / MeVoici
 */

let deferredPrompt = null;
const isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
const isMobile = /Android|iPhone|iPad|iPod/i.test(navigator.userAgent);
const isIOS = /iPhone|iPad|iPod/i.test(navigator.userAgent);

// 1. Enregistrement du Service Worker
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js')
            .then((registration) => {
                // Service worker registered
            })
            .catch((error) => {
                console.warn('[PWA] Service Worker non actif (nécessite HTTPS ou localhost):', error);
            });
    });
}

// 2. Écoute de l'événement natif Chrome/Android
window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt = e;
    showPwaInstallBanner();
});

// 3. Affichage automatique de la bannière sur mobile si non installé
window.addEventListener('DOMContentLoaded', () => {
    if (isMobile && !isStandalone && !sessionStorage.getItem('pwa_dismissed')) {
        setTimeout(() => {
            showPwaInstallBanner();
        }, 1500);
    }
});

function showPwaInstallBanner() {
    if (isStandalone || document.getElementById('pwa-install-banner') || sessionStorage.getItem('pwa_dismissed')) {
        return;
    }

    const banner = document.createElement('div');
    banner.id = 'pwa-install-banner';
    banner.style.cssText = `
        position: fixed;
        bottom: 16px;
        left: 16px;
        right: 16px;
        z-index: 99999;
        background: #ffffff;
        border-radius: 14px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        padding: 14px 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        border: 1px solid rgba(64, 81, 137, 0.2);
        animation: pwaSlideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        max-width: 420px;
        margin: 0 auto;
    `;

    banner.innerHTML = `
        <style>
            @keyframes pwaSlideUp {
                from { transform: translateY(120%); opacity: 0; }
                to { transform: translateY(0); opacity: 1; }
            }
        </style>
        <img src="/assets/images/icons/icon-72x72.png" alt="MeVoici" style="width: 42px; height: 42px; border-radius: 10px; flex-shrink: 0; box-shadow: 0 2px 6px rgba(0,0,0,0.1);">
        <div style="flex-grow: 1; text-align: left; min-width: 0;">
            <div style="font-weight: 700; font-size: 13px; color: #212529; margin-bottom: 2px;">Installer MeVoici</div>
            <div style="font-size: 11px; color: #6c757d; line-height: 1.3; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Ajouter à l'écran d'accueil</div>
        </div>
        <div style="display: flex; align-items: center; gap: 6px; flex-shrink: 0;">
            <button id="pwa-install-btn" style="background: #405189; color: #ffffff; border: none; padding: 8px 14px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer;">
                Installer
            </button>
            <button id="pwa-dismiss-btn" style="background: transparent; border: none; color: #adb5bd; font-size: 20px; cursor: pointer; padding: 4px 6px; line-height: 1;" title="Fermer">
                &times;
            </button>
        </div>
    `;

    document.body.appendChild(banner);

    // Clic sur Installer
    document.getElementById('pwa-install-btn').addEventListener('click', async () => {
        if (deferredPrompt) {
            deferredPrompt.prompt();
            const { outcome } = await deferredPrompt.userChoice;
            deferredPrompt = null;
            banner.remove();
        } else if (isIOS) {
            alert("Pour installer sur iPhone :\n1. Appuyez sur le bouton Partager (icône carré avec flèche ⎋ en bas).\n2. Sélectionnez 'Sur l'écran d'accueil'.");
        } else {
            alert("Pour installer sur Android :\n1. Appuyez sur les 3 points (⋮) en haut à droite du navigateur.\n2. Sélectionnez 'Ajouter à l'écran d'accueil' ou 'Installer l'application'.");
        }
    });

    // Clic sur Fermer
    document.getElementById('pwa-dismiss-btn').addEventListener('click', () => {
        sessionStorage.setItem('pwa_dismissed', 'true');
        banner.remove();
    });
}

// 4. Confirmation après installation
window.addEventListener('appinstalled', () => {
    deferredPrompt = null;
    const banner = document.getElementById('pwa-install-banner');
    if (banner) banner.remove();
});
