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
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="/manifest.json">
    
    <!-- Icons -->
    <link rel="icon" type="image/png" sizes="32x32" href="/images/icon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/images/icon-16x16.png">
    <link rel="apple-touch-icon" href="/images/icon-192x192.png">
    
    <title>All Disaster Alerts</title>


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

        .alerts-grid {
            display: grid;
            gap: 1rem;
        }

        .alert-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border-left: 4px solid;
        }

        .alert-card.high {
            border-left-color: #e74c3c;
        }

        .alert-card.medium {
            border-left-color: #f39c12;
        }

        .alert-card.low {
            border-left-color: #27ae60;
        }

        .alert-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .alert-title {

            font-size: 1.25rem;
            font-weight: bold;
            color: #2c3e50;
        }

        .severity-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
        }

        .severity-high {
            background-color: #e74c3c;
            color: white;
        }

        .severity-medium {
            background-color: #f39c12;
            color: white;
        }

        .severity-low {
            background-color: #27ae60;
            color: white;
        }

        .alert-description {
            color: #7f8c8d;
            margin-bottom: 1rem;
            line-height: 1.5;
        }

        .alert-meta {
            display: flex;
            justify-content: space-between;

            align-items: center;
            font-size: 0.9rem;
            color: #95a5a6;
        }


        .view-details-btn {
            background-color: #3498db;
            color: white;

            padding: 0.5rem 1rem;
            text-decoration: none;
            border-radius: 4px;
            font-size: 0.9rem;
        }


        .view-details-btn:hover {
            background-color: #2980b9;
        }


        .back-link {
            color: #3498db;
            text-decoration: none;
            margin-bottom: 1rem;
            display: inline-block;
        }


        .back-link:hover {
            text-decoration: underline;
        }

    </style>

</head>

<body>


    <div class="header">
        <h1>🚨 All Disaster Alerts</h1>
        <p>Stay informed about ongoing emergencies and warnings</p>

    </div>

    <div class="nav">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <a href="{{ route('alerts.index') }}">All Alerts</a>
        <a href="{{ route('shelters.index') }}">Shelters</a>
        <a href="{{ route('requests.create') }}">Emergency Request</a>
        <a href="{{ route('login') }}">Login</a>
    </div>

    <div class="container">
        <a href="{{ route('dashboard') }}" class="back-link">← Back to Dashboard</a>
        
        <h2>Current Alerts ({{ count($alerts) }})</h2>

        <div class="alerts-grid">
            @if(count($alerts) > 0)
                @foreach($alerts as $alert)
                    <div class="alert-card {{ strtolower($alert['severity']) }}">

                        <div class="alert-header">


                            <div class="alert-title">{{ $alert['title'] }}</div>
                            <span class="severity-badge severity-{{ strtolower($alert['severity']) }}">
                                {{ $alert['severity'] }}
                            </span>
                        </div>
                        
                        <div class="alert-description">
                            {{ $alert['description'] }}
                        </div>
                        
                        <div class="alert-meta">


                            <div>
                                📍 {{ $alert['location'] }}
                                <br>
                                🕒 {{ $alert['created_at'] }}
                            </div>


                            <a href="{{ route('alerts.show', $alert['id']) }}" class="view-details-btn">
                                View Details
                            </a>
                        </div>
                    </div>
                    
                @endforeach
            @else
                <div class="alert-card">
                    <p>No alerts available at this time.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- PWA Installation Script -->
    <script>
        // PWA Installation Logic
        let deferredPrompt;

        // Listen for beforeinstallprompt event
        window.addEventListener('beforeinstallprompt', (e) => {
            console.log('PWA: beforeinstallprompt event fired');
            e.preventDefault();
            deferredPrompt = e;
            
            // Show install banner
            showInstallBanner();
        });

        // Service Worker Registration
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then((registration) => {
                        console.log('SW: Service Worker registered successfully', registration);
                    })
                    .catch((error) => {
                        console.log('SW: Service Worker registration failed', error);
                    });
            });
        }

        function showInstallBanner() {
            // Create install banner if it doesn't exist
            if (!document.getElementById('pwa-install-banner')) {
                const banner = document.createElement('div');
                banner.id = 'pwa-install-banner';
                banner.innerHTML = `
                    <div style="position: fixed; bottom: 20px; left: 20px; right: 20px; max-width: 400px; margin: 0 auto; background: linear-gradient(135deg, #1e40af, #3b82f6); color: white; padding: 20px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); z-index: 1000; animation: slideUp 0.5s ease-out;">
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <div style="flex: 1;">
                                <h4 style="margin: 0 0 5px 0; font-size: 1.1rem; font-weight: bold;">📱 Install Disaster Alert App</h4>
                                <p style="margin: 0; font-size: 0.9rem; opacity: 0.9;">Get instant access even when offline!</p>
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                <button id="pwa-install-btn" style="background: white; color: #1e40af; border: none; padding: 8px 16px; border-radius: 8px; font-weight: bold; cursor: pointer; font-size: 0.85rem;">Install</button>
                                <button id="pwa-dismiss-btn" style="background: transparent; color: white; border: 1px solid rgba(255,255,255,0.3); padding: 6px 16px; border-radius: 8px; cursor: pointer; font-size: 0.8rem;">Not Now</button>
                            </div>
                        </div>
                    </div>
                    <style>
                        @keyframes slideUp { from { transform: translateY(100%); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
                    </style>
                `;
                document.body.appendChild(banner);

                // Add event listeners
                document.getElementById('pwa-install-btn').addEventListener('click', async () => {
                    if (deferredPrompt) {
                        deferredPrompt.prompt();
                        const { outcome } = await deferredPrompt.userChoice;
                        console.log('PWA: User choice:', outcome);
                        deferredPrompt = null;
                        document.getElementById('pwa-install-banner').remove();
                    }
                });

                document.getElementById('pwa-dismiss-btn').addEventListener('click', () => {
                    document.getElementById('pwa-install-banner').remove();
                    localStorage.setItem('pwa-dismissed-alerts', 'true');
                });
            }
        }

        // Check if should show banner
        function shouldShowBanner() {
            // Don't show if already dismissed on this page
            if (localStorage.getItem('pwa-dismissed-alerts')) {
                return false;
            }
            
            // Don't show if already running as PWA
            if (window.matchMedia('(display-mode: standalone)').matches) {
                return false;
            }
            
            // Don't show if already installed (check for app capability)
            if (window.navigator.standalone === true) {
                return false;
            }
            
            return true;
        }

        // Show banner if conditions are met
        setTimeout(() => {
            if (shouldShowBanner()) {
                showInstallBanner();
            }
        }, 2000);
    </script>
</body>
</html>
