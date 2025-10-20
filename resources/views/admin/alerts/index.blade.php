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
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: #0D1326;
            color: #E4E8F5;
            min-height: 100vh;
        }
        .admin-header { 
            background: #091F57;
            color: #E4E8F5;
            padding: 1.2rem 2rem; 
            display: flex; 
            justify-content: space-between; 
            align-items: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
            flex-wrap: wrap;
            gap: 1rem;
        }
        .admin-header h1 {
            font-size: clamp(1.2rem, 3vw, 1.8rem);
        }
        .admin-nav { 
            background: #091F57;
            padding: 0.8rem 2rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            overflow-x: auto;
            white-space: nowrap;
        }
        .admin-nav a { 
            color: #E4E8F5;
            text-decoration: none; 
            margin-right: 1.5rem; 
            padding: 0.6rem 1.2rem; 
            border-radius: 6px;
            transition: all 0.3s ease;
            display: inline-block;
            font-size: clamp(0.85rem, 2vw, 0.95rem);
        }
        .admin-nav a:hover { 
            background: rgba(43, 85, 189, 0.3);
        }
        .admin-nav a.active { 
            background: #2B55BD;
            box-shadow: 0 2px 8px rgba(43, 85, 189, 0.4);
        }
        .container { 
            max-width: 1400px; 
            margin: 2rem auto; 
            padding: 0 1rem;
        }
        @media (max-width: 768px) {
            .container {
                margin: 1rem auto;
                padding: 0 0.5rem;
            }
        }
        .stats-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); 
            gap: 1.2rem; 
            margin-bottom: 2rem;
        }
        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
        .stat-card { 
            background: linear-gradient(135deg, #091F57 0%, #0D1326 100%);
            padding: 1.8rem; 
            border-radius: 12px;
            border: 1px solid rgba(43, 85, 189, 0.2);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 6px 16px rgba(43, 85, 189, 0.4);
        }
        .stat-number { 
            font-size: clamp(2rem, 5vw, 2.5rem);
            font-weight: bold; 
            color: #2B55BD;
            text-shadow: 0 2px 4px rgba(43, 85, 189, 0.3);
        }
        .stat-label { 
            color: #E4E8F5;
            margin-top: 0.5rem;
            font-size: clamp(0.85rem, 2vw, 0.95rem);
            opacity: 0.9;
        }
        .alerts-table { 
            background: #091F57;
            border-radius: 12px; 
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(43, 85, 189, 0.2);
        }
        @media (max-width: 768px) {
            .alerts-table {
                overflow-x: auto;
            }
        }
        .alerts-table table { 
            width: 100%; 
            border-collapse: collapse;
            min-width: 600px;
        }
        .alerts-table th, .alerts-table td { 
            padding: 1.2rem; 
            text-align: left; 
            border-bottom: 1px solid rgba(43, 85, 189, 0.2);
        }
        .alerts-table th { 
            background: rgba(43, 85, 189, 0.2);
            font-weight: 600;
            color: #E4E8F5;
            font-size: clamp(0.85rem, 2vw, 0.95rem);
        }
        .alerts-table td {
            color: #E4E8F5;
            font-size: clamp(0.8rem, 2vw, 0.9rem);
        }
        .alerts-table tr:hover {
            background: rgba(43, 85, 189, 0.1);
        }
        .severity-high { 
            color: #ff6b6b;
            font-weight: bold;
            text-shadow: 0 1px 3px rgba(255, 107, 107, 0.3);
        }
        .severity-medium { 
            color: #ffa94d;
            font-weight: bold;
            text-shadow: 0 1px 3px rgba(255, 169, 77, 0.3);
        }
        .severity-critical { 
            color: #ff5252;
            font-weight: bold;
            text-shadow: 0 1px 3px rgba(255, 82, 82, 0.3);
        }
        .status-active { 
            color: #51cf66;
            text-shadow: 0 1px 3px rgba(81, 207, 102, 0.3);
        }
        .status-expired { 
            color: #868e96;
        }
        .btn { 
            padding: 0.6rem 1.2rem; 
            border: none; 
            border-radius: 6px; 
            cursor: pointer; 
            text-decoration: none; 
            display: inline-block;
            transition: all 0.3s ease;
            font-size: clamp(0.8rem, 2vw, 0.9rem);
            font-weight: 500;
        }
        .btn-primary { 
            background: #2B55BD;
            color: #E4E8F5;
            box-shadow: 0 2px 8px rgba(43, 85, 189, 0.3);
        }
        .btn-primary:hover {
            background: #3d6fd4;
            box-shadow: 0 4px 12px rgba(43, 85, 189, 0.5);
            transform: translateY(-2px);
        }
        .btn-success { 
            background: #51cf66;
            color: #091F57;
            box-shadow: 0 2px 8px rgba(81, 207, 102, 0.3);
        }
        .btn-success:hover {
            background: #69db7c;
            box-shadow: 0 4px 12px rgba(81, 207, 102, 0.5);
            transform: translateY(-2px);
        }
        .btn-danger { 
            background: #ff6b6b;
            color: #E4E8F5;
            box-shadow: 0 2px 8px rgba(255, 107, 107, 0.3);
        }
        .btn-danger:hover {
            background: #ff8787;
            box-shadow: 0 4px 12px rgba(255, 107, 107, 0.5);
            transform: translateY(-2px);
        }
        .action-buttons {
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .action-buttons h2 {
            color: #E4E8F5;
            font-size: clamp(1.3rem, 4vw, 1.8rem);
        }
        .alert-message {
            padding: 1rem 1.5rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            border-left: 4px solid;
            font-size: clamp(0.85rem, 2vw, 0.95rem);
        }
        .alert-success {
            background: rgba(81, 207, 102, 0.1);
            border-color: #51cf66;
            color: #51cf66;
        }
        .alert-error {
            background: rgba(255, 107, 107, 0.1);
            border-color: #ff6b6b;
            color: #ff6b6b;
        }
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