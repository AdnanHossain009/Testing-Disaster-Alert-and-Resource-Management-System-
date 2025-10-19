<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Admin - Manage Alerts</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f5f5; }
        .admin-header { background: #2d3748; color: white; padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; }
        .admin-nav { background: #4a5568; padding: 0.5rem 2rem; }
        .admin-nav a { color: white; text-decoration: none; margin-right: 2rem; padding: 0.5rem 1rem; border-radius: 5px; }
        .admin-nav a:hover, .admin-nav a.active { background: #2d3748; }
        .container { max-width: 1200px; margin: 2rem auto; padding: 0 1rem; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
        .stat-card { background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); text-align: center; }
        .stat-number { font-size: 2rem; font-weight: bold; color: #2d3748; }
        .stat-label { color: #718096; margin-top: 0.5rem; }
        .alerts-table { background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .alerts-table table { width: 100%; border-collapse: collapse; }
        .alerts-table th, .alerts-table td { padding: 1rem; text-align: left; border-bottom: 1px solid #e2e8f0; }
        .alerts-table th { background: #f7fafc; font-weight: 600; }
        .severity-high { color: #e53e3e; font-weight: bold; }
        .severity-medium { color: #ed8936; font-weight: bold; }
        .severity-critical { color: #9f1239; font-weight: bold; }
        .status-active { color: #48bb78; }
        .status-expired { color: #a0aec0; }
        .btn { padding: 0.5rem 1rem; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn-primary { background: #4299e1; color: white; }
        .btn-success { background: #48bb78; color: white; }
        .btn-danger { background: #e53e3e; color: white; }
    </style>
</head>
<body>
    @section('page_title', '�️ Admin Panel - Manage Alerts')
    @include('admin.partials.header')

    <div class="container">
        <!-- Action Buttons -->
        <div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center;">
            <h2>Alert Management</h2>
            <a href="{{ route('admin.alerts.create') }}" class="btn btn-success">
                ➕ Create New Alert
            </a>
        </div>

        @if(session('success'))
        <div style="background: #48bb78; color: white; padding: 1rem; border-radius: 5px; margin-bottom: 1rem;">
            {{ session('success') }}
        </div>
        @endif

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number">{{ $stats['total_alerts'] }}</div>
                <div class="stat-label">Total Alerts</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $stats['active_alerts'] }}</div>
                <div class="stat-label">Active Alerts</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $stats['high_severity'] }}</div>
                <div class="stat-label">High Severity</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $stats['critical_severity'] }}</div>
                <div class="stat-label">Critical Severity</div>
            </div>
        </div>

        <div class="alerts-table">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Severity</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($alerts as $alert)
                    <tr>
                        <td>{{ $alert->id }}</td>
                        <td>{{ $alert->title }}</td>
                        <td>{{ $alert->type }}</td>
                        <td class="severity-{{ strtolower($alert->severity) }}">{{ $alert->severity }}</td>
                        <td>{{ $alert->location }}</td>
                        <td class="status-{{ strtolower($alert->status) }}">{{ $alert->status }}</td>
                        <td>{{ $alert->created_at->format('M d, Y H:i') }}</td>
                        <td>
                            <a href="{{ route('alerts.show', $alert->id) }}" class="btn btn-primary">View</a>
                            <a href="{{ route('admin.alerts.edit', $alert->id) }}" class="btn btn-success">Edit</a>
                            <form method="POST" action="{{ route('admin.alerts.destroy', $alert->id) }}" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to delete this alert?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="margin-top: 2rem;">
            {{ $alerts->links() }}
        </div>
    </div>
</body>
</html>