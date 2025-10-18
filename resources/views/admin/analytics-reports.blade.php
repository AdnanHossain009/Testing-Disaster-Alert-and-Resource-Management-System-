<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics Dashboard - Disaster Alert System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
            margin: 0;
            padding: 0;
        }
        .analytics-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .header h1 {
            margin: 0 0 10px 0;
            font-size: 2rem;
        }
        .header p {
            margin: 0;
            opacity: 0.9;
        }
        .export-buttons {
            float: right;
            margin-top: -10px;
        }
        .export-btn {
            background: white;
            color: #667eea;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            margin-left: 10px;
            font-weight: bold;
            display: inline-block;
            transition: all 0.3s;
        }
        .export-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .stat-value {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .stat-label {
            color: #666;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .stat-icon {
            font-size: 2rem;
            float: right;
        }
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .chart-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .chart-card h3 {
            margin-top: 0;
            color: #333;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }
        .chart-container {
            position: relative;
            height: 300px;
        }
        .table-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .table-card h3 {
            margin-top: 0;
            color: #333;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table th {
            background: #f8f9fa;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: #333;
            border-bottom: 2px solid #dee2e6;
        }
        table td {
            padding: 12px;
            border-bottom: 1px solid #dee2e6;
        }
        table tr:hover {
            background: #f8f9fa;
        }
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .badge-critical { background: #fee; color: #e74c3c; }
        .badge-high { background: #fef5e7; color: #f39c12; }
        .badge-moderate { background: #fef9e7; color: #f1c40f; }
        .badge-low { background: #ecf0f1; color: #95a5a6; }
        .badge-active { background: #d5f4e6; color: #27ae60; }
        .badge-pending { background: #fef5e7; color: #f39c12; }
        .badge-completed { background: #d5f4e6; color: #27ae60; }
        .badge-in-progress { background: #e3f2fd; color: #2196f3; }
        .progress-bar {
            width: 100%;
            height: 20px;
            background: #ecf0f1;
            border-radius: 10px;
            overflow: hidden;
        }
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #3498db, #2980b9);
            text-align: center;
            color: white;
            font-size: 0.75rem;
            line-height: 20px;
            transition: width 0.5s;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="analytics-container">
        <a href="{{ route('admin.dashboard') }}" class="back-link">← Back to Dashboard</a>
        
        <div class="header">
            <div class="export-buttons">
                <a href="{{ route('analytics.export.pdf') }}" class="export-btn" title="Download PDF Report">
                    📄 Export PDF
                </a>
                <a href="{{ route('analytics.export.txt') }}" class="export-btn" title="Download Text Report">
                    📝 Export TXT
                </a>
            </div>
            <h1>📊 Analytics Dashboard</h1>
            <p>Comprehensive disaster management system analytics and reports</p>
        </div>

        <!-- Summary Statistics -->
        <div class="stats-grid">
            <div class="stat-card" style="border-left: 4px solid #e74c3c;">
                <span class="stat-icon">🚨</span>
                <div class="stat-value" style="color: #e74c3c;">{{ $alerts['total'] }}</div>
                <div class="stat-label">Total Alerts</div>
                <small>{{ $alerts['active'] }} Active</small>
            </div>

            <div class="stat-card" style="border-left: 4px solid #3498db;">
                <span class="stat-icon">🏠</span>
                <div class="stat-value" style="color: #3498db;">{{ $shelters['total'] }}</div>
                <div class="stat-label">Total Shelters</div>
                <small>{{ $shelters['available_space'] }} spaces available</small>
            </div>

            <div class="stat-card" style="border-left: 4px solid #f39c12;">
                <span class="stat-icon">📋</span>
                <div class="stat-value" style="color: #f39c12;">{{ $requests['total'] }}</div>
                <div class="stat-label">Emergency Requests</div>
                <small>{{ $requests['pending'] }} Pending</small>
            </div>

            <div class="stat-card" style="border-left: 4px solid #9b59b6;">
                <span class="stat-icon">👥</span>
                <div class="stat-value" style="color: #9b59b6;">{{ $requests['total_people'] }}</div>
                <div class="stat-label">People Affected</div>
                <small>Needing assistance</small>
            </div>
        </div>

        <!-- Charts Row 1 -->
        <div class="charts-grid">
            <!-- Alerts by Severity -->
            <div class="chart-card">
                <h3>🚨 Alerts by Severity</h3>
                <div class="chart-container">
                    <canvas id="alertsSeverityChart"></canvas>
                </div>
            </div>

            <!-- Requests by Status -->
            <div class="chart-card">
                <h3>📊 Requests by Status</h3>
                <div class="chart-container">
                    <canvas id="requestsStatusChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Charts Row 2 -->
        <div class="charts-grid">
            <!-- Requests by Type -->
            <div class="chart-card">
                <h3>📋 Requests by Type</h3>
                <div class="chart-container">
                    <canvas id="requestsTypeChart"></canvas>
                </div>
            </div>

            <!-- Trends Over Time -->
            <div class="chart-card">
                <h3>📈 Requests Trend (Last 7 Days)</h3>
                <div class="chart-container">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Shelter Capacity -->
        <div class="table-card">
            <h3>🏠 Shelter Capacity Overview</h3>
            <div style="margin-bottom: 20px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                    <span><strong>Overall Occupancy Rate:</strong></span>
                    <span><strong>{{ $shelters['occupancy_rate'] }}%</strong></span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: {{ $shelters['occupancy_rate'] }}%;">
                        {{ $shelters['total_occupied'] }}/{{ $shelters['total_capacity'] }}
                    </div>
                </div>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>Shelter Name</th>
                        <th>Location</th>
                        <th>Capacity</th>
                        <th>Occupied</th>
                        <th>Available</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($shelters['list'] as $shelter)
                    <tr>
                        <td><strong>{{ $shelter->name }}</strong></td>
                        <td>{{ $shelter->city }}</td>
                        <td>{{ $shelter->capacity }}</td>
                        <td>{{ $shelter->current_occupancy }}</td>
                        <td>
                            <strong style="color: {{ $shelter->capacity - $shelter->current_occupancy > 0 ? '#27ae60' : '#e74c3c' }};">
                                {{ $shelter->capacity - $shelter->current_occupancy }}
                            </strong>
                        </td>
                        <td>
                            <span class="badge badge-{{ strtolower(str_replace(' ', '-', $shelter->status)) }}">
                                {{ $shelter->status }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Recent Requests -->
        <div class="table-card">
            <h3>📋 Recent Emergency Requests</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Location</th>
                        <th>People</th>
                        <th>Urgency</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($requests['recent'] as $request)
                    <tr>
                        <td>#{{ $request->id }}</td>
                        <td>{{ $request->name }}</td>
                        <td>{{ $request->request_type }}</td>
                        <td>{{ $request->location }}</td>
                        <td>{{ $request->people_count }}</td>
                        <td>
                            <span class="badge badge-{{ strtolower($request->urgency) }}">
                                {{ $request->urgency }}
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-{{ strtolower(str_replace(' ', '-', $request->status)) }}">
                                {{ $request->status }}
                            </span>
                        </td>
                        <td>{{ $request->created_at->format('M d, Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Alerts by Severity Chart (Pie)
        const alertsSeverityCtx = document.getElementById('alertsSeverityChart').getContext('2d');
        new Chart(alertsSeverityCtx, {
            type: 'pie',
            data: {
                labels: {!! json_encode(array_keys($alerts['by_severity'])) !!},
                datasets: [{
                    data: {!! json_encode(array_values($alerts['by_severity'])) !!},
                    backgroundColor: ['#e74c3c', '#f39c12', '#f1c40f', '#95a5a6'],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Requests by Status Chart (Doughnut)
        const requestsStatusCtx = document.getElementById('requestsStatusChart').getContext('2d');
        new Chart(requestsStatusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'Assigned', 'In Progress', 'Completed', 'Cancelled'],
                datasets: [{
                    data: [
                        {{ $requests['pending'] }},
                        {{ $requests['assigned'] }},
                        {{ $requests['in_progress'] }},
                        {{ $requests['completed'] }},
                        {{ $requests['cancelled'] }}
                    ],
                    backgroundColor: ['#f39c12', '#3498db', '#9b59b6', '#27ae60', '#95a5a6'],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Requests by Type Chart (Bar)
        const requestsTypeCtx = document.getElementById('requestsTypeChart').getContext('2d');
        new Chart(requestsTypeCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode(array_keys($requests['by_type'])) !!},
                datasets: [{
                    label: 'Number of Requests',
                    data: {!! json_encode(array_values($requests['by_type'])) !!},
                    backgroundColor: '#3498db',
                    borderColor: '#2980b9',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        // Trend Chart (Line)
        const trendCtx = document.getElementById('trendChart').getContext('2d');
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode(array_column($trends, 'date')) !!},
                datasets: [{
                    label: 'Requests',
                    data: {!! json_encode(array_column($trends, 'requests')) !!},
                    borderColor: '#e74c3c',
                    backgroundColor: 'rgba(231, 76, 60, 0.1)',
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'Assignments',
                    data: {!! json_encode(array_column($trends, 'assignments')) !!},
                    borderColor: '#27ae60',
                    backgroundColor: 'rgba(39, 174, 96, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
</body>
</html>
