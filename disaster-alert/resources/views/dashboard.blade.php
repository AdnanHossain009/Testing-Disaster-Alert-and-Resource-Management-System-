<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- PWA Support -->
    <meta name="theme-color" content="#1e40af">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Disaster Alert">
    <meta name="msapplication-TileColor" content="#1e40af">
    <meta name="msapplication-config" content="/browserconfig.xml">
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="/manifest.json">
    
    <!-- Icons -->
    <link rel="icon" type="image/png" sizes="32x32" href="/images/icon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/images/icon-16x16.png">
    <link rel="apple-touch-icon" href="/images/icon-192x192.png">

    <title>Disaster Alert Dashboard</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }

        .header {
            background-color: #2c3e50;
            color: white;
            padding: 1rem;
            text-align: center;
        }

        .nav {
            background-color: #34495e;
            padding: 0.5rem;
            text-align: center;
        }

        .nav a {
            color: white;
            text-decoration: none;
            margin: 0 1rem;
            padding: 0.5rem 1rem;
            border-radius: 4px;
        }

        .nav a:hover {
            background-color: #5d6d7e;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }


        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
        }


        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #2c3e50;
        }


        .stat-label {
            color: #7f8c8d;
            margin-top: 0.5rem;
        }

        .alerts-section {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .alert-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            
            padding: 1rem;
            border-bottom: 1px solid #ecf0f1;
        }


        .alert-item:last-child {
            border-bottom: none;
        }


        .severity-high {
            background-color: #e74c3c;
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.8rem;
        }


        .severity-medium {
            background-color: #f39c12;
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;

            font-size: 0.8rem;
        }


        .view-all-btn {
            background-color: #3498db;
            color: white;
            padding: 0.75rem 1.5rem;

            text-decoration: none;
            border-radius: 4px;
            display: inline-block;
            margin-top: 1rem;
        }
    </style>

</head>


<body>

    <div class="header">
        <h1>🚨 Disaster Alert & Resource Management System</h1>
        <p>Real-time monitoring and emergency response coordination</p>
    </div>

    <div class="nav">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <a href="{{ route('alerts.index') }}">All Alerts</a>
        <a href="{{ route('shelters.index') }}">Shelters</a>
        <a href="{{ route('requests.create') }}">Emergency Request</a>
        <a href="{{ route('login') }}">Login</a>
    </div>

    <div class="container">
        <h2>System Overview</h2>
        
        <!-- statistics cards -->

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number">{{ $stats['total_alerts'] }}</div>
                <div class="stat-label">Total Alerts</div>
            </div>


            <div class="stat-card">
                <div class="stat-number">{{ $stats['active_alerts'] }}</div>
                <div class="stat-label">Active Alerts</div>
            </div>


            <div class="stat-card">
                <div class="stat-number">{{ $stats['high_severity'] }}</div>
                <div class="stat-label">High Severity</div>
            </div>


            <div class="stat-card">
                <div class="stat-number">{{ $stats['medium_severity'] }}</div>
                <div class="stat-label">Medium Severity</div>
            </div>

        </div>

        <!-- recent alerts -->
        <div class="alerts-section">
            <h3>Recent High Priority Alerts</h3>
            @if(count($recentAlerts) > 0)
                @foreach($recentAlerts as $alert)
                    <div class="alert-item">


                        <div>
                            <strong>{{ $alert['title'] }}</strong>
                            <br>
                            <small>{{ $alert['created_at'] }}</small>
                        </div>


                        <div>
                            <span class="severity-{{ strtolower($alert['severity']) }}">
                                {{ $alert['severity'] }}
                            </span>
                        </div>


                    </div>
                @endforeach
            @else
                <p>No recent alerts available.</p>
            @endif
            
            <a href="{{ route('alerts.index') }}" class="view-all-btn">View All Alerts</a>
        </div>
    </div>

    <!-- PWA Installation Prompt -->
    <div id="pwa-install-banner" class="pwa-banner" style="display: none;">
        <div class="pwa-banner-content">
            <div class="pwa-banner-text">
                <h4>📱 Install Disaster Alert App</h4>
                <p>Get instant access even when offline! Install our app for faster emergency response.</p>
            </div>
            <div class="pwa-banner-actions">
                <button id="pwa-install-btn" class="pwa-install-btn">Install App</button>
                <button id="pwa-dismiss-btn" class="pwa-dismiss-btn">Not Now</button>
            </div>
        </div>
    </div>

    <!-- Offline Status Indicator -->
    <div id="offline-indicator" class="offline-indicator" style="display: none;">
        <div class="offline-content">
            <span class="offline-icon">📡</span>
            <span class="offline-text">You're offline - Using cached data</span>
            <button id="retry-connection" class="retry-btn">Retry</button>
        </div>
    </div>

    <style>
        /* PWA Installation Banner */
        .pwa-banner {
            position: fixed;
            bottom: 20px;
            left: 20px;
            right: 20px;
            max-width: 400px;
            margin: 0 auto;
            background: linear-gradient(135deg, #1e40af, #3b82f6);
            color: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            z-index: 1000;
            animation: slideUp 0.5s ease-out;
        }

        .pwa-banner-content {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .pwa-banner-text h4 {
            margin: 0 0 5px 0;
            font-size: 1.1rem;
            font-weight: bold;
        }

        .pwa-banner-text p {
            margin: 0;
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .pwa-banner-actions {
            display: flex;
            flex-direction: column;
            gap: 8px;
            min-width: 80px;
        }

        .pwa-install-btn {
            background: white;
            color: #1e40af;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            font-size: 0.85rem;
        }

        .pwa-dismiss-btn {
            background: transparent;
            color: white;
            border: 1px solid rgba(255,255,255,0.3);
            padding: 6px 16px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.8rem;
        }

        /* Offline Indicator */
        .offline-indicator {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: #ef4444;
            color: white;
            padding: 10px;
            text-align: center;
            z-index: 2000;
            animation: slideDown 0.3s ease-out;
        }

        .offline-content {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .retry-btn {
            background: rgba(255,255,255,0.2);
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.8rem;
        }

        @keyframes slideUp {
            from { transform: translateY(100%); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        @keyframes slideDown {
            from { transform: translateY(-100%); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        @media (max-width: 600px) {
            .pwa-banner {
                left: 10px;
                right: 10px;
                bottom: 10px;
            }
            
            .pwa-banner-content {
                flex-direction: column;
                text-align: center;
            }
            
            .pwa-banner-actions {
                flex-direction: row;
                justify-content: center;
                min-width: auto;
            }
        }
    </style>

    <script>
        // PWA Installation Logic
        let deferredPrompt;
        const installBanner = document.getElementById('pwa-install-banner');
        const installBtn = document.getElementById('pwa-install-btn');
        const dismissBtn = document.getElementById('pwa-dismiss-btn');

        // Listen for beforeinstallprompt event
        window.addEventListener('beforeinstallprompt', (e) => {
            console.log('PWA: beforeinstallprompt event fired');
            e.preventDefault();
            deferredPrompt = e;
            
            // Show install banner if not dismissed on this page
            if (!localStorage.getItem('pwa-dismissed-dashboard') && !window.matchMedia('(display-mode: standalone)').matches) {
                installBanner.style.display = 'block';
            }
        });

        // Install button click
        installBtn.addEventListener('click', async () => {
            console.log('PWA: Install button clicked');
            
            if (deferredPrompt) {
                deferredPrompt.prompt();
                const { outcome } = await deferredPrompt.userChoice;
                console.log('PWA: User choice:', outcome);
                
                if (outcome === 'accepted') {
                    console.log('PWA: User accepted installation');
                } else {
                    console.log('PWA: User dismissed installation');
                }
                
                deferredPrompt = null;
                installBanner.style.display = 'none';
            }
        });

        // Dismiss button click
        dismissBtn.addEventListener('click', () => {
            installBanner.style.display = 'none';
            localStorage.setItem('pwa-dismissed-dashboard', 'true');
        });

        // Check if should show banner on page load
        setTimeout(() => {
            // Only show if not already dismissed on this page and PWA not already installed
            if (!localStorage.getItem('pwa-dismissed-dashboard') && !window.matchMedia('(display-mode: standalone)').matches) {
                if (deferredPrompt) {
                    installBanner.style.display = 'block';
                } else {
                    // Force show for testing/debugging
                    console.log('PWA: Showing test banner (no beforeinstallprompt)');
                    installBanner.style.display = 'block';
                }
            }
        }, 1500);

        // Listen for app installation
        window.addEventListener('appinstalled', (evt) => {
            console.log('PWA: App was installed successfully');
            installBanner.style.display = 'none';
        });

        // Service Worker Registration
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then((registration) => {
                        console.log('SW: Service Worker registered successfully', registration);
                        
                        // Listen for updates
                        registration.addEventListener('updatefound', () => {
                            const newWorker = registration.installing;
                            newWorker.addEventListener('statechange', () => {
                                if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                                    console.log('SW: New version available');
                                    showUpdateNotification();
                                }
                            });
                        });
                    })
                    .catch((error) => {
                        console.log('SW: Service Worker registration failed', error);
                    });
            });
        }

        // Online/Offline Status Management
        const offlineIndicator = document.getElementById('offline-indicator');
        const retryBtn = document.getElementById('retry-connection');

        // Show offline indicator
        function showOfflineIndicator() {
            offlineIndicator.style.display = 'block';
            document.body.style.paddingTop = '50px';
        }

        // Hide offline indicator
        function hideOfflineIndicator() {
            offlineIndicator.style.display = 'none';
            document.body.style.paddingTop = '0';
        }

        // Online/Offline event listeners
        window.addEventListener('online', () => {
            console.log('Network: Back online');
            hideOfflineIndicator();
            syncOfflineData();
        });

        window.addEventListener('offline', () => {
            console.log('Network: Gone offline');
            showOfflineIndicator();
        });

        // Check initial connection status
        if (!navigator.onLine) {
            showOfflineIndicator();
        }

        // Retry connection
        retryBtn.addEventListener('click', () => {
            if (navigator.onLine) {
                hideOfflineIndicator();
                location.reload();
            } else {
                alert('Still offline. Please check your internet connection.');
            }
        });

        // Sync offline data when back online
        async function syncOfflineData() {
            try {
                const offlineRequests = JSON.parse(localStorage.getItem('offlineRequests') || '[]');
                
                if (offlineRequests.length > 0) {
                    console.log('Syncing', offlineRequests.length, 'offline requests');
                    
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
                                console.log('Synced offline request:', request.id);
                                // Remove from localStorage
                                const remaining = offlineRequests.filter(r => r.id !== request.id);
                                localStorage.setItem('offlineRequests', JSON.stringify(remaining));
                            }
                        } catch (error) {
                            console.error('Failed to sync request:', request.id, error);
                        }
                    }
                    
                    // Show success notification
                    if (offlineRequests.length > 0) {
                        showNotification('✅ Offline requests synced successfully!', 'success');
                    }
                }
            } catch (error) {
                console.error('Error syncing offline data:', error);
            }
        }

        // Show update notification
        function showUpdateNotification() {
            const notification = document.createElement('div');
            notification.innerHTML = `
                <div style="position: fixed; top: 20px; right: 20px; background: #22c55e; color: white; padding: 15px; border-radius: 10px; z-index: 3000; max-width: 300px;">
                    <strong>🔄 Update Available</strong><br>
                    <small>A new version of the app is available.</small><br>
                    <button onclick="location.reload()" style="background: white; color: #22c55e; border: none; padding: 5px 10px; border-radius: 5px; margin-top: 10px; cursor: pointer;">
                        Update Now
                    </button>
                </div>
            `;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 10000);
        }

        // General notification function
        function showNotification(message, type = 'info') {
            const colors = {
                success: '#22c55e',
                error: '#ef4444',
                warning: '#f59e0b',
                info: '#3b82f6'
            };
            
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: ${colors[type]};
                color: white;
                padding: 15px;
                border-radius: 10px;
                z-index: 3000;
                max-width: 300px;
                animation: slideIn 0.3s ease-out;
            `;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.style.animation = 'slideOut 0.3s ease-out forwards';
                setTimeout(() => notification.remove(), 300);
            }, 5000);
        }

        // Add slideIn/slideOut animations
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideIn {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            @keyframes slideOut {
                from { transform: translateX(0); opacity: 1; }
                to { transform: translateX(100%); opacity: 0; }
            }
        `;
        document.head.appendChild(style);
    </script>
    
</body>
</html>
