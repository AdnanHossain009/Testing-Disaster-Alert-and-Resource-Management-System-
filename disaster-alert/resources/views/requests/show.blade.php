<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Request #{{ $request['id'] }} - Disaster Alert System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .tracking-header {
            background: linear-gradient(135deg, #2c5aa0, #1e3f72);
            color: white;
            padding: 2rem 0;
        }
        .status-timeline {
            position: relative;
            padding: 1rem 0;
        }
        .status-step {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        .status-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            margin-right: 1rem;
            font-size: 1.2rem;
        }
        .status-completed { background-color: #28a745; }
        .status-current { background-color: #007bff; }
        .status-pending { background-color: #6c757d; }
        .urgency-critical { color: #dc3545; }
        .urgency-high { color: #fd7e14; }
        .urgency-medium { color: #ffc107; }
        .urgency-low { color: #20c997; }
        .info-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 1.5rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body class="bg-light">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #2c5aa0, #1e3f72);">
        <div class="container">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <i class="fas fa-shield-alt me-2"></i>Disaster Alert System
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a>
                <a class="nav-link" href="{{ route('alerts.index') }}">Alerts</a>
                <a class="nav-link" href="{{ route('shelters.index') }}">Shelters</a>
                <a class="nav-link" href="{{ route('requests.create') }}">Emergency Request</a>
            </div>
        </div>
    </nav>

    <!-- Header -->
    <div class="tracking-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="mb-2"><i class="fas fa-search me-3"></i>Request Tracking</h1>
                    <p class="mb-0">Track the status of your emergency request</p>
                </div>
                <div class="col-md-4 text-end">
                    <div class="badge bg-light text-dark fs-6">
                        Request #{{ $request['id'] }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container my-4">
        <a href="{{ route('dashboard') }}" class="btn btn-outline-primary mb-3">
            <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
        </a>

        <div class="row">
            <!-- Request Status Timeline -->
            <div class="col-md-8">
                <div class="info-card">
                    <h4 class="mb-4"><i class="fas fa-clock me-2"></i>Request Status Timeline</h4>
                    
                    <div class="status-timeline">
                        <!-- Submitted -->
                        <div class="status-step">
                            <div class="status-icon status-completed">
                                <i class="fas fa-paper-plane"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Request Submitted</h6>
                                <small class="text-muted">{{ date('M j, Y g:i A', strtotime($request['created_at'])) }}</small>
                            </div>
                        </div>

                        <!-- Under Review -->
                        <div class="status-step">
                            <div class="status-icon {{ in_array($request['status'], ['Pending']) ? 'status-current' : 'status-completed' }}">
                                <i class="fas fa-search"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Under Review</h6>
                                <small class="text-muted">
                                    @if($request['status'] === 'Pending')
                                        Admin is reviewing your request
                                    @else
                                        Completed
                                    @endif
                                </small>
                            </div>
                        </div>

                        <!-- Assigned -->
                        <div class="status-step">
                            <div class="status-icon {{ in_array($request['status'], ['Assigned', 'In Progress', 'Completed']) ? 'status-completed' : 'status-pending' }}">
                                <i class="fas fa-user-check"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Shelter Assigned</h6>
                                <small class="text-muted">
                                    @if($request['assigned_at'])
                                        {{ date('M j, Y g:i A', strtotime($request['assigned_at'])) }}
                                        @if($request['assigned_by_name'])
                                            by {{ $request['assigned_by_name'] }}
                                        @endif
                                    @else
                                        Waiting for assignment
                                    @endif
                                </small>
                            </div>
                        </div>

                        <!-- Completed -->
                        <div class="status-step">
                            <div class="status-icon {{ $request['status'] === 'Completed' ? 'status-completed' : 'status-pending' }}">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Request Completed</h6>
                                <small class="text-muted">
                                    @if($request['status'] === 'Completed')
                                        Request has been completed successfully
                                    @else
                                        Pending completion
                                    @endif
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Request Details -->
                <div class="info-card">
                    <h4 class="mb-4"><i class="fas fa-info-circle me-2"></i>Request Details</h4>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Emergency Type:</strong> {{ $request['emergency_type'] }}</p>
                            <p><strong>Location:</strong> {{ $request['location'] }}</p>
                            <p><strong>People Count:</strong> {{ $request['family_size'] }} people</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Urgency:</strong> 
                                <span class="urgency-{{ strtolower($request['urgency']) }} fw-bold">
                                    {{ $request['urgency'] }}
                                </span>
                            </p>
                            <p><strong>Current Status:</strong> 
                                <span class="badge bg-{{ $request['status'] === 'Pending' ? 'warning' : ($request['status'] === 'Assigned' ? 'info' : 'success') }}">
                                    {{ $request['status'] }}
                                </span>
                            </p>
                            <p><strong>Contact:</strong> {{ $request['phone'] }}</p>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <p><strong>Description:</strong></p>
                        <p class="text-muted">{{ $request['description'] }}</p>
                    </div>

                    @if($request['special_needs'])
                    <div class="mt-3">
                        <p><strong>Special Requirements:</strong></p>
                        <p class="text-muted">{{ $request['special_needs'] }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Current Status Card -->
            <div class="col-md-4">
                <div class="info-card">
                    <h5 class="mb-3">
                        <i class="fas fa-clipboard-check me-2"></i>Current Status
                    </h5>
                    
                    <div class="text-center mb-3">
                        <div class="badge bg-{{ $request['status'] === 'Pending' ? 'warning' : ($request['status'] === 'Assigned' ? 'info' : 'success') }} fs-6 p-2">
                            {{ $request['status'] }}
                        </div>
                    </div>

                    @if($request['status'] === 'Pending')
                        <div class="alert alert-warning">
                            <i class="fas fa-clock me-2"></i>
                            <strong>Waiting for Assignment</strong><br>
                            An admin will review your request and assign you to the nearest available shelter shortly.
                        </div>
                    @elseif($request['status'] === 'Assigned')
                        <div class="alert alert-success">
                            <i class="fas fa-home me-2"></i>
                            <strong>Shelter Assigned!</strong><br>
                            You have been assigned to a shelter. Please proceed as instructed.
                        </div>
                        
                        @if($request['assigned_shelter'])
                        <div class="mt-3">
                            <h6>Assigned Shelter:</h6>
                            <p class="mb-1"><strong>{{ $request['assigned_shelter'] }}</strong></p>
                            <button class="btn btn-primary btn-sm" onclick="alert('Shelter contact details would be shown here')">
                                <i class="fas fa-phone me-1"></i>Contact Shelter
                            </button>
                        </div>
                        @endif
                    @elseif($request['status'] === 'Completed')
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Request Completed</strong><br>
                            Your emergency request has been successfully completed.
                        </div>
                    @endif

                    @if($request['admin_notes'])
                    <div class="mt-3">
                        <h6>Admin Notes:</h6>
                        <p class="text-muted small">{{ $request['admin_notes'] }}</p>
                    </div>
                    @endif
                </div>

                <!-- Emergency Contacts -->
                <div class="info-card">
                    <h5 class="mb-3">
                        <i class="fas fa-phone me-2"></i>Emergency Contacts
                    </h5>
                    
                    <div class="d-grid gap-2">
                        <a href="tel:999" class="btn btn-danger">
                            <i class="fas fa-phone me-2"></i>Emergency: 999
                        </a>
                        <button class="btn btn-outline-primary" onclick="alert('Admin contact: +880-1XXXXXXXXX')">
                            <i class="fas fa-headset me-2"></i>Support Center
                        </button>
                    </div>
                </div>

                <!-- Actions -->
                <div class="info-card">
                    <h5 class="mb-3">
                        <i class="fas fa-cogs me-2"></i>Actions
                    </h5>
                    
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary" onclick="window.print()">
                            <i class="fas fa-print me-2"></i>Print Details
                        </button>
                        <button class="btn btn-outline-secondary" onclick="location.reload()">
                            <i class="fas fa-sync me-2"></i>Refresh Status
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>