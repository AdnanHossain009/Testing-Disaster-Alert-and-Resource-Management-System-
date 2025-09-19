<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Shelter - Admin Panel</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #2c5aa0, #1e3f72);
        }
        .header {
            background: linear-gradient(135deg, #2c5aa0, #1e3f72);
            color: white;
            padding: 1rem;
            text-align: center;
        }
        .nav {
            background-color: #1e3f72;
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
            background-color: #2c5aa0;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 2rem;
        }
        .form-container {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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
        .form-input, .form-select, .form-textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
            box-sizing: border-box;
        }
        .form-textarea {
            min-height: 120px;
            resize: vertical;
        }
        .help-text {
            font-size: 0.9rem;
            color: #7f8c8d;
            margin-top: 0.25rem;
        }
        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 4px;
            text-decoration: none;
            font-weight: bold;
            border: none;
            cursor: pointer;
            font-size: 1rem;
        }
        .btn-success {
            background-color: #27ae60;
            color: white;
        }
        .btn-secondary {
            background-color: #95a5a6;
            color: white;
        }
        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }
        .back-link {
            color: #2c5aa0;
            text-decoration: none;
            margin-bottom: 1rem;
            display: inline-block;
        }
        .required {
            color: #e74c3c;
        }
        .alert-info {
            background-color: #d1ecf1;
            color: #0c5460;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
            border: 1px solid #bee5eb;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        .form-row .form-group {
            margin-bottom: 1.5rem;
        }
        .facilities-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 0.5rem;
            margin-top: 0.5rem;
        }
        .facility-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .facility-checkbox {
            width: auto;
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🏠 Create New Emergency Shelter</h1>
        <p>Admin Panel - Shelter Management System</p>
    </div>

    <div class="nav">
        <a href="{{ route('admin.dashboard') }}">Dashboard</a>
        <a href="{{ route('admin.alerts') }}">Manage Alerts</a>
        <a href="{{ route('admin.shelters') }}">Manage Shelters</a>
        <a href="{{ route('admin.requests') }}">Manage Requests</a>
        <a href="{{ route('login') }}">Logout</a>
    </div>

    <div class="container">
        <a href="{{ route('admin.shelters') }}" class="back-link">← Back to Shelter Management</a>

        <div class="alert-info">
            <strong>🏠 Shelter Creation Guidelines:</strong>
            <ul>
                <li>Provide accurate location and contact information for emergency responders</li>
                <li>Set realistic capacity based on available space and resources</li>
                <li>Include all available facilities to help citizens make informed decisions</li>
                <li>Ensure contact information is current and monitored during emergencies</li>
            </ul>
        </div>

        <div class="form-container">
            <h2>Emergency Shelter Details</h2>

            <form action="{{ route('admin.shelters.store') }}" method="POST">
                @csrf

                <!-- Shelter Name -->
                <div class="form-group">
                    <label class="form-label" for="name">Shelter Name <span class="required">*</span></label>
                    <input type="text" id="name" name="name" class="form-input" 
                           placeholder="e.g., Dhaka Community Center Emergency Shelter" 
                           value="{{ old('name') }}" required>
                    <div class="help-text">Provide a clear, identifiable name for the shelter</div>
                </div>

                <!-- Address -->
                <div class="form-group">
                    <label class="form-label" for="address">Street Address <span class="required">*</span></label>
                    <input type="text" id="address" name="address" class="form-input" 
                           placeholder="e.g., 123 Relief Road, Dhanmondi"
                           value="{{ old('address') }}" required>
                    <div class="help-text">Full street address including area/neighborhood</div>
                </div>

                <!-- Location Details -->
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="city">City <span class="required">*</span></label>
                        <input type="text" id="city" name="city" class="form-input" 
                               placeholder="e.g., Dhaka"
                               value="{{ old('city') }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="state">State/Division <span class="required">*</span></label>
                        <input type="text" id="state" name="state" class="form-input" 
                               placeholder="e.g., Dhaka Division"
                               value="{{ old('state') }}" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="postal_code">Postal Code <span class="required">*</span></label>
                        <input type="text" id="postal_code" name="postal_code" class="form-input" 
                               placeholder="e.g., 1205"
                               value="{{ old('postal_code') }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="capacity">Capacity <span class="required">*</span></label>
                        <input type="number" id="capacity" name="capacity" class="form-input" 
                               placeholder="e.g., 150" min="1"
                               value="{{ old('capacity') }}" required>
                        <div class="help-text">Maximum number of people this shelter can accommodate</div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="contact_phone">Contact Phone</label>
                        <input type="tel" id="contact_phone" name="contact_phone" class="form-input" 
                               placeholder="e.g., +880-1700-000000"
                               value="{{ old('contact_phone') }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="contact_email">Contact Email</label>
                        <input type="email" id="contact_email" name="contact_email" class="form-input" 
                               placeholder="e.g., shelter@example.com"
                               value="{{ old('contact_email') }}">
                    </div>
                </div>

                <!-- Coordinates (Optional) -->
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="latitude">Latitude (Optional)</label>
                        <input type="number" id="latitude" name="latitude" class="form-input" 
                               placeholder="e.g., 23.8103" step="any"
                               value="{{ old('latitude') }}">
                        <div class="help-text">GPS coordinates for precise location</div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="longitude">Longitude (Optional)</label>
                        <input type="number" id="longitude" name="longitude" class="form-input" 
                               placeholder="e.g., 90.4125" step="any"
                               value="{{ old('longitude') }}">
                    </div>
                </div>

                <!-- Available Facilities -->
                <div class="form-group">
                    <label class="form-label">Available Facilities</label>
                    <div class="facilities-grid">
                        <div class="facility-item">
                            <input type="checkbox" id="facility_medical" name="facilities[]" value="Medical Aid" class="facility-checkbox">
                            <label for="facility_medical">🏥 Medical Aid</label>
                        </div>
                        <div class="facility-item">
                            <input type="checkbox" id="facility_food" name="facilities[]" value="Food Service" class="facility-checkbox">
                            <label for="facility_food">🍽️ Food Service</label>
                        </div>
                        <div class="facility-item">
                            <input type="checkbox" id="facility_water" name="facilities[]" value="Clean Water" class="facility-checkbox">
                            <label for="facility_water">💧 Clean Water</label>
                        </div>
                        <div class="facility-item">
                            <input type="checkbox" id="facility_restrooms" name="facilities[]" value="Restrooms" class="facility-checkbox">
                            <label for="facility_restrooms">🚻 Restrooms</label>
                        </div>
                        <div class="facility-item">
                            <input type="checkbox" id="facility_security" name="facilities[]" value="Security" class="facility-checkbox">
                            <label for="facility_security">🛡️ Security</label>
                        </div>
                        <div class="facility-item">
                            <input type="checkbox" id="facility_wifi" name="facilities[]" value="WiFi" class="facility-checkbox">
                            <label for="facility_wifi">📶 WiFi</label>
                        </div>
                        <div class="facility-item">
                            <input type="checkbox" id="facility_bedding" name="facilities[]" value="Bedding" class="facility-checkbox">
                            <label for="facility_bedding">🛏️ Bedding</label>
                        </div>
                        <div class="facility-item">
                            <input type="checkbox" id="facility_shower" name="facilities[]" value="Shower Facilities" class="facility-checkbox">
                            <label for="facility_shower">🚿 Shower Facilities</label>
                        </div>
                        <div class="facility-item">
                            <input type="checkbox" id="facility_parking" name="facilities[]" value="Parking" class="facility-checkbox">
                            <label for="facility_parking">🚗 Parking</label>
                        </div>
                        <div class="facility-item">
                            <input type="checkbox" id="facility_generator" name="facilities[]" value="Backup Generator" class="facility-checkbox">
                            <label for="facility_generator">⚡ Backup Generator</label>
                        </div>
                    </div>
                    <div class="help-text">Select all facilities available at this shelter</div>
                </div>

                <!-- Special Notes -->
                <div class="form-group">
                    <label class="form-label" for="special_notes">Special Notes (Optional)</label>
                    <textarea id="special_notes" name="special_notes" class="form-textarea" 
                              placeholder="Any special instructions, accessibility features, or important notes about this shelter...">{{ old('special_notes') }}</textarea>
                    <div class="help-text">Include accessibility information, special requirements, or operational notes</div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="submit" class="btn btn-success">
                        🏠 Create Shelter & Make Available
                    </button>
                    <a href="{{ route('admin.shelters') }}" class="btn btn-secondary">
                        Cancel
                    </a>
                </div>
            </form>
        </div>

        <div style="margin-top: 2rem; padding: 1rem; background: #fff3cd; border-radius: 4px;">
            <strong>⚠️ Important:</strong> Once created, this shelter will be visible to all citizens seeking emergency accommodation. 
            Make sure all information is accurate and the shelter is properly prepared.
        </div>
    </div>
</body>
</html>