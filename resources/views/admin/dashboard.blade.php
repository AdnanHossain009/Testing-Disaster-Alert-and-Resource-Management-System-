<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Admin Dashboard - Disaster Alert System</title>
    @include('admin.partials.dark-theme-styles')
    <style>
        /* Page-specific overrides - kept minimal as dark theme handles most styling */
        .welcome-section {
            background: #091F57;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid rgba(43, 85, 189, 0.2);
        }
        .welcome-section h2 {
            color: #E4E8F5;
        }
        .welcome-section p {
            color: #E4E8F5;
            opacity: 0.9;
        }
        .stat-card.alerts {
            border-left-color: #ff6b6b;
        }
        .stat-card.shelters {
            border-left-color: #51cf66;
        }
        .stat-card.requests {
            border-left-color: #4ecdc4;
        }
        .dashboard-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
        }
        @media (max-width: 768px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }
        .activity-feed, .quick-actions {
            background: #091F57;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border: 1px solid rgba(43, 85, 189, 0.2);
        }
        .section-title {
            font-size: 1.2rem;
            font-weight: bold;
            color: #E4E8F5;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid rgba(43, 85, 189, 0.3);
        }
        .activity-item {
            display: flex;
            align-items: center;
            padding: 1rem;
            border-bottom: 1px solid rgba(43, 85, 189, 0.2);
            gap: 1rem;
            background: rgba(43, 85, 189, 0.05);
            border-radius: 6px;
            margin-bottom: 0.5rem;
        }
        .activity-item:last-child {
            border-bottom: none;
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
            flex-shrink: 0;
        }
        .activity-icon.high, .activity-icon.critical {
            background-color: #ff6b6b;
        }
        .activity-icon.medium {
            background-color: #ffa94d;
        }
        .activity-icon.low {
            background-color: #51cf66;
        }
        .activity-content {
            flex: 1;
        }
        .activity-message {
            font-weight: bold;
            color: #E4E8F5;
            margin-bottom: 0.25rem;
        }
        .activity-time {
            font-size: 0.8rem;
            color: #E4E8F5;
            opacity: 0.7;
        }
        .action-btn {
            display: block;
            background-color: #2B55BD;
            color: white;
            padding: 0.75rem 1rem;
            text-decoration: none;
            border-radius: 6px;
            margin-bottom: 0.5rem;
            text-align: center;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        .action-btn:hover {
            background-color: #3d6fd4;
            transform: translateY(-2px);
        }
        .action-btn.primary {
            background-color: #2B55BD;
        }
        .action-btn.danger {
            background-color: #ff6b6b;
        }
        .action-btn.success {
            background-color: #51cf66;
            color: #091F57;
        }
        .action-btn.warning {
            background-color: #ffa94d;
            color: #091F57;
        }
        .system-status {
            background: linear-gradient(135deg, #51cf66, #69db7c);
            color: #091F57;
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1rem;
            text-align: center;
            font-weight: bold;
        }
    </style>
</head>
<body>
    @section('page_title', '�️ Admin Control Panel')
    @include('admin.partials.header')

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
                <div class="stat-number" data-stat="total-alerts">{{ $stats['total_alerts'] }}</div>
                <div class="stat-label">Total Alerts</div>
            </div>
            <div class="stat-card alerts">
                <div class="stat-number" data-stat="active-alerts">{{ $stats['active_alerts'] }}</div>
                <div class="stat-label">Active Alerts</div>
            </div>
            <div class="stat-card shelters">
                <div class="stat-number" data-stat="total-shelters">{{ $stats['total_shelters'] }}</div>
                <div class="stat-label">Total Shelters</div>
            </div>
            <div class="stat-card shelters">
                <div class="stat-number" data-stat="available-shelters">{{ $stats['available_shelters'] }}</div>
                <div class="stat-label">Available Shelters</div>
            </div>
            <div class="stat-card requests">
                <div class="stat-number" data-stat="pending">{{ $stats['pending_requests'] }}</div>
                <div class="stat-label">Pending Requests</div>
            </div>
            <div class="stat-card requests">
                <div class="stat-number" data-stat="in-progress">{{ $stats['assigned_requests'] }}</div>
                <div class="stat-label">In Progress</div>
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
                <a href="{{ route('admin.notifications') }}" class="action-btn">� Notification Settings</a>
                <a href="#" class="action-btn">� View Analytics</a>
                <a href="#" class="action-btn">�️ Map Overview</a>

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
            console.log('❌ Real-time connection lost - Using auto-refresh fallback');
            
            // DISABLED: Toast notification (annoying when Pusher not configured)
            // Auto-refresh fallback handles updates automatically every 2 minutes
            // No need to show error since page will refresh and get latest data
        });

        // Error handling
        pusher.connection.bind('error', function(err) {
            console.error('Pusher connection error:', err);
        });

        console.log('🚀 Emergency Real-time Notification System Initialized');
    </script>

    <!-- Live Dashboard Updates Styles -->
    <style>
        /* New row animation */
        @keyframes slideInFromTop {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        .new-row-animation {
            animation: slideInFromTop 0.5s ease-out;
        }
        
        /* Highlight row effect */
        .highlight-row {
            background-color: #fff3cd !important;
            transition: background-color 0.3s ease;
        }
        
        /* Status badge pulse animation */
        @keyframes statusPulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
        }
        
        .status-update-pulse {
            animation: statusPulse 0.6s ease-in-out;
        }
        
        /* Stat counter animation */
        @keyframes statPulse {
            0%, 100% {
                transform: scale(1);
                color: inherit;
            }
            50% {
                transform: scale(1.15);
                color: #3498db;
            }
        }
        
        .stat-update-pulse {
            animation: statPulse 0.6s ease-in-out;
            font-weight: bold;
        }
        
        /* Update indicator */
        .update-indicator {
            position: fixed;
            top: 80px;
            right: -350px;
            background: #3498db;
            color: white;
            padding: 12px 20px;
            border-radius: 6px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transition: right 0.3s ease-in-out;
            z-index: 1000;
            font-size: 14px;
            max-width: 300px;
        }
        
        .update-indicator.show {
            right: 20px;
        }
        
        .update-indicator.success {
            background: #27ae60;
        }
        
        .update-indicator.info {
            background: #3498db;
        }
        
        .update-indicator.warning {
            background: #f39c12;
        }
        
        /* Status badges */
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-pending {
            background: #ffeaa7;
            color: #d63031;
        }
        
        .status-assigned,
        .status-in-progress {
            background: #74b9ff;
            color: #0984e3;
        }
        
        .status-completed {
            background: #55efc4;
            color: #00b894;
        }
        
        .status-cancelled {
            background: #dfe6e9;
            color: #636e72;
        }
        
        /* Urgency badges */
        .urgency-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .urgency-critical {
            background: #ff7675;
            color: #d63031;
        }
        
        .urgency-high {
            background: #fdcb6e;
            color: #e17055;
        }
        
        .urgency-medium {
            background: #74b9ff;
            color: #0984e3;
        }
        
        .urgency-low {
            background: #dfe6e9;
            color: #636e72;
        }
        
        /* Live indicator dot */
        .live-indicator {
            display: inline-block;
            width: 8px;
            height: 8px;
            background: #27ae60;
            border-radius: 50%;
            margin-right: 6px;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.3;
            }
        }
    </style>

    <!-- Include Live Dashboard JavaScript -->
    @vite(['resources/js/live-dashboard.js'])
</body>
</html>
