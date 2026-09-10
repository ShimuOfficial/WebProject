@extends('layouts.app')
@section('title', 'Tables')
@section('subtitle', 'Manage restaurant tables and seating')

@section('content')
    <div class="admin-toolbar">
        <div class="d-flex gap-3 flex-wrap">
            <div class="d-flex align-items-center gap-2 text-sm"><span class="dot dot-success"></span> Available</div>
            <div class="d-flex align-items-center gap-2 text-sm"><span class="dot dot-danger"></span> Occupied</div>
            <div class="d-flex align-items-center gap-2 text-sm"><span class="dot dot-warning"></span> Reserved</div>
            <div class="d-flex align-items-center gap-2 text-sm"><span class="dot" style="background:#6b7280"></span> Maintenance</div>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTableModal"><i
                class="fas fa-plus me-2"></i>Add Table</button>
    </div>

    <div class="row g-3 g-lg-4">
        @forelse($tables as $table)
            <div class="col-6 col-sm-4 col-md-3 col-xl-2 fade-in min-w-0">
                <div class="table-card {{ $table->status }}">
                    <i class="fas fa-chair fa-2x mb-2 no-opacity"></i>
                    <div class="table-num">{{ $table->table_number }}</div>
                    <div class="table-cap"><i class="fas fa-users me-1"></i>{{ $table->capacity }} seats</div>
                    @if ($table->location)
                        <div class="table-cap mt-1"><i
                                class="fas fa-map-marker-alt me-1"></i>{{ ucfirst($table->location) }}</div>
                    @endif
                    <div class="mt-2"><span
                            class="status-badge {{ $table->status }} text-xs">{{ ucfirst($table->status) }}</span></div>
                    @if ($table->activeOrder)
                    @endif
                    <div class="mt-3 d-flex gap-1 justify-content-center">
                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                            data-bs-target="#editTable{{ $table->id }}" title="Edit"><i
                                class="fas fa-edit"></i></button>
                        <form action="{{ route('tables.destroy', $table) }}" method="POST"
                            onsubmit="return confirm('Delete?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" title="Delete"><i
                                    class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </div>

            </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class="fas fa-chair fa-3x mb-3 text-muted"></i>
                <p class="text-muted">No tables added yet</p>
            </div>
        @endforelse
    </div>

    @foreach ($tables as $table)
        <!-- Edit -->
        <div class="modal fade" id="editTable{{ $table->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content modal-rounded">
                    <div class="modal-header">
                        <h6 class="modal-title fw-bold">Edit Table {{ $table->table_number }}</h6><button class="btn-close"
                            data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('tables.update', $table) }}" method="POST">
                            @csrf @method('PUT')
                            <div class="mb-3"><label class="form-label fw-semibold">Table Number</label><input
                                    type="text" name="table_number" class="form-control"
                                    value="{{ $table->table_number }}" required></div>
                            <div class="mb-3"><label class="form-label fw-semibold">Capacity</label><input type="number"
                                    name="capacity" class="form-control" value="{{ $table->capacity }}" min="1"
                                    required></div>
                            <div class="mb-3"><label class="form-label fw-semibold">Status</label>
                                <select name="status" class="form-select">
                                    @foreach (['available', 'occupied', 'reserved', 'maintenance'] as $s)
                                        <option value="{{ $s }}" {{ $table->status == $s ? 'selected' : '' }}>
                                            {{ ucfirst($s) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3"><label class="form-label fw-semibold">Location</label><input type="text"
                                    name="location" class="form-control" value="{{ $table->location }}"
                                    placeholder="e.g. Indoor, Outdoor, VIP"></div>
                            <button type="submit" class="btn btn-primary w-100">Update Table</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Add Table Modal -->
    <div class="modal fade" id="addTableModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content modal-rounded">
                <div class="modal-header">
                    <h6 class="modal-title fw-bold"><i class="fas fa-plus-circle me-2"></i>Add New Table</h6><button
                        class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('tables.store') }}" method="POST">
                        @csrf
                        <div class="mb-3"><label class="form-label fw-semibold">Table Number *</label><input
                                type="text" name="table_number" class="form-control" placeholder="e.g. T-01" required>
                        </div>
                        <div class="mb-3"><label class="form-label fw-semibold">Capacity *</label><input type="number"
                                name="capacity" class="form-control" min="1" value="4" required></div>
                        <div class="mb-3"><label class="form-label fw-semibold">Location</label><input type="text"
                                name="location" class="form-control" placeholder="Indoor, Outdoor, VIP"></div>
                        <button type="submit" class="btn btn-primary w-100">Add Table</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
