<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\MenuIngredient;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

/**
 * NotificationController
 *
 * Aggregate various system notifications for admin/chef dashboards.
 */
class NotificationController extends Controller
{
    // Build a list of current notifications and mark last-seen timestamp.
    public function index()
    {
        $user = auth()->user();
        $notifications = collect();
        $lastSeen = session('notifications_last_seen_at');

        // Count orders that should appear in kitchen (approved or preparing)
        $pendingOrders = Order::whereIn('status', ['approved', 'preparing'])->count();
        if ($pendingOrders > 0) {
            // Chef should see the Kitchen KDS link; others view orders list
            $notifications->push([
                'type' => 'warning',
                'title' => 'Pending Kitchen Orders',
                'message' => $pendingOrders . ' orders are waiting in pending or preparing status.',
                'link' => (in_array($user->role ?? '', ['chef']) ? route('kitchen.index') : route('orders.index', ['status' => 'pending'])),
                'link_text' => (in_array($user->role ?? '', ['chef']) ? 'Open KDS' : 'View Orders'),
                'created_at' => now()->timestamp,
            ]);
        }

        $readyOrders = Order::where('status', 'ready')->count();
        if ($readyOrders > 0) {
            $notifications->push([
                'type' => 'info',
                'title' => 'Orders Ready / Delivered',
                'message' => $readyOrders . ' orders are marked ready for service.',
                'link' => route('orders.index', ['status' => 'ready']),
                'link_text' => 'Open Orders',
                'created_at' => now()->timestamp,
            ]);
        }

        if (Schema::hasColumn('orders', 'payment_status')) {
            $unpaidOrders = Order::whereIn('payment_status', ['unpaid', 'partial'])->count();
            if ($unpaidOrders > 0) {
                $notifications->push([
                    'type' => 'danger',
                    'title' => 'Outstanding Payments',
                    'message' => $unpaidOrders . ' orders still need full payment.',
                    'link' => route('orders.index', ['payment_status' => 'unpaid']),
                    'link_text' => 'Check Payments',
                    'created_at' => now()->timestamp,
                ]);
            }
        } else {
            $notifications->push([
                'type' => 'warning',
                'title' => 'Payment Module Not Ready',
                'message' => 'Run migrations to enable payment-based notifications.',
                'link' => route('dashboard'),
                'link_text' => 'Go to Dashboard',
            ]);
        }

        if (in_array($user->role ?? '', ['admin', 'manager'])) {
            if (Schema::hasColumn('orders', 'is_customer_approved') && Schema::hasColumn('orders', 'order_source')) {
                $awaitingApproval = Order::where('order_source', 'customer')
                    ->where('is_customer_approved', false)
                    ->count();

                if ($awaitingApproval > 0) {
                    $notifications->push([
                        'type' => 'warning',
                        'title' => 'Customer Orders Awaiting Approval',
                        'message' => $awaitingApproval . ' customer order(s) are waiting for manager/admin approval.',
                        'link' => route('orders.index'),
                        'link_text' => 'View Orders',
                        'created_at' => now()->timestamp,
                    ]);
                }
            }

            $outOfStockItems = Inventory::outOfStock()->get();
            if ($outOfStockItems->isNotEmpty()) {
                $names = $outOfStockItems->pluck('item_name')->take(5)->implode(', ');
                $extra = $outOfStockItems->count() > 5 ? ' and others' : '';
                $notifications->push([
                    'type' => 'danger',
                    'title' => 'Out of Stock',
                    'message' => $outOfStockItems->count() . ' inventory item(s) have run out: ' . $names . $extra . '. Related dishes were marked unavailable.',
                    'link' => route('inventory.index', ['out_of_stock' => 1]),
                    'link_text' => 'View Out of Stock',
                    'created_at' => now()->timestamp,
                ]);
            }

            $lowStockItems = Inventory::whereColumn('quantity', '<=', 'min_quantity')
                ->where('quantity', '>', 0)
                ->count();
            if ($lowStockItems > 0) {
                $notifications->push([
                    'type' => 'danger',
                    'title' => 'Low Stock Alert',
                    'message' => $lowStockItems . ' inventory items are low in stock.',
                    'link' => route('inventory.index', ['low_stock' => 1]),
                    'link_text' => 'View Inventory',
                    'created_at' => now()->timestamp,
                ]);
            }
        }

        // Chef-specific: show low-stock ingredients relevant to pending/preparing orders
        if (in_array($user->role ?? '', ['chef'])) {
            // Chef cares about approved and preparing orders
            $ordersWithItems = Order::whereIn('status', ['approved', 'preparing'])->with('items')->get();
            $menuIds = $ordersWithItems->flatMap(fn($o) => $o->items->pluck('menu_id'))->filter()->unique()->values()->all();

            if (!empty($menuIds)) {
                $ingredientInventoryIds = MenuIngredient::whereIn('menu_id', $menuIds)->pluck('inventory_id')->unique()->values()->all();

                if (!empty($ingredientInventoryIds)) {
                    $lowRelevant = Inventory::whereIn('id', $ingredientInventoryIds)
                        ->whereColumn('quantity', '<=', 'min_quantity')
                        ->count();

                    if ($lowRelevant > 0) {
                        $notifications->push([
                            'type' => 'danger',
                            'title' => 'Ingredient Low Stock',
                            'message' => $lowRelevant . ' ingredient(s) used by active orders are low in stock.',
                            'link' => route('inventory.index', ['low_stock' => 1]),
                            'link_text' => 'View Ingredients',
                            'created_at' => now()->timestamp,
                        ]);
                    }

                    $outRelevant = Inventory::whereIn('id', $ingredientInventoryIds)
                        ->where('quantity', '<=', 0)
                        ->count();

                    if ($outRelevant > 0) {
                        $notifications->push([
                            'type' => 'danger',
                            'title' => 'Ingredient Out of Stock',
                            'message' => $outRelevant . ' ingredient(s) used by active orders have run out.',
                            'link' => route('inventory.index', ['out_of_stock' => 1]),
                            'link_text' => 'View Ingredients',
                            'created_at' => now()->timestamp,
                        ]);
                    }
                }
            }
        }

        if ($notifications->isEmpty()) {
            $notifications->push([
                'type' => 'success',
                'title' => 'All Clear',
                'message' => 'No important notifications right now.',
                'link' => route('dashboard'),
                'link_text' => 'Back to Dashboard',
            ]);
        }

        // Compute is_new flag based on session last-seen timestamp (so users see new items until they visit)
        $notifications = $notifications->map(function ($note) use ($lastSeen) {
            $note['is_new'] = empty($lastSeen) ? true : (($note['created_at'] ?? 0) > $lastSeen);
            return $note;
        });

        // Update last-seen timestamp AFTER computing flags so page still highlights new items
        session(['notifications_last_seen_at' => now()->timestamp]);

        return view('admin.notifications.index', [
            'notifications' => $notifications,
            'notificationCount' => $notifications->where('type', '!=', 'success')->count(),
        ]);
    }
}
