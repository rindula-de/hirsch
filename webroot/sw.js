var CACHE_NAME = 'hirschcache';
var urlsToCache = [
    '/karte',
    '/zahlen-bitte',
    '/img/essen.jpg',
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
            return Promise.all(keys.filter(key => key !== CACHE_NAME).map(key => caches.delete()))
        })
    )
});
self.addEventListener('fetch', function(event) {
    event.respondWith(
        caches.match(event.request)
        .then(function(response) {
            // Cache hit - return response
            if (response) {
                return response;
            }
            return fetch(event.request, { headers: { Authorization: 'Basic user_auth_string' } }).then(
                function(response) {
                    // Check if we received a valid response
                    if (!response || response.status !== 200 || response.type !== 'basic' || response.url.includes("chrome-extension") || response.url.includes("modalInformationText")) {
                        return response;
                    }
                    // IMPORTANT: Clone the response. A response is a stream
                    // and because we want the browser to consume the response
                    // as well as the cache consuming the response, we need
                    // to clone it so we have two streams.
                    var responseToCache = response.clone();
                    caches.open(CACHE_NAME)
                        .then(function(cache) {
                            cache.put(event.request, responseToCache);
                        });
                    return response;
                }
            );
        })
    );
});