<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Shelters</title>
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
        .shelters-table { background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .shelters-table table { width: 100%; border-collapse: collapse; }
        .shelters-table th, .shelters-table td { padding: 1rem; text-align: left; border-bottom: 1px solid #e2e8f0; }
        .shelters-table th { background: #f7fafc; font-weight: 600; }
        .status-active { color: #48bb78; font-weight: bold; }
        .status-inactive { color: #e53e3e; }
        .capacity-high { color: #e53e3e; }
        .capacity-medium { color: #ed8936; }
        .capacity-low { color: #48bb78; }
        .btn { padding: 0.5rem 1rem; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn-primary { background: #4299e1; color: white; }
        .btn-success { background: #48bb78; color: white; }
        .btn-warning { background: #ed8936; color: white; }
    </style>
</head>
<body>
    <div class="admin-header">
        <h1>🏠 Admin Panel - Manage Shelters</h1>
        <div>
            <span>Welcome, {{ Auth::user()->name }}</span>
            <a href="{{ route('auth.logout') }}" style="margin-left: 1rem; color: #fed7d7;">Logout</a>
        </div>
    </div>

    <div class="admin-nav">
        <a href="{{ route('admin.dashboard') }}">Dashboard</a>
        <a href="{{ route('admin.alerts') }}">Manage Alerts</a>
        <a href="{{ route('admin.shelters') }}" class="active">Manage Shelters</a>
        <a href="{{ route('admin.requests') }}">Manage Requests</a>
        <a href="{{ route('admin.analytics') }}">Analytics</a>
    </div>

    <div class="container">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number">{{ $stats['total_shelters'] }}</div>
                <div class="stat-label">Total Shelters</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $stats['active_shelters'] }}</div>
                <div class="stat-label">Active Shelters</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $stats['available_shelters'] }}</div>
                <div class="stat-label">Available Shelters</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $stats['total_capacity'] }}</div>
                <div class="stat-label">Total Capacity</div>
            </div>
        </div>

        <div class="shelters-table">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Location</th>
                        <th>Capacity</th>
                        <th>Occupancy</th>
                        <th>Status</th>
                        <th>Contact</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($shelters as $shelter)
                    @php
                        $occupancyRate = $shelter->capacity > 0 ? ($shelter->current_occupancy / $shelter->capacity) * 100 : 0;
                        $occupancyClass = $occupancyRate >= 90 ? 'capacity-high' : ($occupancyRate >= 70 ? 'capacity-medium' : 'capacity-low');
                    @endphp
                    <tr>
                        <td>{{ $shelter->id }}</td>
                        <td>{{ $shelter->name }}</td>
                        <td>{{ $shelter->city }}, {{ $shelter->state }}</td>
                        <td>{{ $shelter->capacity }}</td>
                        <td class="{{ $occupancyClass }}">{{ $shelter->current_occupancy }}/{{ $shelter->capacity }} ({{ round($occupancyRate) }}%)</td>
                        <td class="status-{{ strtolower($shelter->status) }}">{{ $shelter->status }}</td>
                        <td>{{ $shelter->contact_phone }}</td>
                        <td>
                            <a href="{{ route('shelters.show', $shelter->id) }}" class="btn btn-primary">View</a>
                            <button class="btn btn-success">Edit</button>
                            <button class="btn btn-warning">Update Capacity</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="margin-top: 2rem;">
            {{ $shelters->links() }}
        </div>
    </div>
</body>
</html>