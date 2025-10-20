<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Create Alert</title>
    @include('admin.partials.dark-theme-styles')
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f5f5; }
        .admin-header { background: #2d3748; color: white; padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; }
        .admin-nav { background: #4a5568; padding: 0.5rem 2rem; }
        .admin-nav a { color: white; text-decoration: none; margin-right: 2rem; padding: 0.5rem 1rem; border-radius: 5px; }
        .admin-nav a:hover, .admin-nav a.active { background: #2d3748; }
        .container { max-width: 800px; margin: 2rem auto; padding: 0 1rem; }
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
    </style>
</head>
<body>
    <div class="admin-header">
        <h1>🛡️ Admin Panel - Create Alert</h1>
        <div>
            <span>Welcome, {{ Auth::user()->name ?? 'Admin' }}</span>
            <a href="{{ route('auth.logout') }}" style="margin-left: 1rem; color: #fed7d7;">Logout</a>
        </div>
    </div>

    <div class="admin-nav">
        <a href="{{ route('admin.dashboard') }}">Dashboard</a>
        <a href="{{ route('admin.alerts') }}" class="active">Manage Alerts</a>
        <a href="{{ route('admin.shelters') }}">Manage Shelters</a>
        <a href="{{ route('admin.requests') }}">Manage Requests</a>
        <a href="{{ route('admin.analytics') }}">Analytics</a>
    </div>

    <div class="container">
        <div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center;">
            <h2>Create New Alert</h2>
            <a href="{{ route('admin.alerts') }}" class="btn btn-secondary">
                ← Back to Alerts
            </a>
        </div>

        <div class="form-card">
            <form method="POST" action="{{ route('admin.alerts.store') }}">
                @csrf
                
                <div class="form-group">
                    <label class="form-label" for="title">Alert Title *</label>
                    <input type="text" id="title" name="title" class="form-input" 
                           placeholder="e.g., Flood Warning - Dhaka" value="{{ old('title') }}" required>
                    @error('title')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="type">Alert Type *</label>
                        <select id="type" name="type" class="form-select" required>
                            <option value="">Select Type</option>
                            <option value="Flood" {{ old('type') == 'Flood' ? 'selected' : '' }}>🌊 Flood</option>
                            <option value="Earthquake" {{ old('type') == 'Earthquake' ? 'selected' : '' }}>🌍 Earthquake</option>
                            <option value="Cyclone" {{ old('type') == 'Cyclone' ? 'selected' : '' }}>🌪️ Cyclone</option>
                            <option value="Fire" {{ old('type') == 'Fire' ? 'selected' : '' }}>🔥 Fire</option>
                            <option value="Health Emergency" {{ old('type') == 'Health Emergency' ? 'selected' : '' }}>🏥 Health Emergency</option>
                            <option value="Other" {{ old('type') == 'Other' ? 'selected' : '' }}>⚠️ Other</option>
                        </select>
                        @error('type')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="severity">Severity Level *</label>
                        <select id="severity" name="severity" class="form-select" required>
                            <option value="">Select Severity</option>
                            <option value="Low" {{ old('severity') == 'Low' ? 'selected' : '' }}>🟢 Low</option>
                            <option value="Moderate" {{ old('severity') == 'Moderate' ? 'selected' : '' }}>🟡 Moderate</option>
                            <option value="High" {{ old('severity') == 'High' ? 'selected' : '' }}>🟠 High</option>
                            <option value="Critical" {{ old('severity') == 'Critical' ? 'selected' : '' }}>🔴 Critical</option>
                        </select>
                        @error('severity')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="location">Affected Location *</label>
                    <input type="text" id="location" name="location" class="form-input" 
                           placeholder="e.g., Dhaka Metropolitan Area" value="{{ old('location') }}" required>
                    @error('location')
                        <div class="error">{{ $message }}</div>
                    @enderror
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
                    <label class="form-label" for="description">Alert Description *</label>
                    <textarea id="description" name="description" class="form-textarea" 
                              placeholder="Provide detailed information about the alert, safety instructions, and recommended actions..." required>{{ old('description') }}</textarea>
                    @error('description')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="expires_at">Expiry Date (Optional)</label>
                    <input type="datetime-local" id="expires_at" name="expires_at" class="form-input" 
                           value="{{ old('expires_at') }}">
                    <small style="color: #718096;">Leave empty for alerts that don't expire</small>
                    @error('expires_at')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">
                    <a href="{{ route('admin.alerts') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Create Alert</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>