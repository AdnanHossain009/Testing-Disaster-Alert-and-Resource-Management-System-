<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Disaster Alert System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        .header {
            background: linear-gradient(135deg, #2c3e50, #34495e);
            color: white;
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header-left h1 {
            margin: 0;
            font-size: 1.5rem;
        }
        .header-right {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .user-info {
            color: #bdc3c7;
        }
        .logout-btn {
            background-color: #e74c3c;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.9rem;
        }
        .nav {
            background-color: #34495e;
            padding: 0.5rem 0;
        }
        .nav-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            gap: 2rem;
            padding: 0 1rem;
        }
        .nav a {
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
        }
        .nav a:hover, .nav a.active {
            background-color: #2c3e50;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }
        .welcome-section {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
            border-left: 4px solid;
        }
        .stat-card.alerts {
            border-left-color: #e74c3c;
        }
        .stat-card.shelters {
            border-left-color: #27ae60;
        }
        .stat-card.requests {
            border-left-color: #3498db;
        }
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        .stat-label {
            color: #7f8c8d;
            font-size: 0.9rem;
        }
        .dashboard-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
        }
        .activity-feed {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .quick-actions {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .section-title {
            font-size: 1.2rem;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #ecf0f1;
        }
        .activity-item {
            display: flex;
            align-items: center;
            padding: 1rem;
            border-bottom: 1px solid #ecf0f1;
            gap: 1rem;
        }
        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: white;
        }
        .activity-icon.high {
            background-color: #e74c3c;
        }
        .activity-icon.medium {
            background-color: #f39c12;
        }
        .activity-icon.low {
            background-color: #3498db;
        }
        .activity-content {
            flex: 1;
        }
        .activity-message {
            font-weight: bold;
            color: #2c3e50;
        }
        .activity-time {
            font-size: 0.8rem;
            color: #7f8c8d;
        }
        .action-btn {
            display: block;
            background-color: #3498db;
            color: white;
            padding: 0.75rem 1rem;
            text-decoration: none;
            border-radius: 6px;
            margin-bottom: 0.5rem;
            text-align: center;
            font-weight: bold;
        }
        .action-btn.primary {
            background-color: #2c3e50;
        }
        .action-btn.danger {
            background-color: #e74c3c;
        }
        .action-btn.success {
            background-color: #27ae60;
        }
        .system-status {
            background: linear-gradient(135deg, #27ae60, #2ecc71);
            color: white;
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1rem;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-left">
            <h1>🛡️ Admin Control Panel</h1>
        </div>
        <div class="header-right">
            <div class="user-info">
                Welcome, {{ session('user')['name'] }} (Administrator)
            </div>
            <a href="{{ route('auth.logout') }}" class="logout-btn">Logout</a>
        </div>
    </div>

    <div class="nav">
        <div class="nav-content">
            <a href="{{ route('admin.dashboard') }}" class="active">Dashboard</a>
            <a href="{{ route('alerts.index') }}">Manage Alerts</a>
            <a href="{{ route('shelters.index') }}">Manage Shelters</a>
            <a href="{{ route('requests.index') }}">Manage Requests</a>
            <a href="#">Analytics</a>
            <a href="#">Settings</a>
        </div>
    </div>

    <div class="container">
        <!-- Welcome Section -->
        <div class="welcome-section">
            <div>
                <h2>System Control Dashboard</h2>
                <p>Monitor and manage disaster response operations</p>
            </div>
            <div class="system-status">
                <strong>✅ All Systems Operational</strong><br>
                <small>Last check: 2 minutes ago</small>
            </div>
        </div>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card alerts">
                <div class="stat-number">{{ $stats['total_alerts'] }}</div>
                <div class="stat-label">Total Alerts</div>
            </div>
            <div class="stat-card alerts">
                <div class="stat-number">{{ $stats['active_alerts'] }}</div>
                <div class="stat-label">Active Alerts</div>
            </div>
            <div class="stat-card shelters">
                <div class="stat-number">{{ $stats['total_shelters'] }}</div>
                <div class="stat-label">Total Shelters</div>
            </div>
            <div class="stat-card shelters">
                <div class="stat-number">{{ $stats['available_shelters'] }}</div>
                <div class="stat-label">Available Shelters</div>
            </div>
            <div class="stat-card requests">
                <div class="stat-number">{{ $stats['pending_requests'] }}</div>
                <div class="stat-label">Pending Requests</div>
            </div>
            <div class="stat-card requests">
                <div class="stat-number">{{ $stats['assigned_requests'] }}</div>
                <div class="stat-label">Assigned Requests</div>
            </div>
        </div>

        <!-- Dashboard Grid -->
        <div class="dashboard-grid">
            <!-- Recent Activity -->
            <div class="activity-feed">
                <div class="section-title">📊 Recent Activity</div>
                
                @foreach($recentActivity as $activity)
                    <div class="activity-item">
                        <div class="activity-icon {{ $activity['priority'] }}">
                            @if($activity['type'] === 'request')
                                🚨
                            @elseif($activity['type'] === 'assignment')
                                🏠
                            @else
                                ⚠️
                            @endif
                        </div>
                        <div class="activity-content">
                            <div class="activity-message">{{ $activity['message'] }}</div>
                            <div class="activity-time">{{ $activity['time'] }}</div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions">
                <div class="section-title">⚡ Quick Actions</div>
                
                <a href="#" class="action-btn danger">🚨 Create New Alert</a>
                <a href="#" class="action-btn success">🏠 Add New Shelter</a>
                <a href="{{ route('requests.index') }}" class="action-btn primary">📋 Review Requests</a>
                <a href="#" class="action-btn">📊 View Analytics</a>
                <a href="#" class="action-btn">🗺️ Map Overview</a>
                <a href="#" class="action-btn">📱 Send Notifications</a>

                <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid #ecf0f1;">
                    <div class="section-title">📈 System Health</div>
                    <div style="font-size: 0.9rem; color: #7f8c8d;">
                        <div style="margin-bottom: 0.5rem;">✅ Alert System: Online</div>
                        <div style="margin-bottom: 0.5rem;">✅ Database: Connected</div>
                        <div style="margin-bottom: 0.5rem;">✅ Notifications: Active</div>
                        <div style="margin-bottom: 0.5rem;">✅ Auto-Assignment: Enabled</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
