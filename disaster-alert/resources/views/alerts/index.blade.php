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

        .alerts-grid {
            display: grid;
            gap: 1rem;
        }

        .alert-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border-left: 4px solid;
        }

        .alert-card.high {
            border-left-color: #e74c3c;
        }

        .alert-card.medium {
            border-left-color: #f39c12;
        }

        .alert-card.low {
            border-left-color: #27ae60;
        }

        .alert-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .alert-title {

            font-size: 1.25rem;
            font-weight: bold;
            color: #2c3e50;
        }

        .severity-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
        }

        .severity-high {
            background-color: #e74c3c;
            color: white;
        }

        .severity-medium {
            background-color: #f39c12;
            color: white;
        }

        .severity-low {
            background-color: #27ae60;
            color: white;
        }

        .alert-description {
            color: #7f8c8d;
            margin-bottom: 1rem;
            line-height: 1.5;
        }

        .alert-meta {
            display: flex;
            justify-content: space-between;

            align-items: center;
            font-size: 0.9rem;
            color: #95a5a6;
        }


        .view-details-btn {
            background-color: #3498db;
            color: white;

            padding: 0.5rem 1rem;
            text-decoration: none;
            border-radius: 4px;
            font-size: 0.9rem;
        }


        .view-details-btn:hover {
            background-color: #2980b9;
        }


        .back-link {
            color: #3498db;
            text-decoration: none;
            margin-bottom: 1rem;
            display: inline-block;
        }


        .back-link:hover {
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

        <a href="#">Shelters</a>
        <a href="#">Requests</a>
        <a href="#">Map</a>
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
