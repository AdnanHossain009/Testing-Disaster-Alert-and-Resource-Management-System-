<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Disaster Alert Dashboard</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }

        .header {
            background-color: #2c3e50;
            color: white;
            padding: 1rem;
            text-align: center;
        }

        .nav {
            background-color: #34495e;
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
            background-color: #5d6d7e;
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
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
        }


        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #2c3e50;
        }


        .stat-label {
            color: #7f8c8d;
            margin-top: 0.5rem;
        }

        .alerts-section {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .alert-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            
            padding: 1rem;
            border-bottom: 1px solid #ecf0f1;
        }


        .alert-item:last-child {
            border-bottom: none;
        }


        .severity-high {
            background-color: #e74c3c;
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.8rem;
        }


        .severity-medium {
            background-color: #f39c12;
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;

            font-size: 0.8rem;
        }


        .view-all-btn {
            background-color: #3498db;
            color: white;
            padding: 0.75rem 1.5rem;

            text-decoration: none;
            border-radius: 4px;
            display: inline-block;
            margin-top: 1rem;
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
                            <small>{{ $alert['created_at'] }}</small>
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
