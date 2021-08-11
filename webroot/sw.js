var CACHE_NAME = 'hirschcache';
var urlsToCache = [
    '/karte',
    '/favicon.png',
    '/img/essen.jpg',
    '/css/style.css',
    '/css/normalize.min.css',
    '/css/milligram.min.css',
    '/css/cake.css',
    '/fallback.html',
    '/js/main.js',
    '/js/main.min.js',
    '/js/pageEnd.js',
    '/js/pageEnd.min.js',
    'https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js',
    'https://cdn.jsdelivr.net/npm/flatpickr',
    'https://fonts.googleapis.com/icon?family=Material+Icons',
    'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css',
    'https://fonts.googleapis.com/css?family=Raleway:400,700',
    'https://fonts.gstatic.com/s/raleway/v22/1Ptug8zYS_SKggPNyCAIT5lu.woff2',
    'https://fonts.gstatic.com/s/raleway/v22/1Ptug8zYS_SKggPNyCkIT5lu.woff2',
    'https://fonts.gstatic.com/s/raleway/v22/1Ptug8zYS_SKggPNyCIIT5lu.woff2',
    'https://fonts.gstatic.com/s/raleway/v22/1Ptug8zYS_SKggPNyCMIT5lu.woff2',
    'https://fonts.gstatic.com/s/raleway/v22/1Ptug8zYS_SKggPNyC0ITw.woff2',
];
self.addEventListener('install', function(event) {
    event.waitUntil(
        caches.open(CACHE_NAME)
        .then(function(cache) {
            console.log('Opened cache ' + CACHE_NAME);
            return cache.addAll(urlsToCache);
        })
    );
});
self.addEventListener('activate', function(event) {
    event.waitUntil(
        caches.keys().then(keys => {
            return Promise.all(keys.filter(key => key !== CACHE_NAME).map(key => caches.delete(key)))
        })
    )
});
self.addEventListener('fetch', function(event) {
    if (event.request.method == "GET") {
        event.respondWith(
            caches.match(event.request)
            .then(function(response) {
                var headers = {};
                if (event.request.url.includes("hirsch.hochwarth-e.com")) {
                    Object.assign(headers, { Authorization: 'Basic user_auth_string' });
                }
                if (event.request.url.includes("get-")) {
                    Object.assign(headers, { Accept: 'application/json' });
                }

                const request = new Request(event.request, { headers });
                // Cache hit - return response
                return response || fetch(request).catch(() => {
                    if (event.request.url.includes("get-tagesessen")) {
                        var init = { "status": 200, "statusText": "Dummy" };
                        return new Response('{"displayData": [], "file": ""}', init);
                    };
                    if (event.request.url.includes("modalInformationText")) {
                        var init = { "status": 418, "statusText": "I am a Teapot" };
                        return new Response("Du bist aktuell offline! Die angezeigten Daten sind unter Umständen nicht aktuell!", init);
                    };
                    if (event.request.url.includes("order-until")) {
                        var init = { "status": 200, "statusText": "Offline" };
                        return new Response("Du bist aktuell offline!", init);
                    };
                    return caches.match("/fallback.html")
                });
            })
        );
    } else {
        return;
    }
});

self.addEventListener('message', (event) => {
    if (event.data === 'SKIP_WAITING') {
        self.skipWaiting();
    }
});