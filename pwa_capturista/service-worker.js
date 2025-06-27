// service-worker.js - PWA Capturista
self.addEventListener('install', event => {
  // Cachear recursos iniciales aquí
  self.skipWaiting();
});

self.addEventListener('fetch', event => {
  // Estrategia de cacheo aquí
}); 