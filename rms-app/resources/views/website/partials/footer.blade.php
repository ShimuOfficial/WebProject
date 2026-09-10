<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <div>
                <div class="footer-brand">
                    <span class="brand-badge" style="width:36px;height:36px;font-size:14px">{{ strtoupper(substr($site['name'], 0, 1)) }}</span>
                    {{ $site['name'] }}
                </div>
                <p class="footer-desc">{{ $site['tagline'] }} Hospitality software for dining rooms that still cook from a real kitchen.</p>
            </div>
            <div class="footer-col">
                <h4>Guest</h4>
                <ul class="footer-links">
                    <li><a href="{{ route('website.menu') }}">Order menu</a></li>
                    <li><a href="{{ route('contact') }}#reserve">Reserve a table</a></li>
                    <li><a href="{{ route('customer.login') }}">Customer login</a></li>
                    <li><a href="{{ route('customer.register') }}">Create account</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>House</h4>
                <ul class="footer-links">
                    @foreach ($site['hours'] as $hour)
                        <li>{{ $hour['label'] ?? 'Daily' }}: {{ $hour['time'] ?? '11AM - 11PM' }}</li>
                    @endforeach
                    <li>{{ $site['phone'] }}</li>
                    <li>{{ $site['email'] }}</li>
                    <li>{{ $site['address'] }}</li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <span>&copy; {{ date('Y') }} {{ $site['name'] }} &mdash; Restaurant operations system</span>
            <span>Inventory, KDS, reservations, guest ordering</span>
        </div>
    </div>
</footer>
