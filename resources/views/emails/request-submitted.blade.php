<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>✅ Request Received</title>
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
            background: linear-gradient(135deg, #27ae60 0%, #229954 100%);
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
        .success-icon {
            font-size: 80px;
            text-align: center;
            margin: 20px 0;
        }
        .request-id {
            background: #d4edda;
            border: 2px solid #27ae60;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin: 20px 0;
        }
        .request-id strong {
            font-size: 24px;
            color: #155724;
        }
        .info-box {
            background: #f8f9fa;
            border-left: 4px solid #27ae60;
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
        .urgency-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 15px;
            font-weight: bold;
            font-size: 12px;
        }
        .urgency-critical { background: #e74c3c; color: white; }
        .urgency-high { background: #f39c12; color: white; }
        .urgency-medium { background: #3498db; color: white; }
        .urgency-low { background: #95a5a6; color: white; }
        .timeline {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .timeline h3 {
            margin-top: 0;
            color: #856404;
        }
        .timeline-step {
            padding: 10px 0;
            border-left: 3px solid #ffc107;
            padding-left: 15px;
            margin-left: 10px;
            position: relative;
        }
        .timeline-step::before {
            content: '●';
            position: absolute;
            left: -9px;
            background: white;
            color: #ffc107;
            font-size: 20px;
        }
        .timeline-step.active {
            border-left-color: #27ae60;
        }
        .timeline-step.active::before {
            color: #27ae60;
        }
        .action-button {
            display: inline-block;
            background: #27ae60;
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 20px 0;
            text-align: center;
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
            <h1>✅ Request Received</h1>
            <p style="margin: 10px 0 0 0; opacity: 0.9;">Your emergency request has been submitted successfully</p>
        </div>

        <div class="content">
            <div class="success-icon">✅</div>
            
            <h2 style="text-align: center; color: #27ae60; margin: 10px 0;">
                Thank you, {{ $request->name }}!
            </h2>

            <div class="request-id">
                <p style="margin: 0; font-size: 14px; color: #666;">Your Request ID</p>
                <strong>#{{ $request->id }}</strong>
                <p style="margin: 10px 0 0 0; font-size: 12px; color: #666;">Please save this ID for tracking</p>
            </div>

            <div class="info-box">
                <div class="info-row">
                    <span class="label">📋 Request Type:</span>
                    <span class="value">{{ $request->request_type }}</span>
                </div>
                <div class="info-row">
                    <span class="label">📍 Location:</span>
                    <span class="value">{{ $request->location }}</span>
                </div>
                <div class="info-row">
                    <span class="label">👥 People Count:</span>
                    <span class="value">{{ $request->people_count }}</span>
                </div>
                <div class="info-row">
                    <span class="label">⚡ Urgency:</span>
                    <span class="value">
                        <span class="urgency-badge urgency-{{ strtolower($request->urgency) }}">
                            {{ $request->urgency }}
                        </span>
                    </span>
                </div>
                <div class="info-row">
                    <span class="label">📞 Contact:</span>
                    <span class="value">{{ $request->phone }}</span>
                </div>
                <div class="info-row">
                    <span class="label">⏰ Submitted:</span>
                    <span class="value">{{ $request->created_at->format('M d, Y h:i A') }}</span>
                </div>
            </div>

            <div class="timeline">
                <h3>📌 What Happens Next?</h3>
                <div class="timeline-step active">
                    <strong>1. Request Received</strong><br>
                    <small>Your emergency request has been logged in our system ✅</small>
                </div>
                <div class="timeline-step">
                    <strong>2. Admin Review</strong><br>
                    <small>Our team will review your request within minutes</small>
                </div>
                <div class="timeline-step">
                    <strong>3. Shelter Assignment</strong><br>
                    <small>We'll assign you to the nearest available shelter</small>
                </div>
                <div class="timeline-step">
                    <strong>4. Notification</strong><br>
                    <small>You'll receive shelter details via email and SMS</small>
                </div>
            </div>

            <div style="background: #d1ecf1; border: 1px solid #bee5eb; padding: 15px; border-radius: 5px; margin: 20px 0;">
                <strong>📱 Track Your Request:</strong><br>
                <p style="margin: 10px 0;">
                    Visit your dashboard to check the status of your request and view assigned shelter information.
                </p>
                <div style="text-align: center;">
                    <a href="{{ url('/citizen/dashboard') }}" class="action-button">
                        📊 View My Dashboard
                    </a>
                </div>
            </div>

            <div style="background: #fff3cd; border: 1px solid #ffc107; padding: 15px; border-radius: 5px;">
                <strong>⚠️ Important:</strong>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>Keep your phone charged and accessible</li>
                    <li>Stay in a safe location</li>
                    <li>Wait for shelter assignment notification</li>
                    <li>For immediate emergency, call 999</li>
                </ul>
            </div>
        </div>

        <div class="footer">
            <p><strong>Disaster Alert & Resource Management System</strong></p>
            <p>Request ID: #{{ $request->id }} | Status: {{ $request->status }}</p>
            <p>For assistance, contact our helpline or visit the emergency center.</p>
        </div>
    </div>
</body>
</html>
