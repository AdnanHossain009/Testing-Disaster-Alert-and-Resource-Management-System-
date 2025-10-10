<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relief Worker Dashboard - Disaster Alert System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
            min-height: 100vh;
        }
        .header {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header h1 {
            color: #2d3748;
            font-size: 1.8rem;
        }
        .user-info {
            color: #4a5568;
            margin-right: 1rem;
        }
        .logout-btn {
            background: #e53e3e;
            color: white;
            padding: 0.5rem 1rem;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
        }
        .logout-btn:hover {
            background: #c53030;
        }
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }
        .card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .card h3 {
            color: #2d3748;
            margin-bottom: 1rem;
        }
        .shelter-item {
            padding: 1rem;
            border: 1px solid #e2e8f0;
            margin-bottom: 1rem;
            border-radius: 5px;
            background: #f7fafc;
        }
        .task-item {
            padding: 0.75rem;
            border-left: 4px solid #48bb78;
            background: #f0fff4;
            margin-bottom: 0.5rem;
            border-radius: 0 5px 5px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn {
            background: #48bb78;
            color: white;
            padding: 0.5rem 1rem;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            transition: background 0.3s;
            font-size: 0.9rem;
        }
        .btn:hover {
            background: #38a169;
        }
        .status-active { color: #48bb78; font-weight: bold; }
        .occupancy-high { color: #e53e3e; }
        .occupancy-medium { color: #ed8936; }
        .occupancy-low { color: #48bb78; }
    </style>
</head>
<body>
    <div class="header">
        <h1>⚡ Relief Worker Dashboard</h1>
        <div>
            <span class="user-info">Welcome, {{ Auth::user()->name }}</span>
            <a href="{{ route('auth.logout') }}" class="logout-btn">Logout</a>
        </div>
    </div>

    <div class="container">
        <div class="dashboard-grid">
            <div class="card">
                <h3>🏠 Assigned Shelters</h3>
                @foreach($assignedShelters as $shelter)
                <div class="shelter-item">
                    <h4>{{ $shelter['name'] }}</h4>
                    <p><strong>Location:</strong> {{ $shelter['location'] }}</p>
                    <p><strong>Occupancy:</strong> 
                        <span class="occupancy-medium">{{ $shelter['current_occupancy'] }}</span>
                    </p>
                    <p><strong>Status:</strong> 
                        <span class="status-active">{{ $shelter['status'] }}</span>
                    </p>
                    <a href="{{ route('shelters.show', 1) }}" class="btn">Manage Shelter</a>
                </div>
                @endforeach
            </div>

            <div class="card">
                <h3>✅ Today's Tasks</h3>
                @foreach($taskList as $task)
                <div class="task-item">
                    <span>{{ $task }}</span>
                    <button class="btn">Mark Done</button>
                </div>
                @endforeach
            </div>

            <div class="card">
                <h3>📊 Quick Actions</h3>
                <div style="display: grid; gap: 1rem;">
                    <a href="{{ route('admin.requests') }}" class="btn">View Pending Requests</a>
                    <a href="{{ route('shelters.index') }}" class="btn">Update Shelter Status</a>
                    <a href="{{ route('alerts.index') }}" class="btn">Check Active Alerts</a>
                    <a href="#" class="btn">Report Emergency</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>