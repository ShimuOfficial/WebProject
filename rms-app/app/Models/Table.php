<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/** DEFENSE: §5.10 dine-in tables; table_number ONLINE is delivery (not booked as a seat) */
class Table extends Model
{
    use HasFactory;

    protected $fillable = ['table_number', 'capacity', 'status', 'location'];

    public function orders() { return $this->hasMany(Order::class); }
    public function reservations() { return $this->hasMany(Reservation::class); }
    public function activeOrder() { return $this->hasOne(Order::class)->whereNotIn('status', ['completed', 'cancelled'])->latest(); }
    public function scopeAvailable($query) { return $query->where('status', 'available'); }
    public function scopeBookable($query)
    {
        return $query
            ->where('table_number', '!=', config('restaurant.delivery.online_table_number', 'ONLINE'))
            ->where('status', '!=', 'maintenance');
    }
}
