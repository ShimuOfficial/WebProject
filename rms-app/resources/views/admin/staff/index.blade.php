@extends('layouts.app')
@section('title', 'Staff')
@section('subtitle', 'Manage your team members')

@section('content')
    <div class="d-flex justify-content-end mb-4">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStaffModal"><i
                class="fas fa-user-plus me-2"></i>Add Staff</button>
    </div>

    <div class="row g-4">
        @forelse($staff as $member)
            <div class="col-xl-3 col-lg-4 col-md-6 fade-in">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <div
                            style="width:64px;height:64px;border-radius:16px;margin:0 auto 16px;display:flex;align-items:center;justify-content:center;font-size:24px;font-weight:700;color:#fff;background:linear-gradient(135deg,{{ ['manager' => '#6366f1,#8b5cf6', 'chef' => '#f59e0b,#fbbf24', 'cashier' => '#06b6d4,#22d3ee'][$member->role] ?? '#6b7280,#9ca3af' }})">
                            {{ strtoupper(substr($member->name, 0, 1)) }}
                        </div>
                        <h6 class="fw-bold mb-1">{{ $member->name }}</h6>
                        <div class="mb-2"><span class="badge"
                                style="background:rgba(99,102,241,.1);color:var(--primary);font-size:11px;text-transform:capitalize">{{ $member->role }}</span>
                        </div>
                        <div style="font-size:13px;color:var(--text-muted)">
                            <div><i class="fas fa-envelope me-1"></i>{{ $member->email }}</div>
                            @if ($member->phone)
                                <div class="mt-1"><i class="fas fa-phone me-1"></i>{{ $member->phone }}</div>
                            @endif
                        </div>
                        <div class="mt-2">
                            @if ($member->is_active)
                                <span class="status-badge available" style="font-size:11px">Active</span>
                            @else
                                <span class="status-badge maintenance" style="font-size:11px">Inactive</span>
                            @endif
                        </div>
                        <div class="mt-3 d-flex gap-1 justify-content-center">
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                data-bs-target="#editStaff{{ $member->id }}"><i class="fas fa-edit"></i></button>
                            <form action="{{ route('staff.destroy', $member) }}" method="POST"
                                onsubmit="return confirm('Remove this staff member?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5 text-muted">
                <i class="fas fa-users fa-3x mb-3"></i>
                <p>No staff members yet</p>
            </div>
        @endforelse
    </div>
    <div class="mt-4">{{ $staff->links() }}</div>

    @foreach ($staff as $member)
        <!-- Edit Modal -->
        <div class="modal fade" id="editStaff{{ $member->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content" style="border-radius:16px;border:none">
                    <div class="modal-header">
                        <h6 class="modal-title fw-bold">Edit {{ $member->name }}</h6><button class="btn-close"
                            data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('staff.update', $member) }}" method="POST">
                            @csrf @method('PUT')
                            <div class="mb-3"><label class="form-label fw-semibold">Name</label><input type="text"
                                    name="name" class="form-control" value="{{ $member->name }}" required></div>
                            <div class="mb-3"><label class="form-label fw-semibold">Email</label><input type="email"
                                    name="email" class="form-control" value="{{ $member->email }}" required></div>
                            <div class="mb-3"><label class="form-label fw-semibold">Role</label>
                                <select name="role" class="form-select">
                                    @foreach (['manager', 'chef', 'cashier'] as $r)
                                        <option value="{{ $r }}" {{ $member->role == $r ? 'selected' : '' }}>
                                            {{ ucfirst($r) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3"><label class="form-label fw-semibold">Phone</label><input type="text"
                                    name="phone" class="form-control" value="{{ $member->phone }}"></div>
                            <div class="mb-3"><label class="form-label fw-semibold">New Password (leave blank to
                                    keep)</label><input type="password" name="password" class="form-control"></div>
                            <div class="mb-3 form-check form-switch">
                                <input type="checkbox" name="is_active" class="form-check-input"
                                    id="active{{ $member->id }}" value="1"
                                    {{ $member->is_active ? 'checked' : '' }}>
                                <label class="form-check-label" for="active{{ $member->id }}">Active</label>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Update Staff</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Add Staff Modal -->
    <div class="modal fade" id="addStaffModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content" style="border-radius:16px;border:none">
                <div class="modal-header">
                    <h6 class="modal-title fw-bold"><i class="fas fa-user-plus me-2"></i>Add Staff Member</h6><button
                        class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('staff.store') }}" method="POST">
                        @csrf
                        <div class="mb-3"><label class="form-label fw-semibold">Full Name *</label><input
                                type="text" name="name" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label fw-semibold">Email *</label><input type="email"
                                name="email" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label fw-semibold">Password *</label><input
                                type="password" name="password" class="form-control" minlength="8" required></div>
                        <div class="mb-3"><label class="form-label fw-semibold">Role *</label>
                            <select name="role" class="form-select">
                                <option value="chef">Chef</option>
                                <option value="cashier">Cashier</option>
                                <option value="manager">Manager</option>

                            </select>
                        </div>
                        <div class="mb-3"><label class="form-label fw-semibold">Phone</label><input type="text"
                                name="phone" class="form-control"></div>
                        <button type="submit" class="btn btn-primary w-100">Add Staff</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
