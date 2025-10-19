<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Citizen Dashboard - Disaster Alert System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .header {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header h1 {
            color: #2d3748;
            font-size: 1.8rem;
        }
        .user-info {
            color: #4a5568;
            margin-right: 1rem;
        }
        .logout-btn {
            background: #e53e3e;
            color: white;
            padding: 0.5rem 1rem;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
        }
        .logout-btn:hover {
            background: #c53030;
        }
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }
        .card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .card h3 {
            color: #2d3748;
            margin-bottom: 1rem;
        }
        .alert-item {
            padding: 0.75rem;
            border-left: 4px solid #e53e3e;
            background: #fed7d7;
            margin-bottom: 0.5rem;
            border-radius: 0 5px 5px 0;
        }
        .request-item {
            padding: 0.75rem;
            border: 1px solid #e2e8f0;
            margin-bottom: 0.5rem;
            border-radius: 5px;
        }
        .status-assigned { border-left-color: #48bb78; }
        .status-pending { border-left-color: #ed8936; }
        .shelter-item {
            padding: 0.75rem;
            border: 1px solid #e2e8f0;
            margin-bottom: 0.5rem;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn {
            background: #4299e1;
            color: white;
            padding: 0.75rem 1.5rem;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            margin-top: 1rem;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #3182ce;
        }
        .btn-emergency {
            background: #e53e3e;
            font-size: 1.1rem;
            font-weight: bold;
        }
        .btn-emergency:hover {
            background: #c53030;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🏠 Citizen Dashboard</h1>
        <div style="display: flex; align-items: center; gap: 1rem;">
            <a href="{{ route('citizen.inbox') }}" style="position: relative; text-decoration: none; color: #2d3748; font-size: 1.5rem;">
                🔔
                @php
                    $unseenCount = \App\Models\InAppNotification::forCitizen(Auth::id())->unseen()->count();
                @endphp
                @if($unseenCount > 0)
                <span style="position: absolute; top: -8px; right: -8px; background: #e53e3e; color: white; border-radius: 50%; width: 20px; height: 20px; font-size: 0.7rem; display: flex; align-items: center; justify-content: center; font-weight: bold;">
                    {{ $unseenCount }}
                </span>
                @endif
            </a>
            <span class="user-info">Welcome, {{ Auth::user()->name }}</span>
            <a href="{{ route('auth.logout') }}" class="logout-btn">Logout</a>
        </div>
    </div>

    <div class="container">
        <div class="card">
            <h2>🚨 Emergency Request</h2>
            <p>Need immediate assistance? Submit an emergency request for shelter, medical aid, or rescue.</p>
            <a href="{{ route('requests.create') }}" class="btn btn-emergency">🆘 Request Emergency Help</a>
        </div>

        <div class="dashboard-grid">
            <div class="card">
                <h3>🚨 Active Alerts</h3>
                @foreach($activeAlerts as $alert)
                <div class="alert-item">
                    <strong>{{ $alert['title'] }}</strong><br>
                    <small>Severity: {{ $alert['severity'] }} • {{ $alert['issued'] }}</small>
                </div>
                @endforeach
                <a href="{{ route('alerts.index') }}" class="btn">View All Alerts</a>
            </div>

            <div class="card">
                <h3>📋 My Requests</h3>
                @if($myRequests->count() > 0)
                    @foreach($myRequests as $request)
                    <div class="request-item status-{{ strtolower($request['status']) }}">
                        <strong>{{ $request['emergency_type'] }}</strong><br>
                        <small>Status: {{ $request['status'] }}</small><br>
                        @if($request['shelter'])
                            <small>Assigned to: {{ $request['shelter'] }}</small><br>
                        @endif
                        <small>{{ $request['submitted'] }}</small>
                    </div>
                    @endforeach
                @else
                    <p>No emergency requests submitted yet.</p>
                @endif
                <a href="{{ route('requests.citizen-dashboard') }}" class="btn">View All My Requests</a>
            </div>

            <div class="card">
                <h3>🏠 Nearest Shelters</h3>
                @foreach($nearestShelters as $shelter)
                <div class="shelter-item">
                    <div>
                        <strong>{{ $shelter['name'] }}</strong><br>
                        <small>{{ $shelter['distance'] }} away</small>
                    </div>
                    <div>
                        <span class="status-{{ strtolower($shelter['availability']) }}">{{ $shelter['availability'] }}</span><br>
                        <small>{{ $shelter['capacity'] }}</small>
                    </div>
                </div>
                @endforeach
                <a href="{{ route('shelters.index') }}" class="btn">View All Shelters</a>
            </div>
        </div>
    </div>
</body>
</html>