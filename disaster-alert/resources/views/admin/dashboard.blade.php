<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Disaster Alert System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .admin-header {
            background: linear-gradient(135deg, #2c5aa0, #1e3f72);
            color: white;
            padding: 2rem 0;
        }
        .card-hover {
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        .stats-card {
            background: linear-gradient(135deg, #2c5aa0, #1e3f72);
            color: white;
            border: none;
        }
        .stats-card .card-body {
            text-align: center;
        }
        .activity-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: white;
            margin-right: 1rem;
        }
        .activity-icon.high { background-color: #dc3545; }
        .activity-icon.medium { background-color: #fd7e14; }
        .activity-icon.low { background-color: #20c997; }
        .quick-action-card {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            border: none;
            text-decoration: none;
            display: block;
            margin-bottom: 1rem;
        }
        .quick-action-card:hover {
            color: white;
            text-decoration: none;
        }
        .system-status {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            border-radius: 8px;
        }
    </style>
</head>
<body class="bg-light">
    <!-- Admin Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #2c5aa0, #1e3f72);">
        <div class="container">
            <a class="navbar-brand" href="{{ route('admin.dashboard') }}">
                <i class="fas fa-shield-alt me-2"></i>Disaster Alert Admin
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('admin.dashboard') }}">
                            <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.alerts') }}">
                            <i class="fas fa-exclamation-triangle me-1"></i>Alerts
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.shelters') }}">
                            <i class="fas fa-home me-1"></i>Shelters
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.requests') }}">
                            <i class="fas fa-hands-helping me-1"></i>Emergency Requests
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-1"></i>Admin
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('dashboard') }}">Public View</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('auth.logout') }}">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Header -->
    <div class="admin-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="mb-2"><i class="fas fa-tachometer-alt me-3"></i>Admin Control Panel</h1>
                    <p class="mb-0">Welcome, {{ session('user')['name'] }} - Monitor and manage disaster response operations</p>
                </div>
                <div class="col-md-4 text-end">
                    <div class="system-status p-3">
                        <strong><i class="fas fa-check-circle me-2"></i>All Systems Operational</strong>
                        <br><small>Last check: 2 minutes ago</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container my-4">
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card stats-card card-hover">
                    <div class="card-body">
                        <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                        <h4>{{ $stats['total_alerts'] }}</h4>
                        <p class="mb-0">Total Alerts</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-success text-white card-hover">
                    <div class="card-body text-center">
                        <i class="fas fa-broadcast-tower fa-2x mb-2"></i>
                        <h4>{{ $stats['active_alerts'] }}</h4>
                        <p class="mb-0">Active Alerts</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-info text-white card-hover">
                    <div class="card-body text-center">
                        <i class="fas fa-home fa-2x mb-2"></i>
                        <h4>{{ $stats['total_shelters'] }}</h4>
                        <p class="mb-0">Total Shelters</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-warning text-dark card-hover">
                    <div class="card-body text-center">
                        <i class="fas fa-bed fa-2x mb-2"></i>
                        <h4>{{ $stats['available_shelters'] }}</h4>
                        <p class="mb-0">Available Shelters</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Second Row Statistics -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card bg-primary text-white card-hover">
                    <div class="card-body text-center">
                        <i class="fas fa-hands-helping fa-2x mb-2"></i>
                        <h4>{{ $stats['pending_requests'] }}</h4>
                        <p class="mb-0">Pending Requests</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-success text-white card-hover">
                    <div class="card-body text-center">
                        <i class="fas fa-check-circle fa-2x mb-2"></i>
                        <h4>{{ $stats['assigned_requests'] }}</h4>
                        <p class="mb-0">Assigned Requests</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-secondary text-white card-hover">
                    <div class="card-body text-center">
                        <i class="fas fa-users fa-2x mb-2"></i>
                        <h4>{{ array_sum([$stats['pending_requests'], $stats['assigned_requests']]) }}</h4>
                        <p class="mb-0">Total Citizens Helped</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-dark text-white card-hover">
                    <div class="card-body text-center">
                        <i class="fas fa-chart-line fa-2x mb-2"></i>
                        <h4>{{ round((($stats['assigned_requests'] / max($stats['pending_requests'] + $stats['assigned_requests'], 1)) * 100), 1) }}%</h4>
                        <p class="mb-0">Response Rate</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dashboard Grid -->
        <div class="row">
            <!-- Recent Activity -->
            <div class="col-lg-8 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Recent Activity</h5>
                    </div>
                    <div class="card-body">
                        @foreach($recentActivity as $activity)
                        <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                            <div class="activity-icon {{ $activity['priority'] }}">
                                @if($activity['type'] === 'request')
                                    <i class="fas fa-hands-helping"></i>
                                @elseif($activity['type'] === 'assignment')
                                    <i class="fas fa-home"></i>
                                @else
                                    <i class="fas fa-exclamation-triangle"></i>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold">{{ $activity['message'] }}</div>
                                <small class="text-muted">{{ $activity['time'] }}</small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <a href="{{ route('admin.alerts.create') }}" class="card quick-action-card card-hover text-decoration-none">
                            <div class="card-body py-2">
                                <i class="fas fa-plus me-2"></i>Create New Alert
                            </div>
                        </a>
                        <a href="{{ route('admin.shelters.create') }}" class="card quick-action-card card-hover text-decoration-none">
                            <div class="card-body py-2">
                                <i class="fas fa-home me-2"></i>Add New Shelter
                            </div>
                        </a>
                        <a href="{{ route('admin.requests') }}" class="card quick-action-card card-hover text-decoration-none">
                            <div class="card-body py-2">
                                <i class="fas fa-list me-2"></i>Review Requests
                            </div>
                        </a>
                        <a href="{{ route('admin.alerts') }}" class="card quick-action-card card-hover text-decoration-none">
                            <div class="card-body py-2">
                                <i class="fas fa-chart-bar me-2"></i>View Analytics
                            </div>
                        </a>
                        <a href="{{ route('dashboard') }}" class="card quick-action-card card-hover text-decoration-none">
                            <div class="card-body py-2">
                                <i class="fas fa-map me-2"></i>Map Overview
                            </div>
                        </a>
                        
                        <div class="mt-4 pt-3 border-top">
                            <h6 class="text-muted mb-3"><i class="fas fa-heartbeat me-2"></i>System Health</h6>
                            <div class="small">
                                <div class="d-flex justify-content-between mb-2">
                                    <span><i class="fas fa-check-circle text-success me-1"></i>Alert System</span>
                                    <span class="text-success">Online</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span><i class="fas fa-check-circle text-success me-1"></i>Database</span>
                                    <span class="text-success">Connected</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span><i class="fas fa-check-circle text-success me-1"></i>Notifications</span>
                                    <span class="text-success">Active</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span><i class="fas fa-check-circle text-success me-1"></i>Auto-Assignment</span>
                                    <span class="text-success">Enabled</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-refresh dashboard every 30 seconds
        setInterval(() => {
            console.log('Dashboard auto-refresh...');
            // In a real application, this would update statistics via AJAX
        }, 30000);

        // Add real-time updates indicator
        setInterval(() => {
            const now = new Date();
            const timeStr = now.toLocaleTimeString();
            document.querySelectorAll('.system-status small').forEach(el => {
                el.textContent = `Last check: ${timeStr}`;
            });
        }, 60000);
    </script>
</body>
</html>
        