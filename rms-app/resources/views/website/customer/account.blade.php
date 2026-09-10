@extends('website.layouts.app')

@section('content')
    <section class="account-page">
        <div class="container account-wrap">
            <div class="account-head">
                <div>
                    <div class="section-label">Customer</div>
                    <h1 class="account-title">My Account</h1>
                </div>
                <div class="account-head-actions">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn btn-primary" type="submit">Logout</button>
                    </form>
                </div>
            </div>

            @if (session('success'))
                <div class="account-notice">{{ session('success') }}</div>
            @endif

            <section class="account-card">
                <form class="account-form" method="POST" action="{{ route('customer.account.update') }}">
                    @csrf
                    @method('PUT')
                    <div class="account-grid">
                        <label class="account-field">
                            <span class="account-label">Name</span>
                            <input class="account-input" type="text" value="{{ auth()->user()->name }}" readonly>
                        </label>
                        <label class="account-field">
                            <span class="account-label">Email</span>
                            <input class="account-input" type="email" value="{{ auth()->user()->email }}" readonly>
                        </label>
                        <label class="account-field">
                            <span class="account-label">Phone</span>
                            <input class="account-input" type="text" value="{{ auth()->user()->phone }}" readonly>
                        </label>
                    </div>

                    <label class="account-field account-field-full">
                        <span class="account-label">Address</span>
                        <textarea class="account-input" name="address" rows="3" placeholder="House, road, city">{{ old('address', auth()->user()->address) }}</textarea>
                    </label>

                    <p class="account-help">
                        Name, email, and phone cannot be changed after registration. You can update your address here.
                    </p>

                    <div class="account-actions">
                        <button class="btn btn-primary" type="submit">Save Changes</button>
                        <a class="btn btn-outline" href="{{ route('customer.orders') }}">View My Orders</a>
                    </div>
                </form>
            </section>
        </div>
    </section>
@endsection