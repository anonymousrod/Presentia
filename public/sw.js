/**
 * Service Worker — Presentia / MeVoici PWA
 * Version: 1.0.2
 */

const CACHE_NAME = 'mevoici-pwa-v1.0.2';
const OFFLINE_URL = '/offline.html';

const CRITICAL_ASSETS = [
    OFFLINE_URL,
    '/manifest.json',
    '/assets/images/icons/icon-192x192.png',
    '/assets/images/icons/icon-512x512.png',
    '/assets/images/icons/apple-touch-icon.png',
    '/assets/css/bootstrap.min.css',
    '/assets/css/icons.min.css',
    '/assets/css/app.min.css',
    '/assets/css/custom.min.css',
    '/assets/js/layout.js',
    '/assets/js/pwa-installer.js'
];

// Installation : Mise en cache garantie élément par élément
self.addEventListener('install', (event) => {
    self.skipWaiting(); // Prendre le contrôle immédiatement

    event.waitUntil(
        caches.open(CACHE_NAME).then(async (cache) => {
            // Mettre en cache chaque ressource individuellement (si l'une échoue, les autres restent en cache)
            for (const asset of CRITICAL_ASSETS) {
                try {
                    await cache.add(asset);
                } catch (err) {
                    console.warn('[PWA] Ressource non mise en cache :', asset, err);
                }
            }
        })
    );
});

// Activation : Nettoyage des anciens caches et prise de contrôle de tous les onglets
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName !== CACHE_NAME) {
                        console.log('[PWA] Suppression ancien cache :', cacheName);
                        return caches.delete(cacheName);
                    }
                })
            );
        }).then(() => self.clients.claim())
    );
});

// Interception des requêtes réseau
self.addEventListener('fetch', (event) => {
    const request = event.request;

    // Uniquement pour les requêtes GET
    if (request.method !== 'GET') {
        return;
    }

    const url = new URL(request.url);

    // 1. NAVIGATION & PAGES HTML (Network-First avec Fallback sur Cache ou Page Offline)
    if (
        request.mode === 'navigate' ||
        request.destination === 'document' ||
        request.headers.get('accept')?.includes('text/html')
    ) {
        event.respondWith(
            fetch(request)
                .then((networkResponse) => {
                    if (networkResponse && networkResponse.status === 200) {
                        const copy = networkResponse.clone();
                        caches.open(CACHE_NAME).then((cache) => cache.put(request, copy));
                    }
                    return networkResponse;
                })
                .catch(async () => {
                    // 1. Chercher la page demandée dans le cache
                    const cachedResponse = await caches.match(request);
                    if (cachedResponse) {
                        return cachedResponse;
                    }

                    // 2. Sinon renvoyer la page offline.html
                    const offlinePage = await caches.match(OFFLINE_URL);
                    if (offlinePage) {
                        return offlinePage;
                    }

                    // 3. Secours ultime en cas de cache vide
                    return new Response(`<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hors connexion — MeVoici</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f3f3f9; color: #495057; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; padding: 20px; text-align: center; }
        .card { background: #fff; padding: 40px 30px; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.06); max-width: 440px; width: 100%; border: 1px solid rgba(0,0,0,0.05); }
        .icon { width: 64px; height: 64px; background: rgba(64,81,137,0.1); color: #405189; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; font-size: 28px; }
        h1 { font-size: 20px; color: #343a40; margin-bottom: 10px; }
        p { font-size: 14px; color: #878a99; line-height: 1.5; margin-bottom: 25px; }
        button { background: #405189; color: #fff; border: none; padding: 12px 28px; border-radius: 50px; font-size: 14px; font-weight: 600; cursor: pointer; width: 100%; transition: all 0.2s; }
        button:hover { background: #364473; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">📡</div>
        <h1>Connexion Internet indisponible</h1>
        <p>Vous êtes actuellement hors-ligne. Vérifiez votre connexion réseau pour accéder aux services MeVoici.</p>
        <button onclick="window.location.reload();">Réessayer la connexion</button>
    </div>
</body>
</html>`, {
                        headers: { 'Content-Type': 'text/html; charset=utf-8' },
                        status: 200
                    });
                })
        );
        return;
    }

    // 2. FICHIERS STATIQUES (CSS, JS, Images, Polices) : Cache-First
    if (
        url.pathname.startsWith('/assets/') ||
        url.pathname.endsWith('.css') ||
        url.pathname.endsWith('.js') ||
        url.pathname.endsWith('.woff2') ||
        url.pathname.endsWith('.woff') ||
        url.pathname.endsWith('.ttf') ||
        url.pathname.endsWith('.png') ||
        url.pathname.endsWith('.webp') ||
        url.pathname.endsWith('.jpg') ||
        url.pathname.endsWith('.svg') ||
        url.pathname.endsWith('.ico')
    ) {
        event.respondWith(
            caches.match(request).then((cachedResponse) => {
                if (cachedResponse) {
                    return cachedResponse;
                }

                return fetch(request).then((networkResponse) => {
                    if (networkResponse && networkResponse.status === 200) {
                        const copy = networkResponse.clone();
                        caches.open(CACHE_NAME).then((cache) => cache.put(request, copy));
                    }
                    return networkResponse;
                }).catch(() => {
                    return new Response('', { status: 408, statusText: 'Request Timeout' });
                });
            })
        );
        return;
    }

    // 3. API / JSON : Fallback JSON
    if (request.headers.get('accept')?.includes('application/json')) {
        event.respondWith(
            fetch(request).catch(async () => {
                const cachedResponse = await caches.match(request);
                if (cachedResponse) {
                    return cachedResponse;
                }
                return new Response(JSON.stringify({ 
                    offline: true, 
                    message: "Vous êtes actuellement hors connexion." 
                }), {
                    headers: { 'Content-Type': 'application/json' },
                    status: 503
                });
            })
        );
        return;
    }

    // 4. Par défaut : fetch normal avec fallback cache
    event.respondWith(
        fetch(request).catch(() => caches.match(request))
    );
});
