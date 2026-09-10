@extends('layouts.app')
@section('title', 'Inventory')
@section('subtitle', 'Track stock and supplies')

@section('content')
    <div class="admin-toolbar">
        <form class="admin-toolbar-form" method="GET">
            <div class="input-group admin-search">
                <span class="input-group-text"><i class="fas fa-search text-muted"></i></span>
                <input type="text" name="search" class="form-control" placeholder="Search items..."
                    value="{{ request('search') }}">
            </div>
            <a href="{{ route('inventory.index', ['low_stock' => 1]) }}"
                class="btn {{ request('low_stock') ? 'btn-danger' : 'btn-outline-danger' }} btn-sm">
                <i class="fas fa-exclamation-triangle me-1"></i>Low Stock
            </a>
            <a href="{{ route('inventory.index', ['out_of_stock' => 1]) }}"
                class="btn {{ request('out_of_stock') ? 'btn-dark' : 'btn-outline-dark' }} btn-sm">
                <i class="fas fa-ban me-1"></i>Out of Stock
            </a>
        </form>
        @if ($canManageInventory)
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addItemModal"><i
                    class="fas fa-plus me-2"></i>Add Ingredients</button>
        @endif
    </div>

    <div class="card fade-in">
        <div class="card-body p-0">
            <div class="table-responsive text-nowrap">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Category</th>
                            <th>Quantity</th>
                            <th>Unit</th>
                            <th>Min Qty</th>
                            <th>Cost/Unit</th>
                            <th>Supplier</th>
                            <th>Status</th>
                            @if ($canManageInventory)
                                <th>Actions</th>
                            @endif
                        </tr>

                    </thead>
                    <tbody>
                        @forelse($items as $item)
                            <tr>
                                <td><strong>{{ $item->item_name }}</strong></td>
                                <td><span class="badge bg-light text-dark">{{ $item->category ?? '—' }}</span></td>
                                <td>
                                    <span
                                        class="{{ $item->isLowStock() ? 'text-danger fw-bold' : '' }}">{{ $item->quantity }}</span>
                                </td>
                                <td>{{ $item->unit }}</td>
                                <td>{{ $item->min_quantity }}</td>
                                <td>৳{{ number_format($item->cost_per_unit, 2) }}</td>
                                <td>{{ $item->supplier ?? '—' }}</td>
                                <td>
                                    @if ($item->isOutOfStock())
                                        <span class="status-badge cancelled"><i class="fas fa-ban me-1"></i>Out of Stock</span>
                                    @elseif ($item->isLowStock())
                                        <span class="status-badge cancelled"><i class="fas fa-exclamation me-1"></i>Low
                                            Stock</span>
                                    @else
                                        <span class="status-badge available">In Stock</span>
                                    @endif
                                </td>
                                @if ($canManageInventory)
                                    <td>
                                        <div class="d-flex gap-1">
                                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                                data-bs-target="#editItem{{ $item->id }}"><i
                                                    class="fas fa-edit"></i></button>
                                            <form action="{{ route('inventory.destroy', $item) }}" method="POST"
                                                onsubmit="return confirm('Delete?')">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger"><i
                                                        class="fas fa-trash"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $canManageInventory ? 9 : 8 }}" class="text-center py-5 text-muted">
                                    <i class="fas fa-boxes-stacked fa-3x mb-3"></i>
                                    <p>No inventory items</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="mt-3">{{ $items->withQueryString()->links() }}</div>

    @if ($canManageInventory)
        @foreach ($items as $item)
            <!-- Edit Modal -->
            <div class="modal fade" id="editItem{{ $item->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content" style="border-radius:16px;border:none">
                        <div class="modal-header">
                            <h6 class="modal-title fw-bold">Edit {{ $item->item_name }}</h6><button class="btn-close"
                                data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('inventory.update', $item) }}" method="POST">
                                @csrf @method('PUT')
                                <div class="row g-3">
                                    <div class="col-6"><label class="form-label fw-semibold">Item Name</label><input
                                            type="text" name="item_name" class="form-control"
                                            value="{{ $item->item_name }}" required></div>
                                    <div class="col-6"><label class="form-label fw-semibold">Category</label><input
                                            type="text" name="category" class="form-control"
                                            value="{{ $item->category }}">
                                    </div>
                                    <div class="col-4"><label class="form-label fw-semibold">Quantity</label><input
                                            type="number" name="quantity" class="form-control" step="0.01"
                                            value="{{ $item->quantity }}" required></div>
                                    <div class="col-4"><label class="form-label fw-semibold">Unit</label><input
                                            type="text" name="unit" class="form-control" value="{{ $item->unit }}"
                                            required></div>
                                    <div class="col-4"><label class="form-label fw-semibold">Min Qty</label><input
                                            type="number" name="min_quantity" class="form-control" step="0.01"
                                            value="{{ $item->min_quantity }}" required></div>
                                    <div class="col-6"><label class="form-label fw-semibold">Cost/Unit</label><input
                                            type="number" name="cost_per_unit" class="form-control" step="0.01"
                                            value="{{ $item->cost_per_unit }}" required></div>
                                    <div class="col-6"><label class="form-label fw-semibold">Supplier</label><input
                                            type="text" name="supplier" class="form-control"
                                            value="{{ $item->supplier }}"></div>
                                    <div class="col-12"><label class="form-label fw-semibold">Notes</label>
                                        <textarea name="notes" class="form-control" rows="2">{{ $item->notes }}</textarea>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary w-100 mt-3">Update Item</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        <!-- Add Item Modal -->
        <div class="modal fade" id="addItemModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content" style="border-radius:16px;border:none">
                    <div class="modal-header">
                        <h6 class="modal-title fw-bold"><i class="fas fa-plus-circle me-2"></i>Add Inventory Item</h6>
                        <button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('inventory.store') }}" method="POST">
                            @csrf
                            <div class="row g-3">
                                <div class="col-6"><label class="form-label fw-semibold">Item Name *</label><input
                                        type="text" name="item_name" class="form-control" required></div>
                                <div class="col-6"><label class="form-label fw-semibold">Category</label><input
                                        type="text" name="category" class="form-control"
                                        placeholder="e.g. Produce, Dairy"></div>
                                <div class="col-4"><label class="form-label fw-semibold">Quantity *</label><input
                                        type="number" name="quantity" class="form-control" step="0.01"
                                        min="0" required></div>
                                <div class="col-4"><label class="form-label fw-semibold">Unit *</label><input
                                        type="text" name="unit" class="form-control" placeholder="kg, L, pcs"
                                        required></div>
                                <div class="col-4"><label class="form-label fw-semibold">Min Qty *</label><input
                                        type="number" name="min_quantity" class="form-control" step="0.01"
                                        min="0" required></div>
                                
                                <div class="col-6"><label class="form-label fw-semibold">Cost/Unit *</label><input
                                        type="number" name="cost_per_unit" class="form-control" step="0.01"
                                        min="0" required></div>
                                <div class="col-6"><label class="form-label fw-semibold">Supplier</label><input
                                        type="text" name="supplier" class="form-control"></div>
                                <div class="col-12"><label class="form-label fw-semibold">Notes</label>
                                    <textarea name="notes" class="form-control" rows="2"></textarea>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 mt-3">Add Item</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
