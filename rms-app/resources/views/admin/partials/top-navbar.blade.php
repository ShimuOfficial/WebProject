<header class="top-navbar">
    <div>
        <button class="nav-btn sidebar-toggle" type="button" aria-label="Open menu" onclick="document.getElementById('sidebar').classList.toggle('show');document.body.classList.toggle('sidebar-open', document.getElementById('sidebar').classList.contains('show'))">
            <i class="fas fa-bars"></i>
        </button>
        <div class="page-title">@yield('title', 'Dashboard')</div>
        <div class="page-subtitle">@yield('subtitle', '')</div>
    </div>
    <div class="navbar-actions">
        <a href="{{ route('website.home') }}" class="btn btn-sm btn-outline-secondary" target="_blank" rel="noopener">View site</a>
        <a href="{{ route('notifications.index') }}" class="nav-btn" title="Notifications"
            @if (!empty($hasNewNotifications)) style="border:2px solid #c2413b" @endif>
            <i class="fas fa-bell"></i>
            @if (!empty($hasNewNotifications))
                <span class="notif-dot"></span>
            @endif
        </a>
        <a href="{{ route('settings.index') }}" class="nav-btn" title="Settings"><i class="fas fa-cog"></i></a>
    </div>
</header>
