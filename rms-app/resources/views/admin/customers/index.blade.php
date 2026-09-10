@extends('layouts.app')
@section('title', 'Customers')
@section('subtitle', 'Manage customer accounts')

@section('content')
    <div class="row g-4">
        @forelse($customers as $customer)
            <div class="col-xl-3 col-lg-4 col-md-6 fade-in">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <div
                            style="width:64px;height:64px;border-radius:16px;margin:0 auto 16px;display:flex;align-items:center;justify-content:center;font-size:24px;font-weight:700;color:#fff;background:linear-gradient(135deg,#06b6d4,#22d3ee)">
                            {{ strtoupper(substr($customer->name, 0, 1)) }}
                        </div>
                        <h6 class="fw-bold mb-1">{{ $customer->name }}</h6>
                        <div class="mb-2">
                            <span class="badge"
                                style="background:rgba(6,182,212,.1);color:#0891b2;font-size:11px;text-transform:capitalize">Customer</span>
                        </div>
                        <div style="font-size:13px;color:var(--text-muted)">
                            <div><i class="fas fa-envelope me-1"></i>{{ $customer->email }}</div>
                            @if ($customer->phone)
                                <div class="mt-1"><i class="fas fa-phone me-1"></i>{{ $customer->phone }}</div>
                            @endif
                        </div>
                        <div class="mt-2">
                            @if ($customer->is_active)
                                <span class="status-badge available" style="font-size:11px">Active</span>
                            @else
                                <span class="status-badge maintenance" style="font-size:11px">Inactive</span>
                            @endif
                        </div>
                        <div class="mt-3 d-flex gap-1 justify-content-center">
                            <form action="{{ route('customers.status', $customer) }}" method="POST"
                                onsubmit="return confirm('Change this customer status?')">
                                @csrf @method('PATCH')
                                <button class="btn btn-sm btn-outline-primary">
                                    {{ $customer->is_active ? 'Set Inactive' : 'Set Active' }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5 text-muted">
                <i class="fas fa-user-friends fa-3x mb-3"></i>
                <p>No customers yet</p>
            </div>
        @endforelse
    </div>

    <div class="mt-4">{{ $customers->links() }}</div>
@endsection
