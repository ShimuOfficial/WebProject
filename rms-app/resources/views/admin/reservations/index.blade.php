@extends('layouts.app')
@section('title', 'Reservations')
@section('subtitle', 'Online table bookings')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <form class="d-flex gap-2" method="GET">
            <select name="status" class="form-select" style="width:auto" onchange="this.form.submit()">
                <option value="">All statuses</option>
                @foreach (\App\Models\Reservation::STATUSES as $status)
                    <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                        {{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </form>
        <a href="{{ route('tables.index') }}" class="btn btn-outline-primary btn-sm">Manage tables</a>
    </div>

    <div class="card fade-in">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>Guest</th>
                            <th>When</th>
                            <th>Party</th>
                            <th>Status</th>
                            <th>Table</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reservations as $reservation)
                            <tr>
                                <td>
                                    <strong>{{ $reservation->name }}</strong>
                                    <div class="text-muted small">{{ $reservation->phone }}</div>
                                    @if ($reservation->notes)
                                        <div class="text-muted small">{{ $reservation->notes }}</div>
                                    @endif
                                </td>
                                <td>{{ $reservation->display_slot }}</td>
                                <td>{{ $reservation->party_size }}</td>
                                <td><span class="status-badge {{ $reservation->status }}">{{ ucfirst($reservation->status) }}</span></td>
                                <td>{{ $reservation->table->table_number ?? 'Unassigned' }}</td>
                                <td>
                                    <form class="d-flex gap-2 flex-wrap" method="POST"
                                        action="{{ route('reservations.update', $reservation) }}">
                                        @csrf
                                        @method('PATCH')
                                        <select name="table_id" class="form-select form-select-sm" style="width:140px">
                                            <option value="">No table</option>
                                            @foreach ($tables as $table)
                                                <option value="{{ $table->id }}"
                                                    {{ (string) $reservation->table_id === (string) $table->id ? 'selected' : '' }}>
                                                    {{ $table->table_number }} ({{ $table->capacity }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <select name="status" class="form-select form-select-sm" style="width:130px">
                                            @foreach (\App\Models\Reservation::STATUSES as $status)
                                                <option value="{{ $status }}"
                                                    {{ $reservation->status === $status ? 'selected' : '' }}>
                                                    {{ ucfirst($status) }}</option>
                                            @endforeach
                                        </select>
                                        <button class="btn btn-sm btn-primary">Save</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">No reservations yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3">{{ $reservations->links() }}</div>
@endsection
