<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** DEFENSE: §5.11 staff confirm / cancel table bookings */
class ReservationController extends Controller
{
    public function index(Request $request)
    {
        $query = Reservation::query()->with(['table', 'user'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return view('admin.reservations.index', [
            'reservations' => $query->paginate(15)->withQueryString(),
            'tables' => Table::query()
                ->where('table_number', '!=', 'ONLINE')
                ->orderBy('table_number')
                ->get(),
        ]);
    }

    public function update(Request $request, Reservation $reservation)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(Reservation::STATUSES)],
            'table_id' => 'nullable|exists:tables,id',
        ]);

        if ($validated['status'] === 'confirmed') {
            $remaining = Reservation::remainingSeats(
                $reservation->reservation_date->toDateString(),
                $reservation->time_slot,
                $reservation->id
            );

            if ($remaining < $reservation->party_size) {
                return back()->withErrors([
                    'status' => 'Not enough seats left to confirm this reservation.',
                ]);
            }
        }

        $reservation->update([
            'status' => $validated['status'],
            'table_id' => $validated['table_id'] ?? $reservation->table_id,
        ]);

        if (!empty($validated['table_id']) && $validated['status'] === 'confirmed') {
            Table::where('id', $validated['table_id'])->update(['status' => 'reserved']);
        }

        return back()->with('success', 'Reservation updated.');
    }
}
