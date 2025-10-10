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
        .btn-assign { background: #9f7aea; color: white; }
        .btn-status { background: #38b2ac; color: white; }
        .bulk-actions { background: white; padding: 1rem; border-radius: 8px; margin-bottom: 1rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .bulk-form { display: flex; gap: 1rem; align-items: center; }
        .bulk-form select { padding: 0.5rem; border: 1px solid #e2e8f0; border-radius: 5px; }
        .request-checkbox { margin-right: 0.5rem; }
        #select-all { margin-right: 0.5rem; }
        .emergency-type { background: #e53e3e; color: white; padding: 2px 8px; border-radius: 12px; font-size: 0.8em; }
        .status { padding: 2px 8px; border-radius: 12px; font-size: 0.8em; font-weight: bold; }
        .status.pending { background: #fed7d7; color: #822727; }
        .status.assigned { background: #bee3f8; color: #2a4365; }
        .status.completed { background: #c6f6d5; color: #1a202c; }
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); }
        .modal-content { background-color: white; margin: 15% auto; padding: 20px; border-radius: 8px; width: 80%; max-width: 500px; }
        .close { color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer; }
        .close:hover { color: black; }
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
        @if(session('success'))
            <div style="background: #c6f6d5; color: #1a202c; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div style="background: #fed7d7; color: #822727; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                {{ session('error') }}
            </div>
        @endif

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

        <!-- Bulk Actions -->
        <div class="bulk-actions">
            <h3>📦 Bulk Operations</h3>
            <form class="bulk-form" method="POST" action="{{ route('admin.requests.bulk-assign') }}">
                @csrf
                <label>
                    <input type="checkbox" id="select-all"> Select All
                </label>
                <span id="selected-count">0 selected</span>
                <select name="shelter_id" required>
                    <option value="">Select Shelter for Bulk Assignment</option>
                    @foreach($availableShelters ?? [] as $shelter)
                        <option value="{{ $shelter->id }}">
                            {{ $shelter->name }} ({{ $shelter->current_occupancy }}/{{ $shelter->capacity }})
                        </option>
                    @endforeach
                </select>
                <input type="text" name="admin_notes" placeholder="Assignment notes (optional)">
                <button type="submit" class="btn btn-success" id="bulk-assign-btn" disabled>
                    🏠 Bulk Assign
                </button>
            </form>
        </div>

        <div class="requests-table">
            <table>
                <thead>
                    <tr>
                        <th>
                            <input type="checkbox" id="select-all-header">
                        </th>
                        <th>ID</th>
                        <th>Citizen</th>
                        <th>Contact</th>
                        <th>Type</th>
                        <th>People</th>
                        <th>Status</th>
                        <th>Assigned Shelter</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($helpRequests as $request)
                    <tr data-request-id="{{ $request->id }}">
                        <td>
                            <input type="checkbox" name="request_ids[]" value="{{ $request->id }}" class="request-checkbox">
                        </td>
                        <td>{{ $request->id }}</td>
                        <td>{{ $request->name }}</td>
                        <td>{{ $request->phone }}</td>
                        <td>
                            <span class="emergency-type">{{ $request->request_type }}</span>
                        </td>
                        <td>{{ $request->people_count ?? 1 }}</td>
                        <td>
                            <span class="status {{ strtolower($request->status) }}">{{ $request->status }}</span>
                        </td>
                        <td>{{ $request->assignment ? $request->assignment->shelter->name : 'Not Assigned' }}</td>
                        <td>{{ $request->created_at->format('M d, H:i') }}</td>
                        <td class="actions">
                            @if($request->status === 'Pending')
                                <a href="{{ route('admin.requests.assign', $request->id) }}" class="btn btn-assign">
                                    📋 Assign
                                </a>
                            @endif
                            
                            <button onclick="showStatusModal({{ $request->id }}, '{{ $request->status }}')" class="btn btn-status">
                                🔄 Status
                            </button>
                            
                            <a href="{{ route('requests.show', $request->id) }}" class="btn btn-primary">View</a>
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

    <!-- Status Update Modal -->
    <div id="statusModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeStatusModal()">&times;</span>
            <h3>Update Request Status</h3>
            <form id="statusForm" method="POST">
                @csrf
                @method('PUT')
                <div style="margin: 1rem 0;">
                    <label>Status:</label>
                    <select name="status" id="statusSelect" style="width: 100%; padding: 0.5rem; margin-top: 0.5rem;">
                        <option value="Pending">Pending</option>
                        <option value="Assigned">Assigned</option>
                        <option value="In Progress">In Progress</option>
                        <option value="Completed">Completed</option>
                        <option value="Cancelled">Cancelled</option>
                    </select>
                </div>
                <div style="margin: 1rem 0;">
                    <label>Admin Notes:</label>
                    <textarea name="admin_notes" rows="3" style="width: 100%; padding: 0.5rem; margin-top: 0.5rem;" placeholder="Add status update notes..."></textarea>
                </div>
                <div style="text-align: right; margin-top: 1rem;">
                    <button type="button" onclick="closeStatusModal()" class="btn" style="background: #gray; margin-right: 1rem;">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Bulk actions
        document.getElementById('select-all').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.request-checkbox');
            checkboxes.forEach(cb => cb.checked = this.checked);
            updateSelectedCount();
        });

        document.getElementById('select-all-header').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.request-checkbox');
            checkboxes.forEach(cb => cb.checked = this.checked);
            updateSelectedCount();
        });

        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('request-checkbox')) {
                updateSelectedCount();
            }
        });

        function updateSelectedCount() {
            const selected = document.querySelectorAll('.request-checkbox:checked');
            const count = selected.length;
            document.getElementById('selected-count').textContent = count + ' selected';
            document.getElementById('bulk-assign-btn').disabled = count === 0;
            
            // Update bulk form with selected IDs
            const form = document.querySelector('.bulk-form');
            const existingInputs = form.querySelectorAll('input[name="request_ids[]"]');
            existingInputs.forEach(input => input.remove());
            
            selected.forEach(checkbox => {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'request_ids[]';
                hiddenInput.value = checkbox.value;
                form.appendChild(hiddenInput);
            });
        }

        // Status modal functions
        function showStatusModal(requestId, currentStatus) {
            document.getElementById('statusModal').style.display = 'block';
            document.getElementById('statusForm').action = `/admin/requests/${requestId}/status`;
            document.getElementById('statusSelect').value = currentStatus;
        }

        function closeStatusModal() {
            document.getElementById('statusModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('statusModal');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        }
    </script>
</body>
</html>