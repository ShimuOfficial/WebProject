<?php

namespace Database\Seeders;

use App\Models\Inventory;
use App\Models\Menu;
use App\Models\MenuIngredient;
use Illuminate\Database\Seeder;

class MenuRecipeSeeder extends Seeder
{
    public function run(): void
    {
        $recipes = [
            'Chicken Biryani' => [
                ['item' => 'Basmati Rice', 'qty' => 0.18, 'unit' => 'kg'],
                ['item' => 'Chicken', 'qty' => 0.22, 'unit' => 'kg'],
                ['item' => 'Potatoes', 'qty' => 0.10, 'unit' => 'kg'],
                ['item' => 'Onions', 'qty' => 0.05, 'unit' => 'kg'],
                ['item' => 'Biryani Masala', 'qty' => 0.015, 'unit' => 'kg'],
                ['item' => 'Ghee', 'qty' => 0.015, 'unit' => 'kg'],
            ],
            'Beef Tehari' => [
                ['item' => 'Chinigura Rice', 'qty' => 0.18, 'unit' => 'kg'],
                ['item' => 'Beef', 'qty' => 0.16, 'unit' => 'kg'],
                ['item' => 'Mustard Oil', 'qty' => 0.02, 'unit' => 'L'],
                ['item' => 'Onions', 'qty' => 0.06, 'unit' => 'kg'],
                ['item' => 'Garam Masala', 'qty' => 0.008, 'unit' => 'kg'],
            ],
            'Kacchi Biryani' => [
                ['item' => 'Basmati Rice', 'qty' => 0.20, 'unit' => 'kg'],
                ['item' => 'Mutton', 'qty' => 0.22, 'unit' => 'kg'],
                ['item' => 'Potatoes', 'qty' => 0.12, 'unit' => 'kg'],
                ['item' => 'Yogurt', 'qty' => 0.05, 'unit' => 'kg'],
                ['item' => 'Biryani Masala', 'qty' => 0.018, 'unit' => 'kg'],
                ['item' => 'Ghee', 'qty' => 0.02, 'unit' => 'kg'],
            ],
            'Morog Polao' => [
                ['item' => 'Chinigura Rice', 'qty' => 0.18, 'unit' => 'kg'],
                ['item' => 'Chicken', 'qty' => 0.25, 'unit' => 'kg'],
                ['item' => 'Ghee', 'qty' => 0.02, 'unit' => 'kg'],
                ['item' => 'Onions', 'qty' => 0.05, 'unit' => 'kg'],
                ['item' => 'Garam Masala', 'qty' => 0.008, 'unit' => 'kg'],
            ],
            'Plain Rice' => [
                ['item' => 'Miniket Rice', 'qty' => 0.18, 'unit' => 'kg'],
            ],
            'Chicken Curry' => [
                ['item' => 'Chicken', 'qty' => 0.22, 'unit' => 'kg'],
                ['item' => 'Onions', 'qty' => 0.06, 'unit' => 'kg'],
                ['item' => 'Garlic', 'qty' => 0.01, 'unit' => 'kg'],
                ['item' => 'Ginger', 'qty' => 0.01, 'unit' => 'kg'],
                ['item' => 'Cooking Oil', 'qty' => 0.025, 'unit' => 'L'],
                ['item' => 'Turmeric Powder', 'qty' => 0.004, 'unit' => 'kg'],
                ['item' => 'Chili Powder', 'qty' => 0.004, 'unit' => 'kg'],
            ],
            'Beef Bhuna' => [
                ['item' => 'Beef', 'qty' => 0.20, 'unit' => 'kg'],
                ['item' => 'Onions', 'qty' => 0.08, 'unit' => 'kg'],
                ['item' => 'Garlic', 'qty' => 0.012, 'unit' => 'kg'],
                ['item' => 'Ginger', 'qty' => 0.012, 'unit' => 'kg'],
                ['item' => 'Mustard Oil', 'qty' => 0.02, 'unit' => 'L'],
                ['item' => 'Garam Masala', 'qty' => 0.006, 'unit' => 'kg'],
            ],
            'Mutton Rezala' => [
                ['item' => 'Mutton', 'qty' => 0.22, 'unit' => 'kg'],
                ['item' => 'Yogurt', 'qty' => 0.06, 'unit' => 'kg'],
                ['item' => 'Cream', 'qty' => 0.03, 'unit' => 'L'],
                ['item' => 'Ghee', 'qty' => 0.018, 'unit' => 'kg'],
                ['item' => 'Cardamom', 'qty' => 0.002, 'unit' => 'kg'],
                ['item' => 'Cinnamon', 'qty' => 0.002, 'unit' => 'kg'],
            ],
            'Rui Fish Curry' => [
                ['item' => 'Rui Fish', 'qty' => 0.25, 'unit' => 'kg'],
                ['item' => 'Mustard Oil', 'qty' => 0.02, 'unit' => 'L'],
                ['item' => 'Onions', 'qty' => 0.05, 'unit' => 'kg'],
                ['item' => 'Tomatoes', 'qty' => 0.06, 'unit' => 'kg'],
                ['item' => 'Turmeric Powder', 'qty' => 0.004, 'unit' => 'kg'],
            ],
            'Prawn Malai Curry' => [
                ['item' => 'Prawns', 'qty' => 0.20, 'unit' => 'kg'],
                ['item' => 'Cream', 'qty' => 0.06, 'unit' => 'L'],
                ['item' => 'Onions', 'qty' => 0.05, 'unit' => 'kg'],
                ['item' => 'Ginger', 'qty' => 0.008, 'unit' => 'kg'],
                ['item' => 'Garam Masala', 'qty' => 0.005, 'unit' => 'kg'],
            ],
            'Mixed Vegetable Bhaji' => [
                ['item' => 'Mixed Vegetables', 'qty' => 0.22, 'unit' => 'kg'],
                ['item' => 'Onions', 'qty' => 0.04, 'unit' => 'kg'],
                ['item' => 'Green Chili', 'qty' => 0.01, 'unit' => 'kg'],
                ['item' => 'Cooking Oil', 'qty' => 0.02, 'unit' => 'L'],
                ['item' => 'Turmeric Powder', 'qty' => 0.003, 'unit' => 'kg'],
            ],
            'Chicken Shingara' => [
                ['item' => 'Flour', 'qty' => 0.06, 'unit' => 'kg'],
                ['item' => 'Chicken', 'qty' => 0.05, 'unit' => 'kg'],
                ['item' => 'Potatoes', 'qty' => 0.05, 'unit' => 'kg'],
                ['item' => 'Cooking Oil', 'qty' => 0.035, 'unit' => 'L'],
            ],
            'Vegetable Samosa' => [
                ['item' => 'Flour', 'qty' => 0.05, 'unit' => 'kg'],
                ['item' => 'Mixed Vegetables', 'qty' => 0.07, 'unit' => 'kg'],
                ['item' => 'Potatoes', 'qty' => 0.04, 'unit' => 'kg'],
                ['item' => 'Cooking Oil', 'qty' => 0.03, 'unit' => 'L'],
            ],
            'Chicken Roll' => [
                ['item' => 'Flour', 'qty' => 0.10, 'unit' => 'kg'],
                ['item' => 'Chicken', 'qty' => 0.12, 'unit' => 'kg'],
                ['item' => 'Onions', 'qty' => 0.03, 'unit' => 'kg'],
                ['item' => 'Cooking Oil', 'qty' => 0.02, 'unit' => 'L'],
            ],
            'Fuchka Plate' => [
                ['item' => 'Flour', 'qty' => 0.07, 'unit' => 'kg'],
                ['item' => 'Chickpeas', 'qty' => 0.10, 'unit' => 'kg'],
                ['item' => 'Potatoes', 'qty' => 0.06, 'unit' => 'kg'],
                ['item' => 'Tamarind', 'qty' => 0.04, 'unit' => 'kg'],
            ],
            'Firni' => [
                ['item' => 'Chinigura Rice', 'qty' => 0.04, 'unit' => 'kg'],
                ['item' => 'Milk', 'qty' => 0.25, 'unit' => 'L'],
                ['item' => 'Sugar', 'qty' => 0.04, 'unit' => 'kg'],
                ['item' => 'Cardamom', 'qty' => 0.001, 'unit' => 'kg'],
            ],
            'Mishti Doi' => [
                ['item' => 'Yogurt', 'qty' => 0.18, 'unit' => 'kg'],
                ['item' => 'Sugar', 'qty' => 0.04, 'unit' => 'kg'],
            ],
            'Rasgulla' => [
                ['item' => 'Paneer', 'qty' => 0.08, 'unit' => 'kg'],
                ['item' => 'Sugar', 'qty' => 0.06, 'unit' => 'kg'],
            ],
            'Borhani' => [
                ['item' => 'Yogurt', 'qty' => 0.18, 'unit' => 'kg'],
                ['item' => 'Mint Leaves', 'qty' => 0.006, 'unit' => 'kg'],
                ['item' => 'Cumin Powder', 'qty' => 0.003, 'unit' => 'kg'],
                ['item' => 'Salt', 'qty' => 0.002, 'unit' => 'kg'],
            ],
            'Lassi' => [
                ['item' => 'Yogurt', 'qty' => 0.16, 'unit' => 'kg'],
                ['item' => 'Milk', 'qty' => 0.08, 'unit' => 'L'],
                ['item' => 'Sugar', 'qty' => 0.03, 'unit' => 'kg'],
            ],
            'Lemon Mint' => [
                ['item' => 'Lemons', 'qty' => 0.08, 'unit' => 'kg'],
                ['item' => 'Mint Leaves', 'qty' => 0.006, 'unit' => 'kg'],
                ['item' => 'Sugar', 'qty' => 0.025, 'unit' => 'kg'],
            ],
            'Milk Tea' => [
                ['item' => 'Tea Leaves', 'qty' => 0.006, 'unit' => 'kg'],
                ['item' => 'Milk', 'qty' => 0.08, 'unit' => 'L'],
                ['item' => 'Sugar', 'qty' => 0.015, 'unit' => 'kg'],
            ],
        ];

        foreach ($recipes as $menuName => $ingredients) {
            $menu = Menu::where('name', $menuName)->first();
            if (!$menu) {
                continue;
            }

            MenuIngredient::where('menu_id', $menu->id)->delete();

            foreach ($ingredients as $row) {
                $inventory = Inventory::firstOrCreate(
                    ['item_name' => $row['item']],
                    [
                        'category' => 'Pantry',
                        'quantity' => 100,
                        'unit' => $row['unit'],
                        'min_quantity' => 10,
                        'cost_per_unit' => 1,
                        'supplier' => 'Auto Added',
                        'notes' => 'Auto-created for menu recipe mapping',
                    ]
                );

                MenuIngredient::create([
                    'menu_id' => $menu->id,
                    'inventory_id' => $inventory->id,
                    'quantity_per_dish' => $row['qty'],
                ]);
            }
        }
    }
}
