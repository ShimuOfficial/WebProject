<div class="row g-4" style="min-height: 70vh;">
    @forelse($orders as $order)
        <div class="col-12 col-md-6 col-lg-4">
            <div class="kitchen-ticket {{ $order->status }} h-100 d-flex flex-column fade-in">
                <div class="ticket-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0 fw-bold">#{{ substr($order->order_number, -4) }}</h5>
                        <div class="text-xs muted"><i
                                class="fas fa-clock me-1"></i>{{ $order->created_at->format('H:i') }}
                            ({{ $order->created_at->diffForHumans(null, true, true) }})
                        </div>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-dark fw-bold fs-6">{{ $order->table->table_number ?? 'Takeaway' }}</span>
                    </div>
                </div>

                <div class="ticket-body flex-grow-1">
                    @foreach ($order->items as $item)
                        <div class="item-row">
                            <span>
                                <span class="qty-badge me-2">{{ $item->quantity }}x</span>
                                {{ $item->menu->name ?? 'Unknown Item' }}
                            </span>
                        </div>
                        @if ($item->notes)
                            <div class="item-notes"><i class="fas fa-exclamation-circle me-1"></i>{{ $item->notes }}
                            </div>
                        @endif
                    @endforeach
                </div>

                <div class="ticket-footer">
                    {{-- Allow chef to start cooking for pending or approved orders --}}
                    @if (in_array($order->status, ['pending', 'approved']))
                        <form action="{{ route('kitchen.update', $order) }}" method="POST" class="w-100">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="preparing">
                            <button class="btn btn-warning w-100 fw-bold text-dark"><i
                                    class="fas fa-fire me-2"></i>Start Cooking</button>
                        </form>
                    @elseif($order->status === 'preparing')
                        <form action="{{ route('kitchen.update', $order) }}" method="POST" class="w-100">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="ready">
                            <button class="btn btn-success w-100 fw-bold"><i class="fas fa-check-circle me-2"></i>Order
                                Ready</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 text-center py-5">
            <i class="fas fa-mug-hot fa-4x muted mb-3 no-opacity"></i>
            <h3 class="text-muted fw-bold">No active tickets</h3>
            <p class="text-muted">Kitchen is clear!</p>
        </div>
    @endforelse
</div>
