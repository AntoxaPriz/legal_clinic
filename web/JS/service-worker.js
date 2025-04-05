// web/service-worker.js
const CACHE_NAME = 'legal-clinic-cache-v1';
const QUEUE_NAME = 'sync-queue';
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
            .then(cache => cache.addAll(urlsToCache))
    );
});

self.addEventListener('fetch', event => {
    if (event.request.method === 'GET') {
        event.respondWith(
            caches.match(event.request)
                .then(response => response || fetch(event.request).then(networkResponse => {
                    const responseToCache = networkResponse.clone();
                    caches.open(CACHE_NAME).then(cache => cache.put(event.request, responseToCache));
                    return networkResponse;
                }).catch(() => caches.match('/index.html')))
        );
    } else {
        event.respondWith(
            fetch(event.request).catch(() => {
                const requestClone = event.request.clone();
                return queueRequest(requestClone);
            })
        );
    }
});

self.addEventListener('sync', event => {
    if (event.tag === 'sync-queued-requests') {
        event.waitUntil(syncQueuedRequests());
    }
});

async function queueRequest(request) {
    const db = await openDB();
    await db.put(QUEUE_NAME, {
        url: request.url,
        method: request.method,
        headers: Object.fromEntries(request.headers.entries()),
        body: await request.text()
    });
    return new Response(JSON.stringify({ success: true, queued: true }), { status: 202 });
}

async function syncQueuedRequests() {
    const db = await openDB();
    const queue = await db.getAll(QUEUE_NAME);
    for (const req of queue) {
        try {
            const response = await fetch(req.url, {
                method: req.method,
                headers: req.headers,
                body: req.method !== 'GET' ? req.body : undefined
            });
            if (response.ok) {
                await db.delete(QUEUE_NAME, req.url);
            }
        } catch (error) {
            console.error('Sync failed for:', req.url, error);
        }
    }
}

function openDB() {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open('legal-clinic-db', 1);
        request.onupgradeneeded = event => {
            const db = event.target.result;
            db.createObjectStore(QUEUE_NAME, { keyPath: 'url' });
        };
        request.onsuccess = event => resolve(event.target.result);
        request.onerror = event => reject(event.target.error);
    });
}

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