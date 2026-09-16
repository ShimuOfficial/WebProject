<section class="hero">
    <div class="container">
        <div class="hero-grid" style="background-image: url('{{ $site['hero_image_url'] }}')">
            <div class="hero-content">
                <div class="hero-badge">
                    <span class="hero-dot"></span>
                    {{ $heroSettings->hero_badge }}
                </div>
                <h1>{{ $heroSettings->hero_title }}<br><span>{{ $heroSettings->hero_accent }}</span></h1>
                <p class="hero-sub">{{ $heroSettings->hero_subtitle }}</p>
                <div class="hero-actions">
                    <a href="{{ route('website.menu') }}" class="btn btn-primary">Order from kitchen</a>
                    <a href="{{ route('contact') }}#reserve" class="btn btn-outline">Reserve a table</a>
                </div>
                <div class="hero-stats">
                    <div class="stat-item">
                        <strong>{{ $featuredItems->count() }}+</strong>
                        <span>Live dishes</span>
                    </div>
                    <div class="stat-divider"></div>
                    <div class="stat-item">
                        <strong>{{ $menuByCategory->keys()->count() }}</strong>
                        <span>Categories</span>
                    </div>
                    <div class="stat-divider"></div>
                    <div class="stat-item">
                        <strong>{{ $site['guest_rating'] }}</strong>
                        <span>{{ $site['content']['guest_stat_label'] ?? 'Guest rating' }}</span>
                    </div>
                    <div class="stat-divider"></div>
                    <div class="stat-item">
                        <strong>KDS</strong>
                        <span>{{ $site['content']['kitchen_stat_label'] ?? 'Kitchen synced' }}</span>
                    </div>
                </div>
            </div>
            <aside class="hero-panel" aria-label="Service highlights">
                @foreach ($site['content']['hero_tiles'] ?? [] as $tile)
                    <div class="hero-tile">
                        <strong>{{ $tile['title'] }}</strong>
                        <span>{{ $tile['text'] }}</span>
                    </div>
                @endforeach
            </aside>
        </div>
    </div>
</section>
