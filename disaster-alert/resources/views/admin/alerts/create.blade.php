<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Alert - Admin Panel</title>
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
            max-width: 800px;
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
        .severity-preview {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0.5rem;
            margin-top: 0.5rem;
        }
        .severity-option {
            padding: 0.5rem;
            text-align: center;
            border-radius: 4px;
            font-size: 0.9rem;
            font-weight: bold;
        }
        .severity-low { background-color: #27ae60; color: white; }
        .severity-medium { background-color: #f39c12; color: white; }
        .severity-high { background-color: #e67e22; color: white; }
        .severity-critical { background-color: #e74c3c; color: white; }
        .back-link {
            color: #e74c3c;
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
    </style>
</head>
<body>
    <div class="header">
        <h1>🚨 Create New Emergency Alert</h1>
        <p>Admin Panel - Alert Management System</p>
    </div>

    <div class="nav">
        <a href="{{ route('admin.dashboard') }}">Dashboard</a>
        <a href="{{ route('admin.alerts') }}">Manage Alerts</a>
        <a href="{{ route('admin.shelters') }}">Manage Shelters</a>
        <a href="{{ route('admin.requests') }}">Manage Requests</a>
        <a href="{{ route('login') }}">Logout</a>
    </div>

    <div class="container">
        <a href="{{ route('admin.alerts') }}" class="back-link">← Back to Alert Management</a>

        <div class="alert-info">
            <strong>📢 Alert Creation Guidelines:</strong>
            <ul>
                <li>Provide clear, concise information that citizens can understand</li>
                <li>Choose appropriate severity level to ensure proper citizen response</li>
                <li>Include specific location details for targeted alerts</li>
                <li>Set expiration time for temporary alerts</li>
            </ul>
        </div>

        <div class="form-container">
            <h2>Emergency Alert Details</h2>

            <form action="{{ route('admin.alerts.store') }}" method="POST">
                @csrf

                <!-- Alert Title -->
                <div class="form-group">
                    <label class="form-label" for="title">Alert Title <span class="required">*</span></label>
                    <input type="text" id="title" name="title" class="form-input" 
                           placeholder="e.g., Flash Flood Warning - Dhaka Metropolitan" 
                           value="{{ old('title') }}" required>
                    <div class="help-text">Keep it concise and descriptive (max 255 characters)</div>
                </div>

                <!-- Alert Type -->
                <div class="form-group">
                    <label class="form-label" for="type">Disaster Type <span class="required">*</span></label>
                    <select id="type" name="type" class="form-select" required>
                        <option value="">Select disaster type...</option>
                        <option value="Flood" {{ old('type') == 'Flood' ? 'selected' : '' }}>🌊 Flood</option>
                        <option value="Earthquake" {{ old('type') == 'Earthquake' ? 'selected' : '' }}>🌍 Earthquake</option>
                        <option value="Cyclone" {{ old('type') == 'Cyclone' ? 'selected' : '' }}>🌪️ Cyclone</option>
                        <option value="Fire" {{ old('type') == 'Fire' ? 'selected' : '' }}>🔥 Fire</option>
                        <option value="Health Emergency" {{ old('type') == 'Health Emergency' ? 'selected' : '' }}>🏥 Health Emergency</option>
                        <option value="Other" {{ old('type') == 'Other' ? 'selected' : '' }}>⚠️ Other</option>
                    </select>
                </div>

                <!-- Severity Level -->
                <div class="form-group">
                    <label class="form-label" for="severity">Severity Level <span class="required">*</span></label>
                    <select id="severity" name="severity" class="form-select" required>
                        <option value="">Select severity level...</option>
                        <option value="Low" {{ old('severity') == 'Low' ? 'selected' : '' }}>Low - Minor Impact</option>
                        <option value="Medium" {{ old('severity') == 'Medium' ? 'selected' : '' }}>Medium - Moderate Impact</option>
                        <option value="High" {{ old('severity') == 'High' ? 'selected' : '' }}>High - Significant Impact</option>
                        <option value="Critical" {{ old('severity') == 'Critical' ? 'selected' : '' }}>Critical - Life Threatening</option>
                    </select>
                    
                    <div class="severity-preview">
                        <div class="severity-option severity-low">Low</div>
                        <div class="severity-option severity-medium">Medium</div>
                        <div class="severity-option severity-high">High</div>
                        <div class="severity-option severity-critical">Critical</div>
                    </div>
                </div>

                <!-- Location -->
                <div class="form-group">
                    <label class="form-label" for="location">Affected Location <span class="required">*</span></label>
                    <input type="text" id="location" name="location" class="form-input" 
                           placeholder="e.g., Dhaka Metropolitan Area, Chittagong Division"
                           value="{{ old('location') }}" required>
                    <div class="help-text">Specify the geographic area affected by this emergency</div>
                </div>

                <!-- Description -->
                <div class="form-group">
                    <label class="form-label" for="description">Alert Description <span class="required">*</span></label>
                    <textarea id="description" name="description" class="form-textarea" 
                              placeholder="Provide detailed information about the emergency situation, recommended actions for citizens, and any safety instructions..."
                              required>{{ old('description') }}</textarea>
                    <div class="help-text">Include specific details, safety instructions, and citizen recommendations</div>
                </div>

                <!-- Expiration Time -->
                <div class="form-group">
                    <label class="form-label" for="expires_at">Alert Expiration (Optional)</label>
                    <input type="datetime-local" id="expires_at" name="expires_at" class="form-input"
                           value="{{ old('expires_at') }}" 
                           min="{{ date('Y-m-d\TH:i') }}">
                    <div class="help-text">Leave empty for alerts without expiration. Set a time for temporary alerts.</div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="submit" class="btn btn-success">
                        📢 Create Alert & Notify Citizens
                    </button>
                    <a href="{{ route('admin.alerts') }}" class="btn btn-secondary">
                        Cancel
                    </a>
                </div>
            </form>
        </div>

        <div style="margin-top: 2rem; padding: 1rem; background: #fff3cd; border-radius: 4px;">
            <strong>⚠️ Important:</strong> Once created, this alert will be visible to all citizens immediately. 
            Make sure all information is accurate before submitting.
        </div>
    </div>

    <script>
        // Auto-update severity preview when selection changes
        document.getElementById('severity').addEventListener('change', function() {
            const selected = this.value.toLowerCase();
            const previews = document.querySelectorAll('.severity-option');
            
            previews.forEach(preview => {
                preview.style.opacity = '0.3';
            });
            
            if (selected) {
                const selectedPreview = document.querySelector('.severity-' + selected);
                if (selectedPreview) {
                    selectedPreview.style.opacity = '1';
                    selectedPreview.style.transform = 'scale(1.1)';
                    selectedPreview.style.transition = 'all 0.3s ease';
                }
            }
        });
    </script>
</body>
</html>