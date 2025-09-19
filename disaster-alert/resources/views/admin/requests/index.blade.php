<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Emergency Requests - Disaster Alert Admin</title>
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
        .priority-high { background-color: #dc3545; }
        .priority-medium { background-color: #fd7e14; }
        .priority-low { background-color: #20c997; }
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
                        <a class="nav-link" href="{{ route('admin.shelters') }}">
                            <i class="fas fa-home me-1"></i>Shelters
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('admin.requests') }}">
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
                    <h1 class="mb-2"><i class="fas fa-hands-helping me-3"></i>Emergency Requests Management</h1>
                    <p class="mb-0">Monitor and manage citizen emergency requests</p>
                </div>
                <div class="col-md-4 text-end">
                    <button class="btn btn-light btn-lg" onclick="location.reload()">
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
                        <i class="fas fa-list fa-2x mb-2"></i>
                        <h4>{{ count($requests) }}</h4>
                        <p class="mb-0">Total Requests</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-warning text-dark card-hover">
                    <div class="card-body text-center">
                        <i class="fas fa-clock fa-2x mb-2"></i>
                        <h4>{{ count(array_filter($requests, fn($r) => $r['status'] === 'Pending')) }}</h4>
                        <p class="mb-0">Pending</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-success text-white card-hover">
                    <div class="card-body text-center">
                        <i class="fas fa-check-circle fa-2x mb-2"></i>
                        <h4>{{ count(array_filter($requests, fn($r) => $r['status'] === 'Assigned')) }}</h4>
                        <p class="mb-0">Assigned</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-danger text-white card-hover">
                    <div class="card-body text-center">
                        <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                        <h4>{{ count(array_filter($requests, fn($r) => $r['priority'] === 'High')) }}</h4>
                        <p class="mb-0">High Priority</p>
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
                            <option value="Pending">Pending</option>
                            <option value="Assigned">Assigned</option>
                            <option value="Resolved">Resolved</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="priorityFilter">
                            <option value="">All Priority</option>
                            <option value="High">High Priority</option>
                            <option value="Medium">Medium Priority</option>
                            <option value="Low">Low Priority</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="emergencyFilter">
                            <option value="">All Types</option>
                            <option value="Flood">Flood</option>
                            <option value="Fire">Fire</option>
                            <option value="Earthquake">Earthquake</option>
                            <option value="Medical">Medical Emergency</option>
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

        <!-- Requests List -->
        <div class="row" id="requestsList">
            @foreach($requests as $request)
            <div class="col-lg-6 mb-4 request-item" 
                 data-status="{{ $request['status'] }}" 
                 data-priority="{{ $request['priority'] }}" 
                 data-type="{{ $request['emergency_type'] }}">
                <div class="card h-100 card-hover">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Request #{{ $request['id'] }}</h6>
                            <small class="text-muted">{{ date('M j, Y g:i A', strtotime($request['created_at'])) }}</small>
                        </div>
                        <div>
                            <span class="badge status-badge 
                                @if($request['status'] === 'Pending') bg-warning text-dark
                                @elseif($request['status'] === 'Assigned') bg-success
                                @else bg-secondary
                                @endif">
                                {{ $request['status'] }}
                            </span>
                            <span class="badge priority-{{ strtolower($request['priority']) }} text-white ms-1">
                                {{ $request['priority'] }} Priority
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-sm-6">
                                <strong><i class="fas fa-user me-2 text-primary"></i>{{ $request['citizen_name'] }}</strong>
                                <br><small class="text-muted">{{ $request['phone'] }}</small>
                            </div>
                            <div class="col-sm-6">
                                <strong><i class="fas fa-map-marker-alt me-2 text-danger"></i>Location:</strong>
                                <br><small>{{ $request['location'] }}</small>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <strong><i class="fas fa-exclamation-circle me-2 text-warning"></i>Emergency Type:</strong>
                            <span class="badge bg-info text-white ms-2">{{ $request['emergency_type'] }}</span>
                        </div>
                        
                        <div class="mb-3">
                            <strong><i class="fas fa-comment me-2 text-info"></i>Description:</strong>
                            <p class="mt-1 text-muted">{{ $request['description'] }}</p>
                        </div>

                        @if($request['status'] === 'Assigned')
                        <div class="alert alert-success" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Assigned to:</strong> {{ $request['assigned_shelter'] }}
                            <br><small>Assigned: {{ date('M j, Y g:i A', strtotime($request['assigned_at'])) }}</small>
                        </div>
                        @endif
                    </div>
                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                @if($request['status'] === 'Pending')
                                    <button class="btn btn-success action-btn" onclick="assignRequest({{ $request['id'] }})">
                                        <i class="fas fa-user-check me-1"></i>Assign
                                    </button>
                                @endif
                                <button class="btn btn-primary action-btn" onclick="viewDetails({{ $request['id'] }})">
                                    <i class="fas fa-eye me-1"></i>Details
                                </button>
                                <button class="btn btn-secondary action-btn" data-phone="{{ $request['phone'] }}" onclick="contactCitizen(this.dataset.phone)">
                                    <i class="fas fa-phone me-1"></i>Contact
                                </button>
                            </div>
                            @if($request['priority'] === 'High')
                            <div class="text-end">
                                <i class="fas fa-exclamation-triangle text-danger me-1"></i>
                                <small class="text-danger fw-bold">URGENT</small>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        @if(empty($requests))
        <div class="text-center py-5">
            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
            <h4 class="text-muted">No Emergency Requests</h4>
            <p class="text-muted">There are currently no emergency requests to display.</p>
        </div>
        @endif
    </div>

    <!-- Assignment Modal -->
    <div class="modal fade" id="assignModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Assign Request to Shelter</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="assignForm">
                        <div class="mb-3">
                            <label class="form-label">Select Shelter:</label>
                            <select class="form-select" id="shelterSelect" required>
                                <option value="">Choose a shelter...</option>
                                @foreach($shelters as $shelter)
                                <option value="{{ $shelter->id }}">
                                    {{ $shelter->name }} 
                                    (Available: {{ $shelter->capacity - $shelter->current_occupancy }}/{{ $shelter->capacity }})
                                    @if($shelter->current_occupancy >= $shelter->capacity * 0.9)
                                        - Nearly Full
                                    @endif
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Assignment Notes:</label>
                            <textarea class="form-control" id="assignmentNotes" rows="3" placeholder="Any special instructions or notes..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" onclick="confirmAssignment()">
                        <i class="fas fa-check me-2"></i>Assign Request
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentRequestId = null;

        function assignRequest(requestId) {
            currentRequestId = requestId;
            const modal = new bootstrap.Modal(document.getElementById('assignModal'));
            modal.show();
        }

        function confirmAssignment() {
            const shelterId = document.getElementById('shelterSelect').value;
            const notes = document.getElementById('assignmentNotes').value;
            
            if (!shelterId) {
                alert('Please select a shelter.');
                return;
            }

            // Create form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/requests/${currentRequestId}/assign`;
            
            // Add CSRF token
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);
            
            // Add shelter ID
            const shelterInput = document.createElement('input');
            shelterInput.type = 'hidden';
            shelterInput.name = 'shelter_id';
            shelterInput.value = shelterId;
            form.appendChild(shelterInput);
            
            // Add admin notes
            const notesInput = document.createElement('input');
            notesInput.type = 'hidden';
            notesInput.name = 'admin_notes';
            notesInput.value = notes;
            form.appendChild(notesInput);
            
            // Submit form
            document.body.appendChild(form);
            form.submit();
        }

        function viewDetails(requestId) {
            // In a real application, this would open a detailed view modal
            alert(`Viewing details for Request #${requestId}`);
        }

        function contactCitizen(phone) {
            // In a real application, this would integrate with communication systems
            alert(`Contacting citizen at ${phone}`);
        }

        function applyFilters() {
            const statusFilter = document.getElementById('statusFilter').value;
            const priorityFilter = document.getElementById('priorityFilter').value;
            const emergencyFilter = document.getElementById('emergencyFilter').value;
            
            const items = document.querySelectorAll('.request-item');
            
            items.forEach(item => {
                let show = true;
                
                if (statusFilter && item.dataset.status !== statusFilter) show = false;
                if (priorityFilter && item.dataset.priority !== priorityFilter) show = false;
                if (emergencyFilter && item.dataset.type !== emergencyFilter) show = false;
                
                item.style.display = show ? 'block' : 'none';
            });
        }

        // Auto-refresh every 30 seconds
        setInterval(() => {
            const lastRefresh = document.createElement('small');
            lastRefresh.className = 'text-muted';
            lastRefresh.textContent = `Last updated: ${new Date().toLocaleTimeString()}`;
            
            // Update refresh indicator if exists
            const existingIndicator = document.querySelector('.refresh-indicator');
            if (existingIndicator) {
                existingIndicator.replaceWith(lastRefresh);
            }
        }, 30000);
    </script>
</body>
</html>