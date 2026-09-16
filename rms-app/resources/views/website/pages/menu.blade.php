{{-- DEFENSE: §5.1 public menu + Foodpanda-style dish preview modal --}}
@extends('website.layouts.app')

@php
    $canAddToCart = auth()->check() && auth()->user()->role === 'customer';
    $isStaff = auth()->check() && auth()->user()->role !== 'customer';
    $cartQtys = $canAddToCart ? session('cart.items', []) : [];
    $fallbackImage = $site['dish_fallback_url'] ?? asset('images/dishes/plain-rice.jpg');
@endphp

@push('head')
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            prefix: 'tw-',
            corePlugins: {
                preflight: false
            }
        };
    </script>
    <style>
        body.dish-modal-open {
            overflow: hidden;
        }

        #dishPreviewModal {
            position: fixed;
            inset: 0;
            z-index: 10000;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            background: rgba(0, 0, 0, .64);
            opacity: 0;
            pointer-events: none;
            transition: opacity .2s ease-out;
            isolation: isolate;
        }

        #dishPreviewModal[hidden] {
            display: none !important;
        }

        #dishPreviewModal.is-open {
            opacity: 1;
            pointer-events: auto;
        }

        #dishPreviewModal .dish-preview-panel {
            transform: translateY(1.25rem) scale(.98);
            transition: transform .24s ease-out;
        }

        #dishPreviewModal.is-open .dish-preview-panel {
            transform: translateY(0) scale(1);
        }

        .js-open-dish-modal {
            cursor: pointer;
        }

        .js-open-dish-modal .dish-img-trigger {
            cursor: pointer;
        }

        @media (min-width: 768px) {
            #dishPreviewModal {
                align-items: center;
                padding: 1.5rem;
            }

            #dishPreviewModal .dish-preview-panel {
                border-radius: 1.5rem;
            }
        }

        #dishPreviewModal .dish-preview-panel {
            position: relative;
            z-index: 10;
            display: flex;
            flex-direction: column;
            width: 100%;
            max-width: 36rem;
            max-height: 92vh;
            overflow: hidden;
            background: #fff;
            border-radius: 1.5rem 1.5rem 0 0;
            box-shadow: 0 25px 50px rgba(0, 0, 0, .25);
        }

        #dishPreviewQtyMinus,
        #dishPreviewQtyPlus,
        #dishPreviewAdd,
        #dishPreviewClose {
            cursor: pointer;
            border: 0;
            font-family: inherit;
        }

        #dishPreviewImage {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            background: #111;
        }

        #dishPreviewModal .tw-px-5 {
            padding: 1rem 1.25rem 1.25rem;
        }

        #dishPreviewDescription {
            display: -webkit-box;
            -webkit-line-clamp: 4;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        #dishPreviewActions {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-top: auto;
            padding-top: 1rem;
            border-top: 1px solid #e5e7eb;
        }

        #dishPreviewAdd {
            height: 2.75rem;
            width: 100%;
            border-radius: 999px;
            color: #fff;
            font-weight: 800;
        }

        #dishPreviewQty {
            display: inline-flex;
            align-items: center;
            overflow: hidden;
            border: 1px solid #d4d4d8;
            border-radius: 999px;
        }
    </style>
@endpush

@section('content')
    <section class="menu-section" id="menu">
        <div class="container">
            <div class="section-head">
                <div>
                    <div class="section-label">{{ $site['content']['menu_label'] ?? 'What we serve' }}</div>
                    <h2 class="section-title">{{ $site['content']['menu_title'] ?? 'Live kitchen menu' }}</h2>
                    <p class="section-sub">{{ $site['content']['menu_intro'] ?? 'Explore today’s dishes. Tap a dish to preview, then add it to your cart.' }}</p>
                </div>
            </div>
            <div id="menuLiveNotice" class="orders-notice" hidden></div>
            @if (session('success'))
                <div class="orders-notice">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="orders-alert">{{ $errors->first() }}</div>
            @endif
            <div class="menu-filters">
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
            </div>
            <div class="grid" id="menuGrid" style="--grid-cols: 4; --grid-cols-md: 2; --grid-cols-sm: 1;">
                @forelse($featuredItems as $index => $item)
                    @php
                        $servings = $item->available_servings;
                        $orderable = $item->isOrderable();
                        $inCart = (int) ($cartQtys[$item->id] ?? 0);
                        $remaining = max(0, $servings - $inCart);
                        $ingredients = $item->menuIngredients
                            ->map(fn($ingredient) => $ingredient->inventory->item_name ?? null)
                            ->filter()
                            ->unique()
                            ->values();
                    @endphp
                    <article class="dish menu-filter-card js-open-dish-modal {{ $orderable ? '' : 'is-oos' }}"
                        data-id="{{ $item->id }}" data-name="{{ $item->name }}"
                        data-category="{{ \Illuminate\Support\Str::slug($item->category) }}"
                        data-category-label="{{ $item->category }}"
                        data-price="{{ number_format((float) $item->price, 2, '.', '') }}"
                        data-price-label="৳ {{ number_format($item->price, 2) }}"
                        data-description="{{ $item->description ?: 'Dish crafted in-house.' }}"
                        data-image="{{ $item->image_url }}" data-orderable="{{ $orderable ? '1' : '0' }}"
                        data-servings="{{ $servings }}" data-remaining="{{ $remaining }}"
                        data-in-cart="{{ $inCart }}"
                        data-ingredients="{{ $ingredients->toJson(JSON_UNESCAPED_UNICODE) }}"
                        data-search="{{ \Illuminate\Support\Str::lower($item->name . ' ' . $item->category . ' ' . $item->description) }}"
                        style="animation-delay:{{ $index * 0.06 }}s">
                        <div class="dish-img">
                            <button class="dish-img-trigger" type="button"
                                aria-label="Preview {{ $item->name }}">
                                <img src="{{ $item->image_url }}" alt="{{ $item->name }}"
                                    onerror="this.src='{{ $fallbackImage }}'">
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
                                @else
                                    <button class="dish-order" type="button">
                                        <svg class="dish-order-icon" viewBox="0 0 24 24" aria-hidden="true">
                                            <path fill="currentColor"
                                                d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2m10 0c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2M7.2 14.8h11.1c.8 0 1.5-.5 1.8-1.2l2.1-6.1c.2-.6-.2-1.3-.9-1.3H6.3L5.6 4H2v2h2l3.6 7.6L6.2 16c-.3.6.1 1.3.8 1.3h12v-2H7.9z" />
                                        </svg>
                                        Order
                                    </button>
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

@push('modals')
    <div id="dishPreviewModal" class="tw-fixed tw-inset-0 tw-z-[10000] tw-flex tw-items-end md:tw-items-center tw-justify-center tw-p-0 md:tw-p-6 tw-bg-black/60"
        hidden role="dialog" aria-modal="true" aria-labelledby="dishPreviewName">
        <div class="js-dish-modal-backdrop tw-absolute tw-inset-0" aria-hidden="true"></div>
        <div
            class="dish-preview-panel tw-relative tw-z-10 tw-flex tw-w-full tw-max-w-xl tw-max-h-[92vh] tw-flex-col tw-overflow-hidden tw-rounded-t-3xl md:tw-rounded-3xl tw-bg-white tw-shadow-2xl">
            <button id="dishPreviewClose" class="tw-absolute tw-right-3 tw-top-3 tw-z-20 tw-grid tw-h-10 tw-w-10 tw-place-items-center tw-rounded-full tw-bg-white/95 tw-text-2xl tw-leading-none tw-text-neutral-800 tw-shadow"
                type="button" aria-label="Close dish preview">&times;</button>
            <div class="dish-preview-image tw-h-40 sm:tw-h-56 tw-shrink-0 tw-bg-neutral-900">
                <img id="dishPreviewImage" class="tw-h-full tw-w-full tw-object-cover" alt=""
                    onerror="this.src='{{ $fallbackImage }}'">
            </div>
            <div class="tw-flex tw-min-h-0 tw-flex-1 tw-flex-col tw-overflow-hidden tw-px-5 tw-pb-5 tw-pt-4">
                <p id="dishPreviewCategory"
                    class="tw-mb-1 tw-text-[11px] tw-font-extrabold tw-uppercase tw-tracking-[0.14em] tw-text-neutral-500">
                </p>
                <div class="tw-mb-2 tw-flex tw-items-start tw-justify-between tw-gap-3">
                    <h3 id="dishPreviewName" class="tw-m-0 tw-text-xl tw-font-extrabold tw-leading-tight tw-text-neutral-900">
                    </h3>
                    <strong id="dishPreviewPrice" class="tw-shrink-0 tw-text-lg tw-font-extrabold"
                        style="color: var(--brand2)"></strong>
                </div>
                <p id="dishPreviewStock" class="tw-mb-3 tw-text-sm tw-font-bold"></p>
                <p id="dishPreviewDescription" class="tw-mb-4 tw-text-sm tw-leading-6 tw-text-neutral-600"></p>
                <div id="dishPreviewIngredientsWrap" class="tw-mb-5" hidden>
                    <p class="tw-mb-2 tw-text-[11px] tw-font-extrabold tw-uppercase tw-tracking-[0.12em] tw-text-neutral-500">
                        Ingredients</p>
                    <div id="dishPreviewIngredients" class="tw-flex tw-flex-wrap tw-gap-2"></div>
                </div>
                <div id="dishPreviewActions" class="tw-mt-auto tw-flex tw-items-center tw-gap-3 tw-border-t tw-border-neutral-200 tw-pt-4">
                    @if ($canAddToCart)
                        <div id="dishPreviewQty"
                            class="tw-inline-flex tw-items-center tw-overflow-hidden tw-rounded-full tw-border tw-border-neutral-300">
                            <button id="dishPreviewQtyMinus" class="tw-h-10 tw-w-10 tw-border-0 tw-bg-transparent tw-text-lg tw-font-extrabold tw-text-neutral-800"
                                type="button" aria-label="Decrease quantity">-</button>
                            <span id="dishPreviewQtyValue" class="tw-min-w-[2rem] tw-text-center tw-text-sm tw-font-extrabold">1</span>
                            <button id="dishPreviewQtyPlus" class="tw-h-10 tw-w-10 tw-border-0 tw-bg-transparent tw-text-lg tw-font-extrabold tw-text-neutral-800"
                                type="button" aria-label="Increase quantity">+</button>
                        </div>
                        <form id="dishPreviewForm" class="tw-flex-1" method="POST"
                            action="{{ route('customer.cart.add') }}">
                            @csrf
                            <input type="hidden" name="menu_id" id="dishPreviewMenuId" value="">
                            <input type="hidden" name="quantity" id="dishPreviewQtyInput" value="1">
                            <button id="dishPreviewAdd" class="tw-h-11 tw-w-full tw-border-0 tw-rounded-full tw-text-sm tw-font-extrabold tw-text-white"
                                type="submit" style="background: var(--brand2)">Add to Cart</button>
                        </form>
                    @elseif ($isStaff)
                        <a class="tw-flex tw-h-11 tw-flex-1 tw-items-center tw-justify-center tw-rounded-full tw-text-sm tw-font-extrabold tw-text-white"
                            href="{{ route('orders.create') }}" style="background: var(--brand2)">Open staff order</a>
                    @else
                        <a class="tw-flex tw-h-11 tw-flex-1 tw-items-center tw-justify-center tw-rounded-full tw-text-sm tw-font-extrabold tw-text-white"
                            href="{{ route('customer.login') }}" style="background: var(--brand2)">Sign in to order</a>
                    @endif
                </div>
                <p id="dishPreviewSoldOut" class="tw-mt-auto tw-hidden tw-rounded-xl tw-bg-red-50 tw-px-4 tw-py-3 tw-text-center tw-text-sm tw-font-bold tw-text-red-800">
                    This dish is currently out of stock.
                </p>
                <span id="dishIngredientChipTemplate"
                    class="tw-hidden tw-rounded-full tw-bg-neutral-100 tw-px-3 tw-py-1 tw-text-xs tw-font-semibold tw-text-neutral-700"
                    hidden></span>
            </div>
        </div>
    </div>
@endpush

@push('scripts')
    <script>
        (function() {
            const modal = document.getElementById('dishPreviewModal');
            if (!modal) return;

            const fallbackImage = @json($fallbackImage);
            const canAddToCart = @json($canAddToCart);
            const panel = modal.querySelector('.dish-preview-panel');
            const imageEl = document.getElementById('dishPreviewImage');
            const nameEl = document.getElementById('dishPreviewName');
            const categoryEl = document.getElementById('dishPreviewCategory');
            const priceEl = document.getElementById('dishPreviewPrice');
            const stockEl = document.getElementById('dishPreviewStock');
            const descriptionEl = document.getElementById('dishPreviewDescription');
            const ingredientsWrap = document.getElementById('dishPreviewIngredientsWrap');
            const ingredientsEl = document.getElementById('dishPreviewIngredients');
            const actionsEl = document.getElementById('dishPreviewActions');
            const soldOutEl = document.getElementById('dishPreviewSoldOut');
            const qtyValueEl = document.getElementById('dishPreviewQtyValue');
            const qtyInputEl = document.getElementById('dishPreviewQtyInput');
            const qtyMinus = document.getElementById('dishPreviewQtyMinus');
            const qtyPlus = document.getElementById('dishPreviewQtyPlus');
            const addBtn = document.getElementById('dishPreviewAdd');
            const form = document.getElementById('dishPreviewForm');
            const menuIdInput = document.getElementById('dishPreviewMenuId');
            const noticeEl = document.getElementById('menuLiveNotice');
            const cards = Array.from(document.querySelectorAll('.js-open-dish-modal'));

            let qty = 1;
            let maxQty = 1;
            let activeCard = null;
            const lastFocus = {
                el: null
            };

            function parseIngredients(raw) {
                try {
                    const parsed = JSON.parse(raw || '[]');
                    return Array.isArray(parsed) ? parsed.filter(Boolean) : [];
                } catch (err) {
                    return [];
                }
            }

            function showNotice(message, isError) {
                if (!noticeEl) return;
                noticeEl.hidden = false;
                noticeEl.className = isError ? 'orders-alert' : 'orders-notice';
                noticeEl.textContent = message;
            }

            function setQty(next) {
                qty = Math.min(maxQty, Math.max(1, next));
                if (qtyValueEl) qtyValueEl.textContent = String(qty);
                if (qtyInputEl) qtyInputEl.value = String(qty);
                if (qtyMinus) qtyMinus.disabled = qty <= 1;
                if (qtyPlus) qtyPlus.disabled = qty >= maxQty;
                if (addBtn) {
                    addBtn.disabled = maxQty < 1;
                    addBtn.textContent = maxQty < 1 ? 'Max in cart' : 'Add to Cart';
                }
            }

            function fillModal(card) {
                activeCard = card;
                const orderable = card.dataset.orderable === '1';
                const servings = Number(card.dataset.servings || 0);
                const remaining = Number(card.dataset.remaining || 0);
                const inCart = Number(card.dataset.inCart || 0);
                const ingredients = parseIngredients(card.dataset.ingredients);
                maxQty = orderable ? Math.max(0, remaining) : 0;

                imageEl.src = card.dataset.image || fallbackImage;
                imageEl.alt = card.dataset.name || '';
                nameEl.textContent = card.dataset.name || '';
                categoryEl.textContent = card.dataset.categoryLabel || '';
                priceEl.textContent = card.dataset.priceLabel || '';
                descriptionEl.textContent = card.dataset.description || '';

                if (!orderable || servings < 1) {
                    stockEl.textContent = 'Out of stock';
                    stockEl.style.color = '#991b1b';
                } else if (remaining < 1) {
                    stockEl.textContent = 'All remaining servings are already in your cart';
                    stockEl.style.color = '#b45309';
                } else if (servings <= 5) {
                    stockEl.textContent = remaining + ' serving' + (remaining === 1 ? '' : 's') + ' left';
                    stockEl.style.color = '#b45309';
                } else {
                    stockEl.textContent = remaining + ' servings available' + (inCart ? ' (' + inCart + ' in cart)' : '');
                    stockEl.style.color = '#166534';
                }

                ingredientsEl.innerHTML = '';
                if (ingredients.length) {
                    ingredientsWrap.hidden = false;
                    ingredients.forEach(name => {
                        const template = document.getElementById('dishIngredientChipTemplate');
                        const chip = template ? template.cloneNode(true) : document.createElement('span');
                        chip.id = '';
                        chip.hidden = false;
                        chip.classList.remove('tw-hidden');
                        chip.textContent = name;
                        ingredientsEl.appendChild(chip);
                    });
                } else {
                    ingredientsWrap.hidden = true;
                }

                if (actionsEl) actionsEl.hidden = !orderable || remaining < 1;
                if (soldOutEl) {
                    soldOutEl.hidden = orderable && remaining > 0;
                    soldOutEl.classList.toggle('tw-hidden', orderable && remaining > 0);
                    soldOutEl.textContent = (!orderable || servings < 1)
                        ? 'This dish is currently out of stock.'
                        : 'All remaining servings are already in your cart.';
                }
                if (menuIdInput) menuIdInput.value = card.dataset.id || '';
                setQty(1);
            }

            function openModal(card) {
                lastFocus.el = document.activeElement;
                fillModal(card);
                modal.hidden = false;
                requestAnimationFrame(() => modal.classList.add('is-open'));
                document.body.classList.add('dish-modal-open');
                modal.setAttribute('aria-hidden', 'false');
                document.getElementById('dishPreviewClose')?.focus();
            }

            function closeModal() {
                modal.classList.remove('is-open');
                document.body.classList.remove('dish-modal-open');
                modal.setAttribute('aria-hidden', 'true');
                window.setTimeout(() => {
                    if (!modal.classList.contains('is-open')) modal.hidden = true;
                }, 200);
                if (lastFocus.el && typeof lastFocus.el.focus === 'function') {
                    lastFocus.el.focus();
                }
            }

            cards.forEach(card => {
                card.addEventListener('click', () => openModal(card));
            });

            document.getElementById('dishPreviewClose')?.addEventListener('click', closeModal);
            modal.querySelector('.js-dish-modal-backdrop')?.addEventListener('click', closeModal);
            panel?.addEventListener('click', event => event.stopPropagation());

            document.addEventListener('keydown', event => {
                if (event.key === 'Escape' && modal.classList.contains('is-open')) {
                    event.preventDefault();
                    closeModal();
                }
            });

            qtyMinus?.addEventListener('click', () => setQty(qty - 1));
            qtyPlus?.addEventListener('click', () => setQty(qty + 1));

            form?.addEventListener('submit', async event => {
                event.preventDefault();
                if (!canAddToCart || !addBtn || addBtn.disabled) return;

                addBtn.disabled = true;
                const token = form.querySelector('[name="_token"]')?.value || '';

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': token
                        },
                        body: new FormData(form)
                    });

                    const payload = await response.json().catch(() => ({}));

                    if (!response.ok) {
                        showNotice(payload.message || 'Could not add this dish to the cart.', true);
                        addBtn.disabled = false;
                        return;
                    }

                    const cartCount = document.getElementById('cartCount');
                    if (cartCount && typeof payload.count !== 'undefined') {
                        cartCount.textContent = String(payload.count);
                    }

                    if (activeCard && payload.added) {
                        activeCard.dataset.inCart = String(payload.added.line_quantity || 0);
                        activeCard.dataset.remaining = String(payload.added.remaining || 0);
                    }

                    showNotice(payload.message || 'Added to cart.');
                    closeModal();
                } catch (err) {
                    showNotice('Could not add this dish to the cart.', true);
                    addBtn.disabled = false;
                }
            });
        })();
    </script>
@endpush
