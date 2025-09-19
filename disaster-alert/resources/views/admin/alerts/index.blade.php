<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Emergency Alerts - Disaster Alert Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .admin-header {
            background: linear-gradient(135deg, #2c5aa0, #1e3f72);
            color: white;
            padding: 2rem 0;
        }
        .status-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }
        .severity-critical { border-left: 4px solid #dc3545; }
        .severity-high { border-left: 4px solid #fd7e14; }
        .severity-medium { border-left: 4px solid #ffc107; }
        .severity-low { border-left: 4px solid #20c997; }
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
        .action-btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            margin: 0 0.1rem;
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
                        <a class="nav-link" href="{{ route('admin.dashboard') }}">
                            <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('admin.alerts') }}">
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
                    <h1 class="mb-2"><i class="fas fa-exclamation-triangle me-3"></i>Emergency Alerts Management</h1>
                    <p class="mb-0">Create, monitor and manage disaster alerts and notifications</p>
                </div>
                <div class="col-md-4 text-end">
                    <a href="{{ route('admin.alerts.create') }}" class="btn btn-light btn-lg me-2">
                        <i class="fas fa-plus me-2"></i>Create New Alert
                    </a>
                    <button class="btn btn-outline-light btn-lg" onclick="location.reload()">
                        <i class="fas fa-sync-alt me-2"></i>Refresh
                    </button>
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
                        <h4>{{ count($alerts) }}</h4>
                        <p class="mb-0">Total Alerts</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-success text-white card-hover">
                    <div class="card-body text-center">
                        <i class="fas fa-broadcast-tower fa-2x mb-2"></i>
                        <h4>{{ count(array_filter($alerts, fn($a) => $a['status'] === 'Active')) }}</h4>
                        <p class="mb-0">Active Alerts</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-danger text-white card-hover">
                    <div class="card-body text-center">
                        <i class="fas fa-skull-crossbones fa-2x mb-2"></i>
                        <h4>{{ count(array_filter($alerts, fn($a) => $a['severity'] === 'Critical')) }}</h4>
                        <p class="mb-0">Critical</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-warning text-dark card-hover">
                    <div class="card-body text-center">
                        <i class="fas fa-clock fa-2x mb-2"></i>
                        <h4>{{ count(array_filter($alerts, fn($a) => $a['status'] === 'Draft')) }}</h4>
                        <p class="mb-0">Drafts</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <select class="form-select" id="statusFilter">
                            <option value="">All Status</option>
                            <option value="Active">Active</option>
                            <option value="Draft">Draft</option>
                            <option value="Expired">Expired</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="severityFilter">
                            <option value="">All Severity</option>
                            <option value="Critical">Critical</option>
                            <option value="High">High</option>
                            <option value="Medium">Medium</option>
                            <option value="Low">Low</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="typeFilter">
                            <option value="">All Types</option>
                            <option value="Flood">Flood</option>
                            <option value="Earthquake">Earthquake</option>
                            <option value="Cyclone">Cyclone</option>
                            <option value="Fire">Fire</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-primary w-100" onclick="applyFilters()">
                            <i class="fas fa-filter me-2"></i>Apply Filters
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alerts List -->
        <div class="row" id="alertsList">
            @foreach($alerts as $alert)
            <div class="col-lg-6 mb-4 alert-item severity-{{ strtolower($alert['severity']) }}" 
                 data-status="{{ $alert['status'] }}" 
                 data-severity="{{ $alert['severity'] }}" 
                 data-type="{{ $alert['type'] }}">
                <div class="card h-100 card-hover">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Alert #{{ $alert['id'] }}</h6>
                            <small class="text-muted">{{ date('M j, Y g:i A', strtotime($alert['created_at'])) }}</small>
                        </div>
                        <div>
                            <span class="badge status-badge 
                                @if($alert['status'] === 'Active') bg-success
                                @elseif($alert['status'] === 'Draft') bg-warning text-dark
                                @else bg-secondary
                                @endif">
                                {{ $alert['status'] }}
                            </span>
                            <span class="badge 
                                @if($alert['severity'] === 'Critical') bg-danger
                                @elseif($alert['severity'] === 'High') bg-warning
                                @elseif($alert['severity'] === 'Medium') bg-info
                                @else bg-success
                                @endif text-white ms-1">
                                {{ $alert['severity'] }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">{{ $alert['title'] }}</h5>
                        
                        <div class="row mb-3">
                            <div class="col-sm-6">
                                <strong><i class="fas fa-map-marker-alt me-2 text-danger"></i>Location:</strong>
                                <br><small>{{ $alert['location'] }}</small>
                            </div>
                            <div class="col-sm-6">
                                <strong><i class="fas fa-tag me-2 text-primary"></i>Type:</strong>
                                <br><span class="badge bg-info text-white">{{ $alert['type'] }}</span>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <strong><i class="fas fa-comment me-2 text-info"></i>Description:</strong>
                            <p class="mt-1 text-muted">{{ $alert['description'] }}</p>
                        </div>

                        @if($alert['expires_at'])
                        <div class="mb-3">
                            <strong><i class="fas fa-clock me-2 text-warning"></i>Expires:</strong>
                            <br><small class="text-muted">{{ date('M j, Y g:i A', strtotime($alert['expires_at'])) }}</small>
                        </div>
                        @endif

                        @if($alert['instructions'])
                        <div class="alert alert-info" role="alert">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Instructions:</strong> {{ $alert['instructions'] }}
                        </div>
                        @endif
                    </div>
                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <a href="{{ route('alerts.show', $alert['id']) }}" class="btn btn-primary action-btn">
                                    <i class="fas fa-eye me-1"></i>View
                                </a>
                                <a href="{{ route('admin.alerts.edit', $alert['id']) }}" class="btn btn-warning action-btn">
                                    <i class="fas fa-edit me-1"></i>Edit
                                </a>
                                @if($alert['status'] === 'Draft')
                                    <button class="btn btn-success action-btn" onclick="publishAlert({{ $alert['id'] }})">
                                        <i class="fas fa-broadcast-tower me-1"></i>Publish
                                    </button>
                                @elseif($alert['status'] === 'Active')
                                    <button class="btn btn-secondary action-btn" onclick="deactivateAlert({{ $alert['id'] }})">
                                        <i class="fas fa-stop me-1"></i>Deactivate
                                    </button>
                                @endif
                                <button class="btn btn-danger action-btn" onclick="deleteAlert({{ $alert['id'] }})">
                                    <i class="fas fa-trash me-1"></i>Delete
                                </button>
                            </div>
                            @if($alert['severity'] === 'Critical')
                            <div class="text-end">
                                <i class="fas fa-skull-crossbones text-danger me-1"></i>
                                <small class="text-danger fw-bold">CRITICAL</small>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        @if(empty($alerts))
        <div class="text-center py-5">
            <i class="fas fa-exclamation-triangle fa-3x text-muted mb-3"></i>
            <h4 class="text-muted">No Emergency Alerts</h4>
            <p class="text-muted">No alerts have been created yet.</p>
            <a href="{{ route('admin.alerts.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Create First Alert
            </a>
        </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function publishAlert(alertId) {
            if (confirm('Publish this alert? It will be visible to all citizens.')) {
                alert(`Alert #${alertId} has been published`);
                location.reload();
            }
        }

        function deactivateAlert(alertId) {
            if (confirm('Deactivate this alert? It will no longer be visible to citizens.')) {
                alert(`Alert #${alertId} has been deactivated`);
                location.reload();
            }
        }

        function deleteAlert(alertId) {
            if (confirm('Are you sure you want to delete this alert? This action cannot be undone.')) {
                alert(`Alert #${alertId} has been deleted`);
                location.reload();
            }
        }

        function applyFilters() {
            const statusFilter = document.getElementById('statusFilter').value;
            const severityFilter = document.getElementById('severityFilter').value;
            const typeFilter = document.getElementById('typeFilter').value;
            
            const items = document.querySelectorAll('.alert-item');
            
            items.forEach(item => {
                let show = true;
                
                if (statusFilter && item.dataset.status !== statusFilter) show = false;
                if (severityFilter && item.dataset.severity !== severityFilter) show = false;
                if (typeFilter && item.dataset.type !== typeFilter) show = false;
                
                item.style.display = show ? 'block' : 'none';
            });
        }

        // Auto-refresh every 60 seconds for active alerts
        setInterval(() => {
            console.log('Checking for alert updates...');
            // In a real application, this would update via AJAX
        }, 60000);
    </script>
</body>
</html>
