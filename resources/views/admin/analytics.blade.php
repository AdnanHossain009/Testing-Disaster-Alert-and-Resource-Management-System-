<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Geographic Analytics - Emergency Response Admin</title>
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
        .admin-header { background: #2d3748; color: white; padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; }
        .admin-nav { background: #4a5568; padding: 0.5rem 2rem; }
        .admin-nav a { color: white; text-decoration: none; margin-right: 2rem; padding: 0.5rem 1rem; border-radius: 5px; }
        .admin-nav a:hover, .admin-nav a.active { background: #2d3748; }
        .container { max-width: 1400px; margin: 2rem auto; padding: 0 1rem; }
        .stats-overview { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
        .stat-card { background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08); text-align: center; border-left: 4px solid #007bff; }
        .stat-number { font-size: 2.5rem; font-weight: bold; color: #2c3e50; margin-bottom: 0.5rem; }
        .stat-label { color: #7f8c8d; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; }
        .heat-map-container { background: white; border-radius: 15px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); overflow: hidden; margin-bottom: 2rem; }
        .card-header { background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 1.5rem; text-align: center; }
        .card-header h3 { margin: 0; font-size: 1.5rem; }
        .card-content { padding: 2rem; }
        #heat-map { height: 500px; width: 100%; border-radius: 10px; }
        .map-controls { background: #f8f9fa; padding: 1rem; border-radius: 10px; margin-bottom: 1rem; display: flex; gap: 1rem; align-items: center; flex-wrap: wrap; }
        .control-group { display: flex; align-items: center; gap: 0.5rem; }
        .control-group label { font-weight: 600; color: #495057; }
        .control-group select, .control-group button { padding: 0.5rem; border: 1px solid #ced4da; border-radius: 4px; }
        .btn { background: #007bff; color: white; border: none; cursor: pointer; transition: background 0.3s ease; }
        .btn:hover { background: #0056b3; }
        .analytics-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; }
        .legend { background: white; padding: 1rem; border-radius: 10px; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08); }
        .legend-item { display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem; }
        .legend-color { width: 20px; height: 20px; border-radius: 50%; }
        .density-low { background: #ffeb3b; }
        .density-medium { background: #ff9800; }
        .density-high { background: #f44336; }
        .density-critical { background: #9c27b0; }
        .chart-container { background: white; border-radius: 10px; padding: 1.5rem; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08); }
        @media (max-width: 768px) { .analytics-grid { grid-template-columns: 1fr; } .stats-overview { grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); } }
    </style>
</head>
<body>
    <div class="admin-header">
        <h1>📊 Geographic Analytics Dashboard</h1>
        <div>
            <span>Welcome, {{ Auth::user()->name ?? 'Admin' }}</span>
            <a href="{{ route('auth.logout') }}" style="margin-left: 1rem; color: #fed7d7;">Logout</a>
        </div>
    </div>

    <div class="admin-nav">
        <a href="{{ route('admin.dashboard') }}">Dashboard</a>
        <a href="{{ route('admin.alerts') }}">Manage Alerts</a>
        <a href="{{ route('admin.shelters') }}">Manage Shelters</a>
        <a href="{{ route('admin.requests') }}">Manage Requests</a>
        <a href="{{ route('admin.analytics') }}" class="active">Analytics</a>
    </div>

    <div class="container">
        <!-- Statistics Overview -->
        <div class="stats-overview">
            <div class="stat-card">
                <div class="stat-number">{{ $analytics['requests']['total'] ?? 0 }}</div>
                <div class="stat-label">Total Requests</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $analytics['shelters']['active'] ?? 0 }}</div>
                <div class="stat-label">Active Shelters</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $analytics['alerts']['total'] ?? 0 }}</div>
                <div class="stat-label">Total Alerts</div>
            </div>
            <div class="stat-card">
                @php
                    $utilization = $analytics['shelters']['capacity_utilization'] ?? (object)['occupied' => 0, 'total' => 100];
                    $percentage = $utilization->total > 0 ? round(($utilization->occupied / $utilization->total) * 100) : 0;
                @endphp
                <div class="stat-number">{{ $percentage }}%</div>
                <div class="stat-label">Shelter Utilization</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">3</div>
                <div class="stat-label">Emergency Hot Zones</div>
            </div>
        </div>

        <!-- Heat Map -->
        <div class="heat-map-container">
            <div class="card-header">
                <h3>🔥 Emergency Request Heat Map</h3>
                <p>Geographic density analysis of emergency requests and response patterns</p>
            </div>
            <div class="card-content">
                <div class="map-controls">
                    <div class="control-group">
                        <label>Time Period:</label>
                        <select id="time-filter">
                            <option value="24h">Last 24 Hours</option>
                            <option value="7d">Last 7 Days</option>
                            <option value="30d">Last 30 Days</option>
                            <option value="all">All Time</option>
                        </select>
                    </div>
                    
                    <div class="control-group">
                        <label>Request Type:</label>
                        <select id="type-filter">
                            <option value="all">All Types</option>
                            <option value="shelter">Shelter</option>
                            <option value="medical">Medical</option>
                            <option value="food">Food</option>
                            <option value="water">Water</option>
                            <option value="rescue">Rescue</option>
                        </select>
                    </div>
                    
                    <div class="control-group">
                        <label>Priority:</label>
                        <select id="priority-filter">
                            <option value="all">All Priorities</option>
                            <option value="critical">Critical</option>
                            <option value="high">High</option>
                            <option value="medium">Medium</option>
                            <option value="low">Low</option>
                        </select>
                    </div>
                    
                    <div class="control-group">
                        <button id="refresh-heat-map" class="btn">🔄 Refresh Data</button>
                        <button id="export-data" class="btn">📊 Export Report</button>
                    </div>
                </div>
                
                <div id="heat-map"></div>
            </div>
        </div>

        <!-- Legend and Insights -->
        <div class="analytics-grid">
            <div class="legend">
                <h4>🏷️ Heat Map Legend</h4>
                <div class="legend-item">
                    <div class="legend-color density-low"></div>
                    <span>Low Density (1-5 requests)</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color density-medium"></div>
                    <span>Medium Density (6-15 requests)</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color density-high"></div>
                    <span>High Density (16-30 requests)</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color density-critical"></div>
                    <span>Critical Density (30+ requests)</span>
                </div>
            </div>
            
            <div class="chart-container">
                <h4>📈 Key Insights</h4>
                <div id="insights-content">
                    <p><strong>🎯 Response Efficiency:</strong> 85% of requests resolved within target time</p>
                    <p><strong>🏘️ Most Affected Area:</strong> Dhanmondi Area (23 requests)</p>
                    <p><strong>⚡ Peak Request Time:</strong> 2:00-4:00 PM</p>
                    <p><strong>🏠 Shelter Utilization:</strong> {{ $percentage }}% average capacity</p>
                    <p><strong>📊 Trend Analysis:</strong> +12% increase from last period</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Leaflet JavaScript -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/leaflet.heat@0.2.0/dist/leaflet-heat.js"></script>
    <script>
        // Sample data for heat map
        let map;
        let heatLayer;
        let shelterMarkers = [];
        
        function initHeatMap() {
            // Initialize map centered on Dhaka, Bangladesh
            map = L.map('heat-map').setView([23.8103, 90.4125], 11);
            
            // Add tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);
            
            // Generate sample heat map data
            const heatData = generateSampleHeatData();
            
            // Create heat layer
            heatLayer = L.heatLayer(heatData, {
                radius: 25,
                blur: 15,
                maxZoom: 17,
                gradient: {
                    0.0: '#ffeb3b',
                    0.3: '#ff9800', 
                    0.6: '#f44336',
                    1.0: '#9c27b0'
                }
            }).addTo(map);
            
            // Add shelter markers
            addShelterMarkers();
        }
        
        function generateSampleHeatData() {
            const data = [];
            const basePoints = [
                [23.8103, 90.4125, 0.8], // Dhaka center
                [23.7806, 90.4193, 0.9], // Old Dhaka
                [23.8223, 90.3654, 0.7], // Dhanmondi
                [23.7461, 90.3742, 0.6], // Wari
                [23.8338, 90.4265, 0.5], // Tejgaon
                [23.7936, 90.4057, 1.0], // Motijheel
                [23.7570, 90.3830, 0.8], // Lalbagh
                [23.8059, 90.3683, 0.6], // New Market
                [23.7697, 90.4092, 0.7], // Sadarghat
                [23.8240, 90.3782, 0.5]  // Shyamoli
            ];
            
            // Generate clusters around base points
            basePoints.forEach(([lat, lng, intensity]) => {
                const clusterSize = Math.floor(intensity * 30) + 5;
                for (let i = 0; i < clusterSize; i++) {
                    const offsetLat = (Math.random() - 0.5) * 0.02;
                    const offsetLng = (Math.random() - 0.5) * 0.02;
                    data.push([
                        lat + offsetLat,
                        lng + offsetLng,
                        Math.random() * intensity
                    ]);
                }
            });
            
            return data;
        }
        
        function addShelterMarkers() {
            const shelters = [
                { id: 1, name: "Central Emergency Shelter", latitude: 23.8103, longitude: 90.4125, capacity: 500, current_occupancy: 320 },
                { id: 2, name: "Dhanmondi Community Center", latitude: 23.8223, longitude: 90.3654, capacity: 300, current_occupancy: 180 },
                { id: 3, name: "Old Dhaka Relief Center", latitude: 23.7806, longitude: 90.4193, capacity: 400, current_occupancy: 380 },
                { id: 4, name: "Tejgaon Emergency Hub", latitude: 23.8338, longitude: 90.4265, capacity: 250, current_occupancy: 100 },
                { id: 5, name: "Motijheel Aid Station", latitude: 23.7936, longitude: 90.4057, capacity: 350, current_occupancy: 280 }
            ];
            
            shelters.forEach(shelter => {
                const occupancyPercent = (shelter.current_occupancy / shelter.capacity) * 100;
                let markerColor = '#27ae60';
                
                if (occupancyPercent >= 90) {
                    markerColor = '#e74c3c';
                } else if (occupancyPercent >= 70) {
                    markerColor = '#f39c12';
                }
                
                const marker = L.marker([shelter.latitude, shelter.longitude], {
                    icon: L.divIcon({
                        className: 'shelter-marker',
                        html: `<div style="background: ${markerColor}; width: 30px; height: 30px; border-radius: 4px; border: 3px solid white; box-shadow: 0 3px 8px rgba(0,0,0,0.4); display: flex; align-items: center; justify-content: center; color: white; font-size: 16px;">🏠</div>`,
                        iconSize: [36, 36],
                        iconAnchor: [18, 18]
                    })
                }).bindPopup(`
                    <div style="min-width: 200px;">
                        <h4>${shelter.name}</h4>
                        <p><strong>Capacity:</strong> ${shelter.current_occupancy}/${shelter.capacity}</p>
                        <p><strong>Availability:</strong> ${shelter.capacity - shelter.current_occupancy} spaces</p>
                        <p><strong>Utilization:</strong> ${Math.round(occupancyPercent)}%</p>
                    </div>
                `).addTo(map);
                
                shelterMarkers.push(marker);
            });
        }
        
        function filterHeatMap() {
            const timeFilter = document.getElementById('time-filter').value;
            const typeFilter = document.getElementById('type-filter').value;
            const priorityFilter = document.getElementById('priority-filter').value;
            
            let filteredData = generateSampleHeatData();
            
            if (timeFilter === '24h') {
                filteredData = filteredData.slice(0, Math.floor(filteredData.length * 0.3));
            } else if (timeFilter === '7d') {
                filteredData = filteredData.slice(0, Math.floor(filteredData.length * 0.7));
            }
            
            if (priorityFilter === 'critical') {
                filteredData = filteredData.filter(point => point[2] > 0.7);
            } else if (priorityFilter === 'high') {
                filteredData = filteredData.filter(point => point[2] > 0.5);
            }
            
            map.removeLayer(heatLayer);
            heatLayer = L.heatLayer(filteredData, {
                radius: 25,
                blur: 15,
                maxZoom: 17,
                gradient: { 0.0: '#ffeb3b', 0.3: '#ff9800', 0.6: '#f44336', 1.0: '#9c27b0' }
            }).addTo(map);
        }
        
        function exportData() {
            const exportData = {
                timestamp: new Date().toISOString(),
                filters: {
                    time: document.getElementById('time-filter').value,
                    type: document.getElementById('type-filter').value,
                    priority: document.getElementById('priority-filter').value
                },
                summary: {
                    total_requests: {{ $analytics['requests']['total'] ?? 0 }},
                    active_shelters: {{ $analytics['shelters']['active'] ?? 0 }},
                    total_alerts: {{ $analytics['alerts']['total'] ?? 0 }}
                }
            };
            
            const dataStr = JSON.stringify(exportData, null, 2);
            const dataBlob = new Blob([dataStr], {type: 'application/json'});
            const url = URL.createObjectURL(dataBlob);
            const link = document.createElement('a');
            link.href = url;
            link.download = `emergency_analytics_${new Date().toISOString().split('T')[0]}.json`;
            link.click();
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            initHeatMap();
            
            document.getElementById('time-filter').addEventListener('change', filterHeatMap);
            document.getElementById('type-filter').addEventListener('change', filterHeatMap);
            document.getElementById('priority-filter').addEventListener('change', filterHeatMap);
            document.getElementById('refresh-heat-map').addEventListener('click', filterHeatMap);
            document.getElementById('export-data').addEventListener('click', exportData);
        });
    </script>
</body>
</html>