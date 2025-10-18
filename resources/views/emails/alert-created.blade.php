<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🚨 Disaster Alert</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
        }
        .severity {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: bold;
            margin-top: 10px;
            font-size: 14px;
        }
        .severity-critical { background: #8b0000; }
        .severity-high { background: #ff6b6b; }
        .severity-moderate { background: #f39c12; }
        .severity-low { background: #95a5a6; }
        .content {
            padding: 30px;
        }
        .alert-icon {
            font-size: 60px;
            text-align: center;
            margin: 20px 0;
        }
        .info-box {
            background: #f8f9fa;
            border-left: 4px solid #e74c3c;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #dee2e6;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .label {
            font-weight: 600;
            color: #495057;
        }
        .value {
            color: #212529;
        }
        .description {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            line-height: 1.6;
        }
        .action-button {
            display: inline-block;
            background: #e74c3c;
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 20px 0;
        }
        .action-button:hover {
            background: #c0392b;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
        }
        .map-link {
            color: #3498db;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚨 DISASTER ALERT</h1>
            <span class="severity severity-{{ strtolower($alert->severity) }}">
                {{ strtoupper($alert->severity) }} SEVERITY
            </span>
        </div>

        <div class="content">
            <div class="alert-icon">⚠️</div>
            
            <h2 style="text-align: center; color: #e74c3c; margin: 10px 0;">
                {{ $alert->title }}
            </h2>

            <div class="info-box">
                <div class="info-row">
                    <span class="label">🏷️ Type:</span>
                    <span class="value">{{ $alert->type }}</span>
                </div>
                <div class="info-row">
                    <span class="label">📍 Location:</span>
                    <span class="value">{{ $alert->location }}</span>
                </div>
                <div class="info-row">
                    <span class="label">⏰ Issued:</span>
                    <span class="value">{{ $alert->created_at->format('M d, Y h:i A') }}</span>
                </div>
                <div class="info-row">
                    <span class="label">📊 Severity:</span>
                    <span class="value" style="color: #e74c3c; font-weight: bold;">{{ $alert->severity }}</span>
                </div>
            </div>

            <div class="description">
                <strong>📋 Alert Details:</strong><br><br>
                {{ $alert->description }}
            </div>

            @if($alert->affected_areas)
            <div class="info-box" style="border-left-color: #f39c12;">
                <strong>🗺️ Affected Areas:</strong><br>
                {{ $alert->affected_areas }}
            </div>
            @endif

            <div style="text-align: center;">
                <a href="https://www.google.com/maps?q={{ $alert->latitude }},{{ $alert->longitude }}" 
                   class="action-button"
                   target="_blank">
                    📍 View on Map
                </a>
            </div>

            <div style="background: #d1ecf1; border: 1px solid #bee5eb; padding: 15px; border-radius: 5px; margin-top: 20px;">
                <strong>🏠 Nearest Shelter Information:</strong><br>
                <p style="margin: 10px 0;">
                    Please visit <a href="{{ url('/shelters') }}" class="map-link">{{ url('/shelters') }}</a> 
                    to find the nearest emergency shelter and check availability.
                </p>
            </div>

            <div style="background: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; border-radius: 5px; margin-top: 15px;">
                <strong>⚡ Emergency Actions:</strong>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>Stay calm and follow official instructions</li>
                    <li>Move to a safe location immediately</li>
                    <li>Keep your phone charged</li>
                    <li>Check on family and neighbors</li>
                    <li>Listen to local authorities</li>
                </ul>
            </div>
        </div>

        <div class="footer">
            <p><strong>Disaster Alert & Resource Management System</strong></p>
            <p>This is an automated alert. For emergency assistance, call 999 or visit your nearest shelter.</p>
            <p>You received this email because you are subscribed to disaster alerts in your area.</p>
            <p style="margin-top: 10px;">
                <a href="{{ url('/') }}" style="color: #6c757d;">View Dashboard</a> | 
                <a href="{{ url('/shelters') }}" style="color: #6c757d;">Find Shelters</a>
            </p>
        </div>
    </div>
</body>
</html>
