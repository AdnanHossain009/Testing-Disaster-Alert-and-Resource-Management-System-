// Service Worker for Bangladesh Disaster Management System
// This enables offline functionality during network failures

const CACHE_NAME = 'disaster-alert-v1.0.0';
const OFFLINE_URL = '/offline.html';

// Critical files to cache for offline use
const CRITICAL_CACHE_FILES = [
  '/',
  '/alerts',
  '/shelters',
  '/request-help',
  '/login',
  '/register',
  '/offline.html',
  // CSS and JS files
  '/css/app.css',
  '/js/app.js',
  // Critical images
  '/images/logo.png',
  '/images/emergency-icon.png'
];

// Install event - Cache critical files
self.addEventListener('install', event => {
  console.log('Service Worker: Installing...');
  
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        console.log('Service Worker: Caching critical files');
        return cache.addAll(CRITICAL_CACHE_FILES);
      })
      .then(() => {
        console.log('Service Worker: Installation complete');
        return self.skipWaiting();
      })
      .catch(error => {
        console.error('Service Worker: Installation failed', error);
      })
  );
});

// Activate event - Clean old caches
self.addEventListener('activate', event => {
  console.log('Service Worker: Activating...');
  
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames
          .filter(cacheName => cacheName !== CACHE_NAME)
          .map(cacheName => {
            console.log('Service Worker: Deleting old cache', cacheName);
            return caches.delete(cacheName);
          })
      );
    }).then(() => {
      console.log('Service Worker: Activation complete');
      return self.clients.claim();
    })
  );
});

// Fetch event - Handle offline requests
self.addEventListener('fetch', event => {
  // Skip non-GET requests
  if (event.request.method !== 'GET') {
    return;
  }

  // Handle emergency requests offline
  if (event.request.url.includes('/request-help') && event.request.method === 'POST') {
    event.respondWith(handleOfflineRequest(event.request));
    return;
  }

  // Cache-first strategy for critical pages
  event.respondWith(
    caches.match(event.request)
      .then(cachedResponse => {
        if (cachedResponse) {
          console.log('Service Worker: Serving from cache', event.request.url);
          return cachedResponse;
        }

        // Network fallback
        return fetch(event.request)
          .then(networkResponse => {
            // Cache successful responses
            if (networkResponse.status === 200) {
              const responseClone = networkResponse.clone();
              caches.open(CACHE_NAME)
                .then(cache => {
                  cache.put(event.request, responseClone);
                });
            }
            return networkResponse;
          })
          .catch(() => {
            // Offline fallback
            console.log('Service Worker: Network failed, serving offline page');
            return caches.match(OFFLINE_URL);
          });
      })
  );
});

// Handle offline emergency requests
async function handleOfflineRequest(request) {
  try {
    const formData = await request.formData();
    const offlineRequest = {
      id: Date.now(),
      name: formData.get('name'),
      phone: formData.get('phone'),
      location: formData.get('location'),
      emergency_type: formData.get('emergency_type'),
      description: formData.get('description'),
      urgency: formData.get('urgency'),
      people_count: formData.get('people_count'),
      timestamp: new Date().toISOString(),
      status: 'offline_pending'
    };

    // Store in IndexedDB for later sync
    await storeOfflineRequest(offlineRequest);

    // Return success response
    return new Response(JSON.stringify({
      success: true,
      message: 'Emergency request stored offline. Will sync when connection returns.',
      request_id: offlineRequest.id
    }), {
      headers: { 'Content-Type': 'application/json' }
    });

  } catch (error) {
    console.error('Error handling offline request:', error);
    return new Response(JSON.stringify({
      success: false,
      message: 'Error storing offline request'
    }), {
      status: 500,
      headers: { 'Content-Type': 'application/json' }
    });
  }
}

// Store offline request in IndexedDB
async function storeOfflineRequest(request) {
  return new Promise((resolve, reject) => {
    const dbRequest = indexedDB.open('DisasterAlertDB', 1);
    
    dbRequest.onerror = () => reject(dbRequest.error);
    
    dbRequest.onsuccess = () => {
      const db = dbRequest.result;
      const transaction = db.transaction(['offline_requests'], 'readwrite');
      const store = transaction.objectStore('offline_requests');
      
      store.add(request);
      transaction.oncomplete = () => resolve();
      transaction.onerror = () => reject(transaction.error);
    };
    
    dbRequest.onupgradeneeded = () => {
      const db = dbRequest.result;
      if (!db.objectStoreNames.contains('offline_requests')) {
        const store = db.createObjectStore('offline_requests', { keyPath: 'id' });
        store.createIndex('timestamp', 'timestamp', { unique: false });
      }
    };
  });
}

// Background sync for offline requests
self.addEventListener('sync', event => {
  if (event.tag === 'background-sync-requests') {
    console.log('Service Worker: Background sync triggered');
    event.waitUntil(syncOfflineRequests());
  }
});

// Sync offline requests when connection returns
async function syncOfflineRequests() {
  try {
    const offlineRequests = await getOfflineRequests();
    
    for (const request of offlineRequests) {
      try {
        const response = await fetch('/request-help', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: JSON.stringify(request)
        });

        if (response.ok) {
          await removeOfflineRequest(request.id);
          console.log('Synced offline request:', request.id);
        }
      } catch (error) {
        console.error('Failed to sync request:', request.id, error);
      }
    }
  } catch (error) {
    console.error('Background sync failed:', error);
  }
}

// Get offline requests from IndexedDB
async function getOfflineRequests() {
  return new Promise((resolve, reject) => {
    const dbRequest = indexedDB.open('DisasterAlertDB', 1);
    
    dbRequest.onsuccess = () => {
      const db = dbRequest.result;
      const transaction = db.transaction(['offline_requests'], 'readonly');
      const store = transaction.objectStore('offline_requests');
      const getAllRequest = store.getAll();
      
      getAllRequest.onsuccess = () => resolve(getAllRequest.result);
      getAllRequest.onerror = () => reject(getAllRequest.error);
    };
    
    dbRequest.onerror = () => reject(dbRequest.error);
  });
}

// Remove synced offline request
async function removeOfflineRequest(requestId) {
  return new Promise((resolve, reject) => {
    const dbRequest = indexedDB.open('DisasterAlertDB', 1);
    
    dbRequest.onsuccess = () => {
      const db = dbRequest.result;
      const transaction = db.transaction(['offline_requests'], 'readwrite');
      const store = transaction.objectStore('offline_requests');
      
      store.delete(requestId);
      transaction.oncomplete = () => resolve();
      transaction.onerror = () => reject(transaction.error);
    };
    
    dbRequest.onerror = () => reject(dbRequest.error);
  });
}

// Push notification handler
self.addEventListener('push', event => {
  const options = {
    body: event.data ? event.data.text() : 'New disaster alert received',
    icon: '/images/icon-192x192.png',
    badge: '/images/badge-72x72.png',
    vibrate: [200, 100, 200],
    data: {
      dateOfArrival: Date.now(),
      primaryKey: 1
    },
    actions: [
      {
        action: 'explore',
        title: 'View Alert',
        icon: '/images/checkmark.png'
      },
      {
        action: 'close',
        title: 'Close',
        icon: '/images/xmark.png'
      }
    ]
  };

  event.waitUntil(
    self.registration.showNotification('Bangladesh Disaster Alert', options)
  );
});

// Notification click handler
self.addEventListener('notificationclick', event => {
  event.notification.close();

  if (event.action === 'explore') {
    event.waitUntil(
      clients.openWindow('/alerts')
    );
  }
});