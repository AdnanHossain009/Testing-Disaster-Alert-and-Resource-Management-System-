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
                        <option value="Flood">Flood</option>
                        <option value="Earthquake">Earthquake</option>
                        <option value="Cyclone">Cyclone</option>
                        <option value="Tsunami">Tsunami</option>
                        <option value="Fire">Fire</option>
                        <option value="Building Collapse">Building Collapse</option>
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
                    <label class="form-label">Special Requirements (check all that apply)</label>
                    <div style="margin-top: 0.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: normal;">
                            <input type="checkbox" name="needs[]" value="medical"> Medical assistance needed
                        </label>
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: normal;">
                            <input type="checkbox" name="needs[]" value="children"> Children in group
                        </label>
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: normal;">
                            <input type="checkbox" name="needs[]" value="elderly"> Elderly person(s)
                        </label>
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: normal;">
                            <input type="checkbox" name="needs[]" value="disability"> Person with disability
                        </label>
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: normal;">
                            <input type="checkbox" name="needs[]" value="pets"> Pets/animals
                        </label>
                    </div>
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
</body>
</html>
