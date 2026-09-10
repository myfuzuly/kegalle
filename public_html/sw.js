const CACHE = 'kegalle-v27';
const OFFLINE_URL = '/offline.html';
const STATIC_ASSETS = [
  '/',
  '/offline.html',
  '/manifest.json',
  '/css/kegalle-fonts.css?v=1',
  '/css/kegalle-frontend.css?v=156',
  '/js/kegalle-main.js?v=9',
  '/images/icon-192.png',
  '/images/icon-512.png',
  '/images/kegalle-placeholder.png',
  '/images/badge-72.png',
];

self.addEventListener('install', e => {
  e.waitUntil(
    caches.open(CACHE)
      .then(c => c.addAll(STATIC_ASSETS))
      .then(() => self.skipWaiting())
  );
});

self.addEventListener('activate', e => {
  e.waitUntil(
    caches.keys()
      .then(keys => Promise.all(keys.filter(k => k !== CACHE).map(k => caches.delete(k))))
      .then(() => self.clients.claim())
  );
});

self.addEventListener('fetch', e => {
  if (e.request.method !== 'GET') return;
  const url = new URL(e.request.url);

  // Skip cross-origin requests (analytics, CDN, etc.)
  if (url.origin !== self.location.origin) return;

  // Skip SW caching for admin routes entirely — let browser handle freshness
  if (url.pathname.startsWith('/admin') || url.pathname.startsWith('/dashboard')) return;

  // Cache-first for static assets (css, js, images, fonts, storage files)
  if (url.pathname.match(/^\/(css|js|images|fonts|storage)\//)) {
    e.respondWith(
      caches.match(e.request).then(cached => {
        if (cached) return cached;
        return fetch(e.request).then(res => {
          if (res.ok) {
            const clone = res.clone();
            caches.open(CACHE).then(c => c.put(e.request, clone));
          }
          return res;
        }).catch(() => new Response('', { status: 404 }));
      })
    );
    return;
  }

  // Network-first for HTML pages — fall back to cache, then offline page
  if (e.request.headers.get('accept')?.includes('text/html')) {
    e.respondWith(
      fetch(e.request)
        .then(res => {
          if (res.ok) {
            const clone = res.clone();
            caches.open(CACHE).then(c => c.put(e.request, clone));
          }
          return res;
        })
        .catch(() =>
          caches.match(e.request)
            .then(cached => cached || caches.match(OFFLINE_URL))
        )
    );
    return;
  }

  // Network-first for API calls — no offline fallback
  if (url.pathname.startsWith('/api/')) {
    e.respondWith(
      fetch(e.request).catch(() =>
        new Response(JSON.stringify({ error: 'offline' }), {
          status: 503,
          headers: { 'Content-Type': 'application/json' },
        })
      )
    );
    return;
  }
});

// ── Push notifications ─────────────────────────────────────────────────────
self.addEventListener('push', e => {
  e.waitUntil(
    fetch('/api/push/latest', { credentials: 'include' })
      .then(r => {
        if (!r.ok) throw new Error('not-ok');
        return r.json();
      })
      .then(data => self.registration.showNotification(data.title || 'kegalle Marketplace', {
        body: data.body || 'You have a new update.',
        icon: '/images/icon-192.png',
        badge: '/images/badge-72.png',
        data: { url: data.url || '/' },
        actions: [{ action: 'open', title: 'View' }],
        tag: 'kegalle-notification',
        renotify: true,
        vibrate: [200, 100, 200],
      }))
      .catch(() => self.registration.showNotification('kegalle Marketplace', {
        body: 'You have a new update. Tap to open.',
        icon: '/images/icon-192.png',
        badge: '/images/badge-72.png',
        data: { url: '/dashboard' },
      }))
  );
});

self.addEventListener('notificationclick', e => {
  e.notification.close();
  const targetUrl = (e.notification.data && e.notification.data.url) || '/dashboard';
  e.waitUntil(
    clients.matchAll({ type: 'window', includeUncontrolled: true }).then(windowClients => {
      for (const client of windowClients) {
        if (client.url.includes(self.location.origin) && 'focus' in client) {
          client.focus();
          if (client.navigate) client.navigate(targetUrl);
          return;
        }
      }
      if (clients.openWindow) return clients.openWindow(targetUrl);
    })
  );
});
