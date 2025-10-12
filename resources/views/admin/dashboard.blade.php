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
                Welcome, {{ Auth::user()->name }} (Administrator)
            </div>
            <a href="{{ route('auth.logout') }}" class="logout-btn">Logout</a>
        </div>
    </div>

    <div class="nav">
        <div class="nav-content">
            <a href="{{ route('admin.dashboard') }}" class="active">Dashboard</a>
            <a href="{{ route('admin.alerts') }}">Manage Alerts</a>
            <a href="{{ route('admin.shelters') }}">Manage Shelters</a>
            <a href="{{ route('admin.requests') }}">Manage Requests</a>
            <a href="{{ route('admin.analytics') }}">Analytics</a>
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
                <a href="{{ route('admin.requests') }}" class="action-btn primary">📋 Review Requests</a>
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

    <!-- Real-time Notifications Container -->
    <div id="notifications-container" style="
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1000;
        max-width: 400px;
    "></div>

    <!-- Pusher JavaScript SDK -->
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script>
        // Initialize Pusher
        const pusher = new Pusher('emergency-key', {
            cluster: 'mt1',
            forceTLS: true,
            enabledTransports: ['ws', 'wss', 'xhr_polling', 'xhr_streaming'],
            disabledTransports: []
        });

        // Subscribe to emergency requests channel
        const channel = pusher.subscribe('emergency-requests');

        // Listen for new request submissions
        channel.bind('new.request.submitted', function(data) {
            console.log('New emergency request received:', data);
            
            // Show real-time notification
            showEmergencyNotification(data);
            
            // Play notification sound for critical requests
            if (data.urgency === 'Critical' || data.urgency === 'High') {
                playNotificationSound();
            }
            
            // Update dashboard statistics in real-time
            updateDashboardStats();
        });

        // Function to show emergency notification
        function showEmergencyNotification(request) {
            const container = document.getElementById('notifications-container');
            
            // Create notification element
            const notification = document.createElement('div');
            notification.style.cssText = `
                background: linear-gradient(135deg, #e74c3c, #c0392b);
                color: white;
                padding: 20px;
                border-radius: 10px;
                margin-bottom: 10px;
                box-shadow: 0 4px 20px rgba(231, 76, 60, 0.3);
                transform: translateX(100%);
                transition: transform 0.5s ease-in-out;
                border-left: 5px solid #fff;
            `;
            
            // Determine urgency color
            let urgencyColor = '#f39c12'; // Medium
            if (request.urgency === 'Critical') urgencyColor = '#e74c3c';
            else if (request.urgency === 'High') urgencyColor = '#e67e22';
            else if (request.urgency === 'Low') urgencyColor = '#27ae60';
            
            notification.innerHTML = `
                <div style="display: flex; align-items: center; margin-bottom: 10px;">
                    <div style="font-size: 24px; margin-right: 10px;">🚨</div>
                    <div>
                        <div style="font-weight: bold; font-size: 16px;">New Emergency Request</div>
                        <div style="font-size: 12px; opacity: 0.9;">Request #${request.id}</div>
                    </div>
                </div>
                <div style="margin-bottom: 8px;">
                    <strong>👤 Name:</strong> ${request.name}
                </div>
                <div style="margin-bottom: 8px;">
                    <strong>📍 Location:</strong> ${request.location}
                </div>
                <div style="margin-bottom: 8px;">
                    <strong>🚨 Type:</strong> ${request.request_type}
                </div>
                <div style="margin-bottom: 8px;">
                    <strong>⚡ Priority:</strong> 
                    <span style="
                        background: ${urgencyColor};
                        color: white;
                        padding: 2px 8px;
                        border-radius: 12px;
                        font-size: 12px;
                        font-weight: bold;
                    ">${request.urgency}</span>
                </div>
                <div style="margin-bottom: 10px;">
                    <strong>👥 People:</strong> ${request.people_count} person(s)
                </div>
                <div style="display: flex; gap: 10px; margin-top: 15px;">
                    <button onclick="viewRequest(${request.id})" style="
                        background: #3498db;
                        color: white;
                        border: none;
                        padding: 8px 16px;
                        border-radius: 5px;
                        cursor: pointer;
                        font-size: 12px;
                    ">👁️ View Details</button>
                    <button onclick="assignRequest(${request.id})" style="
                        background: #27ae60;
                        color: white;
                        border: none;
                        padding: 8px 16px;
                        border-radius: 5px;
                        cursor: pointer;
                        font-size: 12px;
                    ">🏠 Assign Shelter</button>
                    <button onclick="closeNotification(this)" style="
                        background: #95a5a6;
                        color: white;
                        border: none;
                        padding: 8px 16px;
                        border-radius: 5px;
                        cursor: pointer;
                        font-size: 12px;
                    ">✖️ Close</button>
                </div>
            `;
            
            container.appendChild(notification);
            
            // Animate in
            setTimeout(() => {
                notification.style.transform = 'translateX(0)';
            }, 100);
            
            // Auto-remove after 30 seconds
            setTimeout(() => {
                if (notification.parentNode) {
                    closeNotification(notification);
                }
            }, 30000);
        }

        // Function to play notification sound
        function playNotificationSound() {
            // Create audio context for notification sound
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();
            
            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);
            
            oscillator.frequency.setValueAtTime(800, audioContext.currentTime);
            oscillator.frequency.setValueAtTime(600, audioContext.currentTime + 0.1);
            oscillator.frequency.setValueAtTime(800, audioContext.currentTime + 0.2);
            
            gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.3);
            
            oscillator.start(audioContext.currentTime);
            oscillator.stop(audioContext.currentTime + 0.3);
        }

        // Function to update dashboard statistics
        function updateDashboardStats() {
            // Update pending requests count
            const pendingElement = document.querySelector('.stat-number');
            if (pendingElement) {
                const currentCount = parseInt(pendingElement.textContent) || 0;
                pendingElement.textContent = currentCount + 1;
                
                // Add animation effect
                pendingElement.style.transform = 'scale(1.2)';
                pendingElement.style.color = '#e74c3c';
                setTimeout(() => {
                    pendingElement.style.transform = 'scale(1)';
                    pendingElement.style.color = '';
                }, 300);
            }
        }

        // Utility functions
        function viewRequest(requestId) {
            window.open(`/requests/${requestId}`, '_blank');
        }

        function assignRequest(requestId) {
            window.open(`/admin/requests/${requestId}/assign`, '_blank');
        }

        function closeNotification(element) {
            const notification = element.closest('div[style*="background: linear-gradient"]') || element;
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 500);
        }

        // Connection status monitoring
        pusher.connection.bind('connected', function() {
            console.log('✅ Real-time connection established');
            
            // Update system health indicator
            const healthIndicator = document.querySelector('div:contains("Notifications: Active")');
            if (healthIndicator) {
                healthIndicator.style.color = '#27ae60';
            }
        });

        pusher.connection.bind('disconnected', function() {
            console.log('❌ Real-time connection lost');
            
            // Show connection status notification
            const container = document.getElementById('notifications-container');
            const connectionAlert = document.createElement('div');
            connectionAlert.innerHTML = `
                <div style="
                    background: #f39c12;
                    color: white;
                    padding: 15px;
                    border-radius: 8px;
                    margin-bottom: 10px;
                ">
                    ⚠️ Real-time connection lost. Attempting to reconnect...
                </div>
            `;
            container.appendChild(connectionAlert);
        });

        // Error handling
        pusher.connection.bind('error', function(err) {
            console.error('Pusher connection error:', err);
        });

        console.log('🚀 Emergency Real-time Notification System Initialized');
    </script>
</body>
</html>
