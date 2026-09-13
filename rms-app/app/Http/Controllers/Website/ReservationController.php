<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** DEFENSE: §5.11 public booking — past slot + remainingSeats() */
class ReservationController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:120',
            'phone' => 'required|string|max:30',
            'email' => 'nullable|email|max:120',
            'reservation_date' => 'required|date|after_or_equal:today',
            'time_slot' => ['required', Rule::in(Reservation::TIME_SLOTS)],
            'party_size' => 'required|integer|min:1|max:20',
            'notes' => 'nullable|string|max:500',
        ]);

        $slotTime = $validated['time_slot'];
        if ($validated['reservation_date'] === now()->toDateString()) {
            $slotHour = (int) substr($slotTime, 0, 2);
            if ((int) now()->format('H') >= $slotHour) {
                return back()
                    ->withInput()
                    ->withErrors(['time_slot' => 'That time slot has already passed today. Please pick a later time.']);
            }
        }

        $remaining = Reservation::remainingSeats($validated['reservation_date'], $validated['time_slot']);
        if ($remaining < (int) $validated['party_size']) {
            return back()
                ->withInput()
                ->withErrors([
                    'party_size' => $remaining < 1
                        ? 'This date and time is fully booked. Please choose another slot.'
                        : "Only {$remaining} seat(s) left for that slot. Reduce party size or pick another time.",
                ]);
        }

        $user = auth()->user();
        Reservation::create([
            'user_id' => ($user && $user->role === 'customer') ? $user->id : null,
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? ($user->email ?? null),
            'reservation_date' => $validated['reservation_date'],
            'time_slot' => $validated['time_slot'],
            'party_size' => $validated['party_size'],
            'notes' => $validated['notes'] ?? null,
            'status' => 'pending',
        ]);

        return redirect()->to(route('contact') . '#reserve')
            ->with('success', 'Reservation request received. We will confirm your table shortly.');
    }
}
