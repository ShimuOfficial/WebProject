@extends('website.layouts.app')
@section('content')
    <section class="about" id="about">
        <div class="container about-layout">
            <div class="about-copy">
                <div class="section-label">{{ $site['content']['about_label'] ?? 'Our Story' }}</div>
                <h2 class="section-title">{{ $aboutPage['title'] }}</h2>

                <div class="about-story">
                    <p>{{ $aboutPage['text'] }}</p>
                    @foreach ($aboutPage['paragraphs'] ?? [] as $paragraph)
                        <p>{{ $paragraph }}</p>
                    @endforeach
                </div>

                @if (!empty($aboutPage['points']))
                    <div class="about-details">
                        @foreach ($aboutPage['points'] as $point)
                            <div class="about-detail">
                                <strong>{{ $point['title'] ?? 'Kitchen Focus' }}</strong>
                                <span>{{ $point['text'] ?? 'Seasonal prep and fast service.' }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="about-media">
                <img src="{{ $aboutPage['image_url'] }}" alt="{{ $site['name'] }} kitchen">
                <div class="about-media-title">{{ $site['name'] }}</div>
            </div>
        </div>
    </section>
@endsection
