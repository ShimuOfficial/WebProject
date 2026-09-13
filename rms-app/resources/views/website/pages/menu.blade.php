{{-- DEFENSE: §5.1 public menu + add-to-cart --}}
@extends('website.layouts.app')
@section('content')
    <section class="menu-section" id="menu">
        <div class="container">
            <div class="section-head">
                <div>
                    <div class="section-label">What We Serve</div>
                    <h2 class="section-title">Featured Menu</h2>
                    <p class="section-sub">Explore today's dishes. Tap a photo to preview, then order while stock lasts.</p>
                </div>
            </div>
            @if (session('success'))
                <div class="orders-notice">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="orders-alert">{{ $errors->first() }}</div>
            @endif
            <div class="menu-toolbar">
                <input class="menu-search" id="menuSearch" type="search" placeholder="Search dishes&hellip;"
                    aria-label="Search dishes">
            </div>
            <div class="category-dropdown" id="categoryDropdown">
                <button class="btn btn-outline category-dropdown-btn" id="categoryDropdownButton" type="button"
                    aria-haspopup="true" aria-expanded="false">
                    Category: All products
                </button>
                <div class="category-dropdown-menu" id="categoryDropdownMenu" hidden>
                    <button class="cat-tab active" type="button" data-category="">
                        All products
                    </button>
                    @foreach ($orderMenuItems->keys() as $category)
                        <button class="cat-tab" type="button"
                            data-category="{{ \Illuminate\Support\Str::slug($category) }}">{{ $category }}</button>
                    @endforeach
                </div>
            </div>
            <div class="grid" id="menuGrid" style="--grid-cols: 4; --grid-cols-md: 2; --grid-cols-sm: 1;">
                @forelse($featuredItems as $index => $item)
                    @php
                        $servings = $item->available_servings;
                        $orderable = $item->isOrderable();
                    @endphp
                    <article class="dish menu-filter-card {{ $orderable ? '' : 'is-oos' }}"
                        data-category="{{ \Illuminate\Support\Str::slug($item->category) }}"
                        data-search="{{ \Illuminate\Support\Str::lower($item->name . ' ' . $item->category . ' ' . $item->description) }}"
                        style="animation-delay:{{ $index * 0.06 }}s">
                        <div class="dish-img">
                            <button class="dish-img-trigger js-dish-preview" type="button"
                                data-image="{{ $item->image_url }}" data-title="{{ $item->name }}"
                                aria-label="Preview {{ $item->name }}">
                                <img src="{{ $item->image_url }}" alt="{{ $item->name }}"
                                    onerror="this.src='{{ asset('images/dishes/plain-rice.jpg') }}'">
                            </button>
                            @if (!$orderable)
                                <span class="dish-stock-badge is-out">Out of stock</span>
                            @elseif ($servings <= 5)
                                <span class="dish-stock-badge is-low">{{ $servings }} left</span>
                            @endif
                        </div>
                        <div class="dish-body">
                            <div class="dish-name">{{ $item->name }}</div>
                            <p class="dish-desc">{{ $item->description ?: 'Dish crafted in-house.' }}</p>
                            <div class="dish-foot">
                                <span class="dish-price">&#2547; {{ number_format($item->price, 2) }}</span>
                                @if (!$orderable)
                                    <span class="dish-order is-disabled" aria-disabled="true">Sold out</span>
                                @elseif (auth()->check() && auth()->user()->role === 'customer')
                                    <form method="POST" action="{{ route('customer.cart.add') }}">
                                        @csrf
                                        <input type="hidden" name="menu_id" value="{{ $item->id }}">
                                        <button class="dish-order" type="submit">
                                            <svg class="dish-order-icon" viewBox="0 0 24 24" aria-hidden="true">
                                                <path fill="currentColor"
                                                    d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2m10 0c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2M7.2 14.8h11.1c.8 0 1.5-.5 1.8-1.2l2.1-6.1c.2-.6-.2-1.3-.9-1.3H6.3L5.6 4H2v2h2l3.6 7.6L6.2 16c-.3.6.1 1.3.8 1.3h12v-2H7.9z" />
                                            </svg>
                                            Order
                                        </button>
                                    </form>
                                @elseif (auth()->check())
                                    <a class="dish-order" href="{{ route('orders.create') }}">
                                        <svg class="dish-order-icon" viewBox="0 0 24 24" aria-hidden="true">
                                            <path fill="currentColor"
                                                d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2m10 0c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2M7.2 14.8h11.1c.8 0 1.5-.5 1.8-1.2l2.1-6.1c.2-.6-.2-1.3-.9-1.3H6.3L5.6 4H2v2h2l3.6 7.6L6.2 16c-.3.6.1 1.3.8 1.3h12v-2H7.9z" />
                                        </svg>
                                        Order
                                    </a>
                                @else
                                    <a class="dish-order" href="{{ route('customer.login') }}">
                                        <svg class="dish-order-icon" viewBox="0 0 24 24" aria-hidden="true">
                                            <path fill="currentColor"
                                                d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2m10 0c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2M7.2 14.8h11.1c.8 0 1.5-.5 1.8-1.2l2.1-6.1c.2-.6-.2-1.3-.9-1.3H6.3L5.6 4H2v2h2l3.6 7.6L6.2 16c-.3.6.1 1.3.8 1.3h12v-2H7.9z" />
                                        </svg>
                                        Order
                                    </a>
                                @endif
                            </div>
                        </div>
                    </article>
                @empty
                    <div
                        style="grid-column:1/-1;text-align:center;padding:48px;color:var(--muted);background:var(--card);border:1px solid var(--border);border-radius:var(--radius-lg)">
                        <div style="font-size:40px;margin-bottom:12px">&#x1F37D;</div>
                        <p>No menu items yet. Add items from the staff dashboard.</p>
                    </div>
                @endforelse
                <div class="menu-empty" id="menuEmpty">No dishes match your search.</div>
            </div>
        </div>
    </section>
@endsection
