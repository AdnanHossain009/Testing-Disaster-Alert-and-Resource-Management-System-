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
            background: #0D1326;
            min-height: 100vh;
            color: #E4E8F5;
        }
        .header {
            background: #091F57;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
            flex-wrap: wrap;
            gap: 1rem;
        }
        .header h1 {
            color: #E4E8F5;
            font-size: 1.8rem;
        }
        .user-info {
            color: #E4E8F5;
            opacity: 0.9;
            margin-right: 1rem;
        }
        .logout-btn {
            background: #ff6b6b;
            color: white;
            padding: 0.5rem 1rem;
            text-decoration: none;
            border-radius: 5px;
            transition: all 0.3s;
            font-weight: 500;
        }
        .logout-btn:hover {
            background: #ff8787;
            transform: translateY(-2px);
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
        @media (max-width: 768px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }
        .card {
            background: #091F57;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.3);
            border: 1px solid rgba(43, 85, 189, 0.2);
        }
        .card h3 {
            color: #E4E8F5;
            margin-bottom: 1rem;
            font-size: 1.3rem;
        }
        .card h4 {
            color: #E4E8F5;
            margin-bottom: 0.5rem;
        }
        .card p {
            color: #E4E8F5;
            opacity: 0.9;
            line-height: 1.6;
        }
        .shelter-item {
            padding: 1rem;
            border: 1px solid rgba(43, 85, 189, 0.3);
            margin-bottom: 1rem;
            border-radius: 5px;
            background: rgba(43, 85, 189, 0.1);
        }
        .task-item {
            padding: 0.75rem;
            border-left: 4px solid #51cf66;
            background: rgba(81, 207, 102, 0.1);
            margin-bottom: 0.5rem;
            border-radius: 0 5px 5px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn {
            background: #51cf66;
            color: #091F57;
            padding: 0.5rem 1rem;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            transition: all 0.3s;
            font-size: 0.9rem;
            font-weight: 500;
            box-shadow: 0 2px 8px rgba(81, 207, 102, 0.3);
        }
        .btn:hover {
            background: #69db7c;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(81, 207, 102, 0.5);
        }
        .status-active { color: #51cf66; font-weight: bold; text-shadow: 0 1px 3px rgba(81, 207, 102, 0.3); }
        .occupancy-high { color: #ff6b6b; font-weight: bold; }
        .occupancy-medium { color: #ffa94d; font-weight: bold; }
        .occupancy-low { color: #51cf66; }
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