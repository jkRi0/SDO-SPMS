/* global self */

const CACHE_VERSION = 'v2';
const CACHE_NAME = `stms-cache-${CACHE_VERSION}`;
const OFFLINE_URL = './offline.html';

const DB_NAME = 'stms-offline-db';
const DB_VERSION = 1;
const QUEUE_STORE = 'requestQueue';
const SYNC_TAG = 'stms-sync';

const PRECACHE_URLS = [
  './',
  './dashboard.php',
  './login.php',
  './dept_notifications_all.php',
  './change_password.php',
  './transaction_view.php',
  OFFLINE_URL,
  './assets/vendor/bootstrap/bootstrap.min.css',
  './assets/vendor/bootstrap/bootstrap.bundle.min.js',
  './assets/vendor/fontawesome/css/all.min.css',
  './assets/vendor/jquery/jquery-3.7.1.min.js',
  './assets/polling_intervals.js',
  './assets/images/DEPED LOGO.jpg',
  './assets/images/header.jpg',
  './assets/images/footer.jpg',
  './assets/images/pwa-icon.svg'
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => cache.addAll(PRECACHE_URLS)).then(() => self.skipWaiting())
  );
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys()
      .then((keys) => Promise.all(keys.map((key) => (key !== CACHE_NAME ? caches.delete(key) : undefined))))
      .then(() => self.clients.claim())
      .then(() => replayQueue())
  );
});

function isAssetRequest(request) {
  return (
    request.destination === 'style' ||
    request.destination === 'script' ||
    request.destination === 'image' ||
    request.destination === 'font'
  );
}

function isApiRequest(url) {
  return url.pathname.includes('/api/');
}

function expectsJsonResponse(request) {
  const accept = (request.headers.get('accept') || '').toLowerCase();
  return accept.includes('application/json');
}

function isAjaxRequest(request) {
  return (request.headers.get('x-requested-with') || '') === 'XMLHttpRequest';
}

function shouldQueueWrite(request, url) {
  if (request.method === 'GET') {
    return false;
  }
  if (request.mode === 'navigate') {
    return false;
  }
  return isApiRequest(url) || isAjaxRequest(request) || expectsJsonResponse(request);
}

function isSameOrigin(url) {
  return url.origin === self.location.origin;
}

function openDb() {
  return new Promise((resolve, reject) => {
    const req = indexedDB.open(DB_NAME, DB_VERSION);
    req.onupgradeneeded = () => {
      const db = req.result;
      if (!db.objectStoreNames.contains(QUEUE_STORE)) {
        db.createObjectStore(QUEUE_STORE, { keyPath: 'id', autoIncrement: true });
      }
    };
    req.onsuccess = () => resolve(req.result);
    req.onerror = () => reject(req.error);
  });
}

async function enqueueRequest(request) {
  const url = request.url;
  const method = request.method;

  const headers = {};
  request.headers.forEach((value, key) => {
    headers[key] = value;
  });

  const bodyArrayBuffer = await request.clone().arrayBuffer();

  const db = await openDb();
  await new Promise((resolve, reject) => {
    const tx = db.transaction(QUEUE_STORE, 'readwrite');
    tx.oncomplete = () => resolve();
    tx.onerror = () => reject(tx.error);
    tx.objectStore(QUEUE_STORE).add({
      url,
      method,
      headers,
      body: bodyArrayBuffer,
      createdAt: Date.now()
    });
  });

  try {
    if (self.registration && self.registration.sync) {
      await self.registration.sync.register(SYNC_TAG);
    }
  } catch (e) {
  }
}

async function dequeueAll() {
  const db = await openDb();
  return await new Promise((resolve, reject) => {
    const tx = db.transaction(QUEUE_STORE, 'readonly');
    const store = tx.objectStore(QUEUE_STORE);
    const req = store.getAll();
    req.onsuccess = () => resolve(req.result || []);
    req.onerror = () => reject(req.error);
  });
}

async function deleteQueuedId(id) {
  const db = await openDb();
  await new Promise((resolve, reject) => {
    const tx = db.transaction(QUEUE_STORE, 'readwrite');
    tx.oncomplete = () => resolve();
    tx.onerror = () => reject(tx.error);
    tx.objectStore(QUEUE_STORE).delete(id);
  });
}

async function replayQueue() {
  const items = await dequeueAll();
  for (const item of items) {
    try {
      const headersObj = item.headers || {};
      const headers = new Headers();
      Object.keys(headersObj).forEach((k) => {
        const key = String(k).toLowerCase();
        if (key === 'content-length' || key === 'host' || key === 'connection') {
          return;
        }
        headers.set(k, headersObj[k]);
      });
      const res = await fetch(item.url, {
        method: item.method,
        headers,
        body: item.body,
        credentials: 'include'
      });
      if (res && res.ok) {
        await deleteQueuedId(item.id);
      }
    } catch (e) {
      break;
    }
  }
}

async function staleWhileRevalidate(request) {
  const cache = await caches.open(CACHE_NAME);
  const cached = await cache.match(request);

  const fetchPromise = fetch(request)
    .then((response) => {
      if (response && response.ok) {
        cache.put(request, response.clone());
      }
      return response;
    })
    .catch(() => undefined);

  return cached || fetchPromise;
}

async function networkFirst(request, fallbackUrl) {
  const cache = await caches.open(CACHE_NAME);
  try {
    const response = await fetch(request);
    if (response && response.ok) {
      cache.put(request, response.clone());
    }
    return response;
  } catch (e) {
    const cached = await cache.match(request);
    if (cached) return cached;
    if (fallbackUrl) {
      const fallback = await cache.match(fallbackUrl);
      if (fallback) return fallback;
    }
    throw e;
  }
}

self.addEventListener('fetch', (event) => {
  const request = event.request;
  const url = new URL(request.url);

  if (!isSameOrigin(url)) {
    return;
  }

  if (shouldQueueWrite(request, url)) {
    event.respondWith(
      fetch(request.clone()).catch(async () => {
        await enqueueRequest(request);
        return new Response(JSON.stringify({
          success: true,
          queued: true,
          message: 'Request queued (offline). It will sync when online.'
        }), {
          status: 202,
          headers: { 'Content-Type': 'application/json' }
        });
      })
    );
    return;
  }

  if (request.method !== 'GET') {
    return;
  }

  if (isApiRequest(url)) {
    event.respondWith(networkFirst(request));
    return;
  }

  if (request.mode === 'navigate') {
    event.respondWith(
      networkFirst(request, OFFLINE_URL)
    );
    return;
  }

  if (isAssetRequest(request)) {
    event.respondWith(staleWhileRevalidate(request));
  }
});

self.addEventListener('sync', (event) => {
  if (event.tag === SYNC_TAG) {
    event.waitUntil(replayQueue());
  }
});

self.addEventListener('message', (event) => {
  const data = event.data;
  if (!data || typeof data !== 'object') {
    return;
  }

  if (data.type === 'REPLAY_QUEUE') {
    event.waitUntil(replayQueue());
  }
});
