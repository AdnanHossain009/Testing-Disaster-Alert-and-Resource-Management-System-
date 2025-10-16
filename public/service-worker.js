/**
 * Service Worker for Browser Push Notifications
 * Handles offline notifications and background sync
 */

const CACHE_NAME = 'disaster-alert-v1';
const NOTIFICATION_TAG = 'emergency-alert';

// Install service worker
self.addEventListener('install', (event) => {
    console.log('🔧 Service Worker: Installing...');
    
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            console.log('📦 Service Worker: Caching app shell');
            return cache.addAll([
                '/',
                '/admin/dashboard',
                '/admin/requests',
            ]);
        })
    );
    
    self.skipWaiting();
});

// Activate service worker
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

// Handle push notifications
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
    
    // Parse push data if available
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

// Handle fetch requests (for offline support)
self.addEventListener('fetch', (event) => {
    // Only cache GET requests
    if (event.request.method !== 'GET') {
        return;
    }
    
    event.respondWith(
        caches.match(event.request).then((response) => {
            // Return cached version or fetch from network
            return response || fetch(event.request).then((fetchResponse) => {
                // Cache successful responses
                return caches.open(CACHE_NAME).then((cache) => {
                    cache.put(event.request, fetchResponse.clone());
                    return fetchResponse;
                });
            });
        }).catch(() => {
            // Return offline page if available
            return caches.match('/admin/dashboard');
        })
    );
});

// Background sync for offline actions
self.addEventListener('sync', (event) => {
    console.log('🔄 Service Worker: Background sync', event);
    
    if (event.tag === 'sync-notifications') {
        event.waitUntil(syncNotifications());
    }
});

async function syncNotifications() {
    try {
        // Fetch latest emergency data
        const response = await fetch('/api/dashboard-stats');
        const data = await response.json();
        
        console.log('✅ Service Worker: Synced notifications', data);
    } catch (error) {
        console.error('❌ Service Worker: Sync failed', error);
    }
}

console.log('🚀 Service Worker: Loaded and ready');
