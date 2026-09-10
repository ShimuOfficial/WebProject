<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = ['item_name', 'category', 'quantity', 'unit', 'min_quantity', 'cost_per_unit', 'supplier', 'notes'];
    protected $casts = ['quantity' => 'decimal:2', 'min_quantity' => 'decimal:2', 'cost_per_unit' => 'decimal:2'];

    public function menuIngredients()
    {
        return $this->hasMany(MenuIngredient::class);
    }
    public function isLowStock()
    {
        return $this->quantity <= $this->min_quantity;
    }

    public function isOutOfStock(): bool
    {
        return (float) $this->quantity <= 0;
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('quantity', '<=', 'min_quantity');
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('quantity', '<=', 0);
    }
}
