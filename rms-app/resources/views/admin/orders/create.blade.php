{{-- DEFENSE: §5.6 staff dine-in create order --}}
@extends('layouts.app')
@section('title', 'Create Order')
@section('subtitle', 'Add a new order')

@section('content')
    <form method="POST" action="{{ route('orders.store') }}" id="orderForm" novalidate>
        @csrf

        <div class="alert alert-danger fade show d-none" id="orderFormAlert" role="alert"
            data-auto-hide="{{ config('order_messages.ui.alert_autohide_ms', 3000) }}"></div>

        @if ($unconfiguredMenuCount > 0)
            <div class="alert alert-warning fade show" role="alert">
                <i class="fas fa-triangle-exclamation me-2"></i>
                {{ $unconfiguredMenuCount }} menu item(s) are missing recipe ingredients and cannot be ordered yet.
                Configure them in Menu Edit.
            </div>
        @endif

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header"><i class="fas fa-chair me-2"></i>Order Details</div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Table *</label>
                                <select name="table_id" class="form-select @error('table_id') is-invalid @enderror"
                                    required>
                                    <option value="">Select a table from the list</option>
                                    @foreach ($tables as $table)
                                        <option value="{{ $table->id }}"
                                            {{ old('table_id') == $table->id ? 'selected' : '' }}>
                                            {{ $table->table_number }} ({{ $table->capacity }} seats) —
                                            {{ ucfirst($table->status) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Notes</label>
                                <input type="text" name="notes" class="form-control"
                                    placeholder="Special instructions..." value="{{ old('notes') }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header menu-items-header">
                        <div><i class="fas fa-utensils me-2"></i>Menu Items</div>
                        <div class="menu-search-wrap">
                            <i class="fas fa-search"></i>
                            <input type="search" id="menuSearch" class="form-control form-control-sm"
                                placeholder="Search menu item...">
                        </div>
                    </div>
                    <div class="card-body">
                        @foreach ($menuItems as $category => $items)
                            <div class="menu-category-group">
                                <h6 class="fw-bold text-uppercase mb-3 mt-2 menu-category-title">
                                    <i class="fas fa-tag me-1"></i>{{ $category }}
                                </h6>
                                <div class="row g-3 mb-4">
                                    @foreach ($items as $item)
                                        <div class="col-12 col-sm-6 col-lg-4 menu-item-tile min-w-0"
                                            data-search="{{ Str::lower($item->name . ' ' . $item->category . ' ' . $item->description) }}">
                                            <div class="border rounded-3 p-3 h-100 menu-select-card {{ $item->menu_ingredients_count > 0 ? 'is-clickable' : 'is-disabled' }}"
                                                onclick="toggleItem({{ $item->id }}, this)">
                                                <div class="menu-select-card__media">
                                                    <img src="{{ $item->image_url }}" alt="{{ $item->name }}"
                                                        onerror="this.src='{{ $site['dish_fallback_url'] ?? asset('images/dishes/plain-rice.jpg') }}'">
                                                </div>
                                                <div class="d-flex justify-content-between align-items-start gap-2">
                                                    <div class="min-w-0">
                                                        <div class="fw-semibold menu-item-name">{{ $item->name }}
                                                        </div>
                                                        <div class="menu-item-description">
                                                            {{ Str::limit($item->description, 40) }}</div>
                                                        <div class="mt-1 menu-item-meta">
                                                            {{ $item->menu_ingredients_count }} ingredient(s) per dish
                                                        </div>
                                                        <div class="selected-indicator d-none mt-1 menu-selected-indicator">
                                                            <i class="fas fa-check-circle me-1"></i>Selected
                                                            <span class="selected-qty"></span>
                                                        </div>
                                                        @if ($item->menu_ingredients_count === 0)
                                                            <div class="mt-1 menu-recipe-missing">Recipe
                                                                missing: add ingredients in Menu Edit</div>
                                                        @endif
                                                    </div>
                                                    <span
                                                        class="badge bg-primary">৳{{ number_format($item->price, 2) }}</span>
                                                </div>
                                                <div class="mt-2 qty-control d-none">
                                                    <div class="input-group input-group-sm"
                                                        onclick="event.stopPropagation()">
                                                        <button type="button" class="btn btn-outline-secondary"
                                                            onclick="changeQty({{ $item->id }}, -1)">−</button>
                                                        <input type="number" class="form-control text-center qty-input-sm"
                                                            id="qty-{{ $item->id }}" min="1" step="1"
                                                            value="1" onchange="updateOrderSummary()">
                                                        <button type="button" class="btn btn-outline-secondary"
                                                            onclick="changeQty({{ $item->id }}, 1)">+</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                        <div class="text-center text-muted py-4 d-none" id="menuSearchEmpty">
                            <i class="fas fa-search mb-2"></i>
                            <div class="menu-empty-note">No menu item found</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card order-summary-sticky">
                    <div class="card-header"><i class="fas fa-shopping-cart me-2"></i>Order Summary</div>
                    <div class="card-body">
                        <div id="orderSummary">
                            <div class="text-center py-4 text-muted" id="emptyMsg">
                                <i class="fas fa-cart-plus fa-2x mb-2"></i>
                                <p class="menu-empty-note">Click menu items to add</p>
                            </div>
                        </div>

                        <div id="orderItems"></div>
                        <hr class="d-none" id="totalDivider">
                        <div class="d-flex justify-content-between fw-bold d-none" id="totalRow">
                            <span>Total</span><span id="totalAmount">৳0.00</span>
                        </div>

                        <div class="mt-3 d-none" id="inventoryNeedBox">
                            <div class="fw-semibold mb-1 stock-after-order-title">Stock After Order</div>
                            <div id="inventoryNeedList" class="small text-muted"></div>
                        </div>

                        <div class="mt-3">
                            <label class="form-label fw-semibold">Payment Method *</label>
                            <select name="payment_method" id="paymentMethod" class="form-select" required>
                                <option value="">Select payment method</option>
                                @foreach (['cash' => 'Cash', 'rocket' => 'Rocket', 'card' => 'Card'] as $value => $label)
                                    <option value="{{ $value }}"
                                        {{ old('payment_method') === $value ? 'selected' : '' }}>{{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mt-3 {{ old('payment_method') === 'cash' || session('server_paid') ? '' : 'd-none' }}"
                            id="paidAmountBox">
                            <label class="form-label fw-semibold">Paid Amount (Cash)</label>
                            <input type="number" name="paid_amount" id="paidAmount" class="form-control"
                                step="0.01" placeholder="Enter received amount"
                                value="{{ old('paid_amount', session('server_paid') ?? '') }}">
                        </div>

                        <div class="mt-2 {{ session('server_change') ? '' : 'd-none' }}" id="changeBox">
                            <div class="fw-semibold">Change: <span
                                    id="changeAmount">৳{{ session('server_change') ?? '0.00' }}</span></div>
                        </div>

                        <div class="mt-3 d-none" id="paymentReferenceBox">
                            <label class="form-label fw-semibold">Reference (Txn ID) *</label>
                            <input type="text" name="payment_reference" id="paymentReference" class="form-control"
                                value="{{ old('payment_reference') }}" placeholder="Required for bKash, Rocket, Card">
                        </div>

                        <button type="submit" class="btn btn-primary w-100 mt-3" id="submitBtn" disabled>
                            <i class="fas fa-credit-card me-2"></i>Pay
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div id="hiddenInputs"></div>
    </form>

    <script id="orderCreateData" type="application/json">@json($orderCreateData)</script>
@endsection

@push('scripts')
    @vite('resources/js/admin/orders/create.js')
@endpush
