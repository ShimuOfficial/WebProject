<nav class="nav">
    <div class="container nav-inner">
        <a class="brand" href="{{ route('website.home') }}">
            <img class="brand-mark" src="{{ $site['logo_url'] }}" alt="{{ $site['name'] }}">
            {{ $site['name'] }}
        </a>
        <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="siteNav" id="siteNavToggle">Menu</button>
        <div class="nav-links" id="siteNav">
            <a href="{{ route('website.home') }}" class="nav-link {{ request()->routeIs('website.home') ? 'is-active' : '' }}">Home</a>
            <a href="{{ route('website.about') }}" class="nav-link {{ request()->routeIs('website.about') ? 'is-active' : '' }}">About</a>
            <a href="{{ route('website.menu') }}" class="nav-link {{ request()->routeIs('website.menu') ? 'is-active' : '' }}">Menu</a>
            <a href="{{ route('contact') }}#reserve" class="nav-link">Reserve</a>
            <a href="{{ route('contact') }}" class="nav-link {{ request()->routeIs('contact') ? 'is-active' : '' }}">Contact</a>
            @auth
                @if (auth()->user()->role === 'customer')
                    <a class="btn btn-outline" href="{{ route('customer.orders') }}">Orders</a>
                    <a class="btn btn-primary" href="{{ route('customer.cart') }}">
                        Cart <span class="cart-count" id="cartCount">{{ $cartSummary['count'] }}</span>
                    </a>
                @else
                    <a href="{{ route('dashboard') }}" class="btn btn-primary">Staff Console</a>
                @endif
            @else
                <a href="{{ route('customer.login') }}" class="btn btn-outline">Sign in</a>
                <a href="{{ route('website.menu') }}" class="btn btn-primary">Order now</a>
            @endauth
        </div>
    </div>
</nav>
