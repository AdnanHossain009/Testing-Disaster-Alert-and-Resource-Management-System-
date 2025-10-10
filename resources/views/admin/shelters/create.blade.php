<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Create Shelter</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f5f5; }
        .admin-header { background: #2d3748; color: white; padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; }
        .admin-nav { background: #4a5568; padding: 0.5rem 2rem; }
        .admin-nav a { color: white; text-decoration: none; margin-right: 2rem; padding: 0.5rem 1rem; border-radius: 5px; }
        .admin-nav a:hover, .admin-nav a.active { background: #2d3748; }
        .container { max-width: 900px; margin: 2rem auto; padding: 0 1rem; }
        .form-card { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 1.5rem; }
        .form-label { display: block; margin-bottom: 0.5rem; font-weight: 600; color: #2d3748; }
        .form-input, .form-select, .form-textarea { width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 6px; font-size: 1rem; }
        .form-input:focus, .form-select:focus, .form-textarea:focus { outline: none; border-color: #4299e1; }
        .form-textarea { resize: vertical; min-height: 100px; }
        .btn { padding: 0.75rem 1.5rem; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; font-size: 1rem; }
        .btn-primary { background: #4299e1; color: white; }
        .btn-secondary { background: #a0aec0; color: white; }
        .btn:hover { opacity: 0.9; }
        .error { color: #e53e3e; font-size: 0.875rem; margin-top: 0.25rem; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        .form-row-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; }
        .facilities-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 0.5rem; margin-top: 0.5rem; }
        .facility-checkbox { display: flex; align-items: center; gap: 0.5rem; }
    </style>
</head>
<body>
    <div class="admin-header">
        <h1>🛡️ Admin Panel - Create Shelter</h1>
        <div>
            <span>Welcome, {{ Auth::user()->name ?? 'Admin' }}</span>
            <a href="{{ route('auth.logout') }}" style="margin-left: 1rem; color: #fed7d7;">Logout</a>
        </div>
    </div>

    <div class="admin-nav">
        <a href="{{ route('admin.dashboard') }}">Dashboard</a>
        <a href="{{ route('admin.alerts') }}">Manage Alerts</a>
        <a href="{{ route('admin.shelters') }}" class="active">Manage Shelters</a>
        <a href="{{ route('admin.requests') }}">Manage Requests</a>
        <a href="{{ route('admin.analytics') }}">Analytics</a>
    </div>

    <div class="container">
        <div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center;">
            <h2>Create New Shelter</h2>
            <a href="{{ route('admin.shelters') }}" class="btn btn-secondary">
                ← Back to Shelters
            </a>
        </div>

        <div class="form-card">
            <form method="POST" action="{{ route('admin.shelters.store') }}">
                @csrf
                
                <div class="form-group">
                    <label class="form-label" for="name">Shelter Name *</label>
                    <input type="text" id="name" name="name" class="form-input" 
                           placeholder="e.g., Dhaka Community Center" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="description">Description *</label>
                    <textarea id="description" name="description" class="form-textarea" 
                              placeholder="Describe the shelter facilities, accessibility, and other important details..." required>{{ old('description') }}</textarea>
                    @error('description')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="address">Street Address *</label>
                    <input type="text" id="address" name="address" class="form-input" 
                           placeholder="e.g., 27 Dhanmondi Road" value="{{ old('address') }}" required>
                    @error('address')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-row-3">
                    <div class="form-group">
                        <label class="form-label" for="city">City *</label>
                        <input type="text" id="city" name="city" class="form-input" 
                               placeholder="e.g., Dhaka" value="{{ old('city') }}" required>
                        @error('city')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="state">State/Division *</label>
                        <input type="text" id="state" name="state" class="form-input" 
                               placeholder="e.g., Dhaka Division" value="{{ old('state') }}" required>
                        @error('state')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="postal_code">Postal Code *</label>
                        <input type="text" id="postal_code" name="postal_code" class="form-input" 
                               placeholder="e.g., 1209" value="{{ old('postal_code') }}" required>
                        @error('postal_code')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="capacity">Total Capacity *</label>
                        <input type="number" id="capacity" name="capacity" class="form-input" 
                               min="1" placeholder="e.g., 200" value="{{ old('capacity') }}" required>
                        @error('capacity')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="current_occupancy">Current Occupancy</label>
                        <input type="number" id="current_occupancy" name="current_occupancy" class="form-input" 
                               min="0" value="0" readonly style="background: #f7fafc;">
                        <small style="color: #718096;">Will be set to 0 for new shelters</small>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="contact_phone">Contact Phone *</label>
                        <input type="text" id="contact_phone" name="contact_phone" class="form-input" 
                               placeholder="e.g., +880-1712345678" value="{{ old('contact_phone') }}" required>
                        @error('contact_phone')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="contact_email">Contact Email</label>
                        <input type="email" id="contact_email" name="contact_email" class="form-input" 
                               placeholder="e.g., shelter@example.com" value="{{ old('contact_email') }}">
                        @error('contact_email')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="latitude">Latitude (Optional)</label>
                        <input type="number" id="latitude" name="latitude" class="form-input" 
                               step="0.000001" placeholder="e.g., 23.8103" value="{{ old('latitude') }}">
                        @error('latitude')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="longitude">Longitude (Optional)</label>
                        <input type="number" id="longitude" name="longitude" class="form-input" 
                               step="0.000001" placeholder="e.g., 90.4125" value="{{ old('longitude') }}">
                        @error('longitude')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Available Facilities</label>
                    <div class="facilities-grid">
                        @php
                        $facilityOptions = ['Food Service', 'Medical Aid', 'Sleeping Area', 'Children Play Area', 'Restrooms', 'Security', 'Wi-Fi', 'Parking', 'Generator', 'Air Conditioning'];
                        @endphp
                        @foreach($facilityOptions as $facility)
                        <div class="facility-checkbox">
                            <input type="checkbox" name="facilities[]" value="{{ $facility }}" id="facility_{{ $loop->index }}"
                                   {{ in_array($facility, old('facilities', [])) ? 'checked' : '' }}>
                            <label for="facility_{{ $loop->index }}">{{ $facility }}</label>
                        </div>
                        @endforeach
                    </div>
                    @error('facilities')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">
                    <a href="{{ route('admin.shelters') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Create Shelter</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>