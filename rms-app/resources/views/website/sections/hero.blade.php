<section class="hero">
    <div class="container">
        <div class="hero-grid"
            @if ($heroSettings && $heroSettings->hero_background_image) style="background-image: url('{{ asset('storage/' . $heroSettings->hero_background_image) }}')" @endif>
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
                        <strong>4.8</strong>
                        <span>Guest rating</span>
                    </div>
                    <div class="stat-divider"></div>
                    <div class="stat-item">
                        <strong>KDS</strong>
                        <span>Kitchen synced</span>
                    </div>
                </div>
            </div>
            <aside class="hero-panel" aria-label="Service highlights">
                <div class="hero-tile">
                    <strong>Live order tracking</strong>
                    <span>Pending to delivered, with green progress states the kitchen actually uses.</span>
                </div>
                <div class="hero-tile">
                    <strong>Stock-aware menu</strong>
                    <span>Dishes hide themselves when inventory runs out. No ghost items.</span>
                </div>
                <div class="hero-tile">
                    <strong>Table booking online</strong>
                    <span>Pick date, slot and party size. Staff confirms from the CMS.</span>
                </div>
            </aside>
        </div>
    </div>
</section>
