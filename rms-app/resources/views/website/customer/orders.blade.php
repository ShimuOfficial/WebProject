{{-- DEFENSE: §5.5 my orders + status tracker --}}
@extends('website.layouts.app')

@section('content')
    <section class="orders-page">
        <div class="container orders-wrap">
            <div class="orders-head">
                <div>
                    <div class="section-label">Customer</div>
                    <h1 class="orders-title">My Orders</h1>
                </div>
            </div>

            @if (session('success'))
                <div class="orders-notice">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
                <div class="orders-alert">{{ $errors->first() }}</div>
            @endif

            @forelse($orders as $order)
                <div class="orders-card">
                    <div class="orders-line">
                        <div>
                            <div class="orders-number">{{ $order->order_number }}</div>
                            <div class="orders-muted">Placed: {{ $order->created_at->format('M d, Y h:i A') }}</div>
                        </div>
                        <span class="orders-badge {{ $order->status }}">
                            {{ $order->status === 'completed' ? 'Delivered' : ucfirst($order->status) }}
                        </span>
                    </div>

                    <div class="orders-approval">
                        @if ($order->status === 'cancelled')
                            <strong style="color:#991b1b">This order was cancelled.</strong>
                        @elseif (!$order->is_customer_approved)
                            <strong style="color:#92400e">Waiting for restaurant approval.</strong>
                        @else
                            <strong style="color:#166534">Approved by restaurant. Processing started.</strong>
                        @endif
                    </div>

                    <div class="orders-track status-stepper" aria-label="Order progress">
                        @foreach ($trackSteps as $stepIndex => $step)
                            @php
                                $isCancelled = $order->status === 'cancelled';
                                $isComplete = !$isCancelled && $stepIndex < $order->customer_track_index;
                                $isCurrent = !$isCancelled && $stepIndex === $order->customer_track_index;
                            @endphp
                            <div
                                class="status-step {{ $isComplete ? 'is-complete' : '' }} {{ $isCurrent ? 'is-current' : '' }} {{ $isCancelled ? 'is-cancelled' : '' }}">
                                <span class="status-step-dot">
                                    @if ($isComplete)
                                        ✓
                                    @else
                                        {{ $stepIndex + 1 }}
                                    @endif
                                </span>
                                <span class="status-step-label">{{ $trackLabels[$step] ?? ucfirst($step) }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="orders-items">
                        @foreach ($order->items as $item)
                            <div class="orders-muted">{{ $item->quantity }}x {{ $item->menu->name ?? 'Item' }}</div>
                        @endforeach
                    </div>

                    @if (!empty($order->notes))
                        <div class="orders-note" style="margin-top:8px;color:#374151;font-size:14px;white-space:pre-wrap">
                            <strong>Note:</strong>
                            <div style="margin-top:4px">{!! nl2br(e($order->notes)) !!}</div>
                        </div>
                    @endif

                    <div class="orders-line" style="margin-top:10px">
                        <div class="orders-muted">
                            @if (($order->payment_status ?? 'unpaid') === 'paid')
                                Payment: {{ $order->payment_method_label }} paid
                            @elseif (($order->payment_status ?? 'unpaid') === 'refunded')
                                Payment: refunded ({{ $order->payment_method_label }})
                            @elseif (($order->payment_status ?? 'unpaid') === 'partial')
                                Payment: {{ $order->payment_method_label }} partially paid
                            @elseif ($order->is_cash_on_delivery)
                                Payment: Cash on delivery
                            @elseif (($order->payment_method ?? '') === 'sslcommerz')
                                Payment: SSLCommerz pending
                            @else
                                Payment: {{ $order->payment_method_label }} pending
                            @endif
                        </div>
                        <div class="orders-total">Total: BDT {{ number_format($order->total_amount, 2) }}</div>
                    </div>

                    <div class="orders-pay-row">
                        <div class="orders-muted">
                            @if ($order->status === 'cancelled' && ($order->payment_status ?? '') === 'refunded')
                                Cancelled. Refund in progress.
                            @elseif ($order->status === 'cancelled')
                                Cancelled. No payment was collected.
                            @elseif (($order->payment_status ?? 'unpaid') === 'paid')
                                Paid successfully.
                            @elseif ($order->is_cash_on_delivery)
                                unpaid.
                            @else
                                Please pay on delivery.
                            @endif
                        </div>
                        @if ($order->canBeCancelledByCustomer())
                            <form method="POST" action="{{ route('customer.orders.cancel', $order) }}"
                                onsubmit="return confirm('Cancel this order?')">
                                @csrf
                                <button class="btn btn-outline btn-rect" type="submit">Cancel order</button>
                            </form>
                        @endif
                    </div>
                    <p class="orders-muted" style="margin-top:8px">{{ $order->cancellationPolicyHint() }}</p>
                </div>
            @empty
                <div class="orders-card">
                    <div class="orders-empty">No customer orders yet.</div>
                    <div class="orders-muted">Place your first order from the Order Now menu.</div>
                </div>
            @endforelse

            {{ $orders->links() }}

            @include('website.partials.refund-policy')
        </div>
    </section>
@endsection
