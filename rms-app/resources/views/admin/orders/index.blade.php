@extends('layouts.app')
@section('title', 'Orders')
@section('subtitle', 'Manage all restaurant orders')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <form class="d-flex gap-2 flex-wrap" method="GET">
            <select name="status" class="form-select filter-control-auto" onchange="this.form.submit()">
                <option value="">All Status</option>
                @foreach (['pending', 'approved', 'preparing', 'ready', 'served', 'completed', 'cancelled'] as $s)
                    <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>
                        {{ $s === 'ready' ? 'Ready' : ucfirst($s) }}
                    </option>
                @endforeach
            </select>
            <select name="payment_status" class="form-select filter-control-auto" onchange="this.form.submit()">
                <option value="">All Payments</option>
                @foreach (['unpaid', 'partial', 'paid'] as $ps)
                    <option value="{{ $ps }}" {{ request('payment_status') == $ps ? 'selected' : '' }}>
                        {{ ucfirst($ps) }}</option>
                @endforeach
            </select>
            <input type="date" name="date" class="form-control filter-control-auto" value="{{ request('date') }}"
                onchange="this.form.submit()">
        </form>
        <a href="{{ route('orders.create') }}" class="btn btn-primary"><i class="fas fa-plus me-2"></i>New Order</a>
    </div>

    <div class="card fade-in">
        <div class="card-body p-0">
            <div class="table-responsive text-nowrap">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Table</th>
                            <th>Source</th>
                            <th>Items</th>
                            <th>Status</th>
                            <th>Payment</th>
                            <th>Total</th>
                            <th>Time</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td><strong>{{ $order->order_number }}</strong></td>
                                <td><span class="badge bg-light text-dark">{{ $order->table->table_number ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <span class="text-sm muted">
                                        {{ ($order->order_source ?? 'staff') === 'customer' ? 'Customer' : 'Staff' }}
                                    </span>
                                </td>
                                <td>{{ $order->items->count() }} items</td>
                                <td><span class="status-badge {{ $order->status }}">{{ $order->display_status }}</span>
                                </td>
                                <td>
                                    @if (($order->order_source ?? 'staff') === 'customer' && $order->status !== 'completed')
                                        <span class="badge bg-info text-dark">COD</span>
                                    @elseif ($order->payment_status === 'paid')
                                        <span class="badge bg-success">Paid</span>
                                    @elseif($order->payment_status === 'partial')
                                        <span class="badge bg-warning text-dark">Partial</span>
                                    @elseif(($order->payment_method ?? '') === 'cash')
                                        <span class="badge bg-info text-dark">CASH</span>
                                    @elseif($order->payment_method)
                                        <span class="badge bg-warning text-dark">Payment Pending</span>
                                    @else
                                        <span class="badge bg-secondary">Unpaid</span>
                                    @endif
                                    @if ($order->payment_method)
                                        <div class="text-xs muted">
                                            {{ $order->payment_method_label }}</div>
                                    @endif
                                </td>
                                <td><strong>৳{{ number_format($order->total_amount, 2) }}</strong></td>
                                <td class="text-sm muted">
                                    {{ $order->created_at->format('M d, H:i') }}</td>
                                <td>
                                    <div class="d-flex gap-1">
                                        @if (($order->order_source ?? 'staff') === 'customer' && !$order->is_customer_approved)
                                            <form action="{{ route('orders.approve-customer', $order) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button class="btn btn-sm btn-outline-success" title="Approve">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <a href="{{ route('orders.edit', $order) }}" class="btn btn-sm btn-outline-primary"
                                            title="Edit"><i class="fas fa-edit"></i></a>
                                        <form action="{{ route('orders.destroy', $order) }}" method="POST"
                                            onsubmit="return confirm('Delete this order?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" title="Delete"><i
                                                    class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-5">
                                    <i class="fas fa-receipt fa-3x mb-3 muted"></i>
                                    <p class="text-muted">No orders found</p>
                                    <a href="{{ route('orders.create') }}" class="btn btn-primary btn-sm">Create First
                                        Order</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="mt-3">{{ $orders->withQueryString()->links() }}</div>

    @push('scripts')
        <script>
            // Invoice auto-print/download removed - May 14, 2026
            // Use receipt view instead for order details
        </script>
    @endpush
@endsection
