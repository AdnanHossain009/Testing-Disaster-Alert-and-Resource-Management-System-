<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Requests</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f5f5; }
        .admin-header { background: #2d3748; color: white; padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; }
        .admin-nav { background: #4a5568; padding: 0.5rem 2rem; }
        .admin-nav a { color: white; text-decoration: none; margin-right: 2rem; padding: 0.5rem 1rem; border-radius: 5px; }
        .admin-nav a:hover, .admin-nav a.active { background: #2d3748; }
        .container { max-width: 1200px; margin: 2rem auto; padding: 0 1rem; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
        .stat-card { background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); text-align: center; }
        .stat-number { font-size: 2rem; font-weight: bold; color: #2d3748; }
        .stat-label { color: #718096; margin-top: 0.5rem; }
        .requests-table { background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .requests-table table { width: 100%; border-collapse: collapse; }
        .requests-table th, .requests-table td { padding: 1rem; text-align: left; border-bottom: 1px solid #e2e8f0; }
        .requests-table th { background: #f7fafc; font-weight: 600; }
        .status-pending { color: #ed8936; font-weight: bold; }
        .status-assigned { color: #4299e1; font-weight: bold; }
        .status-completed { color: #48bb78; font-weight: bold; }
        .priority-critical { color: #e53e3e; font-weight: bold; }
        .priority-high { color: #ed8936; font-weight: bold; }
        .priority-medium { color: #4299e1; }
        .btn { padding: 0.5rem 1rem; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; margin-right: 0.5rem; }
        .btn-primary { background: #4299e1; color: white; }
        .btn-success { background: #48bb78; color: white; }
        .btn-warning { background: #ed8936; color: white; }
    </style>
</head>
<body>
    <div class="admin-header">
        <h1>📋 Admin Panel - Manage Requests</h1>
        <div>
            <span>Welcome, {{ Auth::user()->name }}</span>
            <a href="{{ route('auth.logout') }}" style="margin-left: 1rem; color: #fed7d7;">Logout</a>
        </div>
    </div>

    <div class="admin-nav">
        <a href="{{ route('admin.dashboard') }}">Dashboard</a>
        <a href="{{ route('admin.alerts') }}">Manage Alerts</a>
        <a href="{{ route('admin.shelters') }}">Manage Shelters</a>
        <a href="{{ route('admin.requests') }}" class="active">Manage Requests</a>
        <a href="{{ route('admin.analytics') }}">Analytics</a>
    </div>

    <div class="container">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number">{{ $stats['total_requests'] }}</div>
                <div class="stat-label">Total Requests</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $stats['pending_requests'] }}</div>
                <div class="stat-label">Pending</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $stats['assigned_requests'] }}</div>
                <div class="stat-label">Assigned</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $stats['completed_requests'] }}</div>
                <div class="stat-label">Completed</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $stats['critical_requests'] }}</div>
                <div class="stat-label">Critical</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $stats['high_requests'] }}</div>
                <div class="stat-label">High Priority</div>
            </div>
        </div>

        <div class="requests-table">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Citizen</th>
                        <th>Type</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Location</th>
                        <th>Assigned Shelter</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($helpRequests as $request)
                    <tr>
                        <td>{{ $request->id }}</td>
                        <td>{{ $request->name }}<br><small>{{ $request->phone }}</small></td>
                        <td>{{ $request->request_type }}</td>
                        <td class="priority-{{ strtolower($request->urgency) }}">{{ $request->urgency }}</td>
                        <td class="status-{{ strtolower($request->status) }}">{{ $request->status }}</td>
                        <td>{{ $request->location }}</td>
                        <td>{{ $request->assignment ? $request->assignment->shelter->name : 'Not assigned' }}</td>
                        <td>{{ $request->created_at->format('M d, H:i') }}</td>
                        <td>
                            <a href="{{ route('requests.show', $request->id) }}" class="btn btn-primary">View</a>
                            @if($request->status === 'Pending')
                                <button class="btn btn-success">Assign</button>
                            @endif
                            <button class="btn btn-warning">Edit</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="margin-top: 2rem;">
            {{ $helpRequests->links() }}
        </div>
    </div>
</body>
</html>