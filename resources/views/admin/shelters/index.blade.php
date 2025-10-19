<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Admin - Manage Shelters</title>
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
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
        .stat-card { background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); text-align: center; }
        .stat-number { font-size: 2rem; font-weight: bold; color: #2d3748; }
        .stat-label { color: #718096; margin-top: 0.5rem; }
        .shelters-table { background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .shelters-table table { width: 100%; border-collapse: collapse; }
        .shelters-table th, .shelters-table td { padding: 1rem; text-align: left; border-bottom: 1px solid #e2e8f0; }
        .shelters-table th { background: #f7fafc; font-weight: 600; }
        .status-active { color: #48bb78; font-weight: bold; }
        .status-inactive { color: #e53e3e; }
        .capacity-high { color: #e53e3e; }
        .capacity-medium { color: #ed8936; }
        .capacity-low { color: #48bb78; }
        .btn { padding: 0.5rem 1rem; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn-primary { background: #4299e1; color: white; }
        .btn-success { background: #48bb78; color: white; }
        .btn-warning { background: #ed8936; color: white; }
        .btn-danger { background: #e53e3e; color: white; }
        
        /* Map Integration Styles */
        .map-section { background: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 2rem; overflow: hidden; }
        .map-header { background: linear-gradient(135deg, #48bb78, #38a169); color: white; padding: 1.5rem; text-align: center; }
        .map-header h3 { margin: 0; font-size: 1.5rem; }
        .map-controls { background: #f8f9fa; padding: 1rem; border-bottom: 1px solid #e9ecef; display: flex; gap: 1rem; align-items: center; flex-wrap: wrap; }
        .map-filter { display: flex; align-items: center; gap: 0.5rem; }
        .map-filter label { font-weight: 600; color: #495057; }
        .map-filter select { padding: 0.5rem; border: 1px solid #ced4da; border-radius: 4px; }
        #shelters-map { height: 400px; width: 100%; }
        .view-toggle { display: flex; gap: 1rem; margin-bottom: 2rem; justify-content: center; }
        .toggle-btn { padding: 0.75rem 1.5rem; border: 2px solid #48bb78; background: white; color: #48bb78; border-radius: 25px; text-decoration: none; font-weight: 600; transition: all 0.3s ease; }
        .toggle-btn.active { background: #48bb78; color: white; }
        .toggle-btn:hover { background: #38a169; color: white; border-color: #38a169; }
        .status-legend { background: white; padding: 1rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 2rem; }
        .legend-item { display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem; }
        .legend-marker { width: 20px; height: 20px; border-radius: 50%; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3); }
        .legend-marker.active { background: #48bb78; }
        .legend-marker.inactive { background: #e53e3e; }
        .legend-marker.high-capacity { background: #e53e3e; }
        .legend-marker.medium-capacity { background: #ed8936; }
        .legend-marker.low-capacity { background: #48bb78; }
    </style>
</head>
<body>
    @section('page_title', '🏠 Admin Panel - Manage Shelters')
    @include('admin.partials.header')

    <div class="container">
        <!-- Action Buttons -->
        <div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center;">
            <h2>Shelter Management</h2>
            <a href="{{ route('admin.shelters.create') }}" class="btn btn-success">
                ➕ Create New Shelter
            </a>
        </div>

        @if(session('success'))
        <div style="background: #48bb78; color: white; padding: 1rem; border-radius: 5px; margin-bottom: 1rem;">
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div style="background: #e53e3e; color: white; padding: 1rem; border-radius: 5px; margin-bottom: 1rem;">
            {{ session('error') }}
        </div>
        @endif

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number">{{ $stats['total_shelters'] }}</div>
                <div class="stat-label">Total Shelters</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $stats['active_shelters'] }}</div>
                <div class="stat-label">Active Shelters</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $stats['available_shelters'] }}</div>
                <div class="stat-label">Available Shelters</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $stats['total_capacity'] }}</div>
                <div class="stat-label">Total Capacity</div>
            </div>
        </div>

        <!-- View Toggle -->
        <div class="view-toggle">
            <a href="#" class="toggle-btn active" id="map-view-btn">🗺️ Geographic View</a>
            <a href="#" class="toggle-btn" id="table-view-btn">📋 Table View</a>
        </div>

        <!-- Geographic Shelters Map -->
        <div class="map-section" id="map-section">
            <div class="map-header">
                <h3>🗺️ Shelter Locations Map</h3>
                <p>View all shelter locations and their current status</p>
            </div>
            
            <div class="map-controls">
                <div class="map-filter">
                    <label>Filter by Status:</label>
                    <select id="status-filter">
                        <option value="all">All Shelters</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                
                <div class="map-filter">
                    <label>Filter by Occupancy:</label>
                    <select id="occupancy-filter">
                        <option value="all">All Occupancy Levels</option>
                        <option value="low">Low (&lt;70%)</option>
                        <option value="medium">Medium (70-89%)</option>
                        <option value="high">High (90%+)</option>
                    </select>
                </div>
            </div>
            
            <div id="shelters-map"></div>
        </div>

        <!-- Map Legend -->
        <div class="status-legend" id="map-legend">
            <h4>🏷️ Map Legend</h4>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <h5>By Status:</h5>
                    <div class="legend-item">
                        <div class="legend-marker active"></div>
                        <span>Active Shelter</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-marker inactive"></div>
                        <span>Inactive Shelter</span>
                    </div>
                </div>
                <div>
                    <h5>By Occupancy:</h5>
                    <div class="legend-item">
                        <div class="legend-marker high-capacity"></div>
                        <span>High Occupancy (90%+)</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-marker medium-capacity"></div>
                        <span>Medium Occupancy (70-89%)</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-marker low-capacity"></div>
                        <span>Low Occupancy (&lt;70%)</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="shelters-table">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Location</th>
                        <th>Capacity</th>
                        <th>Occupancy</th>
                        <th>Status</th>
                        <th>Contact</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($shelters as $shelter)
                    @php
                        $occupancyRate = $shelter->capacity > 0 ? ($shelter->current_occupancy / $shelter->capacity) * 100 : 0;
                        $occupancyClass = $occupancyRate >= 90 ? 'capacity-high' : ($occupancyRate >= 70 ? 'capacity-medium' : 'capacity-low');
                    @endphp
                    <tr>
                        <td>{{ $shelter->id }}</td>
                        <td>{{ $shelter->name }}</td>
                        <td>{{ $shelter->city }}, {{ $shelter->state }}</td>
                        <td>{{ $shelter->capacity }}</td>
                        <td class="{{ $occupancyClass }}">{{ $shelter->current_occupancy }}/{{ $shelter->capacity }} ({{ round($occupancyRate) }}%)</td>
                        <td class="status-{{ strtolower($shelter->status) }}">{{ $shelter->status }}</td>
                        <td>{{ $shelter->contact_phone }}</td>
                        <td>
                            <a href="{{ route('shelters.show', $shelter->id) }}" class="btn btn-primary">View</a>
                            <a href="{{ route('admin.shelters.edit', $shelter->id) }}" class="btn btn-success">Edit</a>
                            <form method="POST" action="{{ route('admin.shelters.destroy', $shelter->id) }}" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to delete this shelter?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="margin-top: 2rem;">
            {{ $shelters->links() }}
        </div>
    </div>

    <!-- Leaflet JS for Maps -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Shelter data from Laravel
        const shelters = @json($shelters->items());
        
        // Initialize the map
        const map = L.map('shelters-map').setView([23.8103, 90.4125], 7); // Center on Bangladesh
        
        // Add OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 18
        }).addTo(map);
        
        // Store all markers for filtering
        let shelterMarkers = [];
        
        // Function to get marker color based on occupancy and status
        function getMarkerColor(shelter) {
            if (shelter.status.toLowerCase() === 'inactive') {
                return '#e53e3e'; // Red for inactive
            }
            
            const occupancyRate = shelter.capacity > 0 ? (shelter.current_occupancy / shelter.capacity) * 100 : 0;
            if (occupancyRate >= 90) return '#e53e3e'; // Red - High occupancy
            if (occupancyRate >= 70) return '#ed8936'; // Orange - Medium occupancy
            return '#48bb78'; // Green - Low occupancy
        }
        
        // Function to create custom marker icon
        function createMarkerIcon(color) {
            return L.divIcon({
                html: `<div style="background-color: ${color}; width: 24px; height: 24px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 6px rgba(0,0,0,0.3);"></div>`,
                className: 'custom-marker',
                iconSize: [24, 24],
                iconAnchor: [12, 12]
            });
        }
        
        // Add shelter markers to map
        shelters.forEach(shelter => {
            if (shelter.latitude && shelter.longitude) {
                const occupancyRate = shelter.capacity > 0 ? (shelter.current_occupancy / shelter.capacity) * 100 : 0;
                const color = getMarkerColor(shelter);
                
                const marker = L.marker([shelter.latitude, shelter.longitude], {
                    icon: createMarkerIcon(color)
                }).addTo(map);
                
                // Create popup content
                const popupContent = `
                    <div style="min-width: 200px;">
                        <h4 style="margin: 0 0 10px 0; color: #2d3748;">🏠 ${shelter.name}</h4>
                        <p style="margin: 5px 0;"><strong>📍 Location:</strong> ${shelter.address}</p>
                        <p style="margin: 5px 0;"><strong>🏙️ City:</strong> ${shelter.city}, ${shelter.state}</p>
                        <p style="margin: 5px 0;"><strong>👥 Capacity:</strong> ${shelter.current_occupancy}/${shelter.capacity} (${Math.round(occupancyRate)}%)</p>
                        <p style="margin: 5px 0;"><strong>📊 Status:</strong> <span style="color: ${shelter.status.toLowerCase() === 'active' ? '#48bb78' : '#e53e3e'};">${shelter.status}</span></p>
                        <p style="margin: 5px 0;"><strong>📞 Contact:</strong> ${shelter.contact_phone}</p>
                        <p style="margin: 5px 0;"><strong>🛏️ Facilities:</strong> ${shelter.facilities || 'N/A'}</p>
                        <div style="margin-top: 10px;">
                            <a href="/shelters/${shelter.id}" style="display: inline-block; background: #4299e1; color: white; padding: 5px 10px; border-radius: 4px; text-decoration: none; margin-right: 5px;">View Details</a>
                            <a href="/admin/shelters/${shelter.id}/edit" style="display: inline-block; background: #48bb78; color: white; padding: 5px 10px; border-radius: 4px; text-decoration: none;">Edit</a>
                        </div>
                    </div>
                `;
                
                marker.bindPopup(popupContent);
                
                // Store marker with shelter data for filtering
                shelterMarkers.push({
                    marker: marker,
                    shelter: shelter,
                    occupancyRate: occupancyRate
                });
            }
        });
        
        // Fit map to show all markers
        if (shelterMarkers.length > 0) {
            const group = L.featureGroup(shelterMarkers.map(sm => sm.marker));
            map.fitBounds(group.getBounds().pad(0.1));
        }
        
        // Filter functionality
        function filterMarkers() {
            const statusFilter = document.getElementById('status-filter').value;
            const occupancyFilter = document.getElementById('occupancy-filter').value;
            
            shelterMarkers.forEach(({ marker, shelter, occupancyRate }) => {
                let show = true;
                
                // Filter by status
                if (statusFilter !== 'all' && shelter.status.toLowerCase() !== statusFilter) {
                    show = false;
                }
                
                // Filter by occupancy
                if (occupancyFilter !== 'all') {
                    if (occupancyFilter === 'low' && occupancyRate >= 70) show = false;
                    if (occupancyFilter === 'medium' && (occupancyRate < 70 || occupancyRate >= 90)) show = false;
                    if (occupancyFilter === 'high' && occupancyRate < 90) show = false;
                }
                
                if (show) {
                    marker.addTo(map);
                } else {
                    map.removeLayer(marker);
                }
            });
        }
        
        // Attach filter event listeners
        document.getElementById('status-filter').addEventListener('change', filterMarkers);
        document.getElementById('occupancy-filter').addEventListener('change', filterMarkers);
        
        // View toggle functionality
        const mapViewBtn = document.getElementById('map-view-btn');
        const tableViewBtn = document.getElementById('table-view-btn');
        const mapSection = document.getElementById('map-section');
        const mapLegend = document.getElementById('map-legend');
        const tableSection = document.querySelector('.shelters-table');
        
        mapViewBtn.addEventListener('click', (e) => {
            e.preventDefault();
            mapViewBtn.classList.add('active');
            tableViewBtn.classList.remove('active');
            mapSection.style.display = 'block';
            mapLegend.style.display = 'block';
            tableSection.style.display = 'none';
            
            // Refresh map size
            setTimeout(() => map.invalidateSize(), 100);
        });
        
        tableViewBtn.addEventListener('click', (e) => {
            e.preventDefault();
            tableViewBtn.classList.add('active');
            mapViewBtn.classList.remove('active');
            mapSection.style.display = 'none';
            mapLegend.style.display = 'none';
            tableSection.style.display = 'block';
        });
        
        // Start with table view (to match requests page behavior)
        tableSection.style.display = 'none';
        mapSection.style.display = 'block';
        mapLegend.style.display = 'block';
    </script>
</body>
</html>