{{-- DEFENSE: §5.1 public homepage --}}
@extends('website.layouts.app')

@section('content')
    @include('website.sections.hero')
    @include('website.sections.features')

    <section class="process">
        <div class="container">
            <div class="section-label">{{ $site['content']['process_label'] ?? 'Guest journey' }}</div>
            <h2 class="section-title">{{ $site['content']['process_title'] ?? 'From menu to plate' }}</h2>
            <div class="process-grid">
                @foreach ($site['content']['process'] ?? [] as $step)
                    <div class="process-step">
                        <b>{{ $step['step'] }}</b>
                        <h3>{{ $step['title'] }}</h3>
                        <p class="section-sub">{{ $step['text'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="home-menu">
        <div class="container">
            <div class="home-menu-head">
                <div>
                    <div class="section-label">{{ $site['content']['featured_label'] ?? 'Tonight’s board' }}</div>
                    <h2 class="section-title">{{ $site['content']['featured_title'] ?? 'Featured dishes' }}</h2>
                </div>
                <a class="btn btn-outline" href="{{ route('website.menu') }}">Full menu</a>
            </div>
            <div class="grid" style="--grid-cols: 4; --grid-cols-md: 2; --grid-cols-sm: 1;">
                @foreach ($featuredItems->take(8) as $index => $item)
                    <article class="dish" style="animation-delay:{{ $index * 0.05 }}s">
                        <div class="dish-img">
                            <button class="dish-img-trigger js-dish-preview" type="button"
                                data-image="{{ $item->image_url }}" data-title="{{ $item->name }}">
                                <img src="{{ $item->image_url }}" alt="{{ $item->name }}"
                                    onerror="this.src='{{ $site['dish_fallback_url'] }}'">
                            </button>
                        </div>
                        <div class="dish-body">
                            <div class="dish-name">{{ $item->name }}</div>
                            <p class="dish-desc">{{ \Illuminate\Support\Str::limit($item->description ?: 'House specialty.', 70) }}</p>
                            <div class="dish-foot">
                                <span class="dish-price">&#2547; {{ number_format($item->price, 2) }}</span>
                                <a class="dish-order" href="{{ route('website.menu') }}">View</a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endsection
