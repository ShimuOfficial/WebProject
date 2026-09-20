{{-- DEFENSE: §5.11 contact + reservation form with live table availability --}}
@extends('website.layouts.app')
@section('content')
    <section class="location" id="contact">
        <div class="container">
            <div class="section-label">{{ $site['content']['contact_label'] ?? 'Contact us' }}</div>
            <h2 class="section-title">{{ $site['content']['contact_title'] ?? 'Location & hours' }}</h2>
            <p class="section-sub">{{ $site['content']['contact_intro'] ?? 'Dine in, pick up, or order delivery. Reserve a table online — no phone call needed.' }}</p>
            <div class="contact-photo">
                <img src="{{ $site['contact_image_url'] }}" alt="{{ $site['name'] }} dining room">
            </div>
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
                        <div class="section-label">{{ $site['content']['reserve_label'] ?? 'Book a table' }}</div>
                        <h2 class="section-title">{{ $site['content']['reserve_title'] ?? 'Online reservation' }}</h2>
                        <p class="section-sub">{{ $site['content']['reserve_intro'] ?? 'Pick date, time, and party size, then choose a free table by number and capacity.' }}</p>
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
                                <span>
                                    {{ $booking->display_slot }} · {{ $booking->party_size }} guests
                                    @if ($booking->table)
                                        · Table {{ $booking->table->table_number }}
                                    @endif
                                </span>
                                <span class="orders-badge {{ $booking->status }}">{{ ucfirst($booking->status) }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif

                <form class="reserve-form" method="POST" action="{{ route('reservations.store') }}" id="reserveForm">
                    @csrf
                    <label>
                        <span class="label-title">Full name</span>
                        <input type="text" name="name" value="{{ old('name', auth()->user()->name ?? '') }}" required>
                    </label>
                    <label>
                        <span class="label-title">Phone</span>
                        <input type="tel" name="phone" id="reservePhone"
                            inputmode="numeric" pattern="^(?:\+?88)?01[3-9]\d{8}$"
                            placeholder="017XXXXXXXX" maxlength="14"
                            value="{{ old('phone', auth()->user()->phone ?? '') }}" required>
                    </label>
                    <label>
                        <span class="label-title">Email</span>
                        <input type="email" name="email" value="{{ old('email', auth()->user()->email ?? '') }}">
                    </label>
                    <label>
                        <span class="label-title">Date</span>
                        <input type="date" name="reservation_date" id="reserveDate" min="{{ now()->toDateString() }}"
                            value="{{ old('reservation_date', now()->toDateString()) }}" required>
                    </label>
                    <label>
                        <span class="label-title">Time slot ({{ \App\Models\Reservation::slotDurationMinutes() }} min)</span>
                        <select name="time_slot" id="reserveSlot" required>
                            @foreach ($reservationSlots as $slot)
                                <option value="{{ $slot }}" {{ old('time_slot') === $slot ? 'selected' : '' }}>
                                    {{ $slot }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label>
                        <span class="label-title">Party size</span>
                        <input type="number" name="party_size" id="reserveParty" min="1" max="20"
                            value="{{ old('party_size', 2) }}" required>
                    </label>
                    <label class="reserve-table-field">
                        <span class="label-title">Available table</span>
                        <select name="table_id" id="reserveTable" required>
                            <option value="">Select date, time &amp; party size first</option>
                        </select>
                        <small id="reserveTableHint" class="section-sub" style="display:block;margin-top:6px"></small>
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

@push('scripts')
<script>
(function () {
    const dateEl = document.getElementById('reserveDate');
    const slotEl = document.getElementById('reserveSlot');
    const partyEl = document.getElementById('reserveParty');
    const tableEl = document.getElementById('reserveTable');
    const hintEl = document.getElementById('reserveTableHint');
    const phoneEl = document.getElementById('reservePhone');
    const availableUrl = @json(route('reservations.available'));
    const oldTableId = @json(old('table_id'));

    phoneEl?.addEventListener('input', () => {
        phoneEl.value = phoneEl.value.replace(/[^\d+]/g, '');
    });

    async function refreshTables() {
        if (!dateEl || !slotEl || !partyEl || !tableEl) return;
        const params = new URLSearchParams({
            reservation_date: dateEl.value,
            time_slot: slotEl.value,
            party_size: partyEl.value || '1',
        });
        tableEl.innerHTML = '<option value=\"\">Loading…</option>';
        hintEl.textContent = '';
        try {
            const res = await fetch(availableUrl + '?' + params.toString(), {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();
            tableEl.innerHTML = '';
            if (!data.tables || !data.tables.length) {
                tableEl.innerHTML = '<option value=\"\">No tables available</option>';
                hintEl.textContent = data.message || 'Try another time or party size.';
                return;
            }
            tableEl.innerHTML = '<option value=\"\">Choose a table</option>';
            data.tables.forEach(t => {
                const opt = document.createElement('option');
                opt.value = t.id;
                opt.textContent = t.label;
                if (String(oldTableId) === String(t.id)) opt.selected = true;
                tableEl.appendChild(opt);
            });
            hintEl.textContent = data.tables.length + ' table(s) free for this slot.';
        } catch (e) {
            tableEl.innerHTML = '<option value=\"\">Could not load tables</option>';
            hintEl.textContent = 'Please refresh and try again.';
        }
    }

    [dateEl, slotEl, partyEl].forEach(el => el?.addEventListener('change', refreshTables));
    refreshTables();
})();
</script>
@endpush
