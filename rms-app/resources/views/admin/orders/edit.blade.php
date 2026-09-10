@extends('layouts.app')
@section('title', 'Edit Order')
@section('subtitle', 'Update order ' . $order->order_number)

@section('content')
    <div class="row g-4">
        <div class="col-lg-8">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <div class="fw-bold mb-1">Unable to complete payment</div>
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card mb-4">
                <div class="card-header"><i class="fas fa-info-circle me-2"></i>Order Information</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-muted text-xs">ORDER NUMBER</label>
                            <div class="fw-bold text-lg">{{ $order->order_number }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-muted text-xs">TABLE</label>
                            <div class="fw-bold">{{ $order->table->table_number ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-muted text-xs">SOURCE</label>
                            <div class="fw-bold text-capitalize">
                                {{ $order->is_online_order ? 'Customer (Online)' : 'Staff (Onsite)' }}
                            </div>
                        </div>
                        @if ($order->is_online_order)
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-muted text-xs">CUSTOMER NAME</label>
                                <div class="fw-bold">{{ $order->user->name ?? 'N/A' }}</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-muted text-xs">PHONE</label>
                                <div class="fw-bold">{{ $order->user->phone ?? 'N/A' }}</div>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label fw-semibold text-muted text-xs">ADDRESS</label>
                                <div class="fw-bold">{{ $order->user->address ?? 'N/A' }}</div>
                            </div>
                        @endif
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-muted text-xs">APPROVAL</label>
                            <div>
                                @if ($order->is_online_order)
                                    @if ($order->is_customer_approved)
                                        <span class="badge bg-success">Approved</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @endif
                                @else
                                    <span class="badge bg-light text-dark">N/A</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-muted text-xs">PAYMENT STATUS</label>
                            <div>
                                @if (($order->order_source ?? 'staff') === 'customer' && $order->status !== 'completed')
                                    <span class="badge bg-info text-dark">COD Due</span>
                                @elseif ($order->payment_status === 'paid')
                                    <span class="badge bg-success">Paid</span>
                                @elseif($order->payment_status === 'partial')
                                    <span class="badge bg-warning text-dark">Partial</span>
                                @elseif(($order->payment_method ?? '') === 'cash')
                                    <span class="badge bg-info text-dark">COD Due</span>
                                @elseif($order->payment_method)
                                    <span class="badge bg-warning text-dark">Payment Pending</span>
                                @else
                                    <span class="badge bg-secondary">Unpaid</span>
                                @endif
                                @if ($order->payment_method)
                                    <div class="text-xs muted mt-1">{{ $order->payment_method_label }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="card">
                <div class="card-header"><i class="fas fa-list me-2"></i>Order Items</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Price</th>
                                    <th>Qty</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->items as $item)
                                    <tr>
                                        <td><strong>{{ $item->menu->name ?? 'Deleted Item' }}</strong>
                                            @if ($item->notes)
                                                <br><small class="text-muted">{{ $item->notes }}</small>
                                            @endif
                                        </td>
                                        <td>৳{{ number_format($item->unit_price, 2) }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td><strong>৳{{ number_format($item->subtotal, 2) }}</strong></td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-light">
                                    <td colspan="3" class="text-end fw-bold">Total</td>
                                    <td class="fw-bold text-lg">
                                        ৳{{ number_format($order->total_amount, 2) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Update Status -->
        <div class="col-lg-4">
            <div class="card sticky-top-100">
                <div class="card-header"><i class="fas fa-sync-alt me-2"></i>Update Status</div>
                <div class="card-body">
                    <form action="{{ route('orders.update', $order) }}" method="POST">
                        @csrf @method('PUT')
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Current Status</label>
                            <div class="mb-2"><span class="status-badge {{ $order->status }} text-base">
                                    {{ $order->display_status }}</span></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Change To</label>
                            <select id="statusSelect" name="status" class="form-select"
                                {{ $order->status === 'cancelled' ? 'disabled' : '' }}>
                                @php
                                    $transitions = [
                                        'pending' => ['pending', 'approved', 'cancelled'],
                                        'approved' => ['approved', 'preparing', 'cancelled'],
                                        'preparing' => ['preparing', 'ready', 'cancelled'],
                                        'ready' => ['ready', 'served', 'completed', 'cancelled'],
                                        'served' => ['served', 'completed'],
                                        'completed' => ['completed'],
                                        'cancelled' => ['cancelled'],
                                    ];
                                    $allowed = $transitions[$order->status] ?? [$order->status];
                                @endphp

                                @foreach ($allowed as $s)
                                    <option value="{{ $s }}" {{ $order->status == $s ? 'selected' : '' }}>
                                        {{ ucfirst($s) }}</option>
                                @endforeach
                            </select>

                            @if ($order->status === 'cancelled')
                                <input type="hidden" name="status" value="cancelled">
                                <div class="text-xs muted mt-1">
                                    Status locked because this order is cancelled.
                                </div>
                            @endif
                        </div>
                        <input type="hidden" name="notes" value="{{ $order->notes }}">
                        <button type="submit" class="btn btn-primary w-100"><i class="fas fa-save me-2"></i>Update
                            Order</button>
                    </form>

                    @if ($order->is_online_order)
                        <div class="mt-3" id="payment-panel">
                            @if (!$order->is_customer_approved)
                                <form action="{{ route('orders.approve-customer', $order) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button class="btn btn-outline-success w-100" type="submit">
                                        <i class="fas fa-check me-2"></i>Approve Customer Order
                                    </button>
                                </form>
                            @elseif ($order->status !== 'completed')
                                <form action="{{ route('orders.pay', $order) }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Payment Method</label>
                                        <select name="payment_method" class="form-select" required>
                                            <option value="">Select method</option>
                                            @foreach (['cash' => 'COD', 'rocket' => 'Rocket', 'card' => 'Card'] as $value => $label)
                                                <option value="{{ $value }}"
                                                    {{ old('payment_method', $order->payment_method) === $value ? 'selected' : '' }}>
                                                    {{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Payment Amount</label>
                                        <input type="number" name="paid_amount" step="0.01" min="0.01"
                                            max="{{ $order->due_amount }}" class="form-control"
                                            value="{{ old('paid_amount', $order->due_amount > 0 ? $order->due_amount : 0) }}"
                                            required>
                                        <small class="text-muted d-block">Order total:
                                            ৳{{ number_format($order->total_amount, 2) }}</small>
                                        <small class="text-muted d-block">Already paid:
                                            ৳{{ number_format($alreadyPaid, 2) }}</small>
                                        <small class="text-muted d-block">Due now:
                                            ৳{{ number_format($order->due_amount, 2) }}</small>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Reference (Txn ID)</label>
                                        <input type="text" name="payment_reference" class="form-control"
                                            value="{{ old('payment_reference', $order->payment_reference) }}"
                                            placeholder="Required for bKash, Rocket, Card">
                                    </div>

                                    @if ($order->paid_at)
                                        <div class="mb-3 text-sm muted">
                                            <i class="fas fa-check-circle me-1"></i>Last payment:
                                            {{ $order->paid_at->format('M d, Y H:i') }}
                                        </div>
                                    @endif

                                    <button id="pay-submit" type="submit" class="btn btn-success w-100"
                                        {{ $order->due_amount <= 0 ? 'disabled' : '' }}>
                                        <i
                                            class="fas fa-credit-card me-2"></i>{{ $order->due_amount <= 0 ? 'Paid' : 'Pay & Print' }}
                                    </button>
                                </form>
                            @endif

                            @if (($order->payment_status ?? 'unpaid') === 'paid')
                                <div class="mt-3 alert alert-success">
                                    <i class="fas fa-check-circle me-2"></i>Payment confirmed. Receipt is available via
                                    view receipt button above.
                                </div>
                            @elseif ($order->is_online_order && $order->status !== 'completed')
                                <div class="alert alert-info mt-3 mb-0 text-sm">
                                    Waiting for payment confirmation.
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <hr>
                <div class="text-sm muted">
                    <div class="mb-1"><i class="fas fa-clock me-1"></i>Created:
                        {{ $order->created_at->format('M d, Y H:i') }}</div>
                    <div><i class="fas fa-edit me-1"></i>Updated: {{ $order->updated_at->format('M d, Y H:i') }}</div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const params = new URLSearchParams(window.location.search);
            if (params.get('pay') !== '1') return;

            const panel = document.getElementById('payment-panel');
            if (!panel) return;

            panel.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });

            const paymentType = panel.querySelector('select[name="payment_method"]');
            if (paymentType) paymentType.focus({
                preventScroll: true
            });
        });
    </script>
@endpush
