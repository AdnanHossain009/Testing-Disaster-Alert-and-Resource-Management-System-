<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emergency Shelters</title>
    
    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#27ae60">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Disaster Alert">
    <meta name="mobile-web-app-capable" content="yes">
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="/manifest.json">
    
    <!-- PWA Icons -->
    <link rel="icon" type="image/png" sizes="192x192" href="/icon-192x192.png">
    <link rel="icon" type="image/png" sizes="512x512" href="/icon-512x512.png">
    <link rel="apple-touch-icon" href="/icon-192x192.png">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        .header {
            background-color: #27ae60;
            color: white;
            padding: 1rem;
            text-align: center;
        }
        .nav {
            background-color: #2ecc71;
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
            background-color: #27ae60;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
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
            color: #27ae60;
        }
        .stat-label {
            color: #7f8c8d;
            margin-top: 0.5rem;
        }
        .shelters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 1.5rem;
        }
        .shelter-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow: hidden;
            border-left: 4px solid;
        }
        .shelter-card.available {
            border-left-color: #27ae60;
        }
        .shelter-card.nearly-full {
            border-left-color: #f39c12;
        }
        .shelter-card.full {
            border-left-color: #e74c3c;
        }
        .shelter-header {
            padding: 1.5rem;
            border-bottom: 1px solid #ecf0f1;
        }
        .shelter-name {
            font-size: 1.25rem;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }
        .shelter-location {
            color: #7f8c8d;
            margin-bottom: 1rem;
        }
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
        }
        .status-available {
            background-color: #27ae60;
            color: white;
        }
        .status-nearly-full {
            background-color: #f39c12;
            color: white;
        }
        .status-full {
            background-color: #e74c3c;
            color: white;
        }
        .shelter-body {
            padding: 1.5rem;
        }
        .capacity-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
        }
        .capacity-bar {
            background-color: #ecf0f1;
            height: 8px;
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 1rem;
        }
        .capacity-fill {
            height: 100%;
            transition: width 0.3s ease;
        }
        .capacity-fill.available {
            background-color: #27ae60;
        }
        .capacity-fill.nearly-full {
            background-color: #f39c12;
        }
        .capacity-fill.full {
            background-color: #e74c3c;
        }
        .facilities {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }
        .facility-tag {
            background-color: #3498db;
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            font-size: 0.75rem;
        }
        .shelter-actions {
            display: flex;
            gap: 0.5rem;
        }
        .btn {
            padding: 0.5rem 1rem;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.9rem;
            text-align: center;
        }
        .btn-primary {
            background-color: #3498db;
            color: white;
        }
        .btn-secondary {
            background-color: #95a5a6;
            color: white;
        }
        .back-link {
            color: #27ae60;
            text-decoration: none;
            margin-bottom: 1rem;
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🏠 Emergency Shelters</h1>
        <p>Safe havens during disasters - Real-time availability</p>
    </div>

    <div class="nav">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <a href="{{ route('alerts.index') }}">Alerts</a>
        <a href="{{ route('shelters.index') }}">Shelters</a>
        <a href="{{ route('requests.create') }}">Emergency Request</a>
        <a href="{{ route('login') }}">Login</a>
    </div>

    <div class="container">
        <a href="{{ route('dashboard') }}" class="back-link">← Back to Dashboard</a>
        
        <h2>Shelter System Overview</h2>
        
        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number">{{ $stats['total_shelters'] }}</div>
                <div class="stat-label">Total Shelters</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $stats['available_shelters'] }}</div>
                <div class="stat-label">Available Shelters</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $stats['total_capacity'] }}</div>
                <div class="stat-label">Total Capacity</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $stats['total_occupied'] }}</div>
                <div class="stat-label">Currently Occupied</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $stats['total_available'] }}</div>
                <div class="stat-label">Available Spaces</div>
            </div>
        </div>

        <h3>All Shelters ({{ count($shelters) }})</h3>

        <!-- Shelters Grid -->
        <div class="shelters-grid">
            @foreach($shelters as $shelter)
                @php
                    $occupancyPercentage = ($shelter['occupied'] / $shelter['capacity']) * 100;
                    $statusClass = $occupancyPercentage >= 90 ? 'full' : ($occupancyPercentage >= 70 ? 'nearly-full' : 'available');
                    $widthPercent = round($occupancyPercentage);
                @endphp
                
                <div class="shelter-card {{ $statusClass }}">
                    <div class="shelter-header">
                        <div class="shelter-name">{{ $shelter['name'] }}</div>
                        <div class="shelter-location">📍 {{ $shelter['location'] }}</div>
                        <span class="status-badge status-{{ str_replace([' ', '-'], '-', strtolower($shelter['status'])) }}">
                            {{ $shelter['status'] }}
                        </span>
                    </div>
                    
                    <div class="shelter-body">
                        <!-- Capacity Information -->
                        <div class="capacity-info">
                            <span><strong>{{ $shelter['occupied'] }}/{{ $shelter['capacity'] }}</strong> occupied</span>
                            <span><strong>{{ $shelter['available'] }}</strong> available</span>
                        </div>
                        
                        <!-- Capacity Bar -->
                        <div class="capacity-bar">
                            <div class="capacity-fill {{ $statusClass }}" data-width="{{ $widthPercent }}"></div>
                        </div>
                        
                        <!-- Facilities -->
                        <div class="facilities">
                            @foreach($shelter['facilities'] as $facility)
                                <span class="facility-tag">{{ $facility }}</span>
                            @endforeach
                        </div>
                        
                        <!-- Contact Info -->
                        <div style="margin-bottom: 1rem; color: #7f8c8d; font-size: 0.9rem;">
                            📞 {{ $shelter['contact'] }}
                        </div>
                        
                        <!-- Actions -->
                        <div class="shelter-actions">
                            <a href="{{ route('shelters.show', $shelter['id']) }}" class="btn btn-primary">
                                View Details
                            </a>
                            @if($shelter['available'] > 0)
                                <a href="#" class="btn btn-secondary">Request Space</a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <script>
        // Set capacity bar widths dynamically to avoid CSS parser issues
        document.addEventListener('DOMContentLoaded', function() {
            const capacityFills = document.querySelectorAll('.capacity-fill');
            capacityFills.forEach(function(fill) {
                const width = fill.getAttribute('data-width');
                if (width) {
                    fill.style.width = width + '%';
                }
            });
        });

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
                    <div style="position: fixed; bottom: 20px; left: 20px; right: 20px; max-width: 400px; margin: 0 auto; background: linear-gradient(135deg, #27ae60, #2ecc71); color: white; padding: 20px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); z-index: 1000; animation: slideUp 0.5s ease-out;">
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <div style="flex: 1;">
                                <h4 style="margin: 0 0 5px 0; font-size: 1.1rem; font-weight: bold;">📱 Install Disaster Alert App</h4>
                                <p style="margin: 0; font-size: 0.9rem; opacity: 0.9;">Get instant access even when offline!</p>
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                <button id="pwa-install-btn" style="background: white; color: #27ae60; border: none; padding: 8px 16px; border-radius: 8px; font-weight: bold; cursor: pointer; font-size: 0.85rem;">Install</button>
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
                    localStorage.setItem('pwa-dismissed-shelters', 'true');
                });
            }
        }

        // Check if should show banner
        function shouldShowBanner() {
            // Don't show if already dismissed on this page
            if (localStorage.getItem('pwa-dismissed-shelters')) {
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
