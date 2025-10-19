<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Requests</title>
    <!-- Leaflet CSS for Maps -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f5f5; }
        .admin-header { background: #2d3748; color: white; padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; }
        .admin-nav { background: #4a5568; padding: 0.5rem 2rem; }
        .admin-nav a { color: white; text-decoration: none; margin-right: 2rem; padding: 0.5rem 1rem; border-radius: 5px; }
        .admin-nav a:hover, .admin-nav a.active { background: #2d3748; }
        .container { max-width: 1200px; margin: 2rem auto; padding: 0 1rem; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
        .stat-card { background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); text-align: center; }
        .stat-number { font-size: 2rem; font-weight: bold; color: #2d3748; }
        .stat-label { color: #718096; margin-top: 0.5rem; }
        .requests-table { background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .requests-table table { width: 100%; border-collapse: collapse; }
        .requests-table th, .requests-table td { padding: 1rem; text-align: left; border-bottom: 1px solid #e2e8f0; }
        .requests-table th { background: #f7fafc; font-weight: 600; }
        .status-pending { color: #ed8936; font-weight: bold; }
        .status-assigned { color: #4299e1; font-weight: bold; }
        .status-completed { color: #48bb78; font-weight: bold; }
        .priority-critical { color: #e53e3e; font-weight: bold; }
        .priority-high { color: #ed8936; font-weight: bold; }
        .priority-medium { color: #4299e1; }
        .btn { padding: 0.5rem 1rem; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; margin-right: 0.5rem; }
        .btn-primary { background: #4299e1; color: white; }
        .btn-success { background: #48bb78; color: white; }
        .btn-warning { background: #ed8936; color: white; }
        .btn-assign { background: #9f7aea; color: white; }
        .btn-status { background: #38b2ac; color: white; }
        .bulk-actions { background: white; padding: 1rem; border-radius: 8px; margin-bottom: 1rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .bulk-form { display: flex; gap: 1rem; align-items: center; }
        .bulk-form select { padding: 0.5rem; border: 1px solid #e2e8f0; border-radius: 5px; }
        .request-checkbox { margin-right: 0.5rem; }
        #select-all { margin-right: 0.5rem; }
        .emergency-type { background: #e53e3e; color: white; padding: 2px 8px; border-radius: 12px; font-size: 0.8em; }
        .status { padding: 2px 8px; border-radius: 12px; font-size: 0.8em; font-weight: bold; }
        .status.pending { background: #fed7d7; color: #822727; }
        .status.assigned { background: #bee3f8; color: #2a4365; }
        .status.completed { background: #c6f6d5; color: #1a202c; }
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); }
        .modal-content { background-color: white; margin: 15% auto; padding: 20px; border-radius: 8px; width: 80%; max-width: 500px; }
        .close { color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer; }
        .close:hover { color: black; }
        
        /* Map Integration Styles */
        .map-section { background: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 2rem; overflow: hidden; }
        .map-header { background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 1.5rem; text-align: center; }
        .map-header h3 { margin: 0; font-size: 1.5rem; }
        .map-controls { background: #f8f9fa; padding: 1rem; border-bottom: 1px solid #e9ecef; display: flex; gap: 1rem; align-items: center; flex-wrap: wrap; }
        .map-filter { display: flex; align-items: center; gap: 0.5rem; }
        .map-filter label { font-weight: 600; color: #495057; }
        .map-filter select { padding: 0.5rem; border: 1px solid #ced4da; border-radius: 4px; }
        #requests-map { height: 400px; width: 100%; }
        .view-toggle { display: flex; gap: 1rem; margin-bottom: 2rem; justify-content: center; }
        .toggle-btn { padding: 0.75rem 1.5rem; border: 2px solid #667eea; background: white; color: #667eea; border-radius: 25px; text-decoration: none; font-weight: 600; transition: all 0.3s ease; }
        .toggle-btn.active { background: #667eea; color: white; }
        .toggle-btn:hover { background: #5a67d8; color: white; border-color: #5a67d8; }
        .priority-legend { background: white; padding: 1rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 2rem; }
        .legend-item { display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem; }
        .legend-marker { width: 20px; height: 20px; border-radius: 50%; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3); }
        .legend-marker.critical { background: #e53e3e; }
        .legend-marker.high { background: #ed8936; }
        .legend-marker.medium { background: #4299e1; }
        .legend-marker.low { background: #48bb78; }
        .legend-marker.pending { background: #ecc94b; }
        .legend-marker.assigned { background: #9f7aea; }
        .legend-marker.completed { background: #38b2ac; }
    </style>
</head>
<body>
    <div class="admin-header">
        <h1>📋 Admin Panel - Manage Requests</h1>
        <div style="display: flex; align-items: center; gap: 1.5rem;">
            <a href="{{ route('admin.inbox') }}" style="position: relative; text-decoration: none; color: white; font-size: 1.5rem;">
                🔔
                @php
                    $unseenCount = \App\Models\InAppNotification::forAdmin()->unseen()->count();
                @endphp
                @if($unseenCount > 0)
                <span style="position: absolute; top: -8px; right: -8px; background: #f56565; color: white; border-radius: 50%; width: 20px; height: 20px; font-size: 0.7rem; display: flex; align-items: center; justify-content: center; font-weight: bold;">
                    {{ $unseenCount }}
                </span>
                @endif
            </a>
            <span>Welcome, {{ Auth::user()->name }}</span>
            <a href="{{ route('auth.logout') }}" style="color: #fed7d7;">Logout</a>
        </div>
    </div>

    <div class="admin-nav">
        <a href="{{ route('admin.dashboard') }}">Dashboard</a>
        <a href="{{ route('admin.alerts') }}">Manage Alerts</a>
        <a href="{{ route('admin.shelters') }}">Manage Shelters</a>
        <a href="{{ route('admin.requests') }}" class="active">Manage Requests</a>
        <a href="{{ route('admin.analytics') }}">Analytics</a>
        <a href="{{ route('admin.inbox') }}">📬 Notifications</a>
    </div>

    <div class="container">
        @if(session('success'))
            <div style="background: #c6f6d5; color: #1a202c; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div style="background: #fed7d7; color: #822727; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                {{ session('error') }}
            </div>
        @endif

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number">{{ $stats['total_requests'] }}</div>
                <div class="stat-label">Total Requests</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $stats['pending_requests'] }}</div>
                <div class="stat-label">Pending</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $stats['assigned_requests'] }}</div>
                <div class="stat-label">Assigned</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $stats['completed_requests'] }}</div>
                <div class="stat-label">Completed</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $stats['critical_requests'] }}</div>
                <div class="stat-label">Critical</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $stats['high_requests'] }}</div>
                <div class="stat-label">High Priority</div>
            </div>
        </div>

        <!-- View Toggle -->
        <div class="view-toggle">
            <a href="#" class="toggle-btn active" id="map-view-btn">🗺️ Geographic View</a>
            <a href="#" class="toggle-btn" id="table-view-btn">📋 Table View</a>
        </div>

        <!-- Geographic Request Map -->
        <div class="map-section" id="map-section">
            <div class="map-header">
                <h3>🗺️ Emergency Requests Geographic View</h3>
                <p>Visualize request locations and assign to nearest shelters</p>
            </div>
            
            <div class="map-controls">
                <div class="map-filter">
                    <label>Filter by Status:</label>
                    <select id="status-filter">
                        <option value="all">All Requests</option>
                        <option value="pending">Pending</option>
                        <option value="assigned">Assigned</option>
                        <option value="in-progress">In Progress</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>
                
                <div class="map-filter">
                    <label>Filter by Priority:</label>
                    <select id="priority-filter">
                        <option value="all">All Priorities</option>
                        <option value="critical">Critical</option>
                        <option value="high">High</option>
                        <option value="medium">Medium</option>
                        <option value="low">Low</option>
                    </select>
                </div>
                
                <div class="map-filter">
                    <button id="show-shelters" class="btn btn-primary">🏠 Show Shelters</button>
                    <button id="hide-shelters" class="btn btn-secondary" style="display: none;">🏠 Hide Shelters</button>
                </div>
            </div>
            
            <div id="requests-map"></div>
        </div>

        <!-- Map Legend -->
        <div class="priority-legend" id="map-legend">
            <h4>🏷️ Map Legend</h4>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <h5>By Priority:</h5>
                    <div class="legend-item">
                        <div class="legend-marker critical"></div>
                        <span>Critical Priority</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-marker high"></div>
                        <span>High Priority</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-marker medium"></div>
                        <span>Medium Priority</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-marker low"></div>
                        <span>Low Priority</span>
                    </div>
                </div>
                <div>
                    <h5>By Status:</h5>
                    <div class="legend-item">
                        <div class="legend-marker pending"></div>
                        <span>Pending Requests</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-marker assigned"></div>
                        <span>Assigned Requests</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-marker completed"></div>
                        <span>Completed Requests</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bulk Actions -->
        <div class="bulk-actions">
            <h3>📦 Bulk Operations</h3>
            <form class="bulk-form" method="POST" action="{{ route('admin.requests.bulk-assign') }}">
                @csrf
                <label>
                    <input type="checkbox" id="select-all"> Select All
                </label>
                <span id="selected-count">0 selected</span>
                <select name="shelter_id" required>
                    <option value="">Select Shelter for Bulk Assignment</option>
                    @foreach($availableShelters ?? [] as $shelter)
                        <option value="{{ $shelter->id }}">
                            {{ $shelter->name }} ({{ $shelter->current_occupancy }}/{{ $shelter->capacity }})
                        </option>
                    @endforeach
                </select>
                <input type="text" name="admin_notes" placeholder="Assignment notes (optional)">
                <button type="submit" class="btn btn-success" id="bulk-assign-btn" disabled>
                    🏠 Bulk Assign
                </button>
            </form>
        </div>

        <div class="requests-table" id="requests-table">
            <table>
                <thead>
                    <tr>
                        <th>
                            <input type="checkbox" id="select-all-header">
                        </th>
                        <th>ID</th>
                        <th>Citizen</th>
                        <th>Contact</th>
                        <th>Type</th>
                        <th>People</th>
                        <th>Status</th>
                        <th>Assigned Shelter</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($helpRequests as $request)
                    <tr data-request-id="{{ $request->id }}">
                        <td>
                            <input type="checkbox" name="request_ids[]" value="{{ $request->id }}" class="request-checkbox">
                        </td>
                        <td>{{ $request->id }}</td>
                        <td>{{ $request->name }}</td>
                        <td>{{ $request->phone }}</td>
                        <td>
                            <span class="emergency-type">{{ $request->request_type }}</span>
                        </td>
                        <td>{{ $request->people_count ?? 1 }}</td>
                        <td>
                            <span class="status {{ strtolower($request->status) }}">{{ $request->status }}</span>
                        </td>
                        <td>{{ $request->assignment ? $request->assignment->shelter->name : 'Not Assigned' }}</td>
                        <td>{{ $request->created_at->format('M d, H:i') }}</td>
                        <td class="actions">
                            @if($request->status === 'Pending')
                                <a href="{{ route('admin.requests.assign', $request->id) }}" class="btn btn-assign">
                                    📋 Assign
                                </a>
                            @endif
                            
                            <button onclick="showStatusModal({{ $request->id }}, '{{ $request->status }}')" class="btn btn-status">
                                🔄 Status
                            </button>
                            
                            <a href="{{ route('requests.show', $request->id) }}" class="btn btn-primary">View</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="margin-top: 2rem;">
            {{ $helpRequests->links() }}
        </div>
    </div>

    <!-- Status Update Modal -->
    <div id="statusModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeStatusModal()">&times;</span>
            <h3>Update Request Status</h3>
            <form id="statusForm" method="POST">
                @csrf
                @method('PUT')
                <div style="margin: 1rem 0;">
                    <label>Status:</label>
                    <select name="status" id="statusSelect" style="width: 100%; padding: 0.5rem; margin-top: 0.5rem;">
                        <option value="Pending">Pending</option>
                        <option value="Assigned">Assigned</option>
                        <option value="In Progress">In Progress</option>
                        <option value="Completed">Completed</option>
                        <option value="Cancelled">Cancelled</option>
                    </select>
                </div>
                <div style="margin: 1rem 0;">
                    <label>Admin Notes:</label>
                    <textarea name="admin_notes" rows="3" style="width: 100%; padding: 0.5rem; margin-top: 0.5rem;" placeholder="Add status update notes..."></textarea>
                </div>
                <div style="text-align: right; margin-top: 1rem;">
                    <button type="button" onclick="closeStatusModal()" class="btn" style="background: #gray; margin-right: 1rem;">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Leaflet JavaScript -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Request and Shelter data for mapping
        const requests = @json($helpRequests->items());
        const availableShelters = @json($availableShelters ?? []);
        
        // Initialize map
        let map;
        let requestMarkers = [];
        let shelterMarkers = [];
        let sheltersVisible = false;
        
        function initRequestMap() {
            // Center map on first request or default location (Dhaka, Bangladesh)
            const centerLat = requests.length > 0 ? (requests[0].latitude || 23.8103) : 23.8103;
            const centerLng = requests.length > 0 ? (requests[0].longitude || 90.4125) : 90.4125;
            
            map = L.map('requests-map').setView([centerLat, centerLng], 11);
            
            // Add OpenStreetMap tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);
            
            // Add request markers
            addRequestMarkers();
        }
        
        function addRequestMarkers() {
            requestMarkers = [];
            
            requests.forEach(request => {
                // Generate coordinates if not available (simulate real locations around city)
                const lat = request.latitude || (23.8103 + (Math.random() - 0.5) * 0.2);
                const lng = request.longitude || (90.4125 + (Math.random() - 0.5) * 0.2);
                
                // Determine marker color based on priority and status
                let markerColor = '#4299e1'; // Default medium priority
                let borderColor = '#ecc94b'; // Default pending status
                
                // Priority colors
                switch(request.urgency?.toLowerCase()) {
                    case 'critical':
                        markerColor = '#e53e3e';
                        break;
                    case 'high':
                        markerColor = '#ed8936';
                        break;
                    case 'medium':
                        markerColor = '#4299e1';
                        break;
                    case 'low':
                        markerColor = '#48bb78';
                        break;
                }
                
                // Status border colors
                switch(request.status?.toLowerCase()) {
                    case 'pending':
                        borderColor = '#ecc94b';
                        break;
                    case 'assigned':
                        borderColor = '#9f7aea';
                        break;
                    case 'in progress':
                        borderColor = '#4299e1';
                        break;
                    case 'completed':
                        borderColor = '#38b2ac';
                        break;
                }
                
                // Create custom marker icon
                const markerIcon = L.divIcon({
                    className: 'custom-request-marker',
                    html: `<div style="
                        background-color: ${markerColor};
                        width: 25px;
                        height: 25px;
                        border-radius: 50%;
                        border: 4px solid ${borderColor};
                        box-shadow: 0 2px 6px rgba(0,0,0,0.3);
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        color: white;
                        font-weight: bold;
                        font-size: 10px;
                    ">${request.people_count || '1'}</div>`,
                    iconSize: [33, 33],
                    iconAnchor: [16, 16]
                });
                
                // Create marker
                const marker = L.marker([lat, lng], { icon: markerIcon })
                    .bindPopup(createRequestPopupContent(request))
                    .addTo(map);
                
                marker.requestData = request;
                marker.priorityClass = request.urgency?.toLowerCase() || 'medium';
                marker.statusClass = request.status?.toLowerCase().replace(' ', '-') || 'pending';
                requestMarkers.push(marker);
                
                // Add click event to highlight corresponding row
                marker.on('click', function() {
                    highlightRequestRow(request.id);
                });
            });
        }
        
        function addShelterMarkers() {
            shelterMarkers = [];
            
            availableShelters.forEach(shelter => {
                // Generate coordinates if not available
                const lat = shelter.latitude || (23.8103 + (Math.random() - 0.5) * 0.15);
                const lng = shelter.longitude || (90.4125 + (Math.random() - 0.5) * 0.15);
                
                // Determine marker color based on capacity
                const occupancyPercentage = (shelter.current_occupancy / shelter.capacity) * 100;
                let markerColor = '#27ae60'; // Available (green)
                
                if (shelter.status !== 'Active') {
                    markerColor = '#95a5a6'; // Inactive (gray)
                } else if (occupancyPercentage >= 90) {
                    markerColor = '#e74c3c'; // Full (red)
                } else if (occupancyPercentage >= 70) {
                    markerColor = '#f39c12'; // Nearly full (orange)
                }
                
                // Create shelter marker icon (house shape)
                const shelterIcon = L.divIcon({
                    className: 'custom-shelter-marker',
                    html: `<div style="
                        background-color: ${markerColor};
                        width: 30px;
                        height: 30px;
                        border-radius: 4px;
                        border: 3px solid white;
                        box-shadow: 0 3px 8px rgba(0,0,0,0.4);
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        color: white;
                        font-weight: bold;
                        font-size: 16px;
                        transform: rotate(45deg);
                    ">🏠</div>`,
                    iconSize: [36, 36],
                    iconAnchor: [18, 18]
                });
                
                // Create marker
                const marker = L.marker([lat, lng], { icon: shelterIcon })
                    .bindPopup(createShelterPopupContent(shelter));
                
                marker.shelterData = shelter;
                shelterMarkers.push(marker);
            });
        }
        
        function createRequestPopupContent(request) {
            const priorityBadge = getPriorityBadge(request.urgency);
            const statusBadge = getStatusBadge(request.status);
            const assignedShelter = request.assignment ? request.assignment.shelter.name : 'Not Assigned';
            
            return `
                <div style="min-width: 280px;">
                    <h4 style="margin: 0 0 10px 0; color: #2c3e50;">📋 Request #${request.id}</h4>
                    <p style="margin: 5px 0; color: #7f8c8d;"><strong>👤 Citizen:</strong> ${request.name}</p>
                    <p style="margin: 5px 0; color: #7f8c8d;"><strong>📞 Contact:</strong> ${request.phone}</p>
                    <p style="margin: 5px 0; color: #7f8c8d;"><strong>📍 Location:</strong> ${request.location}</p>
                    <p style="margin: 5px 0;"><strong>🚨 Type:</strong> ${request.request_type}</p>
                    <p style="margin: 5px 0;"><strong>👥 People:</strong> ${request.people_count || 1} person(s)</p>
                    <p style="margin: 5px 0;">${priorityBadge} ${statusBadge}</p>
                    <p style="margin: 5px 0; color: #7f8c8d;"><strong>🏠 Assigned Shelter:</strong> ${assignedShelter}</p>
                    <p style="margin: 5px 0; color: #7f8c8d; font-size: 12px;"><strong>📅 Created:</strong> ${new Date(request.created_at).toLocaleString()}</p>
                    <div style="margin-top: 12px; display: flex; gap: 8px;">
                        ${request.status === 'Pending' ? 
                            `<a href="/admin/requests/${request.id}/assign" style="background: #9f7aea; color: white; padding: 6px 12px; text-decoration: none; border-radius: 4px; font-size: 12px;">📋 Assign</a>` : 
                            ''
                        }
                        <button onclick="showStatusModal(${request.id}, '${request.status}')" style="background: #38b2ac; color: white; padding: 6px 12px; border: none; border-radius: 4px; font-size: 12px; cursor: pointer;">🔄 Status</button>
                    </div>
                </div>
            `;
        }
        
        function createShelterPopupContent(shelter) {
            const occupancyPercentage = Math.round((shelter.current_occupancy / shelter.capacity) * 100);
            const statusBadge = shelter.status === 'Active' ? 
                (occupancyPercentage >= 90 ? '🔴 Full' : 
                 occupancyPercentage >= 70 ? '🟡 Nearly Full' : '🟢 Available') :
                '⚪ Inactive';
            
            return `
                <div style="min-width: 260px;">
                    <h4 style="margin: 0 0 10px 0; color: #2c3e50;">🏠 ${shelter.name}</h4>
                    <p style="margin: 5px 0; color: #7f8c8d;">📍 ${shelter.location}</p>
                    <p style="margin: 5px 0; color: #7f8c8d;">📞 ${shelter.contact}</p>
                    <p style="margin: 5px 0; font-weight: bold;">${statusBadge}</p>
                    <p style="margin: 5px 0;">
                        <strong>Capacity:</strong> ${shelter.current_occupancy}/${shelter.capacity} 
                        (${shelter.capacity - shelter.current_occupancy} available)
                    </p>
                    <p style="margin: 5px 0;">
                        <strong>Facilities:</strong> ${Array.isArray(shelter.facilities) ? shelter.facilities.join(', ') : 'N/A'}
                    </p>
                    <div style="margin-top: 10px;">
                        <a href="/admin/shelters/${shelter.id}/edit" style="background: #3498db; color: white; padding: 5px 10px; text-decoration: none; border-radius: 4px; font-size: 12px;">✏️ Edit</a>
                    </div>
                </div>
            `;
        }
        
        function getPriorityBadge(priority) {
            const colors = {
                'Critical': '#e53e3e',
                'High': '#ed8936',
                'Medium': '#4299e1',
                'Low': '#48bb78'
            };
            const color = colors[priority] || colors['Medium'];
            return `<span style="background: ${color}; color: white; padding: 2px 8px; border-radius: 12px; font-size: 12px; font-weight: bold;">🔥 ${priority || 'Medium'}</span>`;
        }
        
        function getStatusBadge(status) {
            const colors = {
                'Pending': '#ecc94b',
                'Assigned': '#9f7aea',
                'In Progress': '#4299e1',
                'Completed': '#38b2ac',
                'Cancelled': '#e53e3e'
            };
            const color = colors[status] || colors['Pending'];
            return `<span style="background: ${color}; color: white; padding: 2px 8px; border-radius: 12px; font-size: 12px; font-weight: bold;">📊 ${status || 'Pending'}</span>`;
        }
        
        function highlightRequestRow(requestId) {
            // Remove existing highlights
            document.querySelectorAll('tr[data-request-id]').forEach(row => {
                row.style.backgroundColor = '';
            });
            
            // Highlight selected row
            const selectedRow = document.querySelector(`tr[data-request-id="${requestId}"]`);
            if (selectedRow) {
                selectedRow.style.backgroundColor = '#e3f2fd';
                selectedRow.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        }
        
        // Filter functionality
        function filterRequests() {
            const statusFilter = document.getElementById('status-filter').value;
            const priorityFilter = document.getElementById('priority-filter').value;
            
            requestMarkers.forEach(marker => {
                const request = marker.requestData;
                let show = true;
                
                // Status filter
                if (statusFilter !== 'all') {
                    show = show && marker.statusClass === statusFilter;
                }
                
                // Priority filter
                if (priorityFilter !== 'all') {
                    show = show && marker.priorityClass === priorityFilter;
                }
                
                if (show) {
                    marker.addTo(map);
                } else {
                    map.removeLayer(marker);
                }
            });
        }
        
        // Toggle shelter visibility
        function toggleShelters() {
            if (sheltersVisible) {
                // Hide shelters
                shelterMarkers.forEach(marker => map.removeLayer(marker));
                document.getElementById('show-shelters').style.display = 'inline-block';
                document.getElementById('hide-shelters').style.display = 'none';
                sheltersVisible = false;
            } else {
                // Show shelters
                if (shelterMarkers.length === 0) {
                    addShelterMarkers();
                }
                shelterMarkers.forEach(marker => marker.addTo(map));
                document.getElementById('show-shelters').style.display = 'none';
                document.getElementById('hide-shelters').style.display = 'inline-block';
                sheltersVisible = true;
            }
        }
        
        // View toggle functionality
        function toggleView(view) {
            const mapSection = document.getElementById('map-section');
            const mapLegend = document.getElementById('map-legend');
            const requestsTable = document.getElementById('requests-table');
            const mapBtn = document.getElementById('map-view-btn');
            const tableBtn = document.getElementById('table-view-btn');
            
            if (view === 'map') {
                mapSection.style.display = 'block';
                mapLegend.style.display = 'block';
                requestsTable.style.display = 'none';
                mapBtn.classList.add('active');
                tableBtn.classList.remove('active');
                
                // Initialize map if not already done
                if (!map) {
                    setTimeout(initRequestMap, 100);
                } else {
                    map.invalidateSize();
                }
            } else {
                mapSection.style.display = 'none';
                mapLegend.style.display = 'none';
                requestsTable.style.display = 'block';
                mapBtn.classList.remove('active');
                tableBtn.classList.add('active');
            }
        }
        
        // Enhanced bulk actions and status modal functions from previous code...
        
        // Bulk actions
        document.getElementById('select-all').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.request-checkbox');
            checkboxes.forEach(cb => cb.checked = this.checked);
            updateSelectedCount();
        });

        document.getElementById('select-all-header').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.request-checkbox');
            checkboxes.forEach(cb => cb.checked = this.checked);
            updateSelectedCount();
        });

        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('request-checkbox')) {
                updateSelectedCount();
            }
        });

        function updateSelectedCount() {
            const selected = document.querySelectorAll('.request-checkbox:checked');
            const count = selected.length;
            document.getElementById('selected-count').textContent = count + ' selected';
            document.getElementById('bulk-assign-btn').disabled = count === 0;
            
            // Update bulk form with selected IDs
            const form = document.querySelector('.bulk-form');
            const existingInputs = form.querySelectorAll('input[name="request_ids[]"]');
            existingInputs.forEach(input => input.remove());
            
            selected.forEach(checkbox => {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'request_ids[]';
                hiddenInput.value = checkbox.value;
                form.appendChild(hiddenInput);
            });
        }

        // Status modal functions
        function showStatusModal(requestId, currentStatus) {
            document.getElementById('statusModal').style.display = 'block';
            document.getElementById('statusForm').action = `/admin/requests/${requestId}/status`;
            document.getElementById('statusSelect').value = currentStatus;
        }

        function closeStatusModal() {
            document.getElementById('statusModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('statusModal');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        }
        
        // Enhanced event listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize map by default
            setTimeout(initRequestMap, 100);
            
            // Map view toggle
            document.getElementById('map-view-btn').addEventListener('click', function(e) {
                e.preventDefault();
                toggleView('map');
            });
            
            document.getElementById('table-view-btn').addEventListener('click', function(e) {
                e.preventDefault();
                toggleView('table');
            });
            
            // Map filters
            document.getElementById('status-filter').addEventListener('change', filterRequests);
            document.getElementById('priority-filter').addEventListener('change', filterRequests);
            
            // Shelter toggle
            document.getElementById('show-shelters').addEventListener('click', toggleShelters);
            document.getElementById('hide-shelters').addEventListener('click', toggleShelters);
        });
    </script>
</body>
</html>