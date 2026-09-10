<header class="top-navbar">
    <div>
        <button class="nav-btn sidebar-toggle" onclick="document.getElementById('sidebar').classList.toggle('show')">
            <i class="fas fa-bars"></i>
        </button>
        <div class="page-title">@yield('title', 'Dashboard')</div>
        <div class="page-subtitle">@yield('subtitle', '')</div>
    </div>
    <div class="navbar-actions">
        <a href="{{ route('notifications.index') }}" class="nav-btn" title="Notifications"
            @if (!empty($hasNewNotifications)) style="border:2px solid #ef4444;border-radius:8px" @endif>
            <i class="fas fa-bell"></i>
            @if (!empty($hasNewNotifications))
                <span class="notif-dot"
                    style="background:#ef4444;border:2px solid white;display:inline-block;width:10px;height:10px;border-radius:50%;position:relative;top:-8px;left:-6px"></span>
            @else
                <span class="notif-dot"></span>
            @endif
        </a>
        <a href="{{ route('settings.index') }}" class="nav-btn" title="Settings"><i class="fas fa-cog"></i></a>
    </div>
</header>
