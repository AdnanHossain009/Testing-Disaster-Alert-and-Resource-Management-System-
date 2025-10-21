
//Service Worker for Browser Push Notifications
 //Handles offline notifications, background sync, and PWA caching
 //Version 1.3 - Fixed caching errors with graceful fallback
 

const CACHE_NAME = 'disaster-alert-v1.3';
const NOTIFICATION_TAG = 'emergency-alert';

// Enhanced cache strategy for different resource types
const CACHE_URLS = {
    essential: [
        '/',
        '/dashboard',
        '/alerts',
        '/shelters',
        '/request-help',
        '/login',
        '/register',
        '/admin/dashboard',
        '/admin/requests',
        '/admin/alerts',
        '/admin/shelters',
        '/citizen/dashboard',
        '/relief/dashboard'
    ],
    api: [
        '/api/alerts',
        '/api/shelters',
        '/api/requests'
    ]
};

// install service worker
self.addEventListener('install', (event) => {
    console.log('🔧 Service Worker: Installing...');
    
    event.waitUntil(
        caches.open(CACHE_NAME).then(async (cache) => {
            console.log('📦 Service Worker: Caching app shell and essential pages');
            
            // Cache URLs individually to avoid failure if one URL doesn't exist
            const cachePromises = CACHE_URLS.essential.map(async (url) => {
                try {
                    await cache.add(url);
                    console.log('✅ Cached:', url);
                } catch (error) {
                    console.warn('⚠️ Failed to cache (will retry on visit):', url);
                }
            });
            
            await Promise.allSettled(cachePromises);
            console.log('✅ Service Worker: Installation complete');
        }).catch((error) => {
            console.error('❌ Service Worker: Installation failed', error);
        })
    );
    
    self.skipWaiting();
});

// activate service worker
self.addEventListener('activate', (event) => {
    console.log('✅ Service Worker: Activated');
    
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName !== CACHE_NAME) {
                        console.log('🗑️ Service Worker: Deleting old cache:', cacheName);
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
    
    return self.clients.claim();
});

// handle push notifications
self.addEventListener('push', (event) => {
    console.log('📬 Service Worker: Push notification received', event);
    
    let notificationData = {
        title: 'Emergency Alert',
        body: 'New emergency notification received',
        icon: '/favicon.ico',
        badge: '/favicon.ico',
        tag: NOTIFICATION_TAG,
        requireInteraction: true,
        data: {
            url: '/admin/dashboard'
        }
    };
    
    // parse push data if available
    if (event.data) {
        try {
            const data = event.data.json();
            notificationData = {
                ...notificationData,
                ...data,
                data: data.data || notificationData.data
            };
        } catch (error) {
            console.error('Error parsing push data:', error);
        }
    }
    
    const promiseChain = self.registration.showNotification(
        notificationData.title,
        {
            body: notificationData.body,
            icon: notificationData.icon,
            badge: notificationData.badge,
            tag: notificationData.tag,
            requireInteraction: notificationData.requireInteraction,
            data: notificationData.data,
            vibrate: [200, 100, 200],
            actions: [
                {
                    action: 'view',
                    title: 'View Details',
                    icon: '/favicon.ico'
                },
                {
                    action: 'dismiss',
                    title: 'Dismiss',
                    icon: '/favicon.ico'
                }
            ]
        }
    );
    
    event.waitUntil(promiseChain);
});

// Handle notification clicks
self.addEventListener('notificationclick', (event) => {
    console.log('🖱️ Service Worker: Notification clicked', event);
    
    event.notification.close();
    
    if (event.action === 'dismiss') {
        return;
    }
    
    // Get the URL from notification data
    const urlToOpen = event.notification.data?.url || '/admin/dashboard';
    
    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true })
            .then((clientList) => {
                // Check if there's already a window open
                for (const client of clientList) {
                    if (client.url.includes(urlToOpen) && 'focus' in client) {
                        return client.focus();
                    }
                }
                
                // Open new window if none exists
                if (clients.openWindow) {
                    return clients.openWindow(urlToOpen);
                }
            })
    );
});

// Handle notification close
self.addEventListener('notificationclose', (event) => {
    console.log('❌ Service Worker: Notification closed', event);
});

// Handle fetch requests (Enhanced with network-first for API, cache-first for assets)
self.addEventListener('fetch', (event) => {
    const url = new URL(event.request.url);
    
    // Only handle same-origin requests
    if (url.origin !== location.origin) {
        return;
    }
    
    // Only cache GET requests
    if (event.request.method !== 'GET') {
        return;
    }
    
    // Network-first strategy for API calls (always get fresh data when online)
    if (url.pathname.startsWith('/api/')) {
        event.respondWith(
            fetch(event.request)
                .then((response) => {
                    // Clone the response before caching
                    const responseToCache = response.clone();
                    
                    caches.open(CACHE_NAME).then((cache) => {
                        cache.put(event.request, responseToCache);
                    });
                    
                    return response;
                })
                .catch(() => {
                    // If network fails, try cache
                    return caches.match(event.request).then((cachedResponse) => {
                        if (cachedResponse) {
                            console.log('📱 Service Worker: Serving API from cache (OFFLINE)', event.request.url);
                            return cachedResponse;
                        }
                        
                        // Return offline indicator
                        return new Response(
                            JSON.stringify({
                                error: 'Offline',
                                message: 'You are currently offline. This data may be outdated.',
                                cached: false
                            }),
                            {
                                status: 503,
                                headers: { 'Content-Type': 'application/json' }
                            }
                        );
                    });
                })
        );
        return;
    }
    
    // Cache-first strategy for pages and assets
    event.respondWith(
        caches.match(event.request).then((cachedResponse) => {
            if (cachedResponse) {
                console.log('💾 Service Worker: Serving from cache', event.request.url);
                return cachedResponse;
            }
            
            // If not in cache, fetch from network
            return fetch(event.request).then((response) => {
                // Don't cache if not a success response
                if (!response || response.status !== 200 || response.type === 'error') {
                    return response;
                }
                
                // Clone the response before caching
                const responseToCache = response.clone();
                
                caches.open(CACHE_NAME).then((cache) => {
                    cache.put(event.request, responseToCache);
                });
                
                return response;
            }).catch(() => {
                // Return offline page if available
                console.log('⚠️ Service Worker: Network failed, no cache available');
                return caches.match('/dashboard');
            });
        })
    );
});

// Background sync for offline actions (Enhanced with alerts and shelters sync)
self.addEventListener('sync', (event) => {
    console.log('🔄 Service Worker: Background sync', event.tag);
    
    if (event.tag === 'sync-notifications') {
        event.waitUntil(syncNotifications());
    }
    
    if (event.tag === 'sync-alerts') {
        event.waitUntil(syncAlerts());
    }
    
    if (event.tag === 'sync-shelters') {
        event.waitUntil(syncShelters());
    }
    
    if (event.tag === 'sync-all') {
        event.waitUntil(Promise.all([
            syncAlerts(),
            syncShelters(),
            syncNotifications()
        ]));
    }
});

async function syncNotifications() {
    try {
        // Fetch latest emergency data
        const response = await fetch('/api/dashboard-stats');
        if (response.ok) {
            const data = await response.json();
            console.log('✅ Service Worker: Synced notifications', data);
            
            // Cache the response
            const cache = await caches.open(CACHE_NAME);
            cache.put('/api/dashboard-stats', response.clone());
        }
    } catch (error) {
        console.error('❌ Service Worker: Notification sync failed', error);
    }
}

async function syncAlerts() {
    try {
        console.log('🚨 Service Worker: Syncing alerts...');
        const response = await fetch('/api/alerts');
        
        if (response.ok) {
            const cache = await caches.open(CACHE_NAME);
            cache.put('/api/alerts', response.clone());
            console.log('✅ Service Worker: Alerts synced successfully');
            
            // Notify clients about updated data
            const clients = await self.clients.matchAll();
            clients.forEach(client => {
                client.postMessage({
                    type: 'ALERTS_SYNCED',
                    timestamp: new Date().toISOString()
                });
            });
        }
    } catch (error) {
        console.error('❌ Service Worker: Alerts sync failed', error);
    }
}

async function syncShelters() {
    try {
        console.log('🏠 Service Worker: Syncing shelters...');
        const response = await fetch('/api/shelters');
        
        if (response.ok) {
            const cache = await caches.open(CACHE_NAME);
            cache.put('/api/shelters', response.clone());
            console.log('✅ Service Worker: Shelters synced successfully');
            
            // Notify clients about updated data
            const clients = await self.clients.matchAll();
            clients.forEach(client => {
                client.postMessage({
                    type: 'SHELTERS_SYNCED',
                    timestamp: new Date().toISOString()
                });
            });
        }
    } catch (error) {
        console.error('❌ Service Worker: Shelters sync failed', error);
    }
}

// Handle messages from clients
self.addEventListener('message', (event) => {
    console.log('� Service Worker: Message received', event.data);
    
    if (event.data.action === 'skipWaiting') {
        self.skipWaiting();
    }
    
    if (event.data.action === 'syncAlerts') {
        event.waitUntil(syncAlerts());
    }
    
    if (event.data.action === 'syncShelters') {
        event.waitUntil(syncShelters());
    }
    
    if (event.data.action === 'clearCache') {
        event.waitUntil(
            caches.keys().then((cacheNames) => {
                return Promise.all(
                    cacheNames.map((cacheName) => {
                        if (cacheName.startsWith('disaster-alert-')) {
                            return caches.delete(cacheName);
                        }
                    })
                );
            }).then(() => {
                event.ports[0].postMessage({ success: true });
            })
        );
    }
});

console.log('�🚀 Service Worker: Loaded and ready (PWA Enhanced v1.1)');
