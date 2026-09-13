<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Menu;
use Illuminate\Http\Request;

/**
 * DEFENSE: §5.13 inventory CRUD — chef is view-only via routes
 */
class InventoryController extends Controller
{
    // List inventory items with optional search and low-stock filter.
    public function index(Request $request)
    {
        $query = Inventory::query();
        if ($request->filled('search')) {
            $query->where('item_name', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('low_stock')) {
            $query->whereColumn('quantity', '<=', 'min_quantity')->where('quantity', '>', 0);
        }
        if ($request->filled('out_of_stock')) {
            $query->where('quantity', '<=', 0);
        }
        $items = $query->latest()->paginate(15);
        $canManageInventory = in_array(auth()->user()->role ?? '', ['admin', 'manager']);
        return view('admin.inventory.index', compact('items', 'canManageInventory'));
    }

    // Validate and create a new inventory item.
    public function store(Request $request)
    {
        $request->validate(['item_name' => 'required|string|max:255', 'quantity' => 'required|numeric|min:0', 'unit' => 'required|string|max:50', 'min_quantity' => 'required|numeric|min:0', 'cost_per_unit' => 'required|numeric|min:0']);
        Inventory::create($request->all());
        Menu::syncAvailabilityFromInventory();
        return redirect()->route('inventory.index')->with('success', 'Inventory item added!');
    }

    // Validate and update an inventory item.
    public function update(Request $request, Inventory $inventory)
    {
        $request->validate(['item_name' => 'required|string|max:255', 'quantity' => 'required|numeric|min:0', 'unit' => 'required|string|max:50', 'min_quantity' => 'required|numeric|min:0', 'cost_per_unit' => 'required|numeric|min:0']);
        $inventory->update($request->all());
        Menu::syncAvailabilityFromInventory();
        return redirect()->route('inventory.index')->with('success', 'Inventory item updated!');
    }

    // Delete an inventory item.
    public function destroy(Inventory $inventory)
    {
        $inventory->delete();
        return redirect()->route('inventory.index')->with('success', 'Inventory item deleted!');
    }
}
