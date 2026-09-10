@extends('website.layouts.app')

@section('content')
    @include('website.sections.hero')
    @include('website.sections.features')

    <section class="process">
        <div class="container">
            <div class="section-label">Guest journey</div>
            <h2 class="section-title">From seat to receipt</h2>
            <div class="process-grid">
                <div class="process-step">
                    <b>01</b>
                    <h3>Browse</h3>
                    <p class="section-sub">Open the live menu. Photos preview in a lightbox. Out-of-stock dishes are marked.</p>
                </div>
                <div class="process-step">
                    <b>02</b>
                    <h3>Order</h3>
                    <p class="section-sub">Cart respects remaining servings. Checkout uses cash on delivery with a visible refund policy.</p>
                </div>
                <div class="process-step">
                    <b>03</b>
                    <h3>Kitchen</h3>
                    <p class="section-sub">Staff approve, chefs cook, status steps turn green: Pending, Approved, Preparing, Ready, Delivered.</p>
                </div>
                <div class="process-step">
                    <b>04</b>
                    <h3>Dine or pickup</h3>
                    <p class="section-sub">Reserve a table online, or track delivery. Cancel while the kitchen has not started.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="home-menu">
        <div class="container">
            <div class="home-menu-head">
                <div>
                    <div class="section-label">Tonight&rsquo;s board</div>
                    <h2 class="section-title">Featured dishes</h2>
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
                                    onerror="this.src='{{ asset('images/dishes/plain-rice.jpg') }}'">
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
