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
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #0D1326; color: #E4E8F5; min-height: 100vh; }
        .admin-header { background: #091F57; color: #E4E8F5; padding: 1.2rem 2rem; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3); flex-wrap: wrap; gap: 1rem; }
        .admin-header h1 { font-size: clamp(1.2rem, 3vw, 1.8rem); }
        .admin-nav { background: #091F57; padding: 0.8rem 2rem; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2); overflow-x: auto; white-space: nowrap; }
        .admin-nav a { color: #E4E8F5; text-decoration: none; margin-right: 1.5rem; padding: 0.6rem 1.2rem; border-radius: 6px; transition: all 0.3s ease; display: inline-block; font-size: clamp(0.85rem, 2vw, 0.95rem); }
        .admin-nav a:hover { background: rgba(43, 85, 189, 0.3); }
        .admin-nav a.active { background: #2B55BD; box-shadow: 0 2px 8px rgba(43, 85, 189, 0.4); }
        .container { max-width: 1400px; margin: 2rem auto; padding: 0 1rem; }
        @media (max-width: 768px) { .container { margin: 1rem auto; padding: 0 0.5rem; } }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.2rem; margin-bottom: 2rem; }
        @media (max-width: 480px) { .stats-grid { grid-template-columns: 1fr; } }
        .stat-card { background: linear-gradient(135deg, #091F57 0%, #0D1326 100%); padding: 1.8rem; border-radius: 12px; border: 1px solid rgba(43, 85, 189, 0.2); box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3); text-align: center; transition: transform 0.3s ease, box-shadow 0.3s ease; }
        .stat-card:hover { transform: translateY(-4px); box-shadow: 0 6px 16px rgba(43, 85, 189, 0.4); }
        .stat-number { font-size: clamp(2rem, 5vw, 2.5rem); font-weight: bold; color: #2B55BD; text-shadow: 0 2px 4px rgba(43, 85, 189, 0.3); }
        .stat-label { color: #E4E8F5; margin-top: 0.5rem; font-size: clamp(0.85rem, 2vw, 0.95rem); opacity: 0.9; }
        .shelters-table { background: #091F57; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3); border: 1px solid rgba(43, 85, 189, 0.2); }
        @media (max-width: 768px) { .shelters-table { overflow-x: auto; } }
        .shelters-table table { width: 100%; border-collapse: collapse; min-width: 600px; }
        .shelters-table th, .shelters-table td { padding: 1.2rem; text-align: left; border-bottom: 1px solid rgba(43, 85, 189, 0.2); }
        .shelters-table th { background: rgba(43, 85, 189, 0.2); font-weight: 600; color: #E4E8F5; font-size: clamp(0.85rem, 2vw, 0.95rem); }
        .shelters-table td { color: #E4E8F5; font-size: clamp(0.8rem, 2vw, 0.9rem); }
        .shelters-table tr:hover { background: rgba(43, 85, 189, 0.1); }
        .status-active { color: #51cf66; font-weight: bold; text-shadow: 0 1px 3px rgba(81, 207, 102, 0.3); }
        .status-inactive { color: #ff6b6b; opacity: 0.8; }
        .capacity-high { color: #ff6b6b; font-weight: bold; }
        .capacity-medium { color: #ffa94d; font-weight: bold; }
        .capacity-low { color: #51cf66; }
        .btn { padding: 0.6rem 1.2rem; border: none; border-radius: 6px; cursor: pointer; text-decoration: none; display: inline-block; transition: all 0.3s ease; font-size: clamp(0.8rem, 2vw, 0.9rem); font-weight: 500; }
        .btn-primary { background: #2B55BD; color: #E4E8F5; box-shadow: 0 2px 8px rgba(43, 85, 189, 0.3); }
        .btn-primary:hover { background: #3d6fd4; box-shadow: 0 4px 12px rgba(43, 85, 189, 0.5); transform: translateY(-2px); }
        .btn-success { background: #51cf66; color: #091F57; box-shadow: 0 2px 8px rgba(81, 207, 102, 0.3); }
        .btn-success:hover { background: #69db7c; box-shadow: 0 4px 12px rgba(81, 207, 102, 0.5); transform: translateY(-2px); }
        .btn-warning { background: #ffa94d; color: #091F57; box-shadow: 0 2px 8px rgba(255, 169, 77, 0.3); }
        .btn-danger { background: #ff6b6b; color: #E4E8F5; box-shadow: 0 2px 8px rgba(255, 107, 107, 0.3); }
        .btn-danger:hover { background: #ff8787; box-shadow: 0 4px 12px rgba(255, 107, 107, 0.5); transform: translateY(-2px); }
        .action-buttons { margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; }
        .action-buttons h2 { color: #E4E8F5; font-size: clamp(1.3rem, 4vw, 1.8rem); }
        .alert-message { padding: 1rem 1.5rem; border-radius: 8px; margin-bottom: 1.5rem; border-left: 4px solid; font-size: clamp(0.85rem, 2vw, 0.95rem); }
        .alert-success { background: rgba(81, 207, 102, 0.1); border-color: #51cf66; color: #51cf66; }
        .alert-error { background: rgba(255, 107, 107, 0.1); border-color: #ff6b6b; color: #ff6b6b; }
        
        /* Map Integration Styles */
        .map-section { background: #091F57; border-radius: 12px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3); margin-bottom: 2rem; overflow: hidden; border: 1px solid rgba(43, 85, 189, 0.2); }
        .map-header { background: linear-gradient(135deg, #2B55BD, #091F57); color: #E4E8F5; padding: 1.5rem; text-align: center; }
        .map-header h3 { margin: 0; font-size: clamp(1.2rem, 3vw, 1.5rem); color: #E4E8F5; }
        .map-header p { margin-top: 0.5rem; opacity: 0.9; font-size: clamp(0.85rem, 2vw, 0.95rem); }
        .map-controls { background: rgba(43, 85, 189, 0.1); padding: 1rem; border-bottom: 1px solid rgba(43, 85, 189, 0.2); display: flex; gap: 1rem; align-items: center; flex-wrap: wrap; }
        .map-filter { display: flex; align-items: center; gap: 0.5rem; }
        .map-filter label { font-weight: 600; color: #E4E8F5; font-size: clamp(0.85rem, 2vw, 0.9rem); }
        .map-filter select { padding: 0.5rem; border: 1px solid rgba(43, 85, 189, 0.3); border-radius: 4px; background: rgba(9, 31, 87, 0.5); color: #E4E8F5; }
        #shelters-map { height: 410px; width: 100%; }
        .view-toggle { display: flex; gap: 1rem; margin-bottom: 2rem; justify-content: center; flex-wrap: wrap; }
        .toggle-btn { padding: 0.75rem 1.5rem; border: 2px solid #2B55BD; background: transparent; color: #E4E8F5; border-radius: 25px; text-decoration: none; font-weight: 600; transition: all 0.3s ease; font-size: clamp(0.85rem, 2vw, 0.95rem); }
        .toggle-btn.active { background: #2B55BD; box-shadow: 0 4px 12px rgba(43, 85, 189, 0.4); }
        .toggle-btn:hover { background: #2B55BD; box-shadow: 0 4px 12px rgba(43, 85, 189, 0.4); }
        .status-legend { background: #091F57; padding: 1.5rem; border-radius: 12px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3); margin-bottom: 2rem; border: 1px solid rgba(43, 85, 189, 0.2); }
        .status-legend h4 { color: #E4E8F5; margin-bottom: 1rem; font-size: clamp(1rem, 2.5vw, 1.2rem); }
        .status-legend h5 { color: #E4E8F5; margin-bottom: 0.5rem; font-size: clamp(0.9rem, 2vw, 1rem); opacity: 0.9; }
        .legend-item { display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem; color: #E4E8F5; font-size: clamp(0.85rem, 2vw, 0.9rem); }
        .legend-marker { width: 20px; height: 20px; border-radius: 50%; border: 2px solid rgba(228, 232, 245, 0.3); box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3); flex-shrink: 0; }
        .legend-marker.active { background: #51cf66; }
        .legend-marker.inactive { background: #ff6b6b; }
        .legend-marker.high-capacity { background: #ff6b6b; }
        .legend-marker.medium-capacity { background: #ffa94d; }
        .legend-marker.low-capacity { background: #51cf66; }
    </style>
</head>
<body>
    @section('page_title', '🏠 Admin Panel - Manage Shelters')
    @include('admin.partials.header')

    <div class="container">
        <!-- Action Buttons -->
        <div class="action-buttons">
            <h2>Shelter Management</h2>
            <a href="{{ route('admin.shelters.create') }}" class="btn btn-success">
                ➕ Create New Shelter
            </a>
        </div>

        @if(session('success'))
        <div class="alert-message alert-success">
            ✓ {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="alert-message alert-error">
            ✗ {{ session('error') }}
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