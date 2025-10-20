<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">


    <title>{{ $alert['title'] }} - Alert Details</title>

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
            text-align:center;
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
            max-width: 1000px;
            margin: 0 auto;
            padding: 2rem;
        }


        .alert-detail {
            background: #091F57;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.3);
            border: 1px solid rgba(43, 85, 189, 0.2);
            border-left: 6px solid;
        }


        .alert-detail.high, .alert-detail.critical {
            border-left-color: #ff6b6b;
        }

        .alert-detail.medium {
            border-left-color: #ffa94d;
        }

        .alert-detail.low {
            border-left-color: #51cf66;
        }

        .alert-header {
            margin-bottom: 2rem;
        }

        .alert-title {

            font-size: 2rem;
            font-weight: bold;
            color: #E4E8F5;
            margin-bottom: 1rem;
        }


        .alert-meta {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));

            gap: 1rem;
            margin-bottom: 2rem;
        }


        .meta-item {
            background-color: rgba(43, 85, 189, 0.15);
            padding: 1rem;
            border-radius: 6px;
            border: 1px solid rgba(43, 85, 189, 0.2);
        }


        .meta-label {
            font-weight: bold;
            color: #E4E8F5;
            margin-bottom: 0.5rem;
        }


        .meta-value {
            color: #E4E8F5;
            opacity: 0.9;
        }

        .severity-badge {

            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: bold;
            display: inline-block;

        }

        .severity-high, .severity-critical {
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


        .description-section {
            background-color: rgba(43, 85, 189, 0.1);
            padding: 1.5rem;
            border-radius: 6px;
            margin-bottom: 2rem;
            border: 1px solid rgba(43, 85, 189, 0.2);
        }


        .description-title {
            font-weight: bold;
            color: #E4E8F5;
            margin-bottom: 1rem;
        }


        .description-text {
            line-height: 1.6;
            color: #E4E8F5;
            opacity: 0.9;
        }


        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }


        .info-card {
            background-color: rgba(43, 85, 189, 0.1);
            border: 1px solid rgba(43, 85, 189, 0.2);
            border-radius: 6px;
            padding: 1.5rem;
        }


        .info-card-title {
            font-weight: bold;
            color: #E4E8F5;
            margin-bottom: 1rem;
            font-size: 1.1rem;
        }
        
        .affected-areas {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .area-tag {
            background-color: rgba(43, 85, 189, 0.3);
            color: #E4E8F5;
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.9rem;
        }

        .emergency-contact {
            background-color: rgba(255, 107, 107, 0.2);
            color: #ff6b6b;
            padding: 0.75rem;
            border-radius: 6px;
            text-align: center;
            font-weight: bold;
            margin-bottom: 0.5rem;
            border: 1px solid #ff6b6b;
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
        
        h2, h3, h4 {
            color: #E4E8F5;
        }
        
        p {
            color: #E4E8F5;
            opacity: 0.9;
        }


        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }


        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            text-align: center;
        }


        .btn-primary {
            background-color: #3498db;
            color: white;
        }


        .btn-secondary {
            background-color: #95a5a6;
            color: white;
        }

    </style>

</head>


<body>
    <div class="header">
        <h1>🚨 Alert Details</h1>
        <p>Detailed information about this emergency</p>
    </div>

    <div class="nav">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <a href="{{ route('alerts.index') }}">All Alerts</a>
        <a href="{{ route('shelters.index') }}">Shelters</a>
        <a href="{{ route('requests.create') }}">Emergency Request</a>
        <a href="{{ route('login') }}">Login</a>
    </div>

    <div class="container">
        <a href="{{ route('alerts.index') }}" class="back-link">← Back to All Alerts</a>
        
        <div class="alert-detail {{ strtolower($alert['severity']) }}">
            <div class="alert-header">
                <div class="alert-title">{{ $alert['title'] }}</div>
            </div>

            <!-- alert meta information -->

            <div class="alert-meta">
                <div class="meta-item">
                    <div class="meta-label">Severity Level</div>
                    <div class="meta-value">
                        <span class="severity-badge severity-{{ strtolower($alert['severity']) }}">
                            {{ $alert['severity'] }}
                        </span>
                    </div>
                </div>


                <div class="meta-item">
                    <div class="meta-label">Location</div>
                    <div class="meta-value">📍 {{ $alert['location'] }}</div>
                </div>


                <div class="meta-item">
                    <div class="meta-label">Alert Time</div>
                    <div class="meta-value">🕒 {{ $alert['created_at'] }}</div>
                </div>


                <div class="meta-item">
                    <div class="meta-label">Alert ID</div>
                    <div class="meta-value">#{{ $alert['id'] }}</div>
                </div>
            </div>

            <!-- description -->

            <div class="description-section">
                <div class="description-title">📋 Description & Instructions</div>
                <div class="description-text">{{ $alert['description'] }}</div>
            </div>

            <!-- additional information -->

            <div class="info-grid">


                <!-- affected Areas -->


                <div class="info-card">
                    <div class="info-card-title">🏘️ Affected Areas</div>
                    <div class="affected-areas">
                        @foreach($alert['affected_areas'] as $area)
                            <span class="area-tag">{{ $area }}</span>
                        @endforeach
                    </div>
                </div>

                <!-- emergency contacts -->

                <div class="info-card">
                    <div class="info-card-title">📞 Emergency Contacts</div>
                    @foreach($alert['emergency_contacts'] as $contact)
                        <div class="emergency-contact">{{ $contact }}</div>
                    @endforeach
                </div>
            </div>

            <!-- action buttons -->

            <div class="action-buttons">
                <a href="#" class="btn btn-primary">Request Help</a>
                <a href="#" class="btn btn-secondary">Find Shelter</a>
                <a href="#" class="btn btn-secondary">View on Map</a>

            </div>
        </div>
    </div>
</body>
</html>
