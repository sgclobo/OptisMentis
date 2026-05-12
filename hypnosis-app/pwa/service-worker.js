/* ==========================================================
   OptisMentis Hypnotherapy — Service Worker
   Caches key static assets and landing page for offline use.
   ========================================================== */

const CACHE_NAME = 'optismentis-v1';
const OFFLINE_URL = '/index.php';

const ASSETS_TO_CACHE = [
  '/index.php',
  '/services.php',
  '/assets/css/style.css',
  '/assets/js/main.js',
  'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
  'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js',
  'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css'
];

self.addEventListener('install', function (event) {
  event.waitUntil(
    caches.open(CACHE_NAME).then(function (cache) {
      return cache.addAll(ASSETS_TO_CACHE);
    }).then(function () {
      self.skipWaiting();
    })
  );
});

self.addEventListener('activate', function (event) {
  event.waitUntil(
    caches.keys().then(function (cacheNames) {
      return Promise.all(
        cacheNames.filter(function (name) {
          return name !== CACHE_NAME;
        }).map(function (name) {
          return caches.delete(name);
        })
      );
    }).then(function () {
      self.clients.claim();
    })
  );
});

self.addEventListener('fetch', function (event) {
  // Only intercept GET requests.
  if (event.request.method !== 'GET') return;

  event.respondWith(
    fetch(event.request).then(function (response) {
      // Clone and store fresh responses for static assets.
      if (response && response.status === 200 && response.type === 'basic') {
        var url = event.request.url;
        if (url.includes('/assets/') || url.includes('jsdelivr.net')) {
          var responseToCache = response.clone();
          caches.open(CACHE_NAME).then(function (cache) {
            cache.put(event.request, responseToCache);
          });
        }
      }
      return response;
    }).catch(function () {
      return caches.match(event.request).then(function (cached) {
        return cached || caches.match(OFFLINE_URL);
      });
    })
  );
});
