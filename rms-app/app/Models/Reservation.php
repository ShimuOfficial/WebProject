<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * DEFENSE: §5.11 reservations — remainingSeats() = table capacity − booked party size
 */
class Reservation extends Model
{
    use HasFactory;

    public const STATUSES = ['pending', 'confirmed', 'cancelled', 'completed'];

    public const TIME_SLOTS = [
        '11:00', '12:00', '13:00', '14:00', '15:00',
        '17:00', '18:00', '19:00', '20:00', '21:00',
    ];

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

    public static function remainingSeats(string $date, string $slot, ?int $ignoreId = null): int
    {
        $capacity = (int) Table::query()
            ->where('table_number', '!=', 'ONLINE')
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
}
