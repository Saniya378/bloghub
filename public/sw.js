const CACHE_NAME = 'bloghub-v2';
const STATIC_ASSETS = [
  '/assets/css/style.css',
  '/assets/css/components.css',
  '/assets/css/dark-mode.css',
  '/assets/js/data.js',
  '/assets/js/components.js',
  '/assets/js/main.js'
];

// Skip service worker entirely on localhost/127.0.0.1 (development)
self.addEventListener('install', (event) => {
  const scope = new URL(self.registration.scope);
  if (scope.hostname === 'localhost' || scope.hostname === '127.0.0.1') {
    self.skipWaiting();
    return;
  }
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll(STATIC_ASSETS);
    })
  );
  self.skipWaiting();
});

// Activate: clean up old caches
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames
          .filter((name) => name !== CACHE_NAME)
          .map((name) => caches.delete(name))
      );
    })
  );
  self.clients.claim();
});

// Fetch: network-first for /assets/ css/js, cache-first for other static, network-first for pages
self.addEventListener('fetch', (event) => {
  const { request } = event;
  const url = new URL(request.url);

  // Only handle GET requests
  if (request.method !== 'GET') return;

  // For /assets/ css and js: network-first with cache fallback
  if (url.pathname.startsWith('/assets/') && (url.pathname.endsWith('.css') || url.pathname.endsWith('.js'))) {
    event.respondWith(
      fetch(request).then((response) => {
        const clone = response.clone();
        caches.open(CACHE_NAME).then((cache) => cache.put(request, clone));
        return response;
      }).catch(() => {
        return caches.match(request);
      })
    );
    return;
  }

  // For other static assets: cache-first strategy
  if (url.pathname.startsWith('/assets/')) {
    event.respondWith(
      caches.match(request).then((cached) => {
        return cached || fetch(request).then((response) => {
          const clone = response.clone();
          caches.open(CACHE_NAME).then((cache) => cache.put(request, clone));
          return response;
        });
      })
    );
    return;
  }

  // For pages: network-first with cache fallback
  event.respondWith(
    fetch(request).then((response) => {
      const clone = response.clone();
      caches.open(CACHE_NAME).then((cache) => cache.put(request, clone));
      return response;
    }).catch(() => {
      return caches.match(request);
    })
  );
});
