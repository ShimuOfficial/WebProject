<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <div>
                <div class="footer-brand">
                    <img class="brand-mark" src="{{ $site['logo_url'] }}" alt="{{ $site['name'] }}">
                    {{ $site['name'] }}
                </div>
                <p class="footer-desc">{{ $site['content']['footer_blurb'] ?? $site['tagline'] }}</p>
                @if (!empty($site['social']))
                    <div class="footer-social">
                        @foreach ($site['social'] as $link)
                            <a href="{{ $link['url'] }}" target="_blank" rel="noopener">{{ $link['label'] }}</a>
                        @endforeach
                    </div>
                @endif
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
            <span>&copy; {{ date('Y') }} {{ $site['name'] }}</span>
            <span>{{ $site['content']['footer_note'] ?? $site['tagline'] }}</span>
        </div>
    </div>
</footer>
