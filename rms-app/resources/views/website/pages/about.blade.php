@extends('website.layouts.app')
@section('content')
    <section class="about" id="about">
        <div class="container about-layout">
            <div class="about-copy">
                <div class="section-label">Our Story</div>
                <h2 class="section-title">
                    {{ $aboutPage['title'] }}</h2>

                <div class="about-story">
                    <p>{{ $aboutPage['text'] }}</p>
                    <p>
                        Our kitchen is built around simple preparation, dependable service, and food that feels fresh
                        from the first plate to the last. Guests can enjoy a calm dine-in experience, place an order for
                        pickup, or choose delivery when they want the same care at home.
                    </p>
                    <p>
                        Every part of the service is handled with attention: ingredients are checked before prep, orders
                        are tracked carefully, and the team keeps the dining room warm, clean, and welcoming throughout
                        the day.
                    </p>
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

            @if ($aboutPage['image_url'])
                <div class="about-media">
                    <img src="{{ $aboutPage['image_url'] }}" alt="{{ $site['name'] }} about image">
                    <div class="about-media-title">{{ $site['name'] }}</div>
                </div>
            @endif
        </div>
    </section>
@endsection
