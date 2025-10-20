<div class="admin-header">
    <h1>@yield('page_title', '🏠 Admin Panel')</h1>
    <div style="display: flex; align-items: center; gap: 1.5rem;">
        <a href="{{ route('admin.inbox') }}" style="position: relative; text-decoration: none; color: #E4E8F5; font-size: 1.5rem;">
            🔔
            @php
                $unseenCount = \App\Models\InAppNotification::forAdmin()->unseen()->count();
            @endphp
            @if($unseenCount > 0)
            <span style="position: absolute; top: -8px; right: -8px; background: #2B55BD; color: #E4E8F5; border-radius: 50%; width: 20px; height: 20px; font-size: 0.7rem; display: flex; align-items: center; justify-content: center; font-weight: bold; box-shadow: 0 2px 4px rgba(43, 85, 189, 0.4);">
                {{ $unseenCount }}
            </span>
            @endif
        </a>
        <span style="color: #E4E8F5;">Welcome, {{ Auth::user()->name }}</span>
        <a href="{{ route('auth.logout') }}" style="color: #E4E8F5; opacity: 0.8; transition: opacity 0.3s;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.8'">Logout</a>
    </div>
</div>

<div class="admin-nav">
    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">📊 Dashboard</a>
    <a href="{{ route('admin.alerts') }}" class="{{ request()->routeIs('admin.alerts*') ? 'active' : '' }}">🚨 Manage Alerts</a>
    <a href="{{ route('admin.shelters') }}" class="{{ request()->routeIs('admin.shelters*') ? 'active' : '' }}">🏠 Manage Shelters</a>
    <a href="{{ route('admin.requests') }}" class="{{ request()->routeIs('admin.requests*') ? 'active' : '' }}">📋 Manage Requests</a>
    <a href="{{ route('admin.analytics') }}" class="{{ request()->routeIs('admin.analytics*') ? 'active' : '' }}">📈 Analytics</a>
    <a href="{{ route('admin.inbox') }}" class="{{ request()->routeIs('admin.inbox*') ? 'active' : '' }}">📬 Notifications</a>
</div>
