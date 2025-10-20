<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Disaster Alerts</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #0D1326;
            color: #E4E8F5;
        }
        .header {
            background-color: #091F57;
            color: white;
            padding: 1rem;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.3);
        }
        .nav {
            background-color: #091F57;
            padding: 0.5rem;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        .nav a {
            color: #E4E8F5;
            text-decoration: none;
            margin: 0 1rem;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            transition: all 0.3s;
        }
        .nav a:hover {
            background-color: rgba(43, 85, 189, 0.3);
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        .alerts-grid {
            display: grid;
            gap: 1rem;
        }
        .alert-card {
            background: #091F57;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.3);
            border-left: 4px solid;
            border: 1px solid rgba(43, 85, 189, 0.2);
            transition: all 0.3s;
        }
        .alert-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.5);
        }
        .alert-card.high,
        .alert-card.critical {
            border-left-color: #ff6b6b;
            border-left-width: 4px;
        }
        .alert-card.medium {
            border-left-color: #ffa94d;
            border-left-width: 4px;
        }
        .alert-card.low {
            border-left-color: #51cf66;
            border-left-width: 4px;
        }
        .alert-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        .alert-title {
            font-size: 1.25rem;
            font-weight: bold;
            color: #E4E8F5;
        }
        .severity-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
        }
        .severity-critical,
        .severity-high {
            background-color: rgba(255, 107, 107, 0.2);
            color: #ff6b6b;
        }
        .severity-medium {
            background-color: rgba(255, 169, 77, 0.2);
            color: #ffa94d;
        }
        .severity-low {
            background-color: rgba(81, 207, 102, 0.2);
            color: #51cf66;
        }
        .alert-description {
            color: #E4E8F5;
            opacity: 0.9;
            margin-bottom: 1rem;
            line-height: 1.5;
        }
        .alert-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.9rem;
            color: #E4E8F5;
            opacity: 0.7;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        .view-details-btn {
            background-color: #2B55BD;
            color: white;
            padding: 0.5rem 1rem;
            text-decoration: none;
            border-radius: 4px;
            font-size: 0.9rem;
            transition: all 0.3s;
            box-shadow: 0 2px 8px rgba(43, 85, 189, 0.3);
        }
        .view-details-btn:hover {
            background-color: #3d6fd4;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(43, 85, 189, 0.5);
        }
        .back-link {
            color: #2B55BD;
            text-decoration: none;
            margin-bottom: 1rem;
            display: inline-block;
            transition: color 0.3s;
        }
        .back-link:hover {
            color: #3d6fd4;
            text-decoration: underline;
        }
    </style>

</head>

<body>


    <div class="header">
        <h1>🚨 All Disaster Alerts</h1>
        <p>Stay informed about ongoing emergencies and warnings</p>

    </div>

    <div class="nav">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <a href="{{ route('alerts.index') }}">All Alerts</a>
        <a href="{{ route('shelters.index') }}">Shelters</a>
        <a href="{{ route('requests.create') }}">Emergency Request</a>
        <a href="{{ route('login') }}">Login</a>
    </div>

    <div class="container">
        <a href="{{ route('dashboard') }}" class="back-link">← Back to Dashboard</a>
        
        <h2>Current Alerts ({{ count($alerts) }})</h2>

        <div class="alerts-grid">
            @if(count($alerts) > 0)
                @foreach($alerts as $alert)
                    <div class="alert-card {{ strtolower($alert['severity']) }}">

                        <div class="alert-header">


                            <div class="alert-title">{{ $alert['title'] }}</div>
                            <span class="severity-badge severity-{{ strtolower($alert['severity']) }}">
                                {{ $alert['severity'] }}
                            </span>
                        </div>
                        
                        <div class="alert-description">
                            {{ $alert['description'] }}
                        </div>
                        
                        <div class="alert-meta">


                            <div>
                                📍 {{ $alert['location'] }}
                                <br>
                                🕒 {{ $alert['created_at'] }}
                            </div>


                            <a href="{{ route('alerts.show', $alert['id']) }}" class="view-details-btn">
                                View Details
                            </a>
                        </div>
                    </div>
                    
                @endforeach
            @else
                <div class="alert-card">
                    <p>No alerts available at this time.</p>
                </div>
            @endif
        </div>
    </div>
</body>
</html>
