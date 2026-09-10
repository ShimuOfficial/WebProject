@extends('layouts.app')
@section('title', 'Settings')
@section('subtitle', 'Manage your account details and password')

@section('content')
    <div class="row g-4">
        <div class="col-xl-7 col-lg-8 fade-in">
            <div class="card">
                <div class="card-header"><i class="fas fa-user-cog me-2"></i>Account Settings</div>
                <div class="card-body">
                    <form action="{{ route('settings.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Name</label>
                            <input type="text" name="name" class="form-control"
                                value="{{ old('name', auth()->user()->name) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control"
                                value="{{ old('email', auth()->user()->email) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Phone</label>
                            <input type="text" name="phone" class="form-control"
                                value="{{ old('phone', auth()->user()->phone) }}" placeholder="Optional">
                        </div>

                        <hr>
                        <h6 class="fw-bold mb-3">Change Password</h6>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Current Password</label>
                            <input type="password" name="current_password" class="form-control"
                                placeholder="Required if changing password">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">New Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Minimum 8 characters">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Confirm New Password</label>
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>

                        <button class="btn btn-primary"><i class="fas fa-save me-2"></i>Save Settings</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-xl-5 col-lg-4 fade-in fade-in-delay-1">
            <div class="card h-100">
                <div class="card-header"><i class="fas fa-id-badge me-2"></i>Current Account</div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="text-muted" style="font-size:12px">Role</div>
                        <div class="fw-semibold" style="text-transform:capitalize">{{ auth()->user()->role ?? 'staff' }}
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted" style="font-size:12px">Status</div>
                        @if (auth()->user()->is_active ?? true)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </div>
                    <div>
                        <div class="text-muted" style="font-size:12px">Last Updated</div>
                        <div class="fw-semibold">{{ auth()->user()->updated_at?->format('M d, Y h:i A') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
