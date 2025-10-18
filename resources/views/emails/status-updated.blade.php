<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>📋 Status Updated</title>
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
            background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%);
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
        .status-icon {
            font-size: 80px;
            text-align: center;
            margin: 20px 0;
        }
        .status-change {
            background: #f8f9fa;
            border: 2px solid #9b59b6;
            padding: 25px;
            border-radius: 10px;
            margin: 20px 0;
            text-align: center;
        }
        .status-badge {
            display: inline-block;
            padding: 10px 25px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 16px;
            margin: 10px;
        }
        .status-pending { background: #f39c12; color: white; }
        .status-assigned { background: #3498db; color: white; }
        .status-in-progress { background: #9b59b6; color: white; }
        .status-completed { background: #27ae60; color: white; }
        .status-cancelled { background: #95a5a6; color: white; }
        .arrow {
            font-size: 30px;
            color: #9b59b6;
            margin: 0 10px;
        }
        .info-box {
            background: #f8f9fa;
            border-left: 4px solid #9b59b6;
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
        .action-button {
            display: inline-block;
            background: #9b59b6;
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
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
            <h1>📋 Status Updated</h1>
            <p style="margin: 10px 0 0 0; opacity: 0.9;">Your request status has been updated</p>
        </div>

        <div class="content">
            <div class="status-icon">🔄</div>
            
            <h2 style="text-align: center; color: #9b59b6; margin: 10px 0;">
                Request #{{ $request->id }} Status Change
            </h2>

            <div class="status-change">
                <p style="margin: 0 0 15px 0; color: #666;">Status Updated From:</p>
                <span class="status-badge status-{{ strtolower(str_replace(' ', '-', $oldStatus)) }}">
                    {{ $oldStatus }}
                </span>
                <span class="arrow">→</span>
                <span class="status-badge status-{{ strtolower(str_replace(' ', '-', $request->status)) }}">
                    {{ $request->status }}
                </span>
                <p style="margin: 15px 0 0 0; color: #666; font-size: 14px;">
                    {{ $request->updated_at->format('M d, Y h:i A') }}
                </p>
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
                    <span class="label">👤 Name:</span>
                    <span class="value">{{ $request->name }}</span>
                </div>
                <div class="info-row">
                    <span class="label">⏰ Last Updated:</span>
                    <span class="value">{{ $request->updated_at->diffForHumans() }}</span>
                </div>
            </div>

            @if($request->status === 'Completed')
            <div style="background: #d4edda; border: 1px solid #27ae60; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <strong>✅ Request Completed!</strong><br>
                <p style="margin: 10px 0;">
                    Your emergency request has been successfully completed. We hope you're safe now.
                    Thank you for using our disaster management system.
                </p>
            </div>
            @elseif($request->status === 'In Progress')
            <div style="background: #e3f2fd; border: 1px solid #2196f3; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <strong>🔄 Request In Progress</strong><br>
                <p style="margin: 10px 0;">
                    Our relief team is currently working on your request. You should receive assistance soon.
                    Please stay at your reported location.
                </p>
            </div>
            @elseif($request->status === 'Assigned')
            <div style="background: #fff3cd; border: 1px solid #ffc107; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <strong>📌 Request Assigned</strong><br>
                <p style="margin: 10px 0;">
                    Your request has been assigned to a relief worker. You'll receive further updates shortly.
                </p>
            </div>
            @elseif($request->status === 'Cancelled')
            <div style="background: #f8d7da; border: 1px solid #f5c6cb; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <strong>❌ Request Cancelled</strong><br>
                <p style="margin: 10px 0;">
                    This request has been cancelled. If you still need assistance, please submit a new request.
                </p>
            </div>
            @endif

            <div style="text-align: center;">
                <a href="{{ url('/citizen/dashboard') }}" class="action-button">
                    📊 View Full Details
                </a>
            </div>

            <div style="background: #f8f9fa; border: 1px solid #dee2e6; padding: 15px; border-radius: 5px; margin: 20px 0;">
                <strong>📞 Need Assistance?</strong><br>
                <p style="margin: 10px 0;">
                    <strong>Emergency Helpline:</strong> 999<br>
                    <strong>Request ID:</strong> #{{ $request->id }}<br>
                    <strong>Phone:</strong> {{ $request->phone }}
                </p>
            </div>
        </div>

        <div class="footer">
            <p><strong>Disaster Alert & Resource Management System</strong></p>
            <p>Request ID: #{{ $request->id }} | Current Status: {{ $request->status }}</p>
            <p>You will receive updates as your request progresses through the system.</p>
        </div>
    </div>
</body>
</html>
