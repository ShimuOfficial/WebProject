{{-- DEFENSE: §5.4 customer cart + checkout form --}}
@extends('website.layouts.app')

@section('content')
    <section class="orders-page">
        <div class="container orders-wrap">
            <div class="orders-head">
                <div>
                    <div class="section-label">Customer</div>
                    <h1 class="orders-title">My Cart</h1>
                </div>
                <div class="account-head-actions">
                    <a class="btn btn-outline" href="{{ route('website.menu') }}">Back</a>
                </div>
            </div>

            @if (session('success'))
                <div class="orders-notice">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
                <div class="orders-alert">{{ $errors->first() }}</div>
            @endif

            <div class="orders-card">
                <div class="order-cart">
                    <div id="cartTotal" class="order-cart-total">৳ {{ number_format($cartTotal, 2) }}</div>
                    <div class="order-cart-items">
                        @forelse ($cartRows as $row)
                            <div class="order-cart-row" data-menu-id="{{ $row['menu']->id }}">
                                <div class="cart-item-title">
                                    <div class="cart-item-media">
                                        <img src="{{ $row['menu']->image_url }}" alt="{{ $row['menu']->name }}"
                                            onerror="this.src='{{ $site['dish_fallback_url'] ?? asset('images/dishes/plain-rice.jpg') }}'">
                                    </div>
                                    <div class="cart-item-info">
                                        <div class="cart-item-head">
                                            <form class="js-cart-remove-form" method="POST"
                                                action="{{ route('customer.cart.remove', $row['menu']) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button class="cart-remove-btn" type="submit"
                                                    aria-label="Remove item">&times;</button>
                                            </form>
                                            <strong>{{ $row['menu']->name }}</strong>
                                        </div>
                                        <span>৳ {{ number_format($row['menu']->price, 2) }} each</span>
                                    </div>
                                </div>
                                <div class="qty-controls">
                                    <form class="js-cart-update-form" method="POST"
                                        action="{{ route('customer.cart.update', $row['menu']) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="quantity" value="{{ max(1, $row['quantity'] - 1) }}">
                                        <button class="qty-btn" type="submit"
                                            {{ $row['quantity'] <= 1 ? 'disabled' : '' }}>-</button>
                                    </form>
                                    <div class="qty-value">{{ $row['quantity'] }}</div>
                                    <form class="js-cart-update-form" method="POST"
                                        action="{{ route('customer.cart.update', $row['menu']) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="quantity" value="{{ $row['quantity'] + 1 }}">
                                        <button class="qty-btn" type="submit">+</button>
                                    </form>
                                </div>
                                <strong><span class="row-subtotal">৳
                                        {{ number_format($row['subtotal'], 2) }}</span></strong>
                            </div>
                        @empty
                            <p class="order-cart-empty">Your cart is empty.</p>
                        @endforelse
                    </div>

                    <form class="order-cart-controls" method="POST" action="{{ route('customer.orders.store') }}">
                        @csrf
                        <div id="cartCheckoutInputs">
                            @foreach ($cartRows as $row)
                                <input type="hidden" name="items[{{ $loop->index }}][menu_id]"
                                    value="{{ $row['menu']->id }}">
                                <input type="hidden" name="items[{{ $loop->index }}][quantity]"
                                    value="{{ $row['quantity'] }}">
                            @endforeach
                        </div>
                        <label>
                            <span class="label-title">Payment Method</span>
                            <select name="payment_method" required>
                                <option value="cash" {{ old('payment_method', 'cash') === 'cash' ? 'selected' : '' }}>
                                    Cash on Delivery</option>
                                
                            </select>
                        </label>
                        <label>
                            <span class="label-title">Notes</span>
                            <textarea name="notes" rows="3" placeholder="Any delivery or cooking notes?"></textarea>
                        </label>
                        <div class="order-cart-actions">
                            <button class="btn btn-primary btn-checkout" type="submit"
                                {{ $cartCount ? '' : 'disabled' }}>Checkout</button>
                            <button class="btn btn-outline btn-rect" type="submit" form="cartClearForm"
                                {{ $cartCount ? '' : 'disabled' }}>Clear Cart</button>
                        </div>
                    </form>
                    @include('website.partials.refund-policy')
                    <form id="cartClearForm" method="POST" action="{{ route('customer.cart.clear') }}">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>
        </div>
    </section>
    <script>
        (function() {
            const csrf = '{{ csrf_token() }}';
            const cartCheckoutInputs = document.getElementById('cartCheckoutInputs');

            function formatCurrency(n) {
                return '৳ ' + Number(n).toFixed(2);
            }

            function syncCheckoutInputs() {
                if (!cartCheckoutInputs) return;

                const rows = Array.from(document.querySelectorAll('.order-cart-row'));
                const inputs = [];

                rows.forEach((row, index) => {
                    const menuId = row.dataset.menuId;
                    const quantity = row.querySelector('.qty-value')?.textContent?.trim() || '1';
                    if (!menuId) return;

                    inputs.push(`<input type="hidden" name="items[${index}][menu_id]" value="${menuId}">`);
                    inputs.push(`<input type="hidden" name="items[${index}][quantity]" value="${quantity}">`);
                });

                cartCheckoutInputs.innerHTML = inputs.join('');
            }

            async function sendJson(url, method = 'PATCH', body = {}) {
                const resp = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrf
                    },
                    body: JSON.stringify(body)
                });
                if (!resp.ok) throw resp;
                return resp.json();
            }

            // Intercept update forms (increment/decrement)
            document.querySelectorAll('.js-cart-update-form').forEach(form => {
                form.addEventListener('submit', async e => {
                    e.preventDefault();
                    const action = form.getAttribute('action');
                    const qtyInput = form.querySelector('input[name="quantity"]');
                    const quantity = Number(qtyInput.value || 1);

                    console.debug('Cart update submit', {
                        action,
                        quantity
                    });

                    try {
                        const payload = await sendJson(action, 'PATCH', {
                            quantity
                        });
                        console.debug('Cart update response', payload);

                        // update cart count and total
                        const cartCount = document.getElementById('cartCount');
                        if (cartCount && typeof payload.count !== 'undefined') cartCount
                            .textContent = String(payload.count);
                        const cartTotal = document.getElementById('cartTotal');
                        if (cartTotal && typeof payload.total !== 'undefined') cartTotal
                            .textContent = formatCurrency(payload.total);

                        // Update row subtotal and quantity display (robust lookup)
                        const row = form.closest('.order-cart-row');
                        let menuId = row ? row.dataset.menuId : null;
                        if (!menuId) {
                            // try to extract id from action url (/customer/cart/{id})
                            const m = action.match(/\/customer\/cart\/(\d+)/);
                            menuId = m ? m[1] : null;
                        }

                        if (menuId) {
                            let item = null;
                            if (Array.isArray(payload.items)) {
                                for (const it of payload.items) {
                                    if (it && ((it.menu && String(it.menu.id) === String(menuId)) ||
                                            String(it.id) === String(menuId))) {
                                        item = it;
                                        break;
                                    }
                                }
                            }

                            if (row && item) {
                                const qtyEl = row.querySelector('.qty-value');
                                if (qtyEl) qtyEl.textContent = String(item.quantity);
                                const subEl = row.querySelector('.row-subtotal');
                                if (subEl) subEl.textContent = formatCurrency(item.subtotal);

                                // Update hidden quantity inputs so subsequent clicks are relative to new qty
                                row.querySelectorAll('.js-cart-update-form').forEach(f => {
                                    const input = f.querySelector('input[name="quantity"]');
                                    const btn = f.querySelector('button');
                                    if (!input || !btn) return;
                                    const isDec = btn.textContent.trim() === '-';
                                    if (isDec) {
                                        input.value = Math.max(1, Number(item.quantity) -
                                            1);
                                    } else {
                                        input.value = Number(item.quantity) + 1;
                                    }
                                });

                                // disable decrement when qty <=1
                                row.querySelectorAll('.qty-btn').forEach(btn => {
                                    const dec = btn.textContent.trim() === '-';
                                    if (dec) btn.disabled = item.quantity <= 1;
                                });
                            } else if (row && !item) {
                                // removed
                                row.remove();
                            }

                            syncCheckoutInputs();
                        }
                    } catch (err) {
                        console.error('Cart update failed', err);
                        try {
                            const payload = await err.json();
                            alert(payload.message || 'Could not update cart.');
                        } catch (e) {
                            window.location.reload();
                        }
                    }
                });
            });

            // Intercept remove forms
            document.querySelectorAll('.js-cart-remove-form').forEach(form => {
                form.addEventListener('submit', async e => {
                    e.preventDefault();
                    const action = form.getAttribute('action');
                    try {
                        const payload = await sendJson(action, 'DELETE', {});
                        const cartCount = document.getElementById('cartCount');
                        if (cartCount && typeof payload.count !== 'undefined') cartCount
                            .textContent = String(payload.count);
                        const cartTotal = document.getElementById('cartTotal');
                        if (cartTotal && typeof payload.total !== 'undefined') cartTotal
                            .textContent = formatCurrency(payload.total);
                        const row = form.closest('.order-cart-row');
                        if (row) row.remove();
                        syncCheckoutInputs();
                    } catch (err) {
                        window.location.reload();
                    }
                });
            });

            // Intercept clear cart: the clear form has id cartClearForm
            const clearForm = document.getElementById('cartClearForm');
            if (clearForm) {
                clearForm.addEventListener('submit', async e => {
                    e.preventDefault();
                    const action = clearForm.getAttribute('action');
                    try {
                        const payload = await sendJson(action, 'DELETE', {});
                        const cartCount = document.getElementById('cartCount');
                        if (cartCount && typeof payload.count !== 'undefined') cartCount.textContent =
                            String(payload.count);
                        const cartTotal = document.getElementById('cartTotal');
                        if (cartTotal && typeof payload.total !== 'undefined') cartTotal.textContent =
                            formatCurrency(payload.total);
                        // remove all rows
                        document.querySelectorAll('.order-cart-row').forEach(r => r.remove());
                        syncCheckoutInputs();
                    } catch (err) {
                        window.location.reload();
                    }
                });
            }

            syncCheckoutInputs();
        })();
    </script>
@endsection
