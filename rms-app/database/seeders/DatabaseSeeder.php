<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Menu;
use App\Models\Table;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Inventory;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['admin', 'manager', 'chef', 'cashier', 'customer'] as $role) {
            Role::findOrCreate($role, 'web');
        }

        // ===== USERS =====
        // Use updateOrCreate so re-running seeds repairs credentials instead of failing
        // on the unique email constraint.
        User::updateOrCreate(
            ['email' => 'admin@restaurant.com'],
            [
                'name' => 'Admin User',
                'password' => 'password',
                'role' => 'admin',
                'phone' => '555-0100',
                'is_active' => true,
            ]
        )->syncLegacyRole();

        $manager = User::updateOrCreate(
            ['email' => 'sarah@restaurant.com'],
            [
                'name' => 'Sarah Johnson',
                'password' => 'password',
                'role' => 'manager',
                'phone' => '555-0101',
                'is_active' => true,
            ]
        );
        $manager->syncLegacyRole();

        User::updateOrCreate(
            ['email' => 'marco@restaurant.com'],
            [
                'name' => 'Chef Marco',
                'password' => 'password',
                'role' => 'chef',
                'phone' => '555-0104',
                'is_active' => true,
            ]
        )->syncLegacyRole();

        $cashier = User::updateOrCreate(
            ['email' => 'lisa@restaurant.com'],
            [
                'name' => 'Lisa Chen',
                'password' => 'password',
                'role' => 'cashier',
                'phone' => '555-0105',
                'is_active' => true,
            ]
        );
        $cashier->syncLegacyRole();

        // ===== MENU ITEMS =====
        $riceItems = [
            ['name' => 'Chicken Biryani', 'description' => 'Aromatic basmati rice cooked with chicken, potato, and house biryani spices', 'price' => 260, 'image' => 'menu-images/chicken-biryani.jpg'],
            ['name' => 'Beef Tehari', 'description' => 'Dhaka-style tehari with mustard oil, tender beef, and fragrant rice', 'price' => 240, 'image' => 'menu-images/beef-tehari.jpg'],
            ['name' => 'Kacchi Biryani', 'description' => 'Slow-cooked mutton kacchi with basmati rice, potato, and special masala', 'price' => 380, 'image' => 'menu-images/kacchi-biryani.jpg'],
            ['name' => 'Morog Polao', 'description' => 'Traditional chicken polao with ghee, spices, and soft rice', 'price' => 300, 'image' => 'menu-images/morog-polao.jpg'],
            ['name' => 'Plain Rice', 'description' => 'Steamed rice served fresh for curry meals', 'price' => 50, 'image' => 'menu-images/plain-rice.jpg'],
        ];

        $curryItems = [
            ['name' => 'Chicken Curry', 'description' => 'Home-style chicken curry with onion, garlic, ginger, and warm spices', 'price' => 180, 'image' => 'menu-images/chicken-curry.jpg'],
            ['name' => 'Beef Bhuna', 'description' => 'Slow-cooked beef bhuna with thick masala gravy', 'price' => 250, 'image' => 'menu-images/beef-bhuna.jpg'],
            ['name' => 'Mutton Rezala', 'description' => 'Rich mutton rezala cooked with yogurt, ghee, and mild spices', 'price' => 360, 'image' => 'menu-images/mutton-rezala.jpg'],
            ['name' => 'Rui Fish Curry', 'description' => 'Fresh rui fish curry with mustard oil and Bengali spices', 'price' => 220, 'image' => 'menu-images/rui-fish-curry.jpg'],
            ['name' => 'Prawn Malai Curry', 'description' => 'Prawns cooked in coconut milk with a mild creamy gravy', 'price' => 320, 'image' => 'menu-images/prawn-malai-curry.jpg'],
            ['name' => 'Mixed Vegetable Bhaji', 'description' => 'Seasonal vegetables stir-fried with onion, chili, and spices', 'price' => 120, 'image' => 'menu-images/mixed-vegetable-bhaji.jpg'],
        ];

        $snacks = [
            ['name' => 'Chicken Shingara', 'description' => 'Crispy pastry filled with spiced chicken and potato', 'price' => 35, 'image' => 'menu-images/chicken-shingara.jpg'],
            ['name' => 'Vegetable Samosa', 'description' => 'Golden samosa filled with mixed vegetables and light spices', 'price' => 25, 'image' => 'menu-images/vegetable-samosa.jpg'],
            ['name' => 'Chicken Roll', 'description' => 'Paratha wrap with chicken, salad, and house sauce', 'price' => 120, 'image' => 'menu-images/chicken-roll.jpg'],
            ['name' => 'Fuchka Plate', 'description' => 'Crispy fuchka served with chickpea filling and tamarind water', 'price' => 100, 'image' => 'menu-images/fuchka-plate.jpg'],
        ];

        $desserts = [
            ['name' => 'Firni', 'description' => 'Creamy rice pudding flavored with cardamom and milk', 'price' => 80, 'image' => 'menu-images/firni.jpg'],
            ['name' => 'Mishti Doi', 'description' => 'Traditional sweet yogurt served chilled', 'price' => 70, 'image' => 'menu-images/mishti-doi.jpg'],
            ['name' => 'Rasgulla', 'description' => 'Soft cheese balls soaked in light sugar syrup', 'price' => 60, 'image' => 'menu-images/rasgulla.jpg'],
        ];

        $beverages = [
            ['name' => 'Borhani', 'description' => 'Spiced yogurt drink served chilled with biryani meals', 'price' => 80, 'image' => 'menu-images/borhani.jpg'],
            ['name' => 'Lassi', 'description' => 'Sweet yogurt drink blended with milk and sugar', 'price' => 90, 'image' => 'menu-images/lassi.jpg'],
            ['name' => 'Lemon Mint', 'description' => 'Fresh lemon drink with mint, sugar, and chilled water', 'price' => 70, 'image' => 'menu-images/lemon-mint.jpg'],
            ['name' => 'Milk Tea', 'description' => 'Classic Bangladeshi milk tea', 'price' => 30, 'image' => 'menu-images/milk-tea.jpg'],
        ];

        $menus = [];
        foreach ($riceItems as $item) {
            $menus[] = Menu::updateOrCreate(
                ['name' => $item['name']],
                array_merge($item, ['category' => 'Rice & Biryani', 'is_available' => true])
            );
        }
        foreach ($curryItems as $item) {
            $menus[] = Menu::updateOrCreate(
                ['name' => $item['name']],
                array_merge($item, ['category' => 'Curry & Bhuna', 'is_available' => true])
            );
        }
        foreach ($snacks as $item) {
            $menus[] = Menu::updateOrCreate(
                ['name' => $item['name']],
                array_merge($item, ['category' => 'Snacks', 'is_available' => true])
            );
        }
        foreach ($desserts as $item) {
            $menus[] = Menu::updateOrCreate(
                ['name' => $item['name']],
                array_merge($item, ['category' => 'Desserts', 'is_available' => true])
            );
        }
        foreach ($beverages as $item) {
            $menus[] = Menu::updateOrCreate(
                ['name' => $item['name']],
                array_merge($item, ['category' => 'Beverages', 'is_available' => true])
            );
        }

        // ===== TABLES =====
        $tables = [];
        $locations = ['Indoor', 'Indoor', 'Indoor', 'Indoor', 'Indoor', 'Outdoor', 'Outdoor', 'Outdoor', 'VIP', 'VIP'];
        $capacities = [2, 4, 4, 6, 4, 4, 6, 2, 8, 6];
        for ($i = 1; $i <= 10; $i++) {
            $tables[] = Table::updateOrCreate(
                ['table_number' => 'T-' . str_pad($i, 2, '0', STR_PAD_LEFT)],
                [
                    'capacity' => $capacities[$i - 1],
                    'status' => $i <= 7 ? 'available' : ($i == 8 ? 'reserved' : ($i == 9 ? 'occupied' : 'maintenance')),
                    'location' => $locations[$i - 1],
                ]
            );
        }

        // ===== ORDERS =====
        $statuses = ['completed', 'completed', 'completed', 'completed', 'served', 'preparing', 'pending'];
        $staffUsers = [$manager, $cashier];

        for ($i = 0; $i < 7; $i++) {
            $order = Order::create([
                'order_number' => 'ORD-' . date('Ymd') . '-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'table_id' => $tables[array_rand(array_slice($tables, 0, 7))]->id,
                'user_id' => $staffUsers[array_rand($staffUsers)]->id,
                'status' => $statuses[$i],
                'notes' => $i == 0 ? 'No onions please' : null,
                'created_at' => now()->subHours(rand(0, 72)),
            ]);

            $itemCount = rand(2, 5);
            $usedMenuIds = [];
            $total = 0;

            for ($j = 0; $j < $itemCount; $j++) {
                $menuItem = $menus[array_rand($menus)];
                if (in_array($menuItem->id, $usedMenuIds)) continue;
                $usedMenuIds[] = $menuItem->id;

                $qty = rand(1, 3);
                $subtotal = $menuItem->price * $qty;
                $total += $subtotal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_id' => $menuItem->id,
                    'quantity' => $qty,
                    'unit_price' => $menuItem->price,
                    'subtotal' => $subtotal,
                ]);
            }

            $order->update(['total_amount' => $total]);
        }

        // ===== INVENTORY =====
        $inventoryItems = [
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
            ['item_name' => 'Bay Leaf', 'category' => 'Spices', 'quantity' => 2, 'unit' => 'kg', 'min_quantity' => 1, 'cost_per_unit' => 420, 'supplier' => 'Moulvibazar Spice House'],
            ['item_name' => 'Tamarind', 'category' => 'Pantry', 'quantity' => 10, 'unit' => 'kg', 'min_quantity' => 3, 'cost_per_unit' => 180, 'supplier' => 'Moulvibazar Grocery'],
            ['item_name' => 'Mint Leaves', 'category' => 'Produce', 'quantity' => 5, 'unit' => 'kg', 'min_quantity' => 1, 'cost_per_unit' => 160, 'supplier' => 'Shyambazar Produce'],
            ['item_name' => 'Lemons', 'category' => 'Produce', 'quantity' => 18, 'unit' => 'kg', 'min_quantity' => 5, 'cost_per_unit' => 110, 'supplier' => 'Shyambazar Produce'],
            ['item_name' => 'Tea Leaves', 'category' => 'Beverages', 'quantity' => 10, 'unit' => 'kg', 'min_quantity' => 3, 'cost_per_unit' => 520, 'supplier' => 'Sylhet Tea Supplier'],
        ];

        foreach ($inventoryItems as $item) {
            Inventory::updateOrCreate(
                ['item_name' => $item['item_name']],
                $item
            );
        }

        $this->call(MenuRecipeSeeder::class);
    }
}
