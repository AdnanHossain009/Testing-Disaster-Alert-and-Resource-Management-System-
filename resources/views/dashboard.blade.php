<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <title>Disaster Alert Dashboard</title>

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

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }


        .stat-card {
            background: #091F57;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.3);
            text-align: center;
            border: 1px solid rgba(43, 85, 189, 0.2);
            transition: all 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(43, 85, 189, 0.3);
        }


        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #2B55BD;
            text-shadow: 0 0 10px rgba(43, 85, 189, 0.3);
        }


        .stat-label {
            color: #E4E8F5;
            opacity: 0.9;
            margin-top: 0.5rem;
        }

        .alerts-section {
            background: #091F57;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.3);
            border: 1px solid rgba(43, 85, 189, 0.2);
        }

        .alerts-section h3 {
            color: #E4E8F5;
            margin-top: 0;
        }

        .alerts-section p {
            color: #E4E8F5;
            opacity: 0.8;
        }

        .alert-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            
            padding: 1rem;
            border-bottom: 1px solid rgba(43, 85, 189, 0.2);
            transition: all 0.3s;
        }

        .alert-item:hover {
            background-color: rgba(43, 85, 189, 0.1);
        }


        .alert-item:last-child {
            border-bottom: none;
        }

        .alert-item strong {
            color: #E4E8F5;
        }

        .alert-item small {
            color: #E4E8F5;
            opacity: 0.7;
        }


        .severity-high {
            background-color: rgba(255, 107, 107, 0.2);
            color: #ff6b6b;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: bold;
        }

        .severity-critical {
            background-color: rgba(255, 107, 107, 0.2);
            color: #ff6b6b;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: bold;
        }


        .severity-medium {
            background-color: rgba(255, 169, 77, 0.2);
            color: #ffa94d;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: bold;
        }

        .severity-low {
            background-color: rgba(81, 207, 102, 0.2);
            color: #51cf66;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: bold;
        }


        .view-all-btn {
            background-color: #2B55BD;
            color: white;
            padding: 0.75rem 1.5rem;
            text-decoration: none;
            border-radius: 4px;
            display: inline-block;
            margin-top: 1rem;
            transition: all 0.3s;
            box-shadow: 0 2px 8px rgba(43, 85, 189, 0.3);
        }

        .view-all-btn:hover {
            background-color: #3d6fd4;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(43, 85, 189, 0.5);
        }

        h2 {
            color: #E4E8F5;
        }
    </style>

</head>


<body>

    <div class="header">
        <h1>🚨 Disaster Alert & Resource Management System</h1>
        <p>Real-time monitoring and emergency response coordination</p>
    </div>

    <div class="nav">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <a href="{{ route('alerts.index') }}">All Alerts</a>
        <a href="{{ route('shelters.index') }}">Shelters</a>
        <a href="{{ route('requests.create') }}">Emergency Request</a>
        <a href="{{ route('login') }}">Login</a>
    </div>

    <div class="container">
        <h2>System Overview</h2>
        
        <!-- statistics cards -->

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number">{{ $stats['total_alerts'] }}</div>
                <div class="stat-label">Total Alerts</div>
            </div>


            <div class="stat-card">
                <div class="stat-number">{{ $stats['active_alerts'] }}</div>
                <div class="stat-label">Active Alerts</div>
            </div>


            <div class="stat-card">
                <div class="stat-number">{{ $stats['high_severity'] }}</div>
                <div class="stat-label">High Severity</div>
            </div>


            <div class="stat-card">
                <div class="stat-number">{{ $stats['medium_severity'] }}</div>
                <div class="stat-label">Medium Severity</div>
            </div>

        </div>

        <!-- recent alerts -->
        <div class="alerts-section">
            <h3>Recent High Priority Alerts</h3>
            @if(count($recentAlerts) > 0)
                @foreach($recentAlerts as $alert)
                    <div class="alert-item">


                        <div>
                            <strong>{{ $alert['title'] }}</strong>
                            <br>
                            <small>📍 {{ $alert['location'] }} • 🕒 {{ $alert['created_at'] }}</small>
                            @if(isset($alert['source']))
                                <br>
                                <small>Source: {{ $alert['source'] }}</small>
                            @endif
                        </div>


                        <div>
                            <span class="severity-{{ strtolower($alert['severity']) }}">
                                {{ $alert['severity'] }}
                            </span>
                        </div>


                    </div>
                @endforeach
            @else
                <p>No recent alerts available.</p>
            @endif
            
            <a href="{{ route('alerts.index') }}" class="view-all-btn">View All Alerts</a>
        </div>
    </div>
    
</body>
</html>
