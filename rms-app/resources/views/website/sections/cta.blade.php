<section class="cta-section">
    <div class="container">
        <div class="cta-inner">
            <div>
                <h2>Book the room. Or send the kitchen an order.</h2>
                <p>Open {{ $site['hours'][0]['time'] ?? '11AM - 11PM' }} &nbsp;&bull;&nbsp; {{ $site['phone'] }} &nbsp;&bull;&nbsp; {{ $site['address'] }}</p>
            </div>
            <div class="hero-actions" style="margin:0">
                <a href="{{ route('contact') }}#reserve" class="btn btn-outline">Reserve a table</a>
                @auth
                    @if (auth()->user()->role === 'customer')
                        <a href="{{ route('customer.account') }}" class="btn btn-primary">My account</a>
                    @else
                        <a href="{{ route('dashboard') }}" class="btn btn-primary">Open console</a>
                    @endif
                @else
                    <a href="{{ route('website.menu') }}" class="btn btn-primary">Start an order</a>
                @endauth
            </div>
        </div>
    </div>
</section>
