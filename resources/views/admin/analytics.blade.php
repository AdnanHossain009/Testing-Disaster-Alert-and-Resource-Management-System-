<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Analytics</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f5f5; }
        .admin-header { background: #2d3748; color: white; padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; }
        .admin-nav { background: #4a5568; padding: 0.5rem 2rem; }
        .admin-nav a { color: white; text-decoration: none; margin-right: 2rem; padding: 0.5rem 1rem; border-radius: 5px; }
        .admin-nav a:hover, .admin-nav a.active { background: #2d3748; }
        .container { max-width: 1200px; margin: 2rem auto; padding: 0 1rem; }
        .analytics-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; }
        .analytics-card { background: white; border-radius: 8px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .analytics-card h3 { color: #2d3748; margin-bottom: 1rem; }
        .chart-container { height: 200px; display: flex; align-items: end; justify-content: space-around; border-bottom: 2px solid #e2e8f0; padding: 1rem 0; }
        .chart-bar { background: linear-gradient(to top, #4299e1, #63b3ed); margin: 0 5px; border-radius: 4px 4px 0 0; min-width: 40px; display: flex; align-items: end; justify-content: center; color: white; font-weight: bold; font-size: 0.8rem; }
        .chart-labels { display: flex; justify-content: space-around; margin-top: 0.5rem; }
        .chart-label { font-size: 0.8rem; color: #718096; text-align: center; }
        .summary-item { display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid #e2e8f0; }
        .summary-label { color: #4a5568; }
        .summary-value { font-weight: bold; color: #2d3748; }
    </style>
</head>
<body>
    <div class="admin-header">
        <h1>📊 Admin Panel - Analytics</h1>
        <div>
            <span>Welcome, {{ Auth::user()->name }}</span>
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
        <div class="analytics-grid">
            <!-- Alerts Analytics -->
            <div class="analytics-card">
                <h3>🚨 Alerts Overview</h3>
                <div class="summary-item">
                    <span class="summary-label">Total Alerts:</span>
                    <span class="summary-value">{{ $analytics['alerts']['total'] }}</span>
                </div>
                
                <h4 style="margin: 1rem 0 0.5rem 0; color: #4a5568;">By Severity:</h4>
                <div class="chart-container">
                    @foreach($analytics['alerts']['by_severity'] as $severity => $count)
                        <div class="chart-bar" style="height: {{ $count > 0 ? ($count / max($analytics['alerts']['by_severity']) * 150) : 5 }}px;">
                            {{ $count }}
                        </div>
                    @endforeach
                </div>
                <div class="chart-labels">
                    @foreach($analytics['alerts']['by_severity'] as $severity => $count)
                        <div class="chart-label">{{ $severity }}</div>
                    @endforeach
                </div>

                <h4 style="margin: 1rem 0 0.5rem 0; color: #4a5568;">By Type:</h4>
                @foreach($analytics['alerts']['by_type'] as $type => $count)
                <div class="summary-item">
                    <span class="summary-label">{{ $type }}:</span>
                    <span class="summary-value">{{ $count }}</span>
                </div>
                @endforeach
            </div>

            <!-- Requests Analytics -->
            <div class="analytics-card">
                <h3>📋 Requests Overview</h3>
                <div class="summary-item">
                    <span class="summary-label">Total Requests:</span>
                    <span class="summary-value">{{ $analytics['requests']['total'] }}</span>
                </div>
                
                <h4 style="margin: 1rem 0 0.5rem 0; color: #4a5568;">By Status:</h4>
                <div class="chart-container">
                    @foreach($analytics['requests']['by_status'] as $status => $count)
                        <div class="chart-bar" style="height: {{ $count > 0 ? ($count / max($analytics['requests']['by_status']) * 150) : 5 }}px;">
                            {{ $count }}
                        </div>
                    @endforeach
                </div>
                <div class="chart-labels">
                    @foreach($analytics['requests']['by_status'] as $status => $count)
                        <div class="chart-label">{{ $status }}</div>
                    @endforeach
                </div>

                <h4 style="margin: 1rem 0 0.5rem 0; color: #4a5568;">By Type:</h4>
                @foreach($analytics['requests']['by_type'] as $type => $count)
                <div class="summary-item">
                    <span class="summary-label">{{ $type }}:</span>
                    <span class="summary-value">{{ $count }}</span>
                </div>
                @endforeach
            </div>

            <!-- Shelters Analytics -->
            <div class="analytics-card">
                <h3>🏠 Shelters Overview</h3>
                <div class="summary-item">
                    <span class="summary-label">Total Shelters:</span>
                    <span class="summary-value">{{ $analytics['shelters']['total'] }}</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Active Shelters:</span>
                    <span class="summary-value">{{ $analytics['shelters']['active'] }}</span>
                </div>
                
                <h4 style="margin: 1rem 0 0.5rem 0; color: #4a5568;">Capacity Utilization:</h4>
                @php
                    $utilization = $analytics['shelters']['capacity_utilization'];
                    $percentage = $utilization->total > 0 ? round(($utilization->occupied / $utilization->total) * 100) : 0;
                @endphp
                <div class="summary-item">
                    <span class="summary-label">Occupied:</span>
                    <span class="summary-value">{{ $utilization->occupied }}</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Total Capacity:</span>
                    <span class="summary-value">{{ $utilization->total }}</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Utilization Rate:</span>
                    <span class="summary-value">{{ $percentage }}%</span>
                </div>
                
                <div style="background: #e2e8f0; height: 20px; border-radius: 10px; margin-top: 1rem; overflow: hidden;">
                    <div style="background: linear-gradient(to right, #48bb78, #4299e1); height: 100%; width: {{ $percentage }}%; border-radius: 10px;"></div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>