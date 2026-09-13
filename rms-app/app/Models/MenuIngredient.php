<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/** DEFENSE: recipe row — quantity_per_dish × order qty = stock needed */
class MenuIngredient extends Model
{
    use HasFactory;

    protected $fillable = ['menu_id', 'inventory_id', 'quantity_per_dish'];

    protected $casts = [
        'quantity_per_dish' => 'decimal:4',
    ];

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }
}
