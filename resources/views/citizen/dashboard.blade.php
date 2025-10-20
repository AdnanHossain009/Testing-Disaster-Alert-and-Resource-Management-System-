<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Citizen Dashboard - Disaster Alert System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #0D1326;
            min-height: 100vh;
            color: #E4E8F5;
        }
        .header {
            background: #091F57;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
            flex-wrap: wrap;
            gap: 1rem;
        }
        .header h1 {
            color: #E4E8F5;
            font-size: 1.8rem;
        }
        .user-info {
            color: #E4E8F5;
            opacity: 0.9;
            margin-right: 1rem;
        }
        .logout-btn {
            background: #ff6b6b;
            color: white;
            padding: 0.5rem 1rem;
            text-decoration: none;
            border-radius: 5px;
            transition: all 0.3s;
            font-weight: 500;
        }
        .logout-btn:hover {
            background: #ff8787;
            transform: translateY(-2px);
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
        @media (max-width: 768px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }
        .card {
            background: #091F57;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.3);
            border: 1px solid rgba(43, 85, 189, 0.2);
        }
        .card h3 {
            color: #E4E8F5;
            margin-bottom: 1rem;
            font-size: 1.3rem;
        }
        .alert-item {
            padding: 0.75rem;
            border-left: 4px solid #ff6b6b;
            background: rgba(255, 107, 107, 0.1);
            margin-bottom: 0.5rem;
            border-radius: 0 5px 5px 0;
            color: #E4E8F5;
        }
        .request-item {
            padding: 0.75rem;
            border: 1px solid rgba(43, 85, 189, 0.3);
            margin-bottom: 0.5rem;
            border-radius: 5px;
            background: rgba(43, 85, 189, 0.05);
            color: #E4E8F5;
        }
        .status-assigned { border-left: 3px solid #51cf66; background: rgba(81, 207, 102, 0.1); }
        .status-pending { border-left: 3px solid #ffa94d; background: rgba(255, 169, 77, 0.1); }
        .shelter-item {
            padding: 0.75rem;
            border: 1px solid rgba(43, 85, 189, 0.3);
            margin-bottom: 0.5rem;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(43, 85, 189, 0.05);
            color: #E4E8F5;
        }
        .btn {
            background: #2B55BD;
            color: white;
            padding: 0.75rem 1.5rem;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            margin-top: 1rem;
            transition: all 0.3s;
            box-shadow: 0 2px 8px rgba(43, 85, 189, 0.3);
            font-weight: 500;
        }
        .btn:hover {
            background: #3d6fd4;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(43, 85, 189, 0.5);
        }
        .btn-emergency {
            background: linear-gradient(135deg, #ff6b6b, #ff8787);
            font-size: 1.1rem;
            font-weight: bold;
            box-shadow: 0 2px 8px rgba(255, 107, 107, 0.3);
        }
        .btn-emergency:hover {
            background: linear-gradient(135deg, #ff8787, #ffa0a0);
            box-shadow: 0 4px 12px rgba(255, 107, 107, 0.5);
        }
        .notification-bell {
            position: relative;
            text-decoration: none;
            color: #E4E8F5;
            font-size: 1.5rem;
            transition: transform 0.3s;
        }
        .notification-bell:hover {
            transform: scale(1.1);
        }
        .notification-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #ff6b6b;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            box-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🏠 Citizen Dashboard</h1>
        <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
            <a href="{{ route('citizen.inbox') }}" class="notification-bell">
                🔔
                @php
                    $unseenCount = \App\Models\InAppNotification::forCitizen(Auth::id())->unseen()->count();
                @endphp
                @if($unseenCount > 0)
                <span class="notification-badge">
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