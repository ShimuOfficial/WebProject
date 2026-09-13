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
    public function activeOrder() { return $this->hasOne(Order::class)->whereNotIn('status', ['completed', 'cancelled'])->latest(); }
    public function scopeAvailable($query) { return $query->where('status', 'available'); }
}
