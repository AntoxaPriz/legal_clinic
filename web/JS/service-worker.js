// web/service-worker.js
const CACHE_NAME = 'legal-clinic-cache-v1';
const urlsToCache = [
    '/',
    '/index.html',
    '/dashboard.html',
    '/profile.html',
    '/tasks.html',
    '/clients.html',
    '/documents.html',
    '/cases.html',
    '/billing.html',
    '/reports.html',
    '/history.html',
    '/settings.html',
    '/contact.html',
    '/help.html',
    '/admin.html',
    '/login.html',
    '/register.html',
    '/logout.html',
    '/css/styles.css',
    '/css/bootstrap.min.css',
    '/js/main.js',
    '/js/bootstrap.bundle.min.js'
];

self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => {
                console.log('Caching app shell');
                return cache.addAll(urlsToCache);
            })
    );
});

self.addEventListener('fetch', event => {
    event.respondWith(
        caches.match(event.request)
            .then(response => {
                if (response) {
                    return response; // Возвращаем кэшированный ресурс
                }
                return fetch(event.request).then(networkResponse => {
                    if (!networkResponse || networkResponse.status !== 200 || networkResponse.type !== 'basic') {
                        return networkResponse;
                    }
                    const responseToCache = networkResponse.clone();
                    caches.open(CACHE_NAME)
                        .then(cache => {
                            cache.put(event.request, responseToCache);
                        });
                    return networkResponse;
                });
            }).catch(() => {
            return caches.match('/index.html'); // Fallback на главную страницу
        })
    );
});

self.addEventListener('activate', event => {
    const cacheWhitelist = [CACHE_NAME];
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cacheName => {
                    if (!cacheWhitelist.includes(cacheName)) {
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
});