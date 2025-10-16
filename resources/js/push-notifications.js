/**
 * Push Notification Manager
 * Handles browser push notifications, permissions, and preferences
 */

class PushNotificationManager {
    constructor() {
        this.serviceWorkerRegistration = null;
        this.subscription = null;
        this.preferences = this.loadPreferences();
        this.initialized = false;
    }

    /**
     * Initialize push notifications
     */
    async init() {
        if (this.initialized) return;

        console.log('🔔 Initializing Push Notification Manager...');

        // Check browser support
        if (!this.checkSupport()) {
            console.warn('⚠️ Push notifications not supported in this browser');
            return false;
        }

        try {
            // Register service worker
            await this.registerServiceWorker();
            
            // Load saved subscription if exists
            await this.loadSubscription();
            
            this.initialized = true;
            console.log('✅ Push Notification Manager initialized');
            return true;
        } catch (error) {
            console.error('❌ Failed to initialize push notifications:', error);
            return false;
        }
    }

    /**
     * Check if browser supports push notifications
     */
    checkSupport() {
        return (
            'serviceWorker' in navigator &&
            'PushManager' in window &&
            'Notification' in window
        );
    }

    /**
     * Register service worker
     */
    async registerServiceWorker() {
        try {
            this.serviceWorkerRegistration = await navigator.serviceWorker.register(
                '/service-worker.js',
                { scope: '/' }
            );

            console.log('✅ Service Worker registered:', this.serviceWorkerRegistration);

            // Wait for service worker to be ready
            await navigator.serviceWorker.ready;

            return this.serviceWorkerRegistration;
        } catch (error) {
            console.error('❌ Service Worker registration failed:', error);
            throw error;
        }
    }

    /**
     * Request notification permission
     */
    async requestPermission() {
        if (!('Notification' in window)) {
            console.error('This browser does not support notifications');
            return false;
        }

        const permission = await Notification.requestPermission();
        console.log('🔔 Notification permission:', permission);

        if (permission === 'granted') {
            await this.subscribe();
            return true;
        } else if (permission === 'denied') {
            this.showPermissionDeniedMessage();
            return false;
        }

        return false;
    }

    /**
     * Subscribe to push notifications
     */
    async subscribe() {
        if (!this.serviceWorkerRegistration) {
            console.error('Service Worker not registered');
            return null;
        }

        try {
            // Get existing subscription or create new one
            this.subscription = await this.serviceWorkerRegistration.pushManager.getSubscription();

            if (!this.subscription) {
                // Create new subscription
                const vapidPublicKey = this.getVapidPublicKey();
                
                this.subscription = await this.serviceWorkerRegistration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: this.urlBase64ToUint8Array(vapidPublicKey)
                });

                console.log('✅ Push subscription created:', this.subscription);

                // Save subscription to server
                await this.saveSubscriptionToServer(this.subscription);
            }

            return this.subscription;
        } catch (error) {
            console.error('❌ Push subscription failed:', error);
            return null;
        }
    }

    /**
     * Unsubscribe from push notifications
     */
    async unsubscribe() {
        if (!this.subscription) {
            console.log('No active subscription to unsubscribe');
            return true;
        }

        try {
            await this.subscription.unsubscribe();
            await this.removeSubscriptionFromServer();
            
            this.subscription = null;
            console.log('✅ Unsubscribed from push notifications');
            return true;
        } catch (error) {
            console.error('❌ Unsubscribe failed:', error);
            return false;
        }
    }

    /**
     * Load existing subscription
     */
    async loadSubscription() {
        if (!this.serviceWorkerRegistration) return;

        this.subscription = await this.serviceWorkerRegistration.pushManager.getSubscription();
        
        if (this.subscription) {
            console.log('📱 Existing subscription loaded:', this.subscription);
        }
    }

    /**
     * Save subscription to server
     */
    async saveSubscriptionToServer(subscription) {
        try {
            const response = await fetch('/api/push-subscription', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify({
                    subscription: subscription.toJSON(),
                    user_id: this.getCurrentUserId()
                })
            });

            if (response.ok) {
                console.log('✅ Subscription saved to server');
                return true;
            } else {
                console.error('❌ Failed to save subscription:', response.statusText);
                return false;
            }
        } catch (error) {
            console.error('❌ Error saving subscription:', error);
            return false;
        }
    }

    /**
     * Remove subscription from server
     */
    async removeSubscriptionFromServer() {
        try {
            const response = await fetch('/api/push-subscription', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                }
            });

            if (response.ok) {
                console.log('✅ Subscription removed from server');
                return true;
            }
        } catch (error) {
            console.error('❌ Error removing subscription:', error);
        }
        return false;
    }

    /**
     * Check if notifications are enabled
     */
    isEnabled() {
        return Notification.permission === 'granted' && this.subscription !== null;
    }

    /**
     * Get notification permission status
     */
    getPermissionStatus() {
        if (!('Notification' in window)) {
            return 'unsupported';
        }
        return Notification.permission;
    }

    /**
     * Show test notification
     */
    async showTestNotification() {
        if (!this.isEnabled()) {
            console.warn('Notifications not enabled');
            return;
        }

        try {
            await this.serviceWorkerRegistration.showNotification(
                '🧪 Test Emergency Alert',
                {
                    body: 'This is a test notification. Your push notifications are working!',
                    icon: '/favicon.ico',
                    badge: '/favicon.ico',
                    tag: 'test-notification',
                    requireInteraction: false,
                    data: { url: '/admin/dashboard' },
                    vibrate: [200, 100, 200],
                    actions: [
                        { action: 'view', title: 'View Dashboard' },
                        { action: 'dismiss', title: 'Dismiss' }
                    ]
                }
            );
            console.log('✅ Test notification shown');
        } catch (error) {
            console.error('❌ Failed to show test notification:', error);
        }
    }

    /**
     * Load preferences from localStorage
     */
    loadPreferences() {
        const saved = localStorage.getItem('notificationPreferences');
        return saved ? JSON.parse(saved) : {
            enabled: false,
            doNotDisturb: false,
            quietHoursStart: '22:00',
            quietHoursEnd: '08:00',
            notifyOnNewRequest: true,
            notifyOnStatusChange: true,
            notifyOnCritical: true,
            soundEnabled: true
        };
    }

    /**
     * Save preferences to localStorage
     */
    savePreferences(preferences) {
        this.preferences = { ...this.preferences, ...preferences };
        localStorage.setItem('notificationPreferences', JSON.stringify(this.preferences));
        console.log('✅ Preferences saved:', this.preferences);
    }

    /**
     * Check if currently in quiet hours
     */
    isInQuietHours() {
        if (!this.preferences.doNotDisturb) return false;

        const now = new Date();
        const currentTime = now.getHours() * 60 + now.getMinutes();
        
        const [startHour, startMin] = this.preferences.quietHoursStart.split(':').map(Number);
        const [endHour, endMin] = this.preferences.quietHoursEnd.split(':').map(Number);
        
        const startTime = startHour * 60 + startMin;
        const endTime = endHour * 60 + endMin;

        if (startTime < endTime) {
            return currentTime >= startTime && currentTime < endTime;
        } else {
            // Handles overnight quiet hours (e.g., 22:00 - 08:00)
            return currentTime >= startTime || currentTime < endTime;
        }
    }

    /**
     * Show permission denied message
     */
    showPermissionDeniedMessage() {
        alert(
            'Notification permissions denied.\n\n' +
            'To enable notifications:\n' +
            '1. Click the lock icon in your browser address bar\n' +
            '2. Find "Notifications" settings\n' +
            '3. Change to "Allow"\n' +
            '4. Refresh this page'
        );
    }

    /**
     * Get VAPID public key (for production, store this securely)
     */
    getVapidPublicKey() {
        // For testing, using a placeholder key
        // In production, generate proper VAPID keys using: npm install web-push
        return 'BEl62iUYgUivxIkv69yViEuiBIa-Ib37J8xYhzjBQ-KmJDJ8F8AX9sC5M3IxKJX8Zp1VZPGGLaT8CvxKX7Lj8Wc';
    }

    /**
     * Convert VAPID key to Uint8Array
     */
    urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding)
            .replace(/-/g, '+')
            .replace(/_/g, '/');

        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);

        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }
        return outputArray;
    }

    /**
     * Get current user ID
     */
    getCurrentUserId() {
        // Try to get from meta tag or cookie
        const userMeta = document.querySelector('meta[name="user-id"]');
        return userMeta ? userMeta.content : null;
    }
}

// Create global instance
window.pushNotificationManager = new PushNotificationManager();

// Auto-initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.pushNotificationManager.init();
    });
} else {
    window.pushNotificationManager.init();
}

console.log('📱 Push Notification Manager loaded');
