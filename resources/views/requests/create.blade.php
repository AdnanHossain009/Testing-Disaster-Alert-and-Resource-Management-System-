<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Emergency Help</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #0D1326;
            color: #E4E8F5;
        }
        .header {
            background-color: #091F57;
            color: white;
            padding: 1rem;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.3);
        }
        .nav {
            background-color: #091F57;
            padding: 0.5rem;
            text-align: center;
        }
        .nav a {
            color: #E4E8F5;
            text-decoration: none;
            margin: 0 1rem;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            transition: all 0.3s;
        }
        .nav a:hover {
            background-color: rgba(43, 85, 189, 0.3);
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 2rem;
        }
        .form-container {
            background: #091F57;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.3);
            border: 1px solid rgba(43, 85, 189, 0.2);
        }
        .emergency-notice {
            background-color: rgba(255, 169, 77, 0.15);
            border: 1px solid #ffa94d;
            border-radius: 6px;
            padding: 1rem;
            margin-bottom: 2rem;
            color: #ffa94d;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
            color: #E4E8F5;
        }
        .form-input {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid rgba(43, 85, 189, 0.4);
            border-radius: 6px;
            font-size: 1rem;
            box-sizing: border-box;
            background-color: #0D1326;
            color: #E4E8F5;
        }
        .form-input:focus {
            outline: none;
            border-color: #2B55BD;
        }
        .form-select {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid rgba(43, 85, 189, 0.4);
            border-radius: 6px;
            font-size: 1rem;
            box-sizing: border-box;
            background-color: #0D1326;
            color: #E4E8F5;
        }
        .form-textarea {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid rgba(43, 85, 189, 0.4);
            border-radius: 6px;
            font-size: 1rem;
            min-height: 100px;
            resize: vertical;
            box-sizing: border-box;
            background-color: #0D1326;
            color: #E4E8F5;
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
            transition: all 0.3s;
        }
        .btn-primary {
            background-color: #2B55BD;
            color: white;
            box-shadow: 0 4px 12px rgba(43, 85, 189, 0.4);
        }
        .btn-primary:hover {
            background-color: #3d6fd4;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(43, 85, 189, 0.6);
        }
        .btn-secondary {
            background-color: rgba(43, 85, 189, 0.3);
            color: #E4E8F5;
            margin-left: 1rem;
        }
        .btn-secondary:hover {
            background-color: rgba(43, 85, 189, 0.5);
        }
        .priority-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
        }
        .priority-option {
            border: 2px solid rgba(43, 85, 189, 0.4);
            border-radius: 6px;
            padding: 1rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            background-color: rgba(43, 85, 189, 0.1);
            color: #E4E8F5;
        }
        .priority-option.critical {
            border-color: #ff6b6b;
            background-color: rgba(255, 107, 107, 0.15);
        }
        .priority-option.high {
            border-color: #ffa94d;
            background-color: rgba(255, 169, 77, 0.15);
        }
        .priority-option.medium {
            border-color: #2B55BD;
            background-color: rgba(43, 85, 189, 0.15);
        }
        .back-link {
            color: #2B55BD;
            text-decoration: none;
            margin-bottom: 1rem;
            display: inline-block;
            transition: color 0.3s;
        }
        .back-link:hover {
            color: #3d6fd4;
            text-decoration: underline;
        }
        .help-text {
            font-size: 0.9rem;
            color: #E4E8F5;
            opacity: 0.7;
            margin-top: 0.25rem;
        }
        h2 {
            color: #E4E8F5;
        }
    </style>
</head>
<body>
    @include('components.language-switcher')
    
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
                    <div class="help-text">Optional - for status updates</div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="location">Current Location *</label>
                    <input type="text" id="location" name="location" class="form-input" 
                           placeholder="e.g., Dhanmondi, Dhaka" required>
                    <div class="help-text">Be as specific as possible for faster response</div>
                </div>

                <!-- Emergency Details -->
                <div class="form-group">
                    <label class="form-label" for="request_type">Emergency Type *</label>
                    <select id="request_type" name="request_type" class="form-select" required>
                        <option value="">Select emergency type</option>
                        <option value="Shelter">Need Shelter</option>
                        <option value="Medical">Medical Emergency</option>
                        <option value="Food">Need Food/Water</option>
                        <option value="Water">Need Water Supply</option>
                        <option value="Rescue">Need Rescue</option>
                        <option value="Other">Other Emergency</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="people_count">Family/Group Size</label>
                    <input type="number" id="people_count" name="people_count" class="form-input" 
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
                              placeholder="Any special needs, medical conditions, disabilities, children, elderly, pets, etc."></textarea>
                    <div class="help-text">Describe any special assistance needed for your group</div>
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

    <script>
        // Register Service Worker for PWA and offline support
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js')
                    .then(function(registration) {
                        console.log('✅ Service Worker registered successfully:', registration.scope);
                    })
                    .catch(function(error) {
                        console.log('❌ Service Worker registration failed:', error);
                    });
            });
        }
    </script>
</body>
</html>
