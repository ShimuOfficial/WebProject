<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'menu_id', 'quantity', 'unit_price', 'subtotal', 'notes'];
    protected $casts = ['unit_price' => 'decimal:2', 'subtotal' => 'decimal:2'];

    public function order() { return $this->belongsTo(Order::class); }
    public function menu() { return $this->belongsTo(Menu::class); }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($item) { $item->subtotal = $item->quantity * $item->unit_price; });
        static::updating(function ($item) { $item->subtotal = $item->quantity * $item->unit_price; });
    }
}
