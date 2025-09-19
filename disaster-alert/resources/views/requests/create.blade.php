<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Emergency Help</title>
    
    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#e74c3c">
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
            background-color: #e74c3c;
            color: white;
            padding: 1rem;
            text-align: center;
        }
        .nav {
            background-color: #c0392b;
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
            background-color: #a93226;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 2rem;
        }
        .form-container {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .emergency-notice {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 6px;
            padding: 1rem;
            margin-bottom: 2rem;
            color: #856404;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
            color: #2c3e50;
        }
        .form-input {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #ddd;
            border-radius: 6px;
            font-size: 1rem;
            box-sizing: border-box;
        }
        .form-input:focus {
            outline: none;
            border-color: #e74c3c;
        }
        .form-select {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #ddd;
            border-radius: 6px;
            font-size: 1rem;
            box-sizing: border-box;
        }
        .form-textarea {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #ddd;
            border-radius: 6px;
            font-size: 1rem;
            min-height: 100px;
            resize: vertical;
            box-sizing: border-box;
        }
        .btn {
            padding: 0.75rem 2rem;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        .btn-primary {
            background-color: #e74c3c;
            color: white;
        }
        .btn-primary:hover {
            background-color: #c0392b;
        }
        .btn-secondary {
            background-color: #95a5a6;
            color: white;
            margin-left: 1rem;
        }
        .priority-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
        }
        .priority-option {
            border: 2px solid #ddd;
            border-radius: 6px;
            padding: 1rem;
            text-align: center;
            cursor: pointer;
        }
        .priority-option.critical {
            border-color: #e74c3c;
            background-color: #fdf2f2;
        }
        .priority-option.high {
            border-color: #f39c12;
            background-color: #fef9e7;
        }
        .priority-option.medium {
            border-color: #3498db;
            background-color: #f4f9fd;
        }
        .back-link {
            color: #e74c3c;
            text-decoration: none;
            margin-bottom: 1rem;
            display: inline-block;
        }
        .help-text {
            font-size: 0.9rem;
            color: #7f8c8d;
            margin-top: 0.25rem;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🚨 Request Emergency Help</h1>
        <p>Quick access to emergency shelter and assistance</p>
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
        
        <div class="form-container">
            <div class="emergency-notice">
                <strong>⚠️ Emergency Request Form</strong><br>
                Fill out this form to request immediate help. If admin is offline, 
                you will be automatically assigned to the nearest available shelter.
            </div>

            @if ($errors->any())
                <div style="background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 1rem; border-radius: 6px; margin-bottom: 1rem;">
                    <strong>Please fix the following errors:</strong>
                    <ul style="margin: 0.5rem 0 0 0;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div style="background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 1rem; border-radius: 6px; margin-bottom: 1rem;">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('requests.store') }}" method="POST">
                @csrf
                
                <!-- Personal Information -->
                <div class="form-group">
                    <label class="form-label" for="name">Full Name *</label>
                    <input type="text" id="name" name="name" class="form-input" required>
                    <div class="help-text">Enter your full legal name</div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="phone">Phone Number *</label>
                    <input type="tel" id="phone" name="phone" class="form-input" 
                           placeholder="+880-1XXXXXXXXX" required>
                    <div class="help-text">Include country code for international numbers</div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Email Address</label>
                    <input type="email" id="email" name="email" class="form-input" 
                           placeholder="your.email@example.com">
                    <div class="help-text">Optional: For follow-up communications</div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="location">Current Location *</label>
                    <input type="text" id="location" name="location" class="form-input" 
                           placeholder="e.g., Dhanmondi, Dhaka" required>
                    <div class="help-text">Be as specific as possible for faster response</div>
                </div>

                <!-- Emergency Details -->
                <div class="form-group">
                    <label class="form-label" for="emergency_type">Emergency Type *</label>
                    <select id="emergency_type" name="emergency_type" class="form-select" required>
                        <option value="">Select emergency type</option>
                        <option value="Shelter">Shelter/Evacuation</option>
                        <option value="Medical">Medical Emergency</option>
                        <option value="Food">Food Assistance</option>
                        <option value="Water">Water Supply</option>
                        <option value="Rescue">Rescue Operation</option>
                        <option value="Other">Other Emergency</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="family_size">Family/Group Size</label>
                    <input type="number" id="family_size" name="family_size" class="form-input" 
                           min="1" max="20" value="1">
                    <div class="help-text">Number of people needing shelter</div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="description">Describe Your Situation *</label>
                    <textarea id="description" name="description" class="form-textarea" 
                              placeholder="Describe your current situation, any injuries, special needs, etc." required></textarea>
                    <div class="help-text">Include any medical conditions, disabilities, or special requirements</div>
                </div>

                <!-- Special Needs -->
                <div class="form-group">
                    <label class="form-label" for="special_needs">Special Requirements</label>
                    <textarea id="special_needs" name="special_needs" class="form-textarea" 
                              placeholder="Describe any medical conditions, disabilities, elderly care needs, children, pets, etc."></textarea>
                    <div class="help-text">Include any medical conditions, disabilities, or special requirements</div>
                </div>

                <!-- Submit Button -->
                <div style="text-align: center; margin-top: 2rem;">
                    <button type="submit" class="btn btn-primary">
                        🚨 Submit Emergency Request
                    </button>
                    <a href="{{ route('dashboard') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
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
                    <div style="position: fixed; bottom: 20px; left: 20px; right: 20px; max-width: 400px; margin: 0 auto; background: linear-gradient(135deg, #e74c3c, #c0392b); color: white; padding: 20px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); z-index: 1000; animation: slideUp 0.5s ease-out;">
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <div style="flex: 1;">
                                <h4 style="margin: 0 0 5px 0; font-size: 1.1rem; font-weight: bold;">📱 Install Disaster Alert App</h4>
                                <p style="margin: 0; font-size: 0.9rem; opacity: 0.9;">Get instant access even when offline!</p>
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                <button id="pwa-install-btn" style="background: white; color: #e74c3c; border: none; padding: 8px 16px; border-radius: 8px; font-weight: bold; cursor: pointer; font-size: 0.85rem;">Install</button>
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
                    localStorage.setItem('pwa-dismissed-requests', 'true');
                });
            }
        }

        // Check if should show banner
        function shouldShowBanner() {
            // Don't show if already dismissed on this page
            if (localStorage.getItem('pwa-dismissed-requests')) {
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
