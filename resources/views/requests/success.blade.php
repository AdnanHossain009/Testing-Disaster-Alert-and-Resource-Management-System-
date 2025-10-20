<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Submitted Successfully</title>
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
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 2rem;
        }
        .success-card {
            background: #091F57;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.3);
            text-align: center;
            border: 1px solid rgba(43, 85, 189, 0.2);
        }
        .success-icon {
            font-size: 4rem;
            color: #51cf66;
            margin-bottom: 1rem;
        }
        .success-title {
            font-size: 1.5rem;
            font-weight: bold;
            color: #51cf66;
            margin-bottom: 1rem;
        }
        .request-id {
            background-color: rgba(81, 207, 102, 0.15);
            border: 2px solid #51cf66;
            border-radius: 8px;
            padding: 1rem;
            margin: 1rem 0;
        }
        .status-info {
            background-color: rgba(43, 85, 189, 0.15);
            border-left: 4px solid #2B55BD;
            padding: 1rem;
            margin: 1rem 0;
            text-align: left;
        }
        .citizen-info {
            background-color: rgba(255, 169, 77, 0.15);
            border: 1px solid #ffa94d;
            border-radius: 6px;
            padding: 1rem;
            margin: 1rem 0;
            text-align: left;
        }
        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            margin: 0.5rem;
            display: inline-block;
            transition: all 0.3s;
        }
        .btn-primary {
            background-color: #2B55BD;
            color: white;
            box-shadow: 0 2px 8px rgba(43, 85, 189, 0.3);
        }
        .btn-primary:hover {
            background-color: #3d6fd4;
            transform: translateY(-2px);
        }
        .btn-secondary {
            background-color: #51cf66;
            color: white;
            box-shadow: 0 2px 8px rgba(81, 207, 102, 0.3);
        }
        .btn-secondary:hover {
            background-color: #4cbb5e;
            transform: translateY(-2px);
        }
        .important-notice {
            background-color: rgba(255, 107, 107, 0.15);
            border: 1px solid #ff6b6b;
            border-radius: 6px;
            padding: 1rem;
            margin: 1rem 0;
            color: #ff6b6b;
        }
        h1, h2, h3, h4, p, strong, small {
            color: #E4E8F5;
        }
        small {
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>✅ Request Submitted Successfully</h1>
        <p>Your emergency request has been processed</p>
    </div>

    <div class="container">
        <div class="success-card">
            <div class="success-icon">✅</div>
            <div class="success-title">Emergency Request Submitted</div>
            
            <div class="request-id">
                <strong>Request ID: #{{ $requestId }}</strong><br>
                <small>Keep this ID for tracking your request</small>
            </div>

            <div class="status-info">
                <strong>Current Status:</strong> {{ $status }}<br>
                <strong>Assignment Type:</strong> 
                @if($status === 'Auto-Assigned')
                    Automatic (Admin offline)
                @else
                    Manual (Admin will review)
                @endif
            </div>

            <div class="important-notice">
                <strong>{{ $message }}</strong>
            </div>

            <!-- Citizen Information Summary -->
            <div class="citizen-info">
                <h4>📋 Request Summary</h4>
                <p><strong>Name:</strong> {{ $citizenData['name'] }}</p>
                <p><strong>Phone:</strong> {{ $citizenData['phone'] }}</p>
                <p><strong>Location:</strong> {{ $citizenData['location'] }}</p>
                <p><strong>Emergency Type:</strong> {{ $citizenData['request_type'] }}</p>
                <p><strong>Group Size:</strong> {{ $citizenData['people_count'] }} people</p>
            </div>

            @if($status === 'Auto-Assigned')
                <div style="background-color: #e8f8f5; border: 2px solid #27ae60; border-radius: 8px; padding: 1rem; margin: 1rem 0;">
                    <h4 style="color: #27ae60;">🏠 Shelter Assignment</h4>
                    <p>You have been automatically assigned to the nearest available shelter.</p>
                    <p><strong>Please proceed to your assigned shelter immediately.</strong></p>
                </div>
            @endif

            <!-- Next Steps -->
            <div style="text-align: left; margin: 1rem 0;">
                <h4>📝 Next Steps:</h4>
                <ol>
                    @if($status === 'Auto-Assigned')
                        <li>Proceed to your assigned shelter immediately</li>
                        <li>Bring identification and essential items</li>
                        <li>Call the shelter contact number if you need directions</li>
                    @else
                        <li>Wait for an admin to review and assign you a shelter</li>
                        <li>Keep your phone available for contact</li>
                        <li>Monitor alerts for updates</li>
                    @endif
                    <li>Keep your Request ID (#{{ $requestId }}) for reference</li>
                    <li>Call 999 for immediate life-threatening emergencies</li>
                </ol>
            </div>

            <!-- Action Buttons -->
            <div style="margin-top: 2rem;">
                @if($status === 'Auto-Assigned')
                    <a href="{{ route('shelters.show', 1) }}" class="btn btn-primary">View Shelter Details</a>
                @endif
                <a href="{{ route('requests.show', $requestId) }}" class="btn btn-secondary">Track Request</a>
                <a href="{{ route('dashboard') }}" class="btn btn-secondary">Back to Dashboard</a>
            </div>

            <!-- Emergency Contact -->
            <div style="margin-top: 2rem; padding: 1rem; background-color: #fff3cd; border-radius: 6px;">
                <strong>🚨 Emergency Contact</strong><br>
                For immediate life-threatening emergencies: <strong>999</strong><br>
                For request updates: <strong>+880-1XXXXXXXXX</strong>
            </div>
        </div>
    </div>
</body>
</html>
