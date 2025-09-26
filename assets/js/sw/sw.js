const CACHE_VERSION = 'v1';
const STATIC_CACHE = `crm-static-${CACHE_VERSION}`;
const API_CACHE = `crm-api-${CACHE_VERSION}`;

const PRECACHE_URLS = [
    `${self.location.origin}/index.php`,
    `${self.location.origin}/assets/css/app.all.css`,
    `${self.location.origin}/assets/css/custom-style.css`,
    `${self.location.origin}/assets/js/app.all.js`,
    `${self.location.origin}/assets/js/select2/select2.js`,
    `${self.location.origin}/assets/js/select2/select2.css`,
    `${self.location.origin}/assets/js/select2/select2-bootstrap.min.css`,
    `${self.location.origin}/assets/bootstrap/css/bootstrap.min.css`
];

const OFFLINE_API_ROUTES = [
    '/index.php/leads/list_data',
    '/index.php/clients/list_data'
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(STATIC_CACHE)
            .then((cache) => cache.addAll(PRECACHE_URLS.map((url) => new Request(url, { credentials: 'include' }))))
            .then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) =>
            Promise.all(
                keys
                    .filter((key) => key !== STATIC_CACHE && key !== API_CACHE)
                    .map((key) => caches.delete(key))
            )
        ).then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', (event) => {
    const { request } = event;
    const requestUrl = new URL(request.url);

    if (shouldHandleApiPost(request, requestUrl)) {
        event.respondWith(handleApiPostRequest(request));
        return;
    }

    if (request.method !== 'GET' || requestUrl.origin !== self.location.origin) {
        return;
    }

    if (shouldHandleStaticAsset(requestUrl)) {
        event.respondWith(cacheFirst(request));
        return;
    }

    if (shouldHandleApiGet(request, requestUrl)) {
        event.respondWith(networkFirst(request, API_CACHE));
        return;
    }

    if (request.destination === 'document') {
        event.respondWith(networkFirst(request, STATIC_CACHE));
    }
});

function shouldHandleStaticAsset(url) {
    return PRECACHE_URLS.includes(url.href) || /\.(?:css|js|png|jpg|jpeg|gif|svg|webp|woff2?|ttf)$/.test(url.pathname);
}

function shouldHandleApiGet(request, url) {
    const acceptHeader = request.headers.get('accept') || '';
    return acceptHeader.includes('application/json') || url.pathname.includes('/list_data');
}

function shouldHandleApiPost(request, url) {
    if (request.method !== 'POST' || url.origin !== self.location.origin) {
        return false;
    }

    return OFFLINE_API_ROUTES.some((route) => url.pathname.endsWith(route));
}

function cacheFirst(request) {
    return caches.match(request).then((cached) => {
        if (cached) {
            return cached;
        }

        return fetch(request).then((response) => {
            const cloned = response.clone();
            caches.open(STATIC_CACHE).then((cache) => cache.put(request, cloned));
            return response;
        });
    });
}

function networkFirst(request, cacheName) {
    return fetch(request)
        .then((response) => {
            const cloned = response.clone();
            caches.open(cacheName).then((cache) => cache.put(request, cloned));
            return response;
        })
        .catch(() =>
            caches.match(request).then((cached) => {
                if (cached) {
                    return annotateOfflineResponse(cached);
                }
                return Promise.reject('offline');
            })
        );
}

function handleApiPostRequest(request) {
    const bodyClonePromise = request.clone().text();

    return bodyClonePromise.then((body) => {
        const cacheKey = buildApiCacheKey(request.url, body);

        return fetch(request.clone())
            .then((response) => {
                const cloned = response.clone();
                caches.open(API_CACHE).then((cache) => cache.put(cacheKey, cloned));
                return response;
            })
            .catch(() =>
                caches.open(API_CACHE)
                    .then((cache) => cache.match(cacheKey))
                    .then((cached) => {
                        if (cached) {
                            return annotateOfflineResponse(cached);
                        }
                        return createEmptyApiResponse();
                    })
            );
    });
}

function buildApiCacheKey(url, body) {
    const normalizedBody = normalizeBody(body);
    const hash = hashString(normalizedBody);
    return `${url}?offline-cache=${hash}`;
}

function normalizeBody(body) {
    const params = new URLSearchParams(body);
    params.delete('draw');
    const entries = Array.from(params.entries()).sort(([a], [b]) => (a < b ? -1 : a > b ? 1 : 0));
    return entries.map(([key, value]) => `${key}=${value}`).join('&');
}

function hashString(input) {
    let hash = 0;
    for (let i = 0; i < input.length; i += 1) {
        hash = (hash << 5) - hash + input.charCodeAt(i);
        hash |= 0; // Convert to 32bit integer
    }
    return Math.abs(hash);
}

function annotateOfflineResponse(response) {
    const cloned = response.clone();
    const contentType = cloned.headers.get('content-type') || '';

    if (contentType.indexOf('application/json') === -1) {
        return Promise.resolve(response);
    }

    return cloned
        .json()
        .then((data) => {
            if (data && typeof data === 'object') {
                data.offline = true;
                return rebuildJsonResponse(response, data);
            }
            return response;
        })
        .catch(() => response);
}

function rebuildJsonResponse(originalResponse, data) {
    const headers = new Headers(originalResponse.headers);
    headers.set('Content-Type', 'application/json');

    return new Response(JSON.stringify(data), {
        status: originalResponse.status,
        statusText: originalResponse.statusText,
        headers
    });
}

function createEmptyApiResponse() {
    return new Response(JSON.stringify({
        data: [],
        recordsTotal: 0,
        recordsFiltered: 0,
        offline: true
    }), {
        status: 200,
        headers: { 'Content-Type': 'application/json' }
    });
}
