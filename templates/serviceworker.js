var CACHE_NAME = '{{ version }}';
var urlsToCache = JSON.parse("{{ urlsToCache }}");
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
        var headers = new Headers(event.request.headers);
        if (event.request.url.startsWith("https://hirsch.hochwarth-e.com/")) {
            headers.append("Authorization", 'Basic {{ credentials.string | raw }}');
        }
        event.respondWith(
            // fetch first, then cache
            fetch(event.request, { headers })
            .catch(function(error) {
                return caches.match(event.request)
                    .then(function(r) {
                        if (r) return r;
                        return caches.match("{{offline_route|raw}}").then(function(rt) { return rt });
                    });
            }));
    }
});

self.addEventListener('message', (event) => {
    if (event.data === 'SKIP_WAITING') {
        self.skipWaiting();
    }
});