<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🏠 Shelter Assigned</title>
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
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
        }
        .content {
            padding: 30px;
        }
        .shelter-icon {
            font-size: 80px;
            text-align: center;
            margin: 20px 0;
        }
        .shelter-card {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            border: 2px solid #3498db;
            padding: 25px;
            border-radius: 10px;
            margin: 20px 0;
        }
        .shelter-name {
            font-size: 24px;
            font-weight: bold;
            color: #1565c0;
            margin: 0 0 15px 0;
        }
        .shelter-info {
            background: white;
            padding: 15px;
            border-radius: 5px;
            margin-top: 15px;
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
        .map-button {
            display: inline-block;
            background: #e74c3c;
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 10px 5px;
            text-align: center;
        }
        .directions-button {
            display: inline-block;
            background: #27ae60;
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 10px 5px;
            text-align: center;
        }
        .capacity-bar {
            background: #ecf0f1;
            height: 25px;
            border-radius: 12px;
            overflow: hidden;
            margin: 10px 0;
        }
        .capacity-fill {
            background: linear-gradient(90deg, #27ae60, #229954);
            height: 100%;
            text-align: center;
            color: white;
            font-weight: bold;
            line-height: 25px;
            font-size: 12px;
        }
        .important-box {
            background: #fff3cd;
            border: 2px solid #ffc107;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .checklist {
            background: #d4edda;
            border: 1px solid #27ae60;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏠 Shelter Assigned</h1>
            <p style="margin: 10px 0 0 0; opacity: 0.9;">Your emergency shelter has been confirmed</p>
        </div>

        <div class="content">
            <div class="shelter-icon">🏠</div>
            
            <h2 style="text-align: center; color: #3498db; margin: 10px 0;">
                {{ $assignment->shelter->name }}
            </h2>

            <div class="shelter-card">
                <p class="shelter-name">📍 {{ $assignment->shelter->name }}</p>
                
                <div class="shelter-info">
                    <div class="info-row">
                        <span class="label">🏢 Address:</span>
                        <span class="value">{{ $assignment->shelter->address }}, {{ $assignment->shelter->city }}</span>
                    </div>
                    <div class="info-row">
                        <span class="label">📞 Contact:</span>
                        <span class="value">{{ $assignment->shelter->contact_phone }}</span>
                    </div>
                    @if($assignment->shelter->contact_email)
                    <div class="info-row">
                        <span class="label">✉️ Email:</span>
                        <span class="value">{{ $assignment->shelter->contact_email }}</span>
                    </div>
                    @endif
                    <div class="info-row">
                        <span class="label">⏰ Assigned:</span>
                        <span class="value">{{ $assignment->created_at->format('M d, Y h:i A') }}</span>
                    </div>
                </div>

                <div style="margin-top: 15px;">
                    <strong>Shelter Capacity:</strong>
                    <div class="capacity-bar">
                        <div class="capacity-fill" style="width: {{ ($assignment->shelter->current_occupancy / $assignment->shelter->capacity) * 100 }}%">
                            {{ $assignment->shelter->current_occupancy }}/{{ $assignment->shelter->capacity }}
                        </div>
                    </div>
                    <small>Available Space: {{ $assignment->shelter->capacity - $assignment->shelter->current_occupancy }} people</small>
                </div>
            </div>

            @if($assignment->shelter->facilities && count($assignment->shelter->facilities) > 0)
            <div style="background: #e8f5e9; border: 1px solid #27ae60; padding: 15px; border-radius: 5px; margin: 20px 0;">
                <strong>✨ Available Facilities:</strong><br>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    @foreach($assignment->shelter->facilities as $facility)
                        <li>{{ $facility }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div style="text-align: center; margin: 20px 0;">
                <a href="https://www.google.com/maps?q={{ $assignment->shelter->latitude }},{{ $assignment->shelter->longitude }}" 
                   class="map-button" target="_blank">
                    🗺️ View on Map
                </a>
                <a href="https://www.google.com/maps/dir/?api=1&destination={{ $assignment->shelter->latitude }},{{ $assignment->shelter->longitude }}" 
                   class="directions-button" target="_blank">
                    🧭 Get Directions
                </a>
            </div>

            <div class="important-box">
                <strong>⚠️ Important Instructions:</strong>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li><strong>Report within 2 hours</strong> of receiving this notification</li>
                    <li>Bring your ID card and this confirmation email/SMS</li>
                    @if($assignment->request && $assignment->request->people_count)
                    <li>Space is reserved for {{ $assignment->request->people_count }} people</li>
                    @endif
                    <li>Follow shelter rules and staff instructions</li>
                </ul>
            </div>

            <div class="checklist">
                <strong>📝 What to Bring:</strong>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>✅ Personal identification documents</li>
                    <li>✅ Essential medicines (if any)</li>
                    <li>✅ Mobile phone and charger</li>
                    <li>✅ Basic clothing and toiletries</li>
                    <li>✅ Important personal documents</li>
                </ul>
            </div>

            <div style="background: #d1ecf1; border: 1px solid #3498db; padding: 15px; border-radius: 5px; margin: 20px 0;">
                <strong>📞 Need Help?</strong><br>
                <p style="margin: 10px 0;">
                    <strong>Shelter Contact:</strong> {{ $assignment->shelter->contact_phone }}<br>
                    <strong>Emergency Helpline:</strong> 999<br>
                    @if($assignment->request)
                    <strong>Request ID:</strong> #{{ $assignment->request->id }}
                    @endif
                </p>
            </div>
        </div>

        <div class="footer">
            <p><strong>Disaster Alert & Resource Management System</strong></p>
            <p>Assignment ID: #{{ $assignment->id }}@if($assignment->request) | Request ID: #{{ $assignment->request->id }}@endif</p>
            <p>Please arrive at the shelter as soon as possible. Stay safe!</p>
        </div>
    </div>
</body>
</html>
