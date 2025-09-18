<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Requests - Admin View</title>
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
        .nav {
            background-color: #2980b9;
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
            background-color: #3498db;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
        }
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #3498db;
        }
        .stat-label {
            color: #7f8c8d;
            margin-top: 0.5rem;
        }
        .requests-grid {
            display: grid;
            gap: 1rem;
        }
        .request-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border-left: 4px solid;
        }
        .request-card.pending {
            border-left-color: #f39c12;
        }
        .request-card.assigned {
            border-left-color: #27ae60;
        }
        .request-card.auto-assigned {
            border-left-color: #3498db;
        }
        .request-card.completed {
            border-left-color: #95a5a6;
        }
        .request-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        .citizen-name {
            font-size: 1.2rem;
            font-weight: bold;
            color: #2c3e50;
        }
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
        }
        .status-pending {
            background-color: #f39c12;
            color: white;
        }
        .status-assigned {
            background-color: #27ae60;
            color: white;
        }
        .status-auto-assigned {
            background-color: #3498db;
            color: white;
        }
        .status-completed {
            background-color: #95a5a6;
            color: white;
        }
        .priority-badge {
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: bold;
            margin-left: 0.5rem;
        }
        .priority-critical {
            background-color: #e74c3c;
            color: white;
        }
        .priority-high {
            background-color: #f39c12;
            color: white;
        }
        .priority-medium {
            background-color: #3498db;
            color: white;
        }
        .request-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }
        .info-item {
            font-size: 0.9rem;
            color: #7f8c8d;
        }
        .info-label {
            font-weight: bold;
            color: #2c3e50;
        }
        .back-link {
            color: #3498db;
            text-decoration: none;
            margin-bottom: 1rem;
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📋 All Citizen Requests</h1>
        <p>Manage emergency assistance requests</p>
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
        
        <h2>Request Management System</h2>
        
        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number">{{ $stats['total_requests'] }}</div>
                <div class="stat-label">Total Requests</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $stats['pending_requests'] }}</div>
                <div class="stat-label">Pending Review</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $stats['assigned_requests'] }}</div>
                <div class="stat-label">Assigned/Auto-Assigned</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $stats['completed_requests'] }}</div>
                <div class="stat-label">Completed</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $stats['auto_assignments'] }}</div>
                <div class="stat-label">Auto-Assignments</div>
            </div>
        </div>

        <h3>All Requests ({{ count($requests) }})</h3>

        <!-- Requests List -->
        <div class="requests-grid">
            @foreach($requests as $request)
                <div class="request-card {{ str_replace([' ', '-'], '-', strtolower($request['status'])) }}">
                    <div class="request-header">
                        <div>
                            <span class="citizen-name">{{ $request['citizen_name'] }}</span>
                            <span class="priority-badge priority-{{ strtolower($request['priority']) }}">
                                {{ $request['priority'] }}
                            </span>
                        </div>
                        <span class="status-badge status-{{ str_replace([' ', '-'], '-', strtolower($request['status'])) }}">
                            {{ $request['status'] }}
                        </span>
                    </div>
                    
                    <div class="request-info">
                        <div class="info-item">
                            <span class="info-label">Emergency:</span> {{ $request['emergency_type'] }}
                        </div>
                        <div class="info-item">
                            <span class="info-label">Location:</span> {{ $request['location'] }}
                        </div>
                        <div class="info-item">
                            <span class="info-label">Phone:</span> {{ $request['phone'] }}
                        </div>
                        <div class="info-item">
                            <span class="info-label">Submitted:</span> {{ $request['created_at'] }}
                        </div>
                        @if($request['assigned_shelter'])
                            <div class="info-item">
                                <span class="info-label">Assigned Shelter:</span> {{ $request['assigned_shelter'] }}
                            </div>
                            <div class="info-item">
                                <span class="info-label">Assignment Type:</span> 
                                @if($request['assignment_type'] === 'Auto')
                                    <span style="color: #3498db;">Automatic (Admin Offline)</span>
                                @else
                                    <span style="color: #27ae60;">Manual Assignment</span>
                                @endif
                            </div>
                        @endif
                    </div>
                    
                    <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #ecf0f1;">
                        <strong>Description:</strong> {{ $request['description'] }}
                    </div>
                    
                    <div style="margin-top: 1rem; text-align: right;">
                        <a href="{{ route('requests.show', $request['id']) }}" 
                           style="background-color: #3498db; color: white; padding: 0.5rem 1rem; text-decoration: none; border-radius: 4px;">
                            View Details
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</body>
</html>
