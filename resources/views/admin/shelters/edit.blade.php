<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Edit Shelter</title>
    @include('admin.partials.dark-theme-styles')
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #0D1326; }
        .admin-header { background: #091F57; color: #E4E8F5; padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid rgba(43, 85, 189, 0.3); }
        .admin-nav { background: #091F57; padding: 0.5rem 2rem; border-bottom: 1px solid rgba(43, 85, 189, 0.2); }
        .admin-nav a { color: #E4E8F5; text-decoration: none; margin-right: 2rem; padding: 0.5rem 1rem; border-radius: 5px; transition: all 0.3s ease; }
        .admin-nav a:hover, .admin-nav a.active { background: #2B55BD; color: white; }
        .container { max-width: 900px; margin: 2rem auto; padding: 0 1rem; }
        .form-card { background: #091F57; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.3); border: 1px solid rgba(43, 85, 189, 0.3); }
        .form-group { margin-bottom: 1.5rem; }
        .form-label { display: block; margin-bottom: 0.5rem; font-weight: 600; color: #E4E8F5; }
        .form-input, .form-select, .form-textarea { width: 100%; padding: 0.75rem; border: 2px solid rgba(43, 85, 189, 0.4); border-radius: 6px; font-size: 1rem; background: #0D1326; color: #E4E8F5; }
        .form-input:focus, .form-select:focus, .form-textarea:focus { outline: none; border-color: #2B55BD; background: #091F57; }
        .form-input::placeholder { color: rgba(228, 232, 245, 0.5); }
        .form-select option { background: #091F57; color: #E4E8F5; }
        .form-textarea { resize: vertical; min-height: 100px; }
        .btn { padding: 0.75rem 1.5rem; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; font-size: 1rem; transition: all 0.3s ease; }
        .btn-primary { background: #2B55BD; color: white; }
        .btn-secondary { background: rgba(43, 85, 189, 0.3); color: #E4E8F5; }
        .btn:hover { opacity: 0.9; transform: translateY(-2px); }
        .error { color: #ff6b6b; font-size: 0.875rem; margin-top: 0.25rem; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        .form-row-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; }
        .facilities-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 0.5rem; margin-top: 0.5rem; }
        .facility-checkbox { display: flex; align-items: center; gap: 0.5rem; }
        .facility-checkbox input[type="checkbox"] { accent-color: #2B55BD; }
        .facility-checkbox label { color: #E4E8F5; cursor: pointer; }
        h2 { color: #E4E8F5; }
    </style>
</head>
<body>
    <div class="admin-header">
        <h1>🛡️ Admin Panel - Edit Shelter</h1>
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
            <h2>Edit Shelter - {{ $shelter->name }}</h2>
            <a href="{{ route('admin.shelters') }}" class="btn btn-secondary">
                ← Back to Shelters
            </a>
        </div>

        <div class="form-card">
            <form method="POST" action="{{ route('admin.shelters.update', $shelter->id) }}">
                @csrf
                @method('PUT')
                
                <div class="form-group">
                    <label class="form-label" for="name">Shelter Name *</label>
                    <input type="text" id="name" name="name" class="form-input" 
                           placeholder="e.g., Dhaka Community Center" value="{{ old('name', $shelter->name) }}" required>
                    @error('name')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="description">Description *</label>
                    <textarea id="description" name="description" class="form-textarea" 
                              placeholder="Describe the shelter facilities, accessibility, and other important details..." required>{{ old('description', $shelter->description) }}</textarea>
                    @error('description')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="address">Street Address *</label>
                    <input type="text" id="address" name="address" class="form-input" 
                           placeholder="e.g., 27 Dhanmondi Road" value="{{ old('address', $shelter->address) }}" required>
                    @error('address')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-row-3">
                    <div class="form-group">
                        <label class="form-label" for="city">City *</label>
                        <input type="text" id="city" name="city" class="form-input" 
                               placeholder="e.g., Dhaka" value="{{ old('city', $shelter->city) }}" required>
                        @error('city')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="state">State/Division *</label>
                        <input type="text" id="state" name="state" class="form-input" 
                               placeholder="e.g., Dhaka Division" value="{{ old('state', $shelter->state) }}" required>
                        @error('state')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="postal_code">Postal Code *</label>
                        <input type="text" id="postal_code" name="postal_code" class="form-input" 
                               placeholder="e.g., 1209" value="{{ old('postal_code', $shelter->postal_code) }}" required>
                        @error('postal_code')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="capacity">Total Capacity *</label>
                        <input type="number" id="capacity" name="capacity" class="form-input" 
                               min="1" placeholder="e.g., 200" value="{{ old('capacity', $shelter->capacity) }}" required>
                        @error('capacity')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="current_occupancy">Current Occupancy *</label>
                        <input type="number" id="current_occupancy" name="current_occupancy" class="form-input" 
                               min="0" value="{{ old('current_occupancy', $shelter->current_occupancy) }}" required>
                        @error('current_occupancy')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="status">Shelter Status *</label>
                    <select id="status" name="status" class="form-select" required>
                        <option value="Active" {{ old('status', $shelter->status) == 'Active' ? 'selected' : '' }}>✅ Active</option>
                        <option value="Maintenance" {{ old('status', $shelter->status) == 'Maintenance' ? 'selected' : '' }}>🔧 Maintenance</option>
                        <option value="Closed" {{ old('status', $shelter->status) == 'Closed' ? 'selected' : '' }}>❌ Closed</option>
                    </select>
                    @error('status')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="contact_phone">Contact Phone *</label>
                        <input type="text" id="contact_phone" name="contact_phone" class="form-input" 
                               placeholder="e.g., +880-1712345678" value="{{ old('contact_phone', $shelter->contact_phone) }}" required>
                        @error('contact_phone')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="contact_email">Contact Email</label>
                        <input type="email" id="contact_email" name="contact_email" class="form-input" 
                               placeholder="e.g., shelter@example.com" value="{{ old('contact_email', $shelter->contact_email) }}">
                        @error('contact_email')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="latitude">Latitude (Optional)</label>
                        <input type="number" id="latitude" name="latitude" class="form-input" 
                               step="0.000001" placeholder="e.g., 23.8103" value="{{ old('latitude', $shelter->latitude) }}">
                        @error('latitude')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="longitude">Longitude (Optional)</label>
                        <input type="number" id="longitude" name="longitude" class="form-input" 
                               step="0.000001" placeholder="e.g., 90.4125" value="{{ old('longitude', $shelter->longitude) }}">
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
                        $currentFacilities = old('facilities', $shelter->facilities ?? []);
                        @endphp
                        @foreach($facilityOptions as $facility)
                        <div class="facility-checkbox">
                            <input type="checkbox" name="facilities[]" value="{{ $facility }}" id="facility_{{ $loop->index }}"
                                   {{ in_array($facility, $currentFacilities) ? 'checked' : '' }}>
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
                    <button type="submit" class="btn btn-primary">Update Shelter</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>