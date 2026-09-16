<section class="features">
    <div class="container">
        <div class="section-label">{{ $site['content']['features_label'] ?? 'Restaurant system' }}</div>
        <h2 class="section-title">{{ $site['content']['features_title'] }}</h2>
        <p class="section-sub">{{ $site['content']['features_intro'] }}</p>
        <div class="grid" style="--grid-cols: 4; --grid-cols-md: 2; --grid-cols-sm: 1;">
            @foreach ($site['content']['features'] ?? [] as $index => $feature)
                <div class="feature-card">
                    <div class="feature-icon">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</div>
                    <h3>{{ $feature['title'] }}</h3>
                    <p>{{ $feature['text'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
