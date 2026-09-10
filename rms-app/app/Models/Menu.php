<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'category', 'description', 'price', 'image', 'is_available'];

    protected $casts = ['price' => 'decimal:2', 'is_available' => 'boolean'];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
    public function menuIngredients()
    {
        return $this->hasMany(MenuIngredient::class);
    }
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function getImageUrlAttribute(): string
    {
        $filename = filled($this->image) ? basename($this->image) : \Illuminate\Support\Str::slug($this->name) . '.jpg';
        $publicDish = public_path('images/dishes/' . $filename);

        if (is_file($publicDish)) {
            return asset('images/dishes/' . $filename);
        }

        if (filled($this->image) && str_starts_with($this->image, 'http')) {
            return $this->image;
        }

        if (filled($this->image) && is_file(storage_path('app/public/' . ltrim($this->image, '/')))) {
            return asset('storage/' . ltrim($this->image, '/'));
        }

        return asset('images/dishes/plain-rice.jpg');
    }

    public function getAvailableServingsAttribute(): int
    {
        $ingredients = $this->relationLoaded('menuIngredients')
            ? $this->menuIngredients
            : $this->menuIngredients()->with('inventory')->get();

        if ($ingredients->isEmpty()) {
            return 0;
        }

        $max = null;

        foreach ($ingredients as $ingredient) {
            $perDish = (float) $ingredient->quantity_per_dish;
            if ($perDish <= 0) {
                continue;
            }

            $onHand = (float) ($ingredient->inventory->quantity ?? 0);
            $servings = (int) floor(($onHand / $perDish) + 1e-9);
            $max = $max === null ? $servings : min($max, $servings);
        }

        return max(0, $max ?? 0);
    }

    public function isOrderable(): bool
    {
        return $this->is_available && $this->available_servings > 0;
    }

    public function estimateIngredients(float $dishCount = 1): array
    {
        return $this->menuIngredients
            ->map(function ($ingredient) use ($dishCount) {
                return [
                    'inventory_id' => $ingredient->inventory_id,
                    'item_name' => $ingredient->inventory->item_name ?? 'Unknown Item',
                    'unit' => $ingredient->inventory->unit ?? 'unit',
                    'quantity' => (float) $ingredient->quantity_per_dish * $dishCount,
                ];
            })
            ->toArray();
    }

    public static function syncAvailabilityFromInventory(): int
    {
        $menus = self::query()
            ->with(['menuIngredients.inventory'])
            ->get();

        $toDisable = [];

        foreach ($menus as $menu) {
            // Keep menu disabled when recipe is missing; it cannot be prepared reliably.
            if ($menu->menuIngredients->isEmpty()) {
                $shouldBeAvailable = false;
            } else {
                $shouldBeAvailable = $menu->available_servings > 0;
            }

            if (!$shouldBeAvailable && $menu->is_available) {
                $toDisable[] = $menu->id;
            }
        }

        $updated = 0;

        if (!empty($toDisable)) {
            $updated += self::query()->whereIn('id', $toDisable)->update(['is_available' => false]);
        }

        return $updated;
    }
}
