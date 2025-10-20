<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>My Notifications - Disaster Alert System</title>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #0D1326;
            min-height: 100vh;
            padding: 2rem;
            color: #E4E8F5;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
        }

        .header {
            background: #091F57;
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(43, 85, 189, 0.2);
        }

        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .header-title {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .header-title h1 {
            font-size: 2rem;
            color: #E4E8F5;
            font-weight: 600;
        }

        .unseen-badge {
            background: linear-gradient(135deg, #2B55BD, #3d6fd4);
            color: white;
            padding: 0.4rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(43, 85, 189, 0.3);
        }

        .header-actions {
            display: flex;
            gap: 1rem;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            border: none;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: #2B55BD;
            box-shadow: 0 2px 8px rgba(43, 85, 189, 0.3);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            background-color: #3d6fd4;
            box-shadow: 0 6px 20px rgba(43, 85, 189, 0.5);
        }

        .btn-outline {
            background: rgba(43, 85, 189, 0.1);
            border: 2px solid rgba(43, 85, 189, 0.4);
            color: #E4E8F5;
        }

        .btn-outline:hover {
            border-color: #2B55BD;
            color: #2B55BD;
            background: rgba(43, 85, 189, 0.2);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .stat-card {
            background: linear-gradient(135deg, #091F57, #0D1326);
            padding: 1.25rem;
            border-radius: 12px;
            border: 2px solid rgba(43, 85, 189, 0.2);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            border-color: #2B55BD;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(43, 85, 189, 0.3);
        }

        .stat-label {
            font-size: 0.85rem;
            color: #E4E8F5;
            opacity: 0.9;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: #2B55BD;
            text-shadow: 0 0 10px rgba(43, 85, 189, 0.3);
        }

        .filters {
            background: #091F57;
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(43, 85, 189, 0.2);
        }

        .filter-tabs {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .filter-tab {
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            background: rgba(43, 85, 189, 0.1);
            color: #E4E8F5;
            border: 2px solid transparent;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
            text-decoration: none;
        }

        .filter-tab:hover {
            background: rgba(43, 85, 189, 0.2);
        }

        .filter-tab.active {
            background: linear-gradient(135deg, #2B55BD, #3d6fd4);
            color: white;
            border-color: transparent;
            box-shadow: 0 2px 8px rgba(43, 85, 189, 0.4);
        }

        .notifications-list {
            background: #091F57;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(43, 85, 189, 0.2);
        }

        .notification-item {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(43, 85, 189, 0.2);
            display: flex;
            gap: 1.25rem;
            align-items: start;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .notification-item:last-child {
            border-bottom: none;
        }

        .notification-item:hover {
            background: rgba(43, 85, 189, 0.1);
        }

        .notification-item.unseen {
            background: linear-gradient(90deg, rgba(43, 85, 189, 0.15), rgba(43, 85, 189, 0.05));
            border-left: 4px solid #2B55BD;
        }

        .notification-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        .notification-content {
            flex: 1;
        }

        .notification-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 0.5rem;
        }

        .notification-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #E4E8F5;
            margin-bottom: 0.25rem;
        }

        .notification-time {
            font-size: 0.85rem;
            color: #E4E8F5;
            opacity: 0.7;
            white-space: nowrap;
        }

        .notification-message {
            color: #E4E8F5;
            opacity: 0.9;
            line-height: 1.6;
            margin-bottom: 0.75rem;
        }

        .notification-footer {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .notification-type {
            font-size: 0.8rem;
            padding: 0.35rem 0.75rem;
            border-radius: 6px;
            background: rgba(43, 85, 189, 0.2);
            color: #E4E8F5;
            text-transform: capitalize;
        }

        .btn-mark-read {
            font-size: 0.85rem;
            padding: 0.4rem 1rem;
            background: transparent;
            border: 1px solid rgba(43, 85, 189, 0.4);
            color: #2B55BD;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-mark-read:hover {
            background: #2B55BD;
            color: white;
            border-color: #2B55BD;
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: #E4E8F5;
            opacity: 0.7;
        }

        .empty-state-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }

        .empty-state-text {
            font-size: 1.25rem;
            color: #E4E8F5;
        }

        .pagination {
            background: #091F57;
            border-radius: 16px;
            padding: 1.5rem;
            margin-top: 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            display: flex;
            justify-content: center;
            border: 1px solid rgba(43, 85, 189, 0.2);
        }

        .success-message {
            background: linear-gradient(135deg, #51cf66, #4cbb5e);
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            box-shadow: 0 4px 20px rgba(72, 187, 120, 0.3);
        }

        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }

            .header-top {
                flex-direction: column;
                align-items: stretch;
                gap: 1rem;
            }

            .header-actions {
                flex-direction: column;
            }

            .stats-grid {
                grid-template-columns: 1fr 1fr;
            }

            .notification-item {
                flex-direction: column;
            }

            .notification-header {
                flex-direction: column;
                gap: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Success Message -->
        @if(session('success'))
        <div class="success-message">
            <span style="font-size: 1.5rem;">✓</span>
            <span>{{ session('success') }}</span>
        </div>
        @endif

        <!-- Header -->
        <div class="header">
            <div class="header-top">
                <div class="header-title">
                    <h1>📬 My Notifications</h1>
                    @if($unseenCount > 0)
                    <span class="unseen-badge">{{ $unseenCount }} New</span>
                    @endif
                </div>
                <div class="header-actions">
                    @if($unseenCount > 0)
                    <form action="{{ route('notifications.citizen.read-all') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-primary">Mark All as Read</button>
                    </form>
                    @endif
                    <a href="{{ route('citizen.dashboard') }}" class="btn btn-outline">← Back to Dashboard</a>
                </div>
            </div>

            <!-- Statistics -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Total Notifications</div>
                    <div class="stat-value">{{ $stats['total'] }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">New Messages</div>
                    <div class="stat-value" style="color: #43e97b;">{{ $stats['unseen'] }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">My Requests</div>
                    <div class="stat-value" style="color: #48bb78;">{{ $stats['request_submitted'] }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Shelter Updates</div>
                    <div class="stat-value" style="color: #4299e1;">{{ $stats['shelter_assigned'] + $stats['status_updated'] }}</div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="filters">
            <div class="filter-tabs">
                <a href="{{ route('citizen.inbox', ['filter' => 'all']) }}" 
                   class="filter-tab {{ $filter == 'all' ? 'active' : '' }}">
                    All Notifications
                </a>
                <a href="{{ route('citizen.inbox', ['filter' => 'unseen']) }}" 
                   class="filter-tab {{ $filter == 'unseen' ? 'active' : '' }}">
                    New Only
                </a>
                <a href="{{ route('citizen.inbox', ['filter' => 'request_submitted']) }}" 
                   class="filter-tab {{ $filter == 'request_submitted' ? 'active' : '' }}">
                    📝 My Requests
                </a>
                <a href="{{ route('citizen.inbox', ['filter' => 'shelter_assigned']) }}" 
                   class="filter-tab {{ $filter == 'shelter_assigned' ? 'active' : '' }}">
                    🏠 Shelter Assigned
                </a>
                <a href="{{ route('citizen.inbox', ['filter' => 'status_updated']) }}" 
                   class="filter-tab {{ $filter == 'status_updated' ? 'active' : '' }}">
                    🔄 Status Updates
                </a>
            </div>
        </div>

        <!-- Notifications List -->
        <div class="notifications-list">
            @forelse($notifications as $notification)
            <div class="notification-item {{ $notification->seen ? '' : 'unseen' }}" 
                 data-id="{{ $notification->id }}">
                <div class="notification-icon" style="background: {{ $notification->color }}20; color: {{ $notification->color }};">
                    {!! $notification->icon !!}
                </div>
                <div class="notification-content">
                    <div class="notification-header">
                        <div>
                            <div class="notification-title">{{ $notification->title }}</div>
                            <div class="notification-time">{{ $notification->time_ago }}</div>
                        </div>
                    </div>
                    <div class="notification-message">{{ $notification->message }}</div>
                    <div class="notification-footer">
                        <span class="notification-type">{{ str_replace('_', ' ', $notification->type) }}</span>
                        @if(!$notification->seen)
                        <button class="btn-mark-read" onclick="markAsRead({{ $notification->id }})">
                            Mark as Read
                        </button>
                        @endif
                        @if($notification->reference_id && $notification->reference_type == 'App\\Models\\HelpRequest')
                        <a href="{{ route('requests.show', $notification->reference_id) }}" 
                           style="color: #43e97b; text-decoration: none; font-size: 0.85rem;">
                            View Request →
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="empty-state">
                <div class="empty-state-icon">📭</div>
                <div class="empty-state-text">No notifications yet</div>
                <p style="margin-top: 1rem; color: #718096;">You'll receive notifications when you submit requests or when your status is updated.</p>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($notifications->hasPages())
        <div class="pagination">
            {{ $notifications->links() }}
        </div>
        @endif
    </div>

    <script>
        async function markAsRead(id) {
            try {
                const response = await fetch(`/notifications/${id}/read`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (response.ok) {
                    const item = document.querySelector(`[data-id="${id}"]`);
                    item.classList.remove('unseen');
                    item.querySelector('.btn-mark-read')?.remove();
                    
                    // Update unseen count
                    const badge = document.querySelector('.unseen-badge');
                    if (badge) {
                        const count = parseInt(badge.textContent);
                        if (count <= 1) {
                            badge.remove();
                            const markAllBtn = document.querySelector('button[type="submit"]');
                            if (markAllBtn) markAllBtn.parentElement.remove();
                        } else {
                            badge.textContent = (count - 1) + ' New';
                        }
                    }
                }
            } catch (error) {
                console.error('Error marking notification as read:', error);
            }
        }

        // Auto-refresh unseen count every 30 seconds
        setInterval(async () => {
            try {
                const response = await fetch('/api/notifications/unseen-count');
                const data = await response.json();
                const badge = document.querySelector('.unseen-badge');
                
                if (data.count > 0 && !badge) {
                    // Reload page if new notifications arrived
                    location.reload();
                } else if (badge && data.count !== parseInt(badge.textContent)) {
                    badge.textContent = data.count + ' New';
                }
            } catch (error) {
                console.error('Error fetching unseen count:', error);
            }
        }, 30000);
    </script>
</body>
</html>
