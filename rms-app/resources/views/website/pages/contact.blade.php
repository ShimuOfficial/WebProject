{{-- DEFENSE: §5.11 contact + reservation form --}}
@extends('website.layouts.app')
@section('content')
    <section class="location" id="contact">
        <div class="container">
            <div class="section-label">Contact Us</div>
            <h2 class="section-title">Location &amp; Hours</h2>
            <p class="section-sub">Dine in, pickup, or order delivery. Reserve a table online — no phone call needed.</p>
            <div class="grid"
                style="--grid-cols: 2; --grid-cols-md: 2; --grid-cols-sm: 1; --grid-gap: 24px; --grid-align: start;">
                <div class="location-card">
                    <strong>Address</strong>
                    <span class="section-sub">{{ $site['address'] }}</span>
                    <div class="location-actions">
                        <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($site['address']) }}"
                            class="btn btn-outline" target="_blank" rel="noopener">Get Directions</a>
                        <a href="tel:{{ $site['phone_href'] }}" class="btn btn-outline">{{ $site['phone'] }}</a>
                    </div>
                </div>
                <div class="location-card">
                    <strong>Hours</strong>
                    <div class="hours-list">
                        @foreach ($site['hours'] as $hour)
                            <div class="hours-row">
                                <span>{{ $hour['label'] ?? 'Daily' }}</span>
                                <span>{{ $hour['time'] ?? '11AM - 11PM' }}</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="section-sub">{{ $site['phone'] }} &bull; {{ $site['email'] }}</div>
                </div>
            </div>

            <div class="reserve-panel" id="reserve">
                <div class="section-head" style="margin-bottom:18px">
                    <div>
                        <div class="section-label">Book a table</div>
                        <h2 class="section-title">Online Reservation</h2>
                        <p class="section-sub">Choose a date, time slot, and party size. We will confirm your booking.</p>
                    </div>
                </div>

                @if (session('success'))
                    <div class="orders-notice">{{ session('success') }}</div>
                @endif
                @if ($errors->any())
                    <div class="orders-alert">{{ $errors->first() }}</div>
                @endif

                @if (($upcomingReservations ?? collect())->isNotEmpty())
                    <div class="reserve-upcoming">
                        <strong>Your upcoming bookings</strong>
                        @foreach ($upcomingReservations as $booking)
                            <div class="reserve-upcoming-row">
                                <span>{{ $booking->display_slot }} · {{ $booking->party_size }} guests</span>
                                <span class="orders-badge {{ $booking->status }}">{{ ucfirst($booking->status) }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif

                <form class="reserve-form" method="POST" action="{{ route('reservations.store') }}">
                    @csrf
                    <label>
                        <span class="label-title">Full name</span>
                        <input type="text" name="name" value="{{ old('name', auth()->user()->name ?? '') }}" required>
                    </label>
                    <label>
                        <span class="label-title">Phone</span>
                        <input type="tel" name="phone" value="{{ old('phone', auth()->user()->phone ?? '') }}" required>
                    </label>
                    <label>
                        <span class="label-title">Email</span>
                        <input type="email" name="email" value="{{ old('email', auth()->user()->email ?? '') }}">
                    </label>
                    <label>
                        <span class="label-title">Date</span>
                        <input type="date" name="reservation_date" min="{{ now()->toDateString() }}"
                            value="{{ old('reservation_date', now()->toDateString()) }}" required>
                    </label>
                    <label>
                        <span class="label-title">Time slot</span>
                        <select name="time_slot" required>
                            @foreach ($reservationSlots as $slot)
                                <option value="{{ $slot }}" {{ old('time_slot') === $slot ? 'selected' : '' }}>
                                    {{ $slot }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label>
                        <span class="label-title">Party size</span>
                        <input type="number" name="party_size" min="1" max="20"
                            value="{{ old('party_size', 2) }}" required>
                    </label>
                    <label class="reserve-notes">
                        <span class="label-title">Special requests</span>
                        <textarea name="notes" rows="3" placeholder="Window seat, birthday, high chair...">{{ old('notes') }}</textarea>
                    </label>
                    <div class="reserve-submit">
                        <button class="btn btn-primary btn-rect" type="submit">Reserve Table</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
