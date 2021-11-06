var CACHE_NAME = '{{ version }}';
var urlsToCache = {{ urlsToCache | json_encode(constant('JSON_UNESCAPED_SLASHES')) | raw }};
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
    if (event.request.method === "GET") {
        event.respondWith(
            caches.match(event.request)
            .then(function(response) {
                var headers = {};
                if (event.request.url.startsWith("https://hirsch.hochwarth-e.com/")) {
                    Object.assign(headers, { Authorization: 'Basic {{ credentials.string | raw }}' });
                }
                if (event.request.url.includes("get-")) {
                    Object.assign(headers, { Accept: 'application/json' });
                }

                const request = new Request(event.request, { headers });
                // Cache hit - return response
                return response || fetch(request).catch(() => {
                    let init;
                    if (event.request.url.includes("get-tagesessen")) {
                        init = {"status": 200, "statusText": "Dummy"};
                        return new Response('{"displayData": [], "file": ""}', init);
                    }
                    if (event.request.url.includes("modalInformationText")) {
                        init = { "status": 418, "statusText": "I am a Teapot" };
                        return new Response("Du bist aktuell offline! Die angezeigten Daten sind unter Umständen nicht aktuell!", init);
                    }
                    if (event.request.url.includes("order-until")) {
                        init = { "status": 200, "statusText": "Offline" };
                        return new Response("Du bist aktuell offline!", init);
                    }
                    return caches.match("/fallback.html")
                });
            })
        );
    }
});

self.addEventListener('message', (event) => {
    if (event.data === 'SKIP_WAITING') {
        self.skipWaiting();
    }
});