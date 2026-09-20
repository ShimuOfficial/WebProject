<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

/** DEFENSE: §5.11 admin confirm / cancel table bookings with locking */
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
                ->where('table_number', '!=', config('restaurant.delivery.online_table_number', 'ONLINE'))
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

        try {
            DB::transaction(function () use ($validated, $reservation) {
                $reservation = Reservation::query()->whereKey($reservation->id)->lockForUpdate()->firstOrFail();

                $tableId = $validated['table_id'] ?? $reservation->table_id;

                if ($validated['status'] === 'confirmed') {
                    if (!$tableId) {
                        throw ValidationException::withMessages([
                            'table_id' => 'Assign a table before confirming.',
                        ]);
                    }

                    $table = Table::query()->whereKey($tableId)->lockForUpdate()->firstOrFail();

                    if ((int) $table->capacity < (int) $reservation->party_size) {
                        throw ValidationException::withMessages([
                            'table_id' => "Table {$table->table_number} is too small for this party.",
                        ]);
                    }

                    if (!Reservation::isTableFree(
                        (int) $tableId,
                        $reservation->reservation_date->toDateString(),
                        $reservation->time_slot,
                        $reservation->id
                    )) {
                        throw ValidationException::withMessages([
                            'table_id' => 'That table already has an overlapping booking for this slot.',
                        ]);
                    }

                    $table->update(['status' => 'reserved']);
                }

                $reservation->update([
                    'status' => $validated['status'],
                    'table_id' => $tableId,
                ]);

                if (in_array($validated['status'], ['cancelled', 'completed'], true) && $reservation->table_id) {
                    $stillHeld = Reservation::query()
                        ->where('table_id', $reservation->table_id)
                        ->whereIn('status', ['pending', 'confirmed'])
                        ->where('id', '!=', $reservation->id)
                        ->whereDate('reservation_date', '>=', now()->toDateString())
                        ->exists();

                    if (!$stillHeld) {
                        Table::where('id', $reservation->table_id)
                            ->where('status', 'reserved')
                            ->update(['status' => 'available']);
                    }
                }
            });
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }

        return back()->with('success', 'Reservation updated.');
    }
}
