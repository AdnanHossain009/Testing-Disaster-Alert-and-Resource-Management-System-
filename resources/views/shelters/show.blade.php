<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $shelter['name'] }} - Shelter Details</title>
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
            max-width: 1000px;
            margin: 0 auto;
            padding: 2rem;
        }
        .shelter-detail {
            background: #091F57;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.3);
            overflow: hidden;
            border: 1px solid rgba(43, 85, 189, 0.2);
        }
        .shelter-header {
            background: linear-gradient(135deg, #2B55BD, #091F57);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .shelter-title {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        .shelter-location {
            font-size: 1.1rem;
            opacity: 0.9;
        }
        .shelter-body {
            padding: 2rem;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }
        .info-section {
            background-color: rgba(43, 85, 189, 0.1);
            padding: 1.5rem;
            border-radius: 8px;
            border: 1px solid rgba(43, 85, 189, 0.2);
        }
        .info-title {
            font-weight: bold;
            color: #E4E8F5;
            margin-bottom: 1rem;
            font-size: 1.2rem;
        }
        .contact-info .info-title {
            color: #51cf66;
            font-size: 1.3rem;
            font-weight: 800;
        }
        .capacity-display {
            text-align: center;
            margin-bottom: 2rem;
        }
        .capacity-circle {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin: 0 auto 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            font-weight: bold;
            color: white;
        }
        .capacity-available {
            background: linear-gradient(135deg, #51cf66, #4cbb5e);
        }
        .capacity-nearly-full {
            background: linear-gradient(135deg, #ffa94d, #ff9a3d);
        }
        .capacity-full {
            background: linear-gradient(135deg, #ff6b6b, #ff5252);
        }
        .capacity-number {
            font-size: 2rem;
        }
        .capacity-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: bold;
            display: inline-block;
            margin-top: 1rem;
        }
        .status-available {
            background-color: rgba(81, 207, 102, 0.2);
            color: #51cf66;
            color: white;
        }
        .status-nearly-full {
            background-color: #f39c12;
            color: white;
        }
        .status-full {
            background-color: #e74c3c;
            color: white;
        }
        .facilities-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
        }
        .facility-item {
            background-color: white;
            border: 2px solid #3498db;
            border-radius: 8px;
            padding: 1rem;
            text-align: center;
            color: #3498db;
            font-weight: bold;
        }
        .safety-list {
            list-style: none;
            padding: 0;
        }
        .safety-list li {
            padding: 0.5rem 0;
            border-bottom: 1px solid #ecf0f1;
        }
        .safety-list li:before {
            content: "✅";
            margin-right: 0.5rem;
        }
        .contact-info {
            background-color: rgba(39, 174, 96, 0.15);
            border-left: 4px solid #27ae60;
            padding: 1.5rem;
            margin: 1rem 0;
            color: #E4E8F5;
        }
        .contact-info p {
            color: #E4E8F5;
            margin: 0.5rem 0;
            font-size: 1rem;
        }
        .contact-info strong {
            color: #51cf66;
            font-weight: bold;
        }
        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            flex-wrap: wrap;
        }
        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            text-align: center;
            flex: 1;
            min-width: 150px;
        }
        .btn-primary {
            background-color: #27ae60;
            color: white;
        }
        .btn-secondary {
            background-color: #3498db;
            color: white;
        }
        .btn-outline {
            background-color: transparent;
            border: 2px solid #27ae60;
            color: #27ae60;
        }
        .back-link {
            color: #27ae60;
            text-decoration: none;
            margin-bottom: 1rem;
            display: inline-block;
        }
        .last-updated {
            text-align: center;
            color: #7f8c8d;
            font-size: 0.9rem;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🏠 Shelter Details</h1>
        <p>Comprehensive shelter information</p>
    </div>

    <div class="nav">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <a href="{{ route('alerts.index') }}">Alerts</a>
        <a href="{{ route('shelters.index') }}">Shelters</a>
        <a href="{{ route('requests.create') }}">Emergency Request</a>
        <a href="{{ route('login') }}">Login</a>
    </div>

    <div class="container">
        <a href="{{ route('shelters.index') }}" class="back-link">← Back to All Shelters</a>
        
        <div class="shelter-detail">
            <!-- Header -->
            <div class="shelter-header">
                <div class="shelter-title">{{ $shelter['name'] }}</div>
                <div class="shelter-location">📍 {{ $shelter['address'] }}</div>
            </div>

            <div class="shelter-body">
                <!-- Capacity Display -->
                <div class="capacity-display">
                    @php
                        $occupancyPercentage = ($shelter['occupied'] / $shelter['capacity']) * 100;
                        $statusClass = $occupancyPercentage >= 90 ? 'full' : ($occupancyPercentage >= 70 ? 'nearly-full' : 'available');
                    @endphp
                    
                    <div class="capacity-circle capacity-{{ $statusClass }}">
                        <div class="capacity-number">{{ $shelter['available'] }}</div>
                        <div class="capacity-label">Available</div>
                    </div>
                    
                    <div>
                        <strong>{{ $shelter['occupied'] }} occupied</strong> out of 
                        <strong>{{ $shelter['capacity'] }} total capacity</strong>
                    </div>
                    
                    <span class="status-badge status-{{ str_replace([' ', '-'], '-', strtolower($shelter['status'])) }}">
                        {{ $shelter['status'] }}
                    </span>
                </div>

                <!-- Information Grid -->
                <div class="info-grid">
                    <!-- Basic Information -->
                    <div class="info-section">
                        <div class="info-title">📋 Basic Information</div>
                        <p><strong>Location:</strong> {{ $shelter['location'] }}</p>
                        <p><strong>Full Address:</strong> {{ $shelter['address'] }}</p>
                        <p><strong>Manager:</strong> {{ $shelter['manager'] }}</p>
                        <p><strong>Contact:</strong> {{ $shelter['contact'] }}</p>
                        <p><strong>Coordinates:</strong> {{ $shelter['coordinates']['lat'] }}, {{ $shelter['coordinates']['lng'] }}</p>
                    </div>

                    <!-- Description -->
                    <div class="info-section">
                        <div class="info-title">📝 Description</div>
                        <p>{{ $shelter['description'] }}</p>
                    </div>
                </div>

                <!-- Facilities -->
                <div class="info-section">
                    <div class="info-title">🏢 Available Facilities</div>
                    <div class="facilities-grid">
                        @foreach($shelter['facilities'] as $facility)
                            <div class="facility-item">{{ $facility }}</div>
                        @endforeach
                    </div>
                </div>

                <!-- Safety Measures -->
                <div class="info-section">
                    <div class="info-title">🛡️ Safety Measures</div>
                    <ul class="safety-list">
                        @foreach($shelter['safety_measures'] as $measure)
                            <li>{{ $measure }}</li>
                        @endforeach
                    </ul>
                </div>

                <!-- Contact Information -->
                <div class="contact-info">
                    <div class="info-title">📞 Emergency Contact</div>
                    <p><strong>Phone:</strong> {{ $shelter['contact'] }}</p>
                    <p><strong>Manager:</strong> {{ $shelter['manager'] }}</p>
                    <p><strong>For emergencies, call:</strong> 999</p>
                </div>

                <!-- Action Buttons -->
                @if($shelter['available'] > 0)
                    <div class="action-buttons">
                        <a href="#" class="btn btn-primary">Request Shelter</a>
                        <a href="#" class="btn btn-secondary">Get Directions</a>
                        <a href="#" class="btn btn-outline">View on Map</a>
                    </div>
                @else
                    <div class="action-buttons">
                        <a href="#" class="btn btn-secondary">Find Alternative</a>
                        <a href="#" class="btn btn-outline">View on Map</a>
                    </div>
                @endif

                <div class="last-updated">
                    Last updated: {{ $shelter['last_updated'] }}
                </div>
            </div>
        </div>
    </div>
</body>
</html>
