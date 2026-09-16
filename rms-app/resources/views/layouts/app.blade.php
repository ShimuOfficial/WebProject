{{-- DEFENSE: staff panel shell (sidebar is role-based) --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — {{ $site['name'] ?? 'Restaurant' }} Console</title>
    <link rel="icon" type="image/svg+xml" href="{{ $site['favicon_url'] ?? asset('images/brand/restaurant-icon.svg') }}">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    @include('layouts.partials.styles')
    @stack('styles')
</head>

<body>
    @include('admin.partials.sidebar')
    <div id="sidebarBackdrop" class="sidebar-backdrop" onclick="document.getElementById('sidebar').classList.remove('show');document.body.classList.remove('sidebar-open')"></div>

    <!-- Main Content -->
    <div class="main-content">
        @include('admin.partials.top-navbar')

        <div class="content-area">
            @include('layouts.partials.alerts')
            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    @stack('scripts')
</body>

</html>
