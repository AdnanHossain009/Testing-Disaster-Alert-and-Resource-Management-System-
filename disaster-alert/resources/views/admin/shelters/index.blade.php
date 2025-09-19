<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Emergency Shelters - Disaster Alert Admin</title>
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
        .capacity-bar {
            height: 8px;
            background-color: #e9ecef;
            border-radius: 4px;
            overflow: hidden;
        }
        .capacity-fill {
            height: 100%;
            transition: width 0.3s ease;
        }
        .capacity-low { background-color: #28a745; }
        .capacity-medium { background-color: #ffc107; }
        .capacity-high { background-color: #dc3545; }
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
                        <a class="nav-link" href="{{ route('admin.alerts') }}">
                            <i class="fas fa-exclamation-triangle me-1"></i>Alerts
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('admin.shelters') }}">
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
                    <h1 class="mb-2"><i class="fas fa-home me-3"></i>Emergency Shelters Management</h1>
                    <p class="mb-0">Monitor and manage emergency shelter capacity and assignments</p>
                </div>
                <div class="col-md-4 text-end">
                    <a href="{{ route('admin.shelters.create') }}" class="btn btn-light btn-lg me-2">
                        <i class="fas fa-plus me-2"></i>Add New Shelter
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
                        <i class="fas fa-home fa-2x mb-2"></i>
                        <h4>{{ count($shelters) }}</h4>
                        <p class="mb-0">Total Shelters</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-success text-white card-hover">
                    <div class="card-body text-center">
                        <i class="fas fa-check-circle fa-2x mb-2"></i>
                        <h4>{{ count(array_filter($shelters, fn($s) => $s['status'] === 'Active')) }}</h4>
                        <p class="mb-0">Active</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-info text-white card-hover">
                    <div class="card-body text-center">
                        <i class="fas fa-users fa-2x mb-2"></i>
                        <h4>{{ array_sum(array_column($shelters, 'capacity')) }}</h4>
                        <p class="mb-0">Total Capacity</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-warning text-dark card-hover">
                    <div class="card-body text-center">
                        <i class="fas fa-bed fa-2x mb-2"></i>
                        <h4>{{ array_sum(array_column($shelters, 'occupied')) }}</h4>
                        <p class="mb-0">Currently Occupied</p>
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
                            <option value="Maintenance">Under Maintenance</option>
                            <option value="Full">Full Capacity</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="locationFilter">
                            <option value="">All Locations</option>
                            <option value="Dhaka">Dhaka</option>
                            <option value="Chittagong">Chittagong</option>
                            <option value="Sylhet">Sylhet</option>
                            <option value="Rajshahi">Rajshahi</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="capacityFilter">
                            <option value="">All Capacities</option>
                            <option value="available">Available Space</option>
                            <option value="low">Low Capacity (&lt;25%)</option>
                            <option value="high">High Capacity (&gt;75%)</option>
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

        <!-- Shelters List -->
        <div class="row" id="sheltersList">
            @foreach($shelters as $shelter)
            <?php
                $occupancyRate = ($shelter['occupied'] / $shelter['capacity']) * 100;
                $capacityClass = $occupancyRate < 50 ? 'capacity-low' : ($occupancyRate < 80 ? 'capacity-medium' : 'capacity-high');
            ?>
            <div class="col-lg-6 mb-4 shelter-item" 
                 data-status="{{ $shelter['status'] }}" 
                 data-location="{{ $shelter['location'] }}" 
                 data-occupancy="{{ $occupancyRate }}">
                <div class="card h-100 card-hover">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1">{{ $shelter['name'] }}</h5>
                            <small class="text-muted"><i class="fas fa-map-marker-alt me-1"></i>{{ $shelter['location'] }}</small>
                        </div>
                        <div>
                            <span class="badge status-badge 
                                @if($shelter['status'] === 'Active') bg-success
                                @elseif($shelter['status'] === 'Maintenance') bg-warning text-dark
                                @else bg-danger
                                @endif">
                                {{ $shelter['status'] }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-sm-6">
                                <strong><i class="fas fa-users me-2 text-primary"></i>Capacity:</strong>
                                <br><span class="h6">{{ $shelter['occupied'] }} / {{ $shelter['capacity'] }}</span>
                            </div>
                            <div class="col-sm-6">
                                <strong><i class="fas fa-percentage me-2 text-info"></i>Occupancy:</strong>
                                <br><span class="h6">{{ number_format($occupancyRate, 1) }}%</span>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <small class="text-muted">Capacity Usage</small>
                                <small class="text-muted">{{ $shelter['capacity'] - $shelter['occupied'] }} available</small>
                            </div>
                            <div class="capacity-bar">
                                <div class="capacity-fill {{ $capacityClass }}" style="width: {{ $occupancyRate }}%"></div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <strong><i class="fas fa-tools me-2 text-secondary"></i>Facilities:</strong>
                            <div class="mt-1">
                                @if(is_array($shelter['facilities']) && count($shelter['facilities']) > 0)
                                    @foreach($shelter['facilities'] as $facility)
                                        <span class="badge bg-light text-dark me-1 mb-1">{{ trim($facility) }}</span>
                                    @endforeach
                                @else
                                    <span class="text-muted">No facilities listed</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <a href="{{ route('admin.shelters.edit', $shelter['id']) }}" class="btn btn-primary action-btn">
                                    <i class="fas fa-edit me-1"></i>Edit
                                </a>
                                <button class="btn btn-info action-btn" onclick="viewDetails({{ $shelter['id'] }})">
                                    <i class="fas fa-eye me-1"></i>Details
                                </button>
                                @if($shelter['status'] === 'Active')
                                    <button class="btn btn-warning action-btn" onclick="setMaintenance({{ $shelter['id'] }})">
                                        <i class="fas fa-tools me-1"></i>Maintenance
                                    </button>
                                @endif
                            </div>
                            @if($occupancyRate > 90)
                            <div class="text-end">
                                <i class="fas fa-exclamation-triangle text-danger me-1"></i>
                                <small class="text-danger fw-bold">NEAR FULL</small>
                            </div>
                            @elseif($occupancyRate > 75)
                            <div class="text-end">
                                <i class="fas fa-info-circle text-warning me-1"></i>
                                <small class="text-warning fw-bold">HIGH OCCUPANCY</small>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        @if(empty($shelters))
        <div class="text-center py-5">
            <i class="fas fa-home fa-3x text-muted mb-3"></i>
            <h4 class="text-muted">No Emergency Shelters</h4>
            <p class="text-muted">No shelters have been configured yet.</p>
            <a href="{{ route('admin.shelters.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add First Shelter
            </a>
        </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function viewDetails(shelterId) {
            // In a real application, this would open a detailed view modal
            alert(`Viewing details for Shelter #${shelterId}`);
        }

        function setMaintenance(shelterId) {
            if (confirm('Set this shelter to maintenance mode? It will be unavailable for new assignments.')) {
                alert(`Shelter #${shelterId} set to maintenance mode`);
                // In a real application, this would make an AJAX call
                location.reload();
            }
        }

        function applyFilters() {
            const statusFilter = document.getElementById('statusFilter').value;
            const locationFilter = document.getElementById('locationFilter').value;
            const capacityFilter = document.getElementById('capacityFilter').value;
            
            const items = document.querySelectorAll('.shelter-item');
            
            items.forEach(item => {
                let show = true;
                
                if (statusFilter && item.dataset.status !== statusFilter) show = false;
                if (locationFilter && item.dataset.location !== locationFilter) show = false;
                
                if (capacityFilter) {
                    const occupancy = parseFloat(item.dataset.occupancy);
                    if (capacityFilter === 'available' && occupancy >= 100) show = false;
                    if (capacityFilter === 'low' && occupancy >= 25) show = false;
                    if (capacityFilter === 'high' && occupancy <= 75) show = false;
                }
                
                item.style.display = show ? 'block' : 'none';
            });
        }

        // Auto-refresh every 60 seconds
        setInterval(() => {
            console.log('Auto-refreshing shelter data...');
            // In a real application, this would update the data via AJAX
        }, 60000);
    </script>
</body>
</html>