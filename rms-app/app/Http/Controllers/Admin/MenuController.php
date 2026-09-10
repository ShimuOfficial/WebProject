<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Menu;
use App\Models\MenuIngredient;
use Illuminate\Http\Request;

/**
 * MenuController
 *
 * Manage CRUD for menu items and their ingredients.
 */
class MenuController extends Controller
{
    // Display a paginated list of menu items.
    public function index(Request $request)
    {
        $query = Menu::with(['menuIngredients.inventory'])->withCount('menuIngredients');
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        $menuItems = $query->latest()->paginate(8);
        $categories = Menu::distinct()->pluck('category');
        $role = auth()->user()->role ?? '';
        $canManageMenu = in_array($role, ['admin', 'manager']);
        $canToggleAvailability = in_array($role, ['admin', 'manager', 'chef']);
        return view('admin.menu.index', compact('menuItems', 'categories', 'canManageMenu', 'canToggleAvailability'));
    }

    // Redirect to edit view for a menu item.
    public function show(Menu $menu)
    {
        return redirect()->route('menu.edit', $menu);
    }

    // Show the form to create a new menu item.
    public function create()
    {
        $categories = Menu::distinct()->pluck('category');
        $inventories = Inventory::orderBy('item_name')->get();
        return view('admin.menu.create', compact('categories', 'inventories'));
    }

    // Validate and store a new menu item.
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0.01',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'ingredients' => 'required|array|min:1',
            'ingredients.*.inventory_id' => 'required_with:ingredients|exists:inventories,id',
            'ingredients.*.quantity_per_dish' => 'required_with:ingredients|numeric|min:0.0001',
        ], [
            'price.min' => 'Please enter a price greater than 0.',
            'price.required' => 'Price is required.',
            'price.numeric' => 'Price must be a number.',
        ]);
        $data = $request->except('image');
        $data['is_available'] = $request->has('is_available');
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('menu-images', 'public');
        }
        $menu = Menu::create($data);
        $this->syncIngredients($menu, $request->input('ingredients', []));
        return redirect()->route('menu.index')->with('success', 'Menu item created successfully!');
    }

    // Show the form to edit an existing menu item.
    public function edit(Menu $menu)
    {
        $menu->load('menuIngredients');
        $categories = Menu::distinct()->pluck('category');
        $inventories = Inventory::orderBy('item_name')->get();
        return view('admin.menu.edit', compact('menu', 'categories', 'inventories'));
    }

    // Validate and update the specified menu item.
    public function update(Request $request, Menu $menu)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0.01',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'ingredients' => 'required|array|min:1',
            'ingredients.*.inventory_id' => 'required_with:ingredients|exists:inventories,id',
            'ingredients.*.quantity_per_dish' => 'required_with:ingredients|numeric|min:0.0001',
        ], [
            'price.min' => 'Please enter a price greater than 0.',
            'price.required' => 'Price is required.',
            'price.numeric' => 'Price must be a number.',
        ]);
        $data = $request->except('image');
        $data['is_available'] = $request->has('is_available');
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('menu-images', 'public');
        }
        $menu->update($data);
        $this->syncIngredients($menu, $request->input('ingredients', []));
        return redirect()->route('menu.index')->with('success', 'Menu item updated successfully!');
    }

    // Delete a menu item.
    public function destroy(Menu $menu)
    {
        $menu->delete();
        return redirect()->route('menu.index')->with('success', 'Menu item deleted successfully!');
    }

    // Toggle availability flag for a menu item.
    public function toggleAvailability(Menu $menu)
    {
        $menu->is_available = !$menu->is_available;
        $menu->save();

        return redirect()->route('menu.index')->with('success', 'Menu availability updated.');
    }

    // Synchronize menu ingredients with provided array.
    private function syncIngredients(Menu $menu, array $ingredients): void
    {
        $clean = collect($ingredients)
            ->filter(fn($item) => !empty($item['inventory_id']) && !empty($item['quantity_per_dish']))
            ->map(fn($item) => [
                'menu_id' => $menu->id,
                'inventory_id' => (int) $item['inventory_id'],
                'quantity_per_dish' => (float) $item['quantity_per_dish'],
                'created_at' => now(),
                'updated_at' => now(),
            ])
            ->unique('inventory_id')
            ->values();

        MenuIngredient::where('menu_id', $menu->id)->delete();

        if ($clean->isNotEmpty()) {
            MenuIngredient::insert($clean->all());
        }
    }
}
