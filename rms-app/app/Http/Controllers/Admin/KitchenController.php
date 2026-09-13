<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Menu;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * DEFENSE: §5.8 KDS — list tickets; preparing/ready deducts stock ONCE
 */
class KitchenController extends Controller
{
    // Show kitchen ticket list or fragment for KDS display.
    public function index(Request $request)
    {
        // Get all pending, approved, and preparing orders
        $orders = Order::with(['items.menu', 'table'])
            ->whereIn('status', ['pending', 'approved', 'preparing'])
            ->where(function ($query) {
                $query->where('order_source', '!=', 'customer')
                    ->orWhere('is_customer_approved', true);
            })
            ->orderBy('created_at', 'asc')
            ->take(9)
            ->get();

        if ($request->boolean('fragment')) {
            return view('admin.kitchen.partials.tickets', compact('orders'));
        }

        return view('admin.kitchen.index', compact('orders'));
    }

    // Update order status to preparing/ready while performing inventory checks/deductions.
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,preparing,ready'
        ]);

        $newStatus = $request->status;

        // DEFENSE: Q8/Q10 — deduct only if inventory_deducted_at is null (no double cut)
        if (in_array($newStatus, ['preparing', 'ready'], true) && $order->inventory_deducted_at === null) {
            try {
                DB::transaction(function () use ($order, $newStatus) {
                    $order->refresh();

                    if ($order->inventory_deducted_at !== null) {
                        $order->update(['status' => $newStatus]);
                        return;
                    }

                    $order->load(['items.menu.menuIngredients']);

                    $requirements = [];
                    foreach ($order->items as $item) {
                        if (!$item->menu) {
                            continue;
                        }

                        foreach ($item->menu->menuIngredients as $ingredient) {
                            $inventoryId = (int) $ingredient->inventory_id;
                            $required = (float) $ingredient->quantity_per_dish * (int) $item->quantity;

                            if ($required <= 0) {
                                continue;
                            }

                            $requirements[$inventoryId] = ($requirements[$inventoryId] ?? 0) + $required;
                        }
                    }

                    $menusToDisable = [];
                    if (!empty($requirements)) {
                        $inventories = Inventory::query()
                            ->whereIn('id', array_keys($requirements))
                            ->lockForUpdate()
                            ->get()
                            ->keyBy('id');

                        $insufficient = [];
                        // Consider existing reservations from other orders when computing availability
                        $otherReserved = [];
                        $reservedOrders = \App\Models\Order::query()
                            ->whereNotNull('reserved_at')
                            ->whereNull('inventory_deducted_at')
                            ->where('id', '!=', $order->id)
                            ->get()
                            ->pluck('reserved_requirements')
                            ->filter()
                            ->values();

                        foreach ($reservedOrders as $r) {
                            foreach ($r as $invId => $q) {
                                $otherReserved[$invId] = ($otherReserved[$invId] ?? 0) + (float) $q;
                            }
                        }

                        foreach ($requirements as $inventoryId => $required) {
                            $inv = $inventories->get($inventoryId);
                            $available = $inv ? (float) $inv->quantity : 0.0;
                            $reservedByOthers = $otherReserved[$inventoryId] ?? 0.0;
                            $effectiveAvailable = max(0, $available - $reservedByOthers);

                            if (!$inv || $effectiveAvailable + 1e-9 < (float) $required) {
                                $name = $inv?->item_name ?? "Item #{$inventoryId}";
                                $unit = $inv?->unit ?? '';
                                $insufficient[] = "{$name} (need {$required}{$unit}, available {$effectiveAvailable}{$unit})";
                            }
                        }

                        if (!empty($insufficient)) {
                            throw new \RuntimeException('Insufficient inventory: ' . implode(', ', $insufficient));
                        }

                        foreach ($requirements as $inventoryId => $required) {
                            $inv = $inventories[$inventoryId];
                            $inv->decrement('quantity', $required);

                            // If this inventory reached zero or below, collect menus that use it to disable
                            if ($inv->quantity <= 0) {
                                $menusUsing = $inv->menuIngredients()->pluck('menu_id')->unique()->all();
                                $menusToDisable = array_merge($menusToDisable, $menusUsing);
                            }
                        }
                    }

                    $order->update([
                        'status' => $newStatus,
                        'inventory_deducted_at' => now(),
                    ]);

                    if (!empty($menusToDisable)) {
                        $menusToDisable = array_unique($menusToDisable);
                        // Mark the menus unavailable so new orders cannot be placed
                        \App\Models\Menu::whereIn('id', $menusToDisable)->update(['is_available' => false]);
                        // Attach list to order instance for use after transaction
                        $order->setRelation('menus_disabled_ids', collect($menusToDisable));
                    }
                });
            } catch (\RuntimeException $e) {
                return redirect()->route('kitchen.index')->withErrors(['inventory' => $e->getMessage()]);
            }

            // If transaction set menus disabled, build a warning message
            $menusDisabledIds = $order->relationLoaded('menus_disabled_ids')
                ? $order->getRelation('menus_disabled_ids')
                : collect();

            Menu::syncAvailabilityFromInventory();

            if ($menusDisabledIds->isNotEmpty()) {
                $menuNames = \App\Models\Menu::whereIn('id', $menusDisabledIds->all())->pluck('name')->toArray();
                $msg = 'The following menu items were set to Unavailable due to stock depletion: ' . implode(', ', $menuNames);
                return redirect()->route('kitchen.index')->with('warning', $msg)->with('success', "Order #{$order->order_number} marked as {$newStatus}!");
            }

            return redirect()->route('kitchen.index')->with('success', "Order #{$order->order_number} marked as {$newStatus}!");
        }

        $order->update(['status' => $newStatus]);

        return redirect()->route('kitchen.index')->with('success', "Order #{$order->order_number} marked as {$newStatus}!");
    }
}
