<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emergency Shelters</title>
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
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
        .nav {
            background-color: #091F57;
            padding: 0.5rem;
            text-align: center;
        }
        .nav a {
            color: #E4E8F5;
            text-decoration: none;
            margin: 0 1rem;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            transition: all 0.3s;
        }
        .nav a:hover {
            background-color: rgba(43, 85, 189, 0.3);
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: #091F57;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.3);
            text-align: center;
            border: 1px solid rgba(43, 85, 189, 0.2);
        }
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #2B55BD;
            text-shadow: 0 0 10px rgba(43, 85, 189, 0.3);
        }
        .stat-label {
            color: #E4E8F5;
            opacity: 0.9;
            margin-top: 0.5rem;
        }
        .shelters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 1.5rem;
        }
        .shelter-card {
            background: #091F57;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.3);
            overflow: hidden;
            border-left: 4px solid;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .shelter-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(43, 85, 189, 0.3);
        }
        .shelter-card.available {
            border-left-color: #51cf66;
        }
        .shelter-card.nearly-full {
            border-left-color: #ffa94d;
        }
        .shelter-card.full {
            border-left-color: #ff6b6b;
        }
        .shelter-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(43, 85, 189, 0.2);
        }
        .shelter-name {
            font-size: 1.25rem;
            font-weight: bold;
            color: #E4E8F5;
            margin-bottom: 0.5rem;
        }
        .shelter-location {
            color: #E4E8F5;
            opacity: 0.7;
            margin-bottom: 1rem;
        }
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
        }
        .status-available {
            background-color: rgba(81, 207, 102, 0.2);
            color: #51cf66;
        }
        .status-nearly-full {
            background-color: rgba(255, 169, 77, 0.2);
            color: #ffa94d;
        }
        .status-full {
            background-color: rgba(255, 107, 107, 0.2);
            color: #ff6b6b;
        }
        .shelter-body {
            padding: 1.5rem;
        }
        .capacity-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
            color: #E4E8F5;
        }
        .capacity-bar {
            background-color: rgba(43, 85, 189, 0.2);
            height: 8px;
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 1rem;
        }
        .capacity-fill {
            height: 100%;
            transition: width 0.3s ease;
        }
        .capacity-fill.available {
            background-color: #51cf66;
        }
        .capacity-fill.nearly-full {
            background-color: #ffa94d;
        }
        .capacity-fill.full {
            background-color: #ff6b6b;
        }
        .facilities {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }
        .facility-tag {
            background-color: rgba(43, 85, 189, 0.3);
            color: #E4E8F5;
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            font-size: 0.75rem;
        }
        .shelter-actions {
            display: flex;
            gap: 0.5rem;
        }
        .btn {
            padding: 0.5rem 1rem;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.9rem;
            text-align: center;
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
            box-shadow: 0 4px 12px rgba(43, 85, 189, 0.5);
        }
        .btn-secondary {
            background-color: rgba(43, 85, 189, 0.3);
            color: #E4E8F5;
        }
        .btn-secondary:hover {
            background-color: rgba(43, 85, 189, 0.5);
        }
        .back-link {
            color: #2B55BD;
            text-decoration: none;
            margin-bottom: 1rem;
            display: inline-block;
            transition: color 0.3s;
        }
        .back-link:hover {
            color: #3d6fd4;
            text-decoration: underline;
        }
        
        /* Map Styles */
        .map-container {
            background: #091F57;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.3);
            margin-bottom: 2rem;
            overflow: hidden;
            border: 1px solid rgba(43, 85, 189, 0.2);
        }
        
        .map-header {
            background: linear-gradient(135deg, #2B55BD, #091F57);
            color: white;
            padding: 1.5rem;
            text-align: center;
        }
        
        .map-header h3 {
            margin: 0;
            font-size: 1.5rem;
        }
        
        .map-controls {
            background: rgba(43, 85, 189, 0.1);
            padding: 1rem;
            border-bottom: 1px solid rgba(43, 85, 189, 0.2);
            display: flex;
            gap: 1rem;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .map-filter {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .map-filter label {
            font-weight: 600;
            color: #E4E8F5;
        }
        
        .map-filter select {
            padding: 0.5rem;
            border: 1px solid rgba(43, 85, 189, 0.4);
            border-radius: 4px;
            background-color: #0D1326;
            color: #E4E8F5;
        }
        
        #shelter-map {
            height: 500px;
            width: 100%;
        }
        
        .view-toggle {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            justify-content: center;
        }
        
        .toggle-btn {
            padding: 0.75rem 1.5rem;
            border: 2px solid #2B55BD;
            background: rgba(43, 85, 189, 0.1);
            color: #E4E8F5;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .toggle-btn.active {
            background: #2B55BD;
            color: white;
        }
        
        .toggle-btn:hover {
            background: #2B55BD;
            color: white;
            border-color: #3d6fd4;
        }
        
        .legend {
            background: #091F57;
            padding: 1rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.3);
            margin-bottom: 2rem;
            border: 1px solid rgba(43, 85, 189, 0.2);
        }
        
        .legend h4 {
            color: #E4E8F5;
        }
        
        .legend-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
            color: #E4E8F5;
        }
        
        .legend-marker {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border: 2px solid white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        
        .legend-marker.available {
            background: #51cf66;
        }
        
        .legend-marker.nearly-full {
            background: #ffa94d;
        }
        
        .legend-marker.full {
            background: #ff6b6b;
        }
        
        .legend-marker.inactive {
            background: #95a5a6;
        }
        
        h2, h3 {
            color: #E4E8F5;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🏠 Emergency Shelters</h1>
        <p>Safe havens during disasters - Real-time availability</p>
    </div>

    <div class="nav">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <a href="{{ route('alerts.index') }}">Alerts</a>
        <a href="{{ route('shelters.index') }}">Shelters</a>
        <a href="{{ route('requests.create') }}">Emergency Request</a>
        <a href="{{ route('login') }}">Login</a>
    </div>

    <div class="container">
        <a href="{{ route('dashboard') }}" class="back-link">← Back to Dashboard</a>
        
        <h2>Shelter System Overview</h2>
        
        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number">{{ $stats['total_shelters'] }}</div>
                <div class="stat-label">Total Shelters</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $stats['available_shelters'] }}</div>
                <div class="stat-label">Available Shelters</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $stats['total_capacity'] }}</div>
                <div class="stat-label">Total Capacity</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $stats['total_occupied'] }}</div>
                <div class="stat-label">Currently Occupied</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $stats['total_available'] }}</div>
                <div class="stat-label">Available Spaces</div>
            </div>
        </div>

        <!-- View Toggle -->
        <div class="view-toggle">
            <a href="#" class="toggle-btn active" id="map-view-btn">🗺️ Map View</a>
            <a href="#" class="toggle-btn" id="list-view-btn">📋 List View</a>
        </div>

        <!-- Interactive Map -->
        <div class="map-container" id="map-container">
            <div class="map-header">
                <h3>🗺️ Interactive Shelter Map</h3>
                <p>Click on markers to see shelter details and availability</p>
            </div>
            
            <div class="map-controls">
                <div class="map-filter">
                    <label>Filter by Status:</label>
                    <select id="status-filter">
                        <option value="all">All Shelters</option>
                        <option value="available">Available Only</option>
                        <option value="nearly-full">Nearly Full</option>
                        <option value="full">Full</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                
                <div class="map-filter">
                    <label>Show Nearest:</label>
                    <button id="find-nearest" class="btn btn-primary">📍 Find Nearest Shelters</button>
                    <button id="clear-routes" class="btn btn-secondary" style="display: none;">🗺️ Clear Routes</button>
                </div>
                
                <div class="map-filter">
                    <label>Distance:</label>
                    <select id="distance-filter">
                        <option value="all">All Distances</option>
                        <option value="1">Within 1 km</option>
                        <option value="3">Within 3 km</option>
                        <option value="5">Within 5 km</option>
                        <option value="10">Within 10 km</option>
                    </select>
                </div>
            </div>
            
            <div id="shelter-map"></div>
        </div>

        <!-- Map Legend -->
        <div class="legend">
            <h4>🏷️ Map Legend</h4>
            <div class="legend-item">
                <div class="legend-marker available"></div>
                <span>Available Shelters (0-70% capacity)</span>
            </div>
            <div class="legend-item">
                <div class="legend-marker nearly-full"></div>
                <span>Nearly Full Shelters (70-90% capacity)</span>
            </div>
            <div class="legend-item">
                <div class="legend-marker full"></div>
                <span>Full Shelters (90%+ capacity)</span>
            </div>
            <div class="legend-item">
                <div class="legend-marker inactive"></div>
                <span>Inactive Shelters</span>
            </div>
        </div>

        <h3>All Shelters ({{ count($shelters) }})</h3>

        <!-- Shelters Grid -->
        <div class="shelters-grid" id="shelters-grid">
            @foreach($shelters as $shelter)
                @php
                    $occupancyPercentage = ($shelter['occupied'] / $shelter['capacity']) * 100;
                    $statusClass = $occupancyPercentage >= 90 ? 'full' : ($occupancyPercentage >= 70 ? 'nearly-full' : 'available');
                    $widthPercent = round($occupancyPercentage);
                @endphp
                
                <div class="shelter-card {{ $statusClass }}" data-shelter-id="{{ $shelter['id'] }}" data-status="{{ $statusClass }}">
                    <div class="shelter-header">
                        <div class="shelter-name">{{ $shelter['name'] }}</div>
                        <div class="shelter-location">📍 {{ $shelter['location'] }}</div>
                        <span class="status-badge status-{{ str_replace([' ', '-'], '-', strtolower($shelter['status'])) }}">
                            {{ $shelter['status'] }}
                        </span>
                    </div>
                    
                    <div class="shelter-body">
                        <!-- Capacity Information -->
                        <div class="capacity-info">
                            <span><strong>{{ $shelter['occupied'] }}/{{ $shelter['capacity'] }}</strong> occupied</span>
                            <span><strong>{{ $shelter['available'] }}</strong> available</span>
                        </div>
                        
                        <!-- Capacity Bar -->
                        <div class="capacity-bar">
                            <div class="capacity-fill {{ $statusClass }}" data-width="{{ $widthPercent }}"></div>
                        </div>
                        
                        <!-- Facilities -->
                        <div class="facilities">
                            @foreach($shelter['facilities'] as $facility)
                                <span class="facility-tag">{{ $facility }}</span>
                            @endforeach
                        </div>
                        
                        <!-- Contact Info -->
                        <div style="margin-bottom: 1rem; color: #7f8c8d; font-size: 0.9rem;">
                            📞 {{ $shelter['contact'] }}
                        </div>
                        
                        <!-- Actions -->
                        <div class="shelter-actions">
                            <a href="{{ route('shelters.show', $shelter['id']) }}" class="btn btn-primary">
                                View Details
                            </a>
                            @if($shelter['available'] > 0)
                                <a href="#" class="btn btn-secondary">Request Space</a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Leaflet JavaScript -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Shelter data for mapping
        const shelters = @json($shelters);
        
        // Initialize map
        let map;
        let markers = [];
        let userLocationMarker = null;
        let routeLines = [];
        let currentUserLocation = null;
        
        function initMap() {
            // Center map on first shelter or default location
            const centerLat = shelters.length > 0 ? shelters[0].latitude || 23.8103 : 23.8103;
            const centerLng = shelters.length > 0 ? shelters[0].longitude || 90.4125 : 90.4125;
            
            map = L.map('shelter-map').setView([centerLat, centerLng], 11);
            
            // Add OpenStreetMap tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);
            
            // Add shelter markers
            addShelterMarkers();
        }
        
        function addShelterMarkers() {
            markers = [];
            
            shelters.forEach(shelter => {
                // Generate coordinates if not available
                const lat = shelter.latitude || (23.8103 + (Math.random() - 0.5) * 0.1);
                const lng = shelter.longitude || (90.4125 + (Math.random() - 0.5) * 0.1);
                
                // Determine marker color based on capacity
                const occupancyPercentage = (shelter.occupied / shelter.capacity) * 100;
                let markerColor = '#27ae60'; // Available (green)
                let statusClass = 'available';
                
                if (shelter.status !== 'Active') {
                    markerColor = '#95a5a6'; // Inactive (gray)
                    statusClass = 'inactive';
                } else if (occupancyPercentage >= 90) {
                    markerColor = '#e74c3c'; // Full (red)
                    statusClass = 'full';
                } else if (occupancyPercentage >= 70) {
                    markerColor = '#f39c12'; // Nearly full (orange)
                    statusClass = 'nearly-full';
                }
                
                // Create custom marker icon
                const markerIcon = L.divIcon({
                    className: 'custom-marker',
                    html: `<div style="
                        background-color: ${markerColor};
                        width: 25px;
                        height: 25px;
                        border-radius: 50%;
                        border: 3px solid white;
                        box-shadow: 0 2px 4px rgba(0,0,0,0.3);
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        color: white;
                        font-weight: bold;
                        font-size: 12px;
                    ">${shelter.available}</div>`,
                    iconSize: [31, 31],
                    iconAnchor: [15, 15]
                });
                
                // Create marker
                const marker = L.marker([lat, lng], { icon: markerIcon })
                    .bindPopup(createPopupContent(shelter))
                    .addTo(map);
                
                marker.shelterData = shelter;
                marker.statusClass = statusClass;
                markers.push(marker);
                
                // Add click event to highlight corresponding card
                marker.on('click', function() {
                    highlightShelterCard(shelter.id);
                });
            });
        }
        
        function createPopupContent(shelter) {
            const occupancyPercentage = Math.round((shelter.occupied / shelter.capacity) * 100);
            const statusBadge = shelter.status === 'Active' ? 
                (occupancyPercentage >= 90 ? '🔴 Full' : 
                 occupancyPercentage >= 70 ? '🟡 Nearly Full' : '🟢 Available') :
                '⚪ Inactive';
            
            return `
                <div style="min-width: 250px;">
                    <h4 style="margin: 0 0 10px 0; color: #2c3e50;">${shelter.name}</h4>
                    <p style="margin: 5px 0; color: #7f8c8d;">📍 ${shelter.location}</p>
                    <p style="margin: 5px 0; color: #7f8c8d;">📞 ${shelter.contact}</p>
                    <p style="margin: 5px 0; font-weight: bold;">${statusBadge}</p>
                    <p style="margin: 5px 0;">
                        <strong>Capacity:</strong> ${shelter.occupied}/${shelter.capacity} 
                        (${shelter.available} available)
                    </p>
                    <p style="margin: 5px 0;">
                        <strong>Facilities:</strong> ${shelter.facilities.join(', ')}
                    </p>
                    <div style="margin-top: 10px;">
                        <a href="/shelters/${shelter.id}" style="
                            background: #3498db;
                            color: white;
                            padding: 5px 10px;
                            text-decoration: none;
                            border-radius: 4px;
                            font-size: 14px;
                        ">View Details</a>
                    </div>
                </div>
            `;
        }
        
        function highlightShelterCard(shelterId) {
            // Remove existing highlights
            document.querySelectorAll('.shelter-card').forEach(card => {
                card.style.boxShadow = '0 2px 4px rgba(0,0,0,0.1)';
            });
            
            // Highlight selected card
            const selectedCard = document.querySelector(`[data-shelter-id="${shelterId}"]`);
            if (selectedCard) {
                selectedCard.style.boxShadow = '0 8px 25px rgba(52, 152, 219, 0.3)';
                selectedCard.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        }
        
        // Filter functionality
        function filterShelters(statusFilter) {
            markers.forEach(marker => {
                const shelter = marker.shelterData;
                let show = true;
                
                switch(statusFilter) {
                    case 'available':
                        show = marker.statusClass === 'available';
                        break;
                    case 'nearly-full':
                        show = marker.statusClass === 'nearly-full';
                        break;
                    case 'full':
                        show = marker.statusClass === 'full';
                        break;
                    case 'inactive':
                        show = marker.statusClass === 'inactive';
                        break;
                    default:
                        show = true;
                }
                
                if (show) {
                    marker.addTo(map);
                } else {
                    map.removeLayer(marker);
                }
            });
            
            // Filter shelter cards too
            document.querySelectorAll('.shelter-card').forEach(card => {
                const cardStatus = card.getAttribute('data-status');
                const shelterStatus = card.querySelector('.status-badge').textContent.toLowerCase();
                
                let show = true;
                switch(statusFilter) {
                    case 'available':
                        show = cardStatus === 'available';
                        break;
                    case 'nearly-full':
                        show = cardStatus === 'nearly-full';
                        break;
                    case 'full':
                        show = cardStatus === 'full';
                        break;
                    case 'inactive':
                        show = shelterStatus.includes('inactive');
                        break;
                    default:
                        show = true;
                }
                
                card.style.display = show ? 'block' : 'none';
            });
        }
        
        // Find nearest shelters with enhanced routing
        function findNearestShelters() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    const userLat = position.coords.latitude;
                    const userLng = position.coords.longitude;
                    currentUserLocation = { lat: userLat, lng: userLng };
                    
                    // Add user location marker
                    if (userLocationMarker) {
                        map.removeLayer(userLocationMarker);
                    }
                    
                    userLocationMarker = L.marker([userLat, userLng], {
                        icon: L.divIcon({
                            className: 'user-location-marker',
                            html: `<div style="
                                background: #e74c3c;
                                width: 25px;
                                height: 25px;
                                border-radius: 50%;
                                border: 4px solid white;
                                box-shadow: 0 3px 8px rgba(0,0,0,0.4);
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                color: white;
                                font-size: 14px;
                            ">📍</div>`,
                            iconSize: [33, 33],
                            iconAnchor: [16, 16]
                        })
                    }).addTo(map).bindPopup('📍 Your Current Location');
                    
                    // Calculate distances and show nearest available shelters
                    const sheltersWithDistance = markers.map(marker => {
                        const shelter = marker.shelterData;
                        const lat = shelter.latitude || (23.8103 + (Math.random() - 0.5) * 0.1);
                        const lng = shelter.longitude || (90.4125 + (Math.random() - 0.5) * 0.1);
                        const distance = getDistance(userLat, userLng, lat, lng);
                        
                        return { marker, shelter, distance, lat, lng };
                    })
                    .filter(item => item.marker.statusClass === 'available') // Only available shelters
                    .sort((a, b) => a.distance - b.distance);
                    
                    // Clear existing routes
                    clearRoutes();
                    
                    // Show routes to nearest 3 available shelters
                    const nearestShelters = sheltersWithDistance.slice(0, 3);
                    
                    nearestShelters.forEach((item, index) => {
                        // Create route line
                        const routeLine = L.polyline([
                            [userLat, userLng],
                            [item.lat, item.lng]
                        ], {
                            color: index === 0 ? '#27ae60' : index === 1 ? '#f39c12' : '#e74c3c',
                            weight: index === 0 ? 5 : 3,
                            opacity: 0.8,
                            dashArray: index === 0 ? null : '10, 5'
                        }).addTo(map);
                        
                        routeLines.push(routeLine);
                        
                        // Update marker popup with distance and route info
                        const originalPopup = item.marker.getPopup().getContent();
                        const routeColor = index === 0 ? '🟢' : index === 1 ? '🟡' : '🔴';
                        const routeLabel = index === 0 ? 'Nearest' : index === 1 ? '2nd Nearest' : '3rd Nearest';
                        
                        item.marker.setPopupContent(
                            originalPopup.replace('</div>', 
                                `<div style="border-top: 1px solid #eee; margin-top: 10px; padding-top: 10px;">
                                    <p style="margin: 5px 0; font-weight: bold; color: #e74c3c;">
                                        ${routeColor} ${routeLabel}: ${item.distance.toFixed(1)} km
                                    </p>
                                    <p style="margin: 5px 0; font-size: 12px; color: #7f8c8d;">
                                        ⏱️ Est. walk time: ${Math.ceil(item.distance * 12)} minutes
                                    </p>
                                </div></div>`
                            )
                        );
                    });
                    
                    // Show nearest shelter popup and zoom to fit all markers
                    if (nearestShelters.length > 0) {
                        const nearest = nearestShelters[0];
                        nearest.marker.openPopup();
                        
                        // Fit map to show user location and nearest shelters
                        const group = new L.featureGroup([
                            userLocationMarker,
                            ...nearestShelters.slice(0, 3).map(item => item.marker)
                        ]);
                        map.fitBounds(group.getBounds().pad(0.1));
                    }
                    
                    // Show clear routes button
                    document.getElementById('clear-routes').style.display = 'inline-block';
                    
                }, function() {
                    alert('Unable to get your location. Please enable location services.');
                });
            } else {
                alert('Geolocation is not supported by this browser.');
            }
        }
        
        // Clear route lines
        function clearRoutes() {
            routeLines.forEach(line => map.removeLayer(line));
            routeLines = [];
            document.getElementById('clear-routes').style.display = 'none';
            
            // Reset marker popups to original content
            markers.forEach(marker => {
                const shelter = marker.shelterData;
                marker.setPopupContent(createPopupContent(shelter));
            });
        }
        
        // Enhanced distance filtering
        function filterByDistance() {
            const maxDistance = parseFloat(document.getElementById('distance-filter').value);
            
            if (!currentUserLocation || maxDistance === 'all') {
                // Show all markers
                markers.forEach(marker => marker.addTo(map));
                return;
            }
            
            markers.forEach(marker => {
                const shelter = marker.shelterData;
                const lat = shelter.latitude || (23.8103 + (Math.random() - 0.5) * 0.1);
                const lng = shelter.longitude || (90.4125 + (Math.random() - 0.5) * 0.1);
                const distance = getDistance(currentUserLocation.lat, currentUserLocation.lng, lat, lng);
                
                if (distance <= maxDistance) {
                    marker.addTo(map);
                } else {
                    map.removeLayer(marker);
                }
            });
        }
        
        // Calculate distance between two points
        function getDistance(lat1, lng1, lat2, lng2) {
            const R = 6371; // Radius of the Earth in km
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLng = (lng2 - lng1) * Math.PI / 180;
            const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                    Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                    Math.sin(dLng/2) * Math.sin(dLng/2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            return R * c;
        }
        
        // View toggle functionality
        function toggleView(view) {
            const mapContainer = document.getElementById('map-container');
            const sheltersGrid = document.getElementById('shelters-grid');
            const mapBtn = document.getElementById('map-view-btn');
            const listBtn = document.getElementById('list-view-btn');
            
            if (view === 'map') {
                mapContainer.style.display = 'block';
                sheltersGrid.style.display = 'none';
                mapBtn.classList.add('active');
                listBtn.classList.remove('active');
                
                // Initialize map if not already done
                if (!map) {
                    setTimeout(initMap, 100);
                } else {
                    map.invalidateSize();
                }
            } else {
                mapContainer.style.display = 'none';
                sheltersGrid.style.display = 'grid';
                mapBtn.classList.remove('active');
                listBtn.classList.add('active');
            }
        }
        
        // Event listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Set capacity bar widths
            const capacityFills = document.querySelectorAll('.capacity-fill');
            capacityFills.forEach(function(fill) {
                const width = fill.getAttribute('data-width');
                if (width) {
                    fill.style.width = width + '%';
                }
            });
            
            // Initialize map
            setTimeout(initMap, 100);
            
            // Event listeners
            document.getElementById('status-filter').addEventListener('change', function() {
                filterShelters(this.value);
            });
            
            document.getElementById('find-nearest').addEventListener('click', findNearestShelters);
            
            document.getElementById('clear-routes').addEventListener('click', clearRoutes);
            
            document.getElementById('distance-filter').addEventListener('change', filterByDistance);
            
            document.getElementById('map-view-btn').addEventListener('click', function(e) {
                e.preventDefault();
                toggleView('map');
            });
            
            document.getElementById('list-view-btn').addEventListener('click', function(e) {
                e.preventDefault();
                toggleView('list');
            });
        });
    </script>
</body>
</html>
