<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * DEFENSE: §5.11 reservations — table locking + slot overlap checks
 */
class Reservation extends Model
{
    use HasFactory;

    public const STATUSES = ['pending', 'confirmed', 'cancelled', 'completed'];

    public const TIME_SLOTS = [
        '11:00', '12:00', '13:00', '14:00', '15:00',
        '17:00', '18:00', '19:00', '20:00', '21:00',
    ];

    /** DEFENSE Q17: Bangladesh mobile only — digits, optional +880 prefix. */
    public const PHONE_REGEX = '/^(?:\+?88)?01[3-9]\d{8}$/';

    protected $fillable = [
        'user_id',
        'table_id',
        'name',
        'phone',
        'email',
        'reservation_date',
        'time_slot',
        'party_size',
        'notes',
        'status',
    ];

    protected $casts = [
        'reservation_date' => 'date',
        'party_size' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function getDisplaySlotAttribute(): string
    {
        $date = $this->reservation_date?->format('M d, Y') ?? '';

        return trim($date . ' at ' . $this->time_slot);
    }

    /**
     * DEFENSE Q9/Q11: Slot length (default 60 min) from config/restaurant.php
     */
    public static function slotDurationMinutes(): int
    {
        return (int) config('restaurant.reservation_slot_minutes', 60);
    }

    /** DEFENSE Q9: Slot start datetime in app timezone (Asia/Dhaka). */
    public static function slotStart(string $date, string $slot): Carbon
    {
        return Carbon::parse($date . ' ' . $slot, config('app.timezone'));
    }

    /** DEFENSE Q9: Slot end = start + duration. */
    public static function slotEnd(string $date, string $slot): Carbon
    {
        return static::slotStart($date, $slot)->addMinutes(static::slotDurationMinutes());
    }

    /** DEFENSE Q9: Reject booking if slot start is already in the past. */
    public static function isSlotInThePast(string $date, string $slot): bool
    {
        return static::slotStart($date, $slot)->lte(now());
    }

    /**
     * DEFENSE Q9: Two slots overlap if [startA,endA) intersects [startB,endB).
     * Used so a 19:00 booking blocks overlapping holds on the same table.
     */
    public static function slotsOverlap(string $date, string $slotA, string $slotB): bool
    {
        $startA = static::slotStart($date, $slotA);
        $endA = static::slotEnd($date, $slotA);
        $startB = static::slotStart($date, $slotB);
        $endB = static::slotEnd($date, $slotB);

        return $startA->lt($endB) && $startB->lt($endA);
    }

    public static function remainingSeats(string $date, string $slot, ?int $ignoreId = null): int
    {
        $capacity = (int) Table::query()
            ->where('table_number', '!=', config('restaurant.delivery.online_table_number', 'ONLINE'))
            ->where('status', '!=', 'maintenance')
            ->sum('capacity');

        $booked = (int) static::query()
            ->whereDate('reservation_date', $date)
            ->where('time_slot', $slot)
            ->whereIn('status', ['pending', 'confirmed'])
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->sum('party_size');

        return max(0, $capacity - $booked);
    }

    /**
     * DEFENSE Q11: List free tables for party size + date/slot.
     * Filters capacity >= party_size and excludes tables with overlapping bookings.
     * Board: "6 joner table kivabe dekhay?" → availableTables() + ReservationController@available
     */
    public static function availableTables(string $date, string $slot, int $partySize, ?int $ignoreReservationId = null)
    {
        $online = config('restaurant.delivery.online_table_number', 'ONLINE');

        $busyTableIds = static::query()
            ->whereDate('reservation_date', $date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->whereNotNull('table_id')
            ->when($ignoreReservationId, fn ($q) => $q->where('id', '!=', $ignoreReservationId))
            ->get(['id', 'table_id', 'time_slot'])
            ->filter(fn ($row) => static::slotsOverlap($date, $slot, $row->time_slot))
            ->pluck('table_id')
            ->unique()
            ->values()
            ->all();

        return Table::query()
            ->where('table_number', '!=', $online)
            ->where('status', '!=', 'maintenance')
            ->where('capacity', '>=', $partySize)
            ->when(!empty($busyTableIds), fn ($q) => $q->whereNotIn('id', $busyTableIds))
            ->orderBy('capacity')
            ->orderBy('table_number')
            ->get();
    }

    /**
     * DEFENSE Q10: Inside a transaction — lock existing bookings for this table, check overlap.
     * lockForUpdate() serializes concurrent booking attempts on the same rows.
     */
    public static function isTableFree(int $tableId, string $date, string $slot, ?int $ignoreReservationId = null): bool
    {
        $conflicts = static::query()
            ->where('table_id', $tableId)
            ->whereDate('reservation_date', $date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->when($ignoreReservationId, fn ($q) => $q->where('id', '!=', $ignoreReservationId))
            ->lockForUpdate()
            ->get(['id', 'time_slot']);

        foreach ($conflicts as $row) {
            if (static::slotsOverlap($date, $slot, $row->time_slot)) {
                return false;
            }
        }

        return true;
    }

    /**
     * DEFENSE Q10: Atomic book — DB::transaction + table lockForUpdate + isTableFree.
     * Two users cannot reserve the same table for overlapping slots.
     * Board: "Double booking kivabe prevent?" → bookAtomically()
     */
    public static function bookAtomically(array $attributes): self
    {
        return DB::transaction(function () use ($attributes) {
            // Lock the physical table row so concurrent requests wait here.
            $table = Table::query()
                ->whereKey($attributes['table_id'])
                ->lockForUpdate()
                ->firstOrFail();

            if ($table->table_number === config('restaurant.delivery.online_table_number', 'ONLINE')) {
                abort(422, 'That table cannot be reserved.');
            }

            if ($table->status === 'maintenance') {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'table_id' => 'That table is under maintenance.',
                ]);
            }

            if ((int) $table->capacity < (int) $attributes['party_size']) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'party_size' => "Table {$table->table_number} seats {$table->capacity}. Choose a larger table or reduce party size.",
                ]);
            }

            if (!static::isTableFree(
                (int) $table->id,
                $attributes['reservation_date'],
                $attributes['time_slot'],
                $attributes['ignore_id'] ?? null
            )) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'table_id' => 'That table was just booked for an overlapping time. Please pick another table or slot.',
                ]);
            }

            unset($attributes['ignore_id']);

            return static::create($attributes);
        });
    }
}
