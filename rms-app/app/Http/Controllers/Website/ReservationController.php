<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/** DEFENSE Q10/Q11/Q17: Public booking — available tables JSON + locked create */
class ReservationController extends Controller
{
    /**
     * DEFENSE Q11: AJAX — free tables for date/slot/party_size.
     * Delegates to Reservation::availableTables().
     */
    public function available(Request $request)
    {
        $validated = $request->validate([
            'reservation_date' => 'required|date|after_or_equal:today',
            'time_slot' => ['required', Rule::in(Reservation::TIME_SLOTS)],
            'party_size' => 'required|integer|min:1|max:20',
        ]);

        if (Reservation::isSlotInThePast($validated['reservation_date'], $validated['time_slot'])) {
            return response()->json([
                'tables' => [],
                'message' => 'That time slot has already started or passed. Please pick a later time.',
            ]);
        }

        $tables = Reservation::availableTables(
            $validated['reservation_date'],
            $validated['time_slot'],
            (int) $validated['party_size']
        )->map(fn (Table $table) => [
            'id' => $table->id,
            'table_number' => $table->table_number,
            'capacity' => $table->capacity,
            'location' => $table->location,
            'label' => sprintf(
                '%s · %d seats%s',
                $table->table_number,
                $table->capacity,
                $table->location ? ' · ' . $table->location : ''
            ),
        ])->values();

        return response()->json([
            'tables' => $tables,
            'slot_minutes' => Reservation::slotDurationMinutes(),
            'message' => $tables->isEmpty()
                ? 'No tables free for that party size and time. Try another slot or smaller party.'
                : null,
        ]);
    }

    /**
     * DEFENSE Q10/Q17: Create reservation.
     * Phone: Reservation::PHONE_REGEX. Persist: Reservation::bookAtomically().
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:120',
            'phone' => ['required', 'string', 'max:15', 'regex:' . Reservation::PHONE_REGEX],
            'email' => 'nullable|email|max:120',
            'reservation_date' => 'required|date|after_or_equal:today',
            'time_slot' => ['required', Rule::in(Reservation::TIME_SLOTS)],
            'party_size' => 'required|integer|min:1|max:20',
            'table_id' => 'required|exists:tables,id',
            'notes' => 'nullable|string|max:500',
        ], [
            'phone.regex' => 'Enter a valid Bangladesh mobile number (e.g. 017XXXXXXXX). Digits only — no letters.',
            'table_id.required' => 'Select an available table before submitting.',
        ]);

        if (Reservation::isSlotInThePast($validated['reservation_date'], $validated['time_slot'])) {
            return back()
                ->withInput()
                ->withErrors(['time_slot' => 'That time slot has already started or passed. Please pick a later time.']);
        }

        $phone = preg_replace('/\s+/', '', $validated['phone']);
        if (str_starts_with($phone, '+88')) {
            $phone = substr($phone, 3);
        } elseif (str_starts_with($phone, '88') && strlen($phone) === 13) {
            $phone = substr($phone, 2);
        }

        $user = auth()->user();

        try {
            $reservation = Reservation::bookAtomically([
                'user_id' => ($user && $user->role === 'customer') ? $user->id : null,
                'table_id' => (int) $validated['table_id'],
                'name' => $validated['name'],
                'phone' => $phone,
                'email' => $validated['email'] ?? ($user->email ?? null),
                'reservation_date' => $validated['reservation_date'],
                'time_slot' => $validated['time_slot'],
                'party_size' => (int) $validated['party_size'],
                'notes' => $validated['notes'] ?? null,
                'status' => 'pending',
            ]);
        } catch (ValidationException $e) {
            return back()->withInput()->withErrors($e->errors());
        }

        $tableLabel = $reservation->table?->table_number ?? 'your table';

        return redirect()->to(route('contact') . '#reserve')
            ->with('success', "Reservation request received for {$tableLabel}. We will confirm shortly. Slot holds for " . Reservation::slotDurationMinutes() . ' minutes.');
    }
}
