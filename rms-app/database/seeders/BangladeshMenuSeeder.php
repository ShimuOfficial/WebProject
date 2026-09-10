<?php

namespace Database\Seeders;

use App\Models\Inventory;
use App\Models\Menu;
use Illuminate\Database\Seeder;

class BangladeshMenuSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->menus() as $item) {
            Menu::updateOrCreate(
                ['name' => $item['name']],
                [
                    'category' => $item['category'],
                    'description' => $item['description'],
                    'price' => $item['price'],
                    'is_available' => true,
                ]
            );
        }

        foreach ($this->inventoryItems() as $item) {
            Inventory::updateOrCreate(
                ['item_name' => $item['item_name']],
                $item
            );
        }

        $this->call(MenuRecipeSeeder::class);
    }

    private function menus(): array
    {
        return [
            ['name' => 'Chicken Biryani', 'category' => 'Rice & Biryani', 'description' => 'Aromatic basmati rice cooked with chicken, potato, and house biryani spices', 'price' => 260],
            ['name' => 'Beef Tehari', 'category' => 'Rice & Biryani', 'description' => 'Dhaka-style tehari with mustard oil, tender beef, and fragrant rice', 'price' => 240],
            ['name' => 'Kacchi Biryani', 'category' => 'Rice & Biryani', 'description' => 'Slow-cooked mutton kacchi with basmati rice, potato, and special masala', 'price' => 380],
            ['name' => 'Morog Polao', 'category' => 'Rice & Biryani', 'description' => 'Traditional chicken polao with ghee, spices, and soft rice', 'price' => 300],
            ['name' => 'Plain Rice', 'category' => 'Rice & Biryani', 'description' => 'Steamed rice served fresh for curry meals', 'price' => 50],
            ['name' => 'Chicken Curry', 'category' => 'Curry & Bhuna', 'description' => 'Home-style chicken curry with onion, garlic, ginger, and warm spices', 'price' => 180],
            ['name' => 'Beef Bhuna', 'category' => 'Curry & Bhuna', 'description' => 'Slow-cooked beef bhuna with thick masala gravy', 'price' => 250],
            ['name' => 'Mutton Rezala', 'category' => 'Curry & Bhuna', 'description' => 'Rich mutton rezala cooked with yogurt, ghee, and mild spices', 'price' => 360],
            ['name' => 'Rui Fish Curry', 'category' => 'Curry & Bhuna', 'description' => 'Fresh rui fish curry with mustard oil and Bengali spices', 'price' => 220],
            ['name' => 'Prawn Malai Curry', 'category' => 'Curry & Bhuna', 'description' => 'Prawns cooked in coconut milk with a mild creamy gravy', 'price' => 320],
            ['name' => 'Mixed Vegetable Bhaji', 'category' => 'Curry & Bhuna', 'description' => 'Seasonal vegetables stir-fried with onion, chili, and spices', 'price' => 120],
            ['name' => 'Chicken Shingara', 'category' => 'Snacks', 'description' => 'Crispy pastry filled with spiced chicken and potato', 'price' => 35],
            ['name' => 'Vegetable Samosa', 'category' => 'Snacks', 'description' => 'Golden samosa filled with mixed vegetables and light spices', 'price' => 25],
            ['name' => 'Chicken Roll', 'category' => 'Snacks', 'description' => 'Paratha wrap with chicken, salad, and house sauce', 'price' => 120],
            ['name' => 'Fuchka Plate', 'category' => 'Snacks', 'description' => 'Crispy fuchka served with chickpea filling and tamarind water', 'price' => 100],
            ['name' => 'Firni', 'category' => 'Desserts', 'description' => 'Creamy rice pudding flavored with cardamom and milk', 'price' => 80],
            ['name' => 'Mishti Doi', 'category' => 'Desserts', 'description' => 'Traditional sweet yogurt served chilled', 'price' => 70],
            ['name' => 'Rasgulla', 'category' => 'Desserts', 'description' => 'Soft cheese balls soaked in light sugar syrup', 'price' => 60],
            ['name' => 'Borhani', 'category' => 'Beverages', 'description' => 'Spiced yogurt drink served chilled with biryani meals', 'price' => 80],
            ['name' => 'Lassi', 'category' => 'Beverages', 'description' => 'Sweet yogurt drink blended with milk and sugar', 'price' => 90],
            ['name' => 'Lemon Mint', 'category' => 'Beverages', 'description' => 'Fresh lemon drink with mint, sugar, and chilled water', 'price' => 70],
            ['name' => 'Milk Tea', 'category' => 'Beverages', 'description' => 'Classic Bangladeshi milk tea', 'price' => 30],
        ];
    }

    private function inventoryItems(): array
    {
        return [
            ['item_name' => 'Basmati Rice', 'category' => 'Rice & Grains', 'quantity' => 80, 'unit' => 'kg', 'min_quantity' => 20, 'cost_per_unit' => 155, 'supplier' => 'Karwan Bazar Rice Traders'],
            ['item_name' => 'Chinigura Rice', 'category' => 'Rice & Grains', 'quantity' => 45, 'unit' => 'kg', 'min_quantity' => 12, 'cost_per_unit' => 145, 'supplier' => 'Karwan Bazar Rice Traders'],
            ['item_name' => 'Miniket Rice', 'category' => 'Rice & Grains', 'quantity' => 90, 'unit' => 'kg', 'min_quantity' => 25, 'cost_per_unit' => 78, 'supplier' => 'Karwan Bazar Rice Traders'],
            ['item_name' => 'Chicken', 'category' => 'Meat', 'quantity' => 35, 'unit' => 'kg', 'min_quantity' => 10, 'cost_per_unit' => 230, 'supplier' => 'Kaptan Bazar Poultry'],
            ['item_name' => 'Beef', 'category' => 'Meat', 'quantity' => 28, 'unit' => 'kg', 'min_quantity' => 8, 'cost_per_unit' => 780, 'supplier' => 'Local Meat Supplier'],
            ['item_name' => 'Mutton', 'category' => 'Meat', 'quantity' => 18, 'unit' => 'kg', 'min_quantity' => 5, 'cost_per_unit' => 1150, 'supplier' => 'Local Meat Supplier'],
            ['item_name' => 'Rui Fish', 'category' => 'Seafood', 'quantity' => 24, 'unit' => 'kg', 'min_quantity' => 6, 'cost_per_unit' => 360, 'supplier' => 'Jatrabari Fish Market'],
            ['item_name' => 'Prawns', 'category' => 'Seafood', 'quantity' => 16, 'unit' => 'kg', 'min_quantity' => 5, 'cost_per_unit' => 760, 'supplier' => 'Jatrabari Fish Market'],
            ['item_name' => 'Potatoes', 'category' => 'Produce', 'quantity' => 60, 'unit' => 'kg', 'min_quantity' => 15, 'cost_per_unit' => 45, 'supplier' => 'Shyambazar Produce'],
            ['item_name' => 'Onions', 'category' => 'Produce', 'quantity' => 55, 'unit' => 'kg', 'min_quantity' => 15, 'cost_per_unit' => 85, 'supplier' => 'Shyambazar Produce'],
            ['item_name' => 'Garlic', 'category' => 'Produce', 'quantity' => 18, 'unit' => 'kg', 'min_quantity' => 5, 'cost_per_unit' => 190, 'supplier' => 'Shyambazar Produce'],
            ['item_name' => 'Ginger', 'category' => 'Produce', 'quantity' => 16, 'unit' => 'kg', 'min_quantity' => 4, 'cost_per_unit' => 210, 'supplier' => 'Shyambazar Produce'],
            ['item_name' => 'Tomatoes', 'category' => 'Produce', 'quantity' => 30, 'unit' => 'kg', 'min_quantity' => 8, 'cost_per_unit' => 70, 'supplier' => 'Shyambazar Produce'],
            ['item_name' => 'Green Chili', 'category' => 'Produce', 'quantity' => 10, 'unit' => 'kg', 'min_quantity' => 3, 'cost_per_unit' => 130, 'supplier' => 'Shyambazar Produce'],
            ['item_name' => 'Mixed Vegetables', 'category' => 'Produce', 'quantity' => 35, 'unit' => 'kg', 'min_quantity' => 10, 'cost_per_unit' => 80, 'supplier' => 'Shyambazar Produce'],
            ['item_name' => 'Chickpeas', 'category' => 'Pantry', 'quantity' => 25, 'unit' => 'kg', 'min_quantity' => 8, 'cost_per_unit' => 120, 'supplier' => 'Moulvibazar Grocery'],
            ['item_name' => 'Flour', 'category' => 'Pantry', 'quantity' => 50, 'unit' => 'kg', 'min_quantity' => 15, 'cost_per_unit' => 65, 'supplier' => 'Moulvibazar Grocery'],
            ['item_name' => 'Sugar', 'category' => 'Pantry', 'quantity' => 45, 'unit' => 'kg', 'min_quantity' => 12, 'cost_per_unit' => 130, 'supplier' => 'Moulvibazar Grocery'],
            ['item_name' => 'Salt', 'category' => 'Pantry', 'quantity' => 20, 'unit' => 'kg', 'min_quantity' => 5, 'cost_per_unit' => 42, 'supplier' => 'Moulvibazar Grocery'],
            ['item_name' => 'Cooking Oil', 'category' => 'Pantry', 'quantity' => 70, 'unit' => 'L', 'min_quantity' => 18, 'cost_per_unit' => 175, 'supplier' => 'Moulvibazar Grocery'],
            ['item_name' => 'Mustard Oil', 'category' => 'Pantry', 'quantity' => 24, 'unit' => 'L', 'min_quantity' => 6, 'cost_per_unit' => 280, 'supplier' => 'Moulvibazar Grocery'],
            ['item_name' => 'Ghee', 'category' => 'Dairy', 'quantity' => 12, 'unit' => 'kg', 'min_quantity' => 3, 'cost_per_unit' => 950, 'supplier' => 'Local Dairy Supplier'],
            ['item_name' => 'Milk', 'category' => 'Dairy', 'quantity' => 60, 'unit' => 'L', 'min_quantity' => 15, 'cost_per_unit' => 90, 'supplier' => 'Local Dairy Supplier'],
            ['item_name' => 'Yogurt', 'category' => 'Dairy', 'quantity' => 35, 'unit' => 'kg', 'min_quantity' => 8, 'cost_per_unit' => 160, 'supplier' => 'Local Dairy Supplier'],
            ['item_name' => 'Cream', 'category' => 'Dairy', 'quantity' => 15, 'unit' => 'L', 'min_quantity' => 4, 'cost_per_unit' => 420, 'supplier' => 'Local Dairy Supplier'],
            ['item_name' => 'Paneer', 'category' => 'Dairy', 'quantity' => 12, 'unit' => 'kg', 'min_quantity' => 3, 'cost_per_unit' => 520, 'supplier' => 'Local Dairy Supplier'],
            ['item_name' => 'Biryani Masala', 'category' => 'Spices', 'quantity' => 12, 'unit' => 'kg', 'min_quantity' => 3, 'cost_per_unit' => 620, 'supplier' => 'Moulvibazar Spice House'],
            ['item_name' => 'Garam Masala', 'category' => 'Spices', 'quantity' => 8, 'unit' => 'kg', 'min_quantity' => 2, 'cost_per_unit' => 700, 'supplier' => 'Moulvibazar Spice House'],
            ['item_name' => 'Turmeric Powder', 'category' => 'Spices', 'quantity' => 8, 'unit' => 'kg', 'min_quantity' => 2, 'cost_per_unit' => 260, 'supplier' => 'Moulvibazar Spice House'],
            ['item_name' => 'Chili Powder', 'category' => 'Spices', 'quantity' => 8, 'unit' => 'kg', 'min_quantity' => 2, 'cost_per_unit' => 360, 'supplier' => 'Moulvibazar Spice House'],
            ['item_name' => 'Cumin Powder', 'category' => 'Spices', 'quantity' => 8, 'unit' => 'kg', 'min_quantity' => 2, 'cost_per_unit' => 520, 'supplier' => 'Moulvibazar Spice House'],
            ['item_name' => 'Cardamom', 'category' => 'Spices', 'quantity' => 3, 'unit' => 'kg', 'min_quantity' => 1, 'cost_per_unit' => 2200, 'supplier' => 'Moulvibazar Spice House'],
            ['item_name' => 'Cinnamon', 'category' => 'Spices', 'quantity' => 4, 'unit' => 'kg', 'min_quantity' => 1, 'cost_per_unit' => 850, 'supplier' => 'Moulvibazar Spice House'],
            ['item_name' => 'Tamarind', 'category' => 'Pantry', 'quantity' => 10, 'unit' => 'kg', 'min_quantity' => 3, 'cost_per_unit' => 180, 'supplier' => 'Moulvibazar Grocery'],
            ['item_name' => 'Mint Leaves', 'category' => 'Produce', 'quantity' => 5, 'unit' => 'kg', 'min_quantity' => 1, 'cost_per_unit' => 160, 'supplier' => 'Shyambazar Produce'],
            ['item_name' => 'Lemons', 'category' => 'Produce', 'quantity' => 18, 'unit' => 'kg', 'min_quantity' => 5, 'cost_per_unit' => 110, 'supplier' => 'Shyambazar Produce'],
            ['item_name' => 'Tea Leaves', 'category' => 'Beverages', 'quantity' => 10, 'unit' => 'kg', 'min_quantity' => 3, 'cost_per_unit' => 520, 'supplier' => 'Sylhet Tea Supplier'],
        ];
    }
}
