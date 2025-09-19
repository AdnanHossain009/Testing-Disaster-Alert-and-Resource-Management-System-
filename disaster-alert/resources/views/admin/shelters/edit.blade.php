<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Shelter - Admin Panel</title>
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
        .status-controls {
            background-color: #f8f9fa;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🏠 Edit Emergency Shelter</h1>
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
            <strong>📝 Editing Shelter:</strong> {{ $shelter->name }}
            <br><small>Last updated: {{ $shelter->updated_at->format('M d, Y H:i') }}</small>
        </div>

        <div class="form-container">
            <h2>Edit Shelter Details</h2>

            <form action="{{ route('admin.shelters.update', $shelter->id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Status Control -->
                <div class="status-controls">
                    <div class="form-group">
                        <label class="form-label" for="is_active">Shelter Status</label>
                        <select id="is_active" name="is_active" class="form-select">
                            <option value="1" {{ $shelter->is_active ? 'selected' : '' }}>🟢 Active - Available for Emergency Use</option>
                            <option value="0" {{ !$shelter->is_active ? 'selected' : '' }}>🔴 Inactive - Temporarily Unavailable</option>
                        </select>
                        <div class="help-text">Set to inactive to temporarily remove from public listings</div>
                    </div>
                </div>

                <!-- Shelter Name -->
                <div class="form-group">
                    <label class="form-label" for="name">Shelter Name <span class="required">*</span></label>
                    <input type="text" id="name" name="name" class="form-input" 
                           value="{{ old('name', $shelter->name) }}" required>
                    <div class="help-text">Provide a clear, identifiable name for the shelter</div>
                </div>

                <!-- Address -->
                <div class="form-group">
                    <label class="form-label" for="address">Street Address <span class="required">*</span></label>
                    <input type="text" id="address" name="address" class="form-input" 
                           value="{{ old('address', $shelter->address) }}" required>
                    <div class="help-text">Full street address including area/neighborhood</div>
                </div>

                <!-- Location Details -->
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="city">City <span class="required">*</span></label>
                        <input type="text" id="city" name="city" class="form-input" 
                               value="{{ old('city', $shelter->city) }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="state">State/Division <span class="required">*</span></label>
                        <input type="text" id="state" name="state" class="form-input" 
                               value="{{ old('state', $shelter->state) }}" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="postal_code">Postal Code <span class="required">*</span></label>
                        <input type="text" id="postal_code" name="postal_code" class="form-input" 
                               value="{{ old('postal_code', $shelter->postal_code) }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="capacity">Capacity <span class="required">*</span></label>
                        <input type="number" id="capacity" name="capacity" class="form-input" 
                               value="{{ old('capacity', $shelter->capacity) }}" min="1" required>
                        <div class="help-text">Maximum number of people this shelter can accommodate</div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="contact_phone">Contact Phone</label>
                        <input type="tel" id="contact_phone" name="contact_phone" class="form-input" 
                               value="{{ old('contact_phone', $shelter->contact_phone) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="contact_email">Contact Email</label>
                        <input type="email" id="contact_email" name="contact_email" class="form-input" 
                               value="{{ old('contact_email', $shelter->contact_email) }}">
                    </div>
                </div>

                <!-- Coordinates (Optional) -->
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="latitude">Latitude (Optional)</label>
                        <input type="number" id="latitude" name="latitude" class="form-input" 
                               value="{{ old('latitude', $shelter->latitude) }}" step="any">
                        <div class="help-text">GPS coordinates for precise location</div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="longitude">Longitude (Optional)</label>
                        <input type="number" id="longitude" name="longitude" class="form-input" 
                               value="{{ old('longitude', $shelter->longitude) }}" step="any">
                    </div>
                </div>

                <!-- Available Facilities -->
                <div class="form-group">
                    <label class="form-label">Available Facilities</label>
                    <div class="facilities-grid">
                        @php
                            $currentFacilities = is_array($shelter->facilities) ? $shelter->facilities : [];
                            $availableFacilities = [
                                'Medical Aid' => '🏥',
                                'Food Service' => '🍽️',
                                'Clean Water' => '💧',
                                'Restrooms' => '🚻',
                                'Security' => '🛡️',
                                'WiFi' => '📶',
                                'Bedding' => '🛏️',
                                'Shower Facilities' => '🚿',
                                'Parking' => '🚗',
                                'Backup Generator' => '⚡'
                            ];
                        @endphp
                        
                        @foreach($availableFacilities as $facility => $icon)
                        <div class="facility-item">
                            <input type="checkbox" 
                                   id="facility_{{ Str::slug($facility) }}" 
                                   name="facilities[]" 
                                   value="{{ $facility }}" 
                                   class="facility-checkbox"
                                   {{ in_array($facility, $currentFacilities) ? 'checked' : '' }}>
                            <label for="facility_{{ Str::slug($facility) }}">{{ $icon }} {{ $facility }}</label>
                        </div>
                        @endforeach
                    </div>
                    <div class="help-text">Select all facilities available at this shelter</div>
                </div>

                <!-- Special Notes -->
                <div class="form-group">
                    <label class="form-label" for="special_notes">Special Notes (Optional)</label>
                    <textarea id="special_notes" name="special_notes" class="form-textarea">{{ old('special_notes', $shelter->special_notes) }}</textarea>
                    <div class="help-text">Include accessibility information, special requirements, or operational notes</div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="submit" class="btn btn-success">
                        💾 Update Shelter Information
                    </button>
                    <a href="{{ route('admin.shelters') }}" class="btn btn-secondary">
                        Cancel
                    </a>
                </div>
            </form>
        </div>

        <div style="margin-top: 2rem; padding: 1rem; background: #fff3cd; border-radius: 4px;">
            <strong>⚠️ Important:</strong> Changes will be immediately visible to citizens seeking emergency accommodation. 
            Make sure all information is accurate before updating.
        </div>
    </div>
</body>
</html>