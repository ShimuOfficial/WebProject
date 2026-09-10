<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentTransaction;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * CustomerOrderController
 *
 * Handle public customer order placement and listing of customer's orders.
 */
class CustomerOrderController extends Controller
{
    // Store a new order placed by a customer (validates inventory and creates order).
    public function store(Request $request)
    {
        $items = $request->input('items', []);
        if (empty($items)) {
            $cartItems = $request->session()->get('cart.items', []);
            foreach ($cartItems as $menuId => $quantity) {
                $items[] = ['menu_id' => $menuId, 'quantity' => $quantity];
            }
        }

        $request->merge(['payment_method' => $request->input('payment_method', 'cash')]);

        $payload = $request->all();
        $payload['items'] = $items;
        $payload['payment_method'] = $request->input('payment_method', 'cash');

        validator($payload, [
            'items' => 'required|array|min:1',
            'items.*.menu_id' => 'required|exists:menus,id',
            'items.*.quantity' => 'required|integer|min:1',
            // Online payments: allow SSLCommerz when enabled locally.
            'payment_method' => 'required|in:cash,sslcommerz',
            'notes' => 'nullable|string|max:1000',
        ])->validate();

        $menuIds = collect($items)->pluck('menu_id')->filter()->unique()->values();
        if ($menuIds->isNotEmpty()) {
            $unavailableMenus = Menu::query()
                ->whereIn('id', $menuIds)
                ->where('is_available', false)
                ->pluck('name')
                ->all();

            if (!empty($unavailableMenus)) {
                Log::info('Customer checkout blocked: unavailable menus in cart.', [
                    'user_id' => auth()->id(),
                    'menus' => $unavailableMenus,
                ]);

                return redirect()->route('website.menu')
                    ->withErrors(['items' => 'Some items are unavailable right now. Please remove unavailable items from cart and try again.']);
            }
        }

        // Require customer to have an address on file before placing an online order
        if (auth()->check() && auth()->user()->role === 'customer') {
            $userAddress = trim((string) auth()->user()->address);
            if ($userAddress === '') {
                return redirect()->route('customer.account')
                    ->withErrors(['address' => 'Please add your delivery address in your account before placing an order.']);
            }
        }

        $result = DB::transaction(function () use ($items, $request) {
            $analysis = $this->analyzeInventoryForItems($items);
            if (!empty($analysis['missing_recipe'])) {
                Log::warning('Customer checkout blocked: missing recipe.', [
                    'user_id' => auth()->id(),
                    'reason' => $analysis['missing_recipe'],
                ]);

                throw ValidationException::withMessages([
                    'items' => 'Some items are not orderable right now. Please try different menu items.',
                ]);
            }

            $requirements = $analysis['requirements'];
            $inventories = empty($requirements)
                ? collect()
                : Inventory::query()
                ->whereIn('id', array_keys($requirements))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $insufficient = [];
            foreach ($requirements as $inventoryId => $requiredQty) {
                $inventory = $inventories->get($inventoryId);
                $available = $inventory ? (float) $inventory->quantity : 0.0;
                $required = (float) $requiredQty;

                if (!$inventory || $available + 1e-9 < $required) {
                    $name = $analysis['inventory_names'][$inventoryId] ?? ($inventory?->item_name ?? "Item #{$inventoryId}");
                    $unit = $inventory?->unit ?? '';
                    $insufficient[] = "{$name} (need {$required}{$unit}, have {$available}{$unit})";
                }
            }

            if (!empty($insufficient)) {
                Log::info('Customer checkout blocked: insufficient inventory.', [
                    'user_id' => auth()->id(),
                    'details' => $insufficient,
                ]);

                throw ValidationException::withMessages([
                    'items' => 'Sorry, requested quantity is not available now. Please reduce quantity and try again.',
                ]);
            }

            $onlineTable = Table::firstOrCreate(
                ['table_number' => 'ONLINE'],
                ['capacity' => 1, 'location' => 'Online', 'status' => 'available']
            );

            $notes = trim((string) $request->notes);

            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'table_id' => $onlineTable->id,
                'user_id' => auth()->id(),
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'payment_method' => $request->payment_method,
                'notes' => $notes,
                'order_source' => 'customer',
                'is_customer_approved' => false,
            ]);

            foreach ($items as $item) {
                $menuItem = Menu::findOrFail($item['menu_id']);

                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_id' => $item['menu_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $menuItem->price,
                    'subtotal' => $menuItem->price * $item['quantity'],
                ]);
            }

            $order->calculateTotal();

            $paymentTransaction = null;

            return [
                'order' => $order,
                'paymentTransaction' => $paymentTransaction,
            ];
        });

        $order = $result['order'];
        $paymentTransaction = $result['paymentTransaction'];

        $request->session()->forget('cart.items');

        // If customer selected SSLCommerz, forward order details to the demo SSLCommerz flow.
        if ($request->input('payment_method') === 'sslcommerz') {
            // Use order_number as tran_id to correlate.
            $order->refresh();

            return view('sslcommerz.forward', [
                'order' => $order,
            ]);
        }

        // Default: cash on delivery
        return redirect()->route('customer.orders')
            ->with('success', 'Order placed for cash on delivery. Waiting for admin/manager approval.');
    }

    // List orders placed by the authenticated customer.
    public function index()
    {
        $orders = Order::with(['items.menu'])
            ->where('user_id', auth()->id())
            ->where('order_source', 'customer')
            ->latest()
            ->paginate(10);

        $trackSteps = ['pending', 'approved', 'preparing', 'ready', 'completed'];
        $trackLabels = [
            'pending' => 'Pending',
            'approved' => 'Approved',
            'preparing' => 'Preparing',
            // Show 'Ready' — do not label as 'Delivered' until an explicit delivery/completion step.
            'ready' => 'Ready',
            'completed' => 'Delivered',
        ];

        return view('website.customer.orders', compact('orders', 'trackSteps', 'trackLabels'));
    }

    public function cancel(Request $request, Order $order)
    {
        if ($order->user_id !== auth()->id() || ($order->order_source ?? 'staff') !== 'customer') {
            abort(403);
        }

        if (!$order->canBeCancelledByCustomer()) {
            return redirect()->route('customer.orders')
                ->withErrors(['cancel' => $order->cancellationPolicyHint()]);
        }

        $wasPaid = in_array($order->payment_status, ['paid', 'partial'], true);

        $order->update([
            'status' => 'cancelled',
            'payment_status' => $wasPaid ? 'refunded' : ($order->payment_status ?? 'unpaid'),
            'reserved_requirements' => null,
            'reserved_at' => null,
        ]);

        $message = $wasPaid
            ? 'Order cancelled. A full refund will be processed under the refund policy.'
            : 'Order cancelled. No payment was collected.';

        return redirect()->route('customer.orders')->with('success', $message);
    }

    // Compute required inventory quantities and check for missing recipes.
    private function analyzeInventoryForItems(array $items): array
    {
        $requiredByInventory = [];
        $inventoryNames = [];
        $missingRecipeMessage = null;

        foreach ($items as $item) {
            $menu = Menu::with('menuIngredients.inventory')->find($item['menu_id']);
            if (!$menu) {
                continue;
            }

            if ($menu->menuIngredients->isEmpty()) {
                $missingRecipeMessage = 'Recipe is missing for menu item "' . $menu->name . '". Add required inventory items in Menu settings first.';
                continue;
            }

            foreach ($menu->menuIngredients as $ingredient) {
                $neededQty = (float) $ingredient->quantity_per_dish * (float) $item['quantity'];
                $requiredByInventory[$ingredient->inventory_id] = ($requiredByInventory[$ingredient->inventory_id] ?? 0) + $neededQty;
                $inventoryNames[$ingredient->inventory_id] = $ingredient->inventory->item_name ?? 'Unknown Item';
            }
        }

        return [
            'requirements' => $requiredByInventory,
            'inventory_names' => $inventoryNames,
            'missing_recipe' => $missingRecipeMessage,
        ];
    }

    // bKash callback and invoice helper removed — tokenized checkout has been disabled.
}
