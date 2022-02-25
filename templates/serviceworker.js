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
self.addEventListener('message', (event) => {
    if (event.data === 'SKIP_WAITING') {
        self.skipWaiting();
    }
});
