<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Emergency Request #{{ $request['id'] }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        .header {
            background-color: #3498db;
            color: white;
            padding: 1rem;
            text-align: center;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem;
        }
        .request-card {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 1rem;
        }
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 0.8rem;
            display: inline-block;
            margin-bottom: 1rem;
        }
        .status-pending { background-color: #fff3cd; color: #856404; }
        .status-assigned { background-color: #d4edda; color: #155724; }
        .status-in-progress { background-color: #cce5ff; color: #004085; }
        .status-completed { background-color: #d1ecf1; color: #0c5460; }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin: 1rem 0;
        }
        .info-item {
            background-color: #f8f9fa;
            padding: 1rem;
            border-radius: 6px;
            border-left: 4px solid #3498db;
        }
        .info-label {
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }
        .info-value {
            color: #34495e;
        }
        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            margin: 0.5rem;
            display: inline-block;
        }
        .btn-primary { background-color: #3498db; color: white; }
        .btn-secondary { background-color: #6c757d; color: white; }
        .btn-success { background-color: #27ae60; color: white; }
        .timeline {
            border-left: 3px solid #3498db;
            padding-left: 1rem;
            margin: 1rem 0;
        }
        .timeline-item {
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #eee;
        }
        .timeline-date {
            font-weight: bold;
            color: #3498db;
        }
        .emergency-contact {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 6px;
            padding: 1rem;
            margin: 1rem 0;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🔍 Track Emergency Request</h1>
        <p>Request ID: #{{ $request['id'] }}</p>
    </div>

    <div class="container">
        <!-- Request Status -->
        <div class="request-card">
            <h2>Request Status</h2>
            <span class="status-badge status-{{ strtolower(str_replace(' ', '-', $request['status'])) }}">
                {{ $request['status'] }}
            </span>
            
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Request ID</div>
                    <div class="info-value">#{{ $request['id'] }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Emergency Type</div>
                    <div class="info-value">{{ $request['emergency_type'] }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Priority Level</div>
                    <div class="info-value">{{ $request['priority'] ?? 'Medium' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Submitted</div>
                    <div class="info-value">{{ $request['created_at'] }}</div>
                </div>
            </div>
        </div>

        <!-- Personal Information -->
        <div class="request-card">
            <h2>📋 Request Details</h2>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Name</div>
                    <div class="info-value">{{ $request['citizen_name'] }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Phone</div>
                    <div class="info-value">{{ $request['phone'] }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Location</div>
                    <div class="info-value">{{ $request['location'] }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Group Size</div>
                    <div class="info-value">{{ $request['family_size'] ?? 1 }} people</div>
                </div>
            </div>
            
            <div class="info-item" style="margin-top: 1rem;">
                <div class="info-label">Description</div>
                <div class="info-value">{{ $request['description'] }}</div>
            </div>
        </div>

        <!-- Assignment Information -->
        @if($request['assigned_shelter'])
        <div class="request-card">
            <h2>🏠 Shelter Assignment</h2>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Assigned Shelter</div>
                    <div class="info-value">{{ $request['assigned_shelter'] }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Assignment Type</div>
                    <div class="info-value">{{ $request['assignment_type'] ?? 'Automatic' }}</div>
                </div>
                @if($request['assigned_at'])
                <div class="info-item">
                    <div class="info-label">Assigned Date</div>
                    <div class="info-value">{{ $request['assigned_at'] }}</div>
                </div>
                @endif
            </div>
            
            @if($request['shelter_id'])
            <div style="margin-top: 1rem;">
                <a href="{{ route('shelters.show', $request['shelter_id']) }}" class="btn btn-primary">
                    View Shelter Details
                </a>
            </div>
            @endif
        </div>
        @endif

        <!-- Status Timeline -->
        <div class="request-card">
            <h2>📅 Status Timeline</h2>
            <div class="timeline">
                <div class="timeline-item">
                    <div class="timeline-date">{{ $request['created_at'] }}</div>
                    <div>Emergency request submitted</div>
                </div>
                @if($request['assigned_at'])
                <div class="timeline-item">
                    <div class="timeline-date">{{ $request['assigned_at'] }}</div>
                    <div>Assigned to {{ $request['assigned_shelter'] }}</div>
                </div>
                @endif
                @if($request['status'] === 'Completed')
                <div class="timeline-item">
                    <div class="timeline-date">Recently</div>
                    <div>Request completed successfully</div>
                </div>
                @endif
            </div>
        </div>

        <!-- Emergency Contact -->
        <div class="emergency-contact">
            <strong>🚨 Emergency Contact</strong><br>
            For immediate life-threatening emergencies: <strong>999</strong><br>
            For request updates: <strong>+880-1XXXXXXXXX</strong>
        </div>

        <!-- Action Buttons -->
        <div style="text-align: center; margin-top: 2rem;">
            @if($request['status'] === 'Pending')
                <div style="background-color: #fff3cd; padding: 1rem; border-radius: 6px; margin-bottom: 1rem;">
                    <strong>⏳ Your request is being processed</strong><br>
                    An admin will assign you a shelter shortly. Please keep your phone available.
                </div>
            @elseif($request['assigned_shelter'])
                <a href="{{ route('shelters.show', $request['shelter_id']) }}" class="btn btn-success">
                    View Shelter Details
                </a>
            @endif
            
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">Back to Dashboard</a>
            <a href="{{ route('requests.create') }}" class="btn btn-primary">Submit New Request</a>
        </div>
    </div>
</body>
</html>