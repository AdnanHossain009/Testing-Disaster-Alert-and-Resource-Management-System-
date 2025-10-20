<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Notification Settings - Disaster Alert System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #0D1326;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: #091F57;
            color: #E4E8F5;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid rgba(43, 85, 189, 0.3);
        }
        .header h1, .header p {
            color: #E4E8F5;
        }
        .settings-card {
            background: #091F57;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(43, 85, 189, 0.3);
        }
        .settings-card h2 {
            color: #E4E8F5;
        }
        .setting-item {
            padding: 15px 0;
            border-bottom: 1px solid rgba(43, 85, 189, 0.3);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .setting-item:last-child {
            border-bottom: none;
        }
        .setting-label {
            flex: 1;
        }
        .setting-label h3 {
            margin: 0 0 5px 0;
            font-size: 16px;
            color: #E4E8F5;
        }
        .setting-label p {
            margin: 0;
            font-size: 14px;
            color: rgba(228, 232, 245, 0.7);
        }
        .toggle-switch {
            position: relative;
            width: 60px;
            height: 34px;
        }
        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }
        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        input:checked + .slider {
            background-color: #27ae60;
        }
        input:checked + .slider:before {
            transform: translateX(26px);
        }
        .time-input {
            padding: 8px 12px;
            border: 2px solid rgba(43, 85, 189, 0.4);
            border-radius: 4px;
            font-size: 14px;
            background: #0D1326;
            color: #E4E8F5;
        }
        .time-input:focus {
            outline: none;
            border-color: #2B55BD;
        }
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: all 0.3s;
        }
        .btn-primary {
            background: #2B55BD;
            color: white;
        }
        .btn-primary:hover {
            background: #3d6fd4;
            transform: translateY(-2px);
        }
        .btn-success {
            background: #51cf66;
            color: #091F57;
        }
        .btn-success:hover {
            background: #69db7c;
            transform: translateY(-2px);
        }
        .btn-danger {
            background: #ff6b6b;
            color: white;
        }
        .btn-danger:hover {
            background: #ff8787;
            transform: translateY(-2px);
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-enabled {
            background: rgba(81, 207, 102, 0.2);
            color: #51cf66;
        }
        .status-disabled {
            background: rgba(255, 107, 107, 0.2);
            color: #ff6b6b;
        }
        .status-pending {
            background: rgba(255, 169, 77, 0.2);
            color: #ffa94d;
        }
        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔔 Notification Settings</h1>
            <p>Manage your emergency alert notification preferences</p>
        </div>

        <!-- Permission Status -->
        <div class="settings-card">
            <h2>📱 Notification Status</h2>
            <div class="setting-item">
                <div class="setting-label">
                    <h3>Browser Permissions</h3>
                    <p id="permission-status">Checking...</p>
                </div>
                <span id="permission-badge" class="status-badge status-pending">Pending</span>
            </div>
            <div class="setting-item">
                <div class="setting-label">
                    <h3>Push Subscription</h3>
                    <p id="subscription-status">Not subscribed</p>
                </div>
                <span id="subscription-badge" class="status-badge status-disabled">Disabled</span>
            </div>
        </div>

        <!-- Notification Preferences -->
        <div class="settings-card">
            <h2>⚙️ Notification Preferences</h2>
            
            <div class="setting-item">
                <div class="setting-label">
                    <h3>Enable Push Notifications</h3>
                    <p>Receive notifications even when browser is closed</p>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" id="toggle-notifications">
                    <span class="slider"></span>
                </label>
            </div>

            <div class="setting-item">
                <div class="setting-label">
                    <h3>New Emergency Requests</h3>
                    <p>Get notified when new emergencies are submitted</p>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" id="notify-new-request" checked>
                    <span class="slider"></span>
                </label>
            </div>

            <div class="setting-item">
                <div class="setting-label">
                    <h3>Status Changes</h3>
                    <p>Get notified when request statuses are updated</p>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" id="notify-status-change" checked>
                    <span class="slider"></span>
                </label>
            </div>

            <div class="setting-item">
                <div class="setting-label">
                    <h3>Critical Alerts Only</h3>
                    <p>Only receive notifications for critical emergencies</p>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" id="notify-critical">
                    <span class="slider"></span>
                </label>
            </div>

            <div class="setting-item">
                <div class="setting-label">
                    <h3>Notification Sound</h3>
                    <p>Play sound with notifications</p>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" id="sound-enabled" checked>
                    <span class="slider"></span>
                </label>
            </div>
        </div>

        <!-- Do Not Disturb -->
        <div class="settings-card">
            <h2>🔕 Do Not Disturb</h2>
            
            <div class="setting-item">
                <div class="setting-label">
                    <h3>Enable Quiet Hours</h3>
                    <p>Mute notifications during specified hours</p>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" id="toggle-dnd">
                    <span class="slider"></span>
                </label>
            </div>

            <div class="setting-item">
                <div class="setting-label">
                    <h3>Quiet Hours Start</h3>
                    <p>Time when quiet hours begin</p>
                </div>
                <input type="time" id="quiet-start" class="time-input" value="22:00">
            </div>

            <div class="setting-item">
                <div class="setting-label">
                    <h3>Quiet Hours End</h3>
                    <p>Time when quiet hours end</p>
                </div>
                <input type="time" id="quiet-end" class="time-input" value="08:00">
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="settings-card">
            <h2>🧪 Testing & Actions</h2>
            <div class="action-buttons">
                <button onclick="requestPermission()" class="btn btn-primary">
                    🔔 Enable Notifications
                </button>
                <button onclick="testNotification()" class="btn btn-success">
                    🧪 Send Test Notification
                </button>
                <button onclick="unsubscribe()" class="btn btn-danger">
                    🔕 Disable Notifications
                </button>
            </div>
        </div>
    </div>

    <script src="/service-worker.js"></script>
    @vite(['resources/js/push-notifications.js'])

    <script>
        // Update UI with current status
        function updateUI() {
            const manager = window.pushNotificationManager;
            
            // Update permission status
            const permission = manager.getPermissionStatus();
            const permissionStatus = document.getElementById('permission-status');
            const permissionBadge = document.getElementById('permission-badge');
            
            if (permission === 'granted') {
                permissionStatus.textContent = 'Notifications allowed';
                permissionBadge.textContent = 'Enabled';
                permissionBadge.className = 'status-badge status-enabled';
            } else if (permission === 'denied') {
                permissionStatus.textContent = 'Notifications blocked';
                permissionBadge.textContent = 'Blocked';
                permissionBadge.className = 'status-badge status-disabled';
            } else {
                permissionStatus.textContent = 'Permission not requested';
                permissionBadge.textContent = 'Pending';
                permissionBadge.className = 'status-badge status-pending';
            }
            
            // Update subscription status
            const subscriptionStatus = document.getElementById('subscription-status');
            const subscriptionBadge = document.getElementById('subscription-badge');
            
            if (manager.isEnabled()) {
                subscriptionStatus.textContent = 'Active and receiving notifications';
                subscriptionBadge.textContent = 'Active';
                subscriptionBadge.className = 'status-badge status-enabled';
            } else {
                subscriptionStatus.textContent = 'Not subscribed to push notifications';
                subscriptionBadge.textContent = 'Inactive';
                subscriptionBadge.className = 'status-badge status-disabled';
            }
            
            // Load preferences into UI
            const prefs = manager.preferences;
            document.getElementById('notify-new-request').checked = prefs.notifyOnNewRequest;
            document.getElementById('notify-status-change').checked = prefs.notifyOnStatusChange;
            document.getElementById('notify-critical').checked = prefs.notifyOnCritical;
            document.getElementById('sound-enabled').checked = prefs.soundEnabled;
            document.getElementById('toggle-dnd').checked = prefs.doNotDisturb;
            document.getElementById('quiet-start').value = prefs.quietHoursStart;
            document.getElementById('quiet-end').value = prefs.quietHoursEnd;
        }

        // Request notification permission
        async function requestPermission() {
            const manager = window.pushNotificationManager;
            const success = await manager.requestPermission();
            
            if (success) {
                alert('✅ Notifications enabled successfully!');
            } else {
                alert('❌ Could not enable notifications. Please check your browser settings.');
            }
            
            updateUI();
        }

        // Send test notification
        async function testNotification() {
            const manager = window.pushNotificationManager;
            
            if (!manager.isEnabled()) {
                alert('Please enable notifications first');
                return;
            }
            
            await manager.showTestNotification();
            alert('🧪 Test notification sent! Check your notifications.');
        }

        // Unsubscribe from notifications
        async function unsubscribe() {
            if (!confirm('Are you sure you want to disable notifications?')) {
                return;
            }
            
            const manager = window.pushNotificationManager;
            const success = await manager.unsubscribe();
            
            if (success) {
                alert('✅ Notifications disabled successfully');
            } else {
                alert('❌ Could not disable notifications');
            }
            
            updateUI();
        }

        // Save preferences when changed
        function savePreferences() {
            const manager = window.pushNotificationManager;
            
            manager.savePreferences({
                notifyOnNewRequest: document.getElementById('notify-new-request').checked,
                notifyOnStatusChange: document.getElementById('notify-status-change').checked,
                notifyOnCritical: document.getElementById('notify-critical').checked,
                soundEnabled: document.getElementById('sound-enabled').checked,
                doNotDisturb: document.getElementById('toggle-dnd').checked,
                quietHoursStart: document.getElementById('quiet-start').value,
                quietHoursEnd: document.getElementById('quiet-end').value
            });
            
            console.log('✅ Preferences saved');
        }

        // Initialize when ready
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(updateUI, 1000); // Wait for manager to initialize
            
            // Add change listeners
            document.querySelectorAll('input[type="checkbox"], input[type="time"]').forEach(input => {
                input.addEventListener('change', savePreferences);
            });
        });

        // Update UI periodically
        setInterval(updateUI, 5000);
    </script>
</body>
</html>
