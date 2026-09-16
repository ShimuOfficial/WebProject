<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div style="display:flex;gap:10px;align-items:center">
            <img src="{{ $site['logo_url'] }}" alt="{{ $site['name'] }}" class="brand-mark" style="width:40px;height:40px;object-fit:contain;border-radius:10px">
            <div>
                <div class="brand-text">{{ $site['name'] }}</div>
                <div class="brand-sub">Operations console</div>
            </div>
        </div>
    </div>

    <nav class="sidebar-menu">
        <div class="menu-label">Main</div>
        <a href="{{ route('dashboard') }}" class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fas fa-th-large"></i> Dashboard
        </a>

        <div class="menu-label">Operations</div>

        @if (in_array($currentUserRole, ['admin', 'manager', 'cashier']))
            <a href="{{ route('orders.index') }}"
                class="menu-item {{ request()->routeIs('orders.*') ? 'active' : '' }}">
                <i class="fas fa-receipt"></i> Orders
            </a>
        @endif

        @if (in_array($currentUserRole, ['chef']))
            <a href="{{ route('kitchen.index') }}"
                class="menu-item {{ request()->routeIs('kitchen.*') ? 'active' : '' }}">
                <i class="fas fa-fire"></i> Kitchen KDS
            </a>
        @endif

        @if (in_array($currentUserRole, ['admin', 'manager']))
            <a href="{{ route('tables.index') }}"
                class="menu-item {{ request()->routeIs('tables.*') ? 'active' : '' }}">
                <i class="fas fa-chair"></i> Tables
            </a>
            <a href="{{ route('reservations.index') }}"
                class="menu-item {{ request()->routeIs('reservations.*') ? 'active' : '' }}">
                <i class="fas fa-calendar-check"></i> Reservations
            </a>
        @endif

        @if (in_array($currentUserRole, ['admin', 'manager', 'chef']))
            <a href="{{ route('menu.index') }}" class="menu-item {{ request()->routeIs('menu.*') ? 'active' : '' }}">
                <i class="fas fa-book-open"></i> Menu
            </a>
        @endif

        @if (in_array($currentUserRole, ['admin', 'manager', 'chef']))
            <div class="menu-label">Management</div>

            @if (in_array($currentUserRole, ['admin', 'manager', 'chef']))
                <a href="{{ route('inventory.index') }}"
                    class="menu-item {{ request()->routeIs('inventory.*') ? 'active' : '' }}">
                    <i class="fas fa-boxes-stacked"></i> Inventory
                </a>
            @endif

            @if ($currentUserRole === 'admin')
                <a href="{{ route('staff.index') }}"
                    class="menu-item {{ request()->routeIs('staff.*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i> Staff
                </a>
                <a href="{{ route('admin.site-settings.index') }}"
                    class="menu-item {{ request()->routeIs('admin.site-settings.*') ? 'active' : '' }}">
                    <i class="fas fa-gear"></i> Site Settings
                </a>
            @endif

            @if (in_array($currentUserRole, ['admin', 'manager']))
                <a href="{{ route('reports.index') }}"
                    class="menu-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                    <i class="fas fa-chart-pie"></i> Reports
                </a>
            @endif
        @endif
    </nav>

    <div class="sidebar-footer">
        <div class="avatar">{{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}</div>
        <div>
            <div class="user-name">{{ auth()->user()->name ?? 'Admin' }}</div>
            <div class="user-role">{{ auth()->user()->role ?? 'admin' }}</div>
        </div>
        <form action="{{ route('logout') }}" method="POST" class="ms-auto">
            @csrf
            <button type="submit" class="nav-btn"
                style="width:32px;height:32px;border:none;background:rgba(255,255,255,.1);color:rgba(255,255,255,.5);"
                title="Logout">
                <i class="fas fa-sign-out-alt" style="font-size:13px"></i>
            </button>
        </form>
    </div>
</aside>
