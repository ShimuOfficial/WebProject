<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Menu;
use App\Models\Table;
use App\Models\Inventory;
use App\Models\PaymentTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * DEFENSE: §5.6 dine-in create/pay · §5.7 approve · §5.9 status machine
 */
class OrderController extends Controller
{
    // List orders with optional filters (status, payment_status, date).
    public function index(Request $request)
    {
        $query = Order::with(['table', 'user', 'items.menu']);
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }
        $orders = $query->latest()->paginate(15);
        return view('admin.orders.index', compact('orders'));
    }

    // Show order creation form for staff/admin.
    public function create()
    {
        $messages = config('order_messages');
        $tables = Table::where('status', 'available')->orWhere('status', 'occupied')->get();
        $inventories = Inventory::orderByDesc('item_name')->get();
        $menus = Menu::with(['menuIngredients.inventory'])->withCount('menuIngredients')->where('is_available', true)->get();
        $unconfiguredMenuCount = $menus->filter(fn($menu) => $menu->menuIngredients->isEmpty())->count();
        $menuItems = $menus->groupBy('category');
        $inventoryMeta = $inventories->mapWithKeys(function ($inventory) {
            return [$inventory->id => [
                'item_name' => $inventory->item_name,
                'quantity' => (float) $inventory->quantity,
                'unit' => $inventory->unit ?? 'unit',
                'min_quantity' => (float) $inventory->min_quantity,
            ]];
        });
        $menuMeta = $menus->mapWithKeys(function ($menu) {
            return [$menu->id => [
                'name' => $menu->name,
                'price' => (float) $menu->price,
                'ingredients' => $menu->menuIngredients->map(function ($ingredient) {
                    return [
                        'inventory_id' => $ingredient->inventory_id,
                        'item_name' => $ingredient->inventory->item_name ?? 'Unknown Item',
                        'unit' => $ingredient->inventory->unit ?? 'unit',
                        'qty_per_dish' => (float) $ingredient->quantity_per_dish,
                    ];
                })->values(),
            ]];
        });
        $orderCreateData = [
            'menuMeta' => $menuMeta,
            'inventoryMeta' => $inventoryMeta,
            'serverTotal' => session('server_total') ?? null,
            'serverChange' => session('server_change') ?? null,
            'messages' => [
                'validation' => $messages['validation'] ?? [],
                'ui' => $messages['ui'] ?? [],
            ],
        ];

        // If no table is pre-selected by old input, default to the first available table (server-side)
        if (empty(old('table_id')) && $tables->isNotEmpty()) {
            session()->flashInput(['table_id' => $tables->first()->id]);
        }

        return view('admin.orders.create', compact('tables', 'menuItems', 'menuMeta', 'inventoryMeta', 'unconfiguredMenuCount', 'orderCreateData'));
    }

    // Validate and persist a new order (staff-created).
    public function store(Request $request)
    {
        $rules = [
            'table_id' => 'required|exists:tables,id',
            'items' => 'required|array|min:1',
            'items.*.menu_id' => 'required|exists:menus,id',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:cash,bkash,rocket,card',
            'payment_reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ];

        // If paying by cash on-site, require paid_amount and forbid negative values
        if ($request->input('payment_method') === 'cash') {
            $rules['paid_amount'] = 'required|numeric|min:0.01';
        } else {
            $rules['paid_amount'] = 'nullable|numeric|min:0';
        }

        $messages = config('order_messages.controller');

        $request->validate($rules, [
            'paid_amount.min' => $messages['paid_amount_min'],
            'paid_amount.required' => $messages['paid_amount_required'],
        ]);

        if (in_array($request->payment_method, ['bkash', 'rocket', 'card']) && blank($request->payment_reference)) {
            return back()->withErrors([
                'payment_reference' => $messages['payment_reference_required'],
            ])->withInput();
        }

        // Allow admin to create orders even if stock prediction shows shortage.
        // We'll record predicted requirements as reservations so admin can see expected deduction.

        $order = Order::create([
            'order_number' => Order::generateOrderNumber(),
            'table_id' => $request->table_id,
            'user_id' => auth()->id(),
            'status' => 'pending',
            'order_source' => 'staff',
            'is_customer_approved' => true,
            'payment_method' => $request->payment_method,
            'payment_reference' => $request->payment_reference,
            'payment_status' => 'unpaid',
            'notes' => $request->notes,
        ]);

        foreach ($request->items as $item) {
            $menuItem = Menu::find($item['menu_id']);
            OrderItem::create([
                'order_id' => $order->id,
                'menu_id' => $item['menu_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $menuItem->price,
                'subtotal' => $menuItem->price * $item['quantity'],
                'notes' => $item['notes'] ?? null,
            ]);
        }

        $totalAmount = (float) $order->calculateTotal();

        $paidInput = (float) ($request->input('paid_amount') ?? 0);

        if ($request->payment_method === 'cash') {
            if ($paidInput < $totalAmount) {
                $change = max(0, $paidInput - $totalAmount);
                return back()->withErrors(['paid_amount' => $messages['cash_paid_less_than_total']])
                    ->withInput()
                    ->with('server_total', number_format($totalAmount, 2))
                    ->with('server_paid', number_format($paidInput, 2))
                    ->with('server_change', number_format($change, 2));
            }

            $change = $paidInput - $totalAmount;

            $order->update([
                'paid_amount' => $paidInput,
                'payment_status' => 'paid',
                'paid_at' => now(),
            ]);

            $this->logPaymentTransaction(
                $order,
                'manual',
                'success',
                $totalAmount,
                $request->payment_method,
                [
                    'source' => 'onsite_order_create',
                    'payment_reference' => $request->payment_reference,
                    'payment_status' => 'paid',
                    'paid_amount' => $paidInput,
                    'change' => $change,
                ]
            );
        } else {
            // For non-cash methods treat as full payment recorded (reference required above)
            $order->update([
                'paid_amount' => $totalAmount,
                'payment_status' => 'paid',
                'paid_at' => now(),
            ]);

            $this->logPaymentTransaction(
                $order,
                'manual',
                'success',
                $totalAmount,
                $request->payment_method,
                [
                    'source' => 'onsite_order_create',
                    'payment_reference' => $request->payment_reference,
                    'payment_status' => 'paid',
                ]
            );
        }

        Table::where('id', $request->table_id)->update(['status' => 'occupied']);

        // Compute reservation requirements and save on order for prediction (no physical deduction yet)
        $requirements = [];
        $order->load('items.menu.menuIngredients');
        foreach ($order->items as $item) {
            if (!$item->menu) continue;
            foreach ($item->menu->menuIngredients as $ing) {
                $invId = (int) $ing->inventory_id;
                $needed = (float) $ing->quantity_per_dish * (int) $item->quantity;
                if ($needed <= 0) continue;
                $requirements[$invId] = ($requirements[$invId] ?? 0) + $needed;
            }
        }

        $order->update([
            'reserved_requirements' => empty($requirements) ? null : $requirements,
            'reserved_at' => now(),
        ]);

        // Check if reservation causes over-commit and warn admin
        $reservedOrders = Order::query()
            ->whereNotNull('reserved_at')
            ->whereNull('inventory_deducted_at')
            ->get()
            ->pluck('reserved_requirements')
            ->filter()
            ->values();

        $aggregateReserved = [];
        foreach ($reservedOrders as $r) {
            foreach ($r as $invId => $q) {
                $aggregateReserved[$invId] = ($aggregateReserved[$invId] ?? 0) + (float) $q;
            }
        }

        $over = [];
        if (!empty($aggregateReserved)) {
            $inventories = Inventory::whereIn('id', array_keys($aggregateReserved))->get()->keyBy('id');
            foreach ($aggregateReserved as $invId => $qty) {
                $inv = $inventories->get($invId);
                $avail = $inv ? (float) $inv->quantity : 0;
                if ($qty > $avail + 1e-9) {
                    $over[] = ($inv?->item_name ?? "Item #{$invId}") . " (reserved {$qty}, available {$avail})";
                }
            }
        }

        $redirect = redirect()->route('orders.receipt', [$order, 'autoprint' => 1, 'return' => 'orders'])
            ->with('success', $messages['paid_order_success']);

        if (!empty($over)) {
            $redirect = $redirect->with('warning', str_replace(':items', implode(', ', $over), $messages['reservation_warning']));
        }

        return $redirect;
    }

    // Edit an existing order (load related data for the form).
    public function edit(Order $order)
    {
        $order->load(['user', 'table', 'items.menu']);
        $tables = Table::all();
        $menuItems = Menu::where('is_available', true)->get()->groupBy('category');
        // Compute already paid amount from stored paid_amount or from payment transactions as a fallback
        $alreadyPaid = (float) ($order->paid_amount ?? PaymentTransaction::where('order_id', $order->id)->sum('amount'));

        return view('admin.orders.edit', compact('order', 'tables', 'menuItems', 'alreadyPaid'));
    }

    // Update order metadata and status, enforcing transitions and inventory checks.
    public function update(Request $request, Order $order)
    {
        $previousStatus = $order->status;

        $request->validate([
            'status' => 'required|in:pending,approved,preparing,ready,served,completed,cancelled',
            'notes' => 'nullable|string',
        ]);

        if ($order->status === 'cancelled' && $request->status !== 'cancelled') {
            return back()->withErrors(['status' => 'Cancelled orders cannot be updated.']);
        }

        if ($request->status === 'cancelled' && $order->status !== 'cancelled') {
            return back()->withErrors(['status' => 'Order cancellation is disabled.']);
        }

        // DEFENSE: §5.9 / Q6 — server-side status machine (dropdown cheat blocked)
        $allowedTransitions = [
            'pending' => ['pending', 'approved', 'cancelled'],
            'approved' => ['approved', 'preparing', 'cancelled'],
            'preparing' => ['preparing', 'ready', 'cancelled'],
            'ready' => ['ready', 'served', 'completed', 'cancelled'],
            'served' => ['served', 'completed'],
            'completed' => ['completed'],
            'cancelled' => ['cancelled'],
        ];

        $from = $previousStatus;
        $allowedFrom = $allowedTransitions[$from] ?? [$from];
        if (!in_array($request->status, $allowedFrom, true)) {
            return back()->withErrors(['status' => "Invalid status transition from {$from} to {$request->status}."]);
        }

        if (in_array($request->status, ['ready', 'served', 'completed']) && !$order->inventory_deducted_at) {
            [$hasStock, $stockMessage] = $this->hasSufficientInventoryForItems(
                $order->items->map(fn($item) => [
                    'menu_id' => $item->menu_id,
                    'quantity' => $item->quantity,
                ])->toArray()
            );

            if (!$hasStock) {
                return back()->withErrors(['status' => $stockMessage]);
            }
        }

        if ($order->order_source === 'customer' && !$order->is_customer_approved && $request->status !== 'cancelled') {
            return back()->withErrors(['status' => 'Customer order must be approved by admin/manager before processing.']);
        }

        $order->update(['status' => $request->status, 'notes' => $request->notes]);

        // If the order is fully delivered/completed and payment method is COD,
        // record the payment automatically so COD doesn't remain due.
        $order->refresh();
        if ($request->status === 'completed' && ($order->payment_method ?? 'cash') === 'cash' && ($order->payment_status ?? 'unpaid') !== 'paid') {
            $order->update([
                'paid_amount' => $order->total_amount,
                'payment_status' => 'paid',
                'paid_at' => now(),
            ]);

            $this->logPaymentTransaction(
                $order,
                'manual',
                'success',
                (float) $order->total_amount,
                'cash',
                ['source' => 'auto_mark_paid_on_completed']
            );
        }

        if (in_array($request->status, ['ready', 'served', 'completed']) && $previousStatus !== $request->status) {
            // When physically deducting, respect and clear reservations for this order.
            $this->deductInventoryForOrder($order->fresh('items.menu'));
            // Clear reservation for the order (it has been consumed)
            $order->update(['reserved_requirements' => null, 'reserved_at' => null]);
        }

        if (in_array($request->status, ['completed', 'cancelled'])) {
            $active = Order::where('table_id', $order->table_id)->where('id', '!=', $order->id)
                ->whereNotIn('status', ['completed', 'cancelled'])->count();
            if ($active === 0) {
                Table::where('id', $order->table_id)->update(['status' => 'available']);
            }
        }
        return redirect()->route('orders.index')->with('success', 'Order updated successfully!');
    }

    // Immediately mark order as fully paid (cash) and log transaction.
    public function fastPay(Order $order)
    {
        $currentPaid = (float) ($order->paid_amount ?? 0);
        $totalAmount = (float) $order->total_amount;
        $remainingDue = max($totalAmount - $currentPaid, 0);

        if ($remainingDue <= 0) {
            return redirect()->route('orders.edit', $order)->with('success', 'This order is already fully paid.');
        }

        $order->update([
            'payment_method' => 'cash',
            'payment_reference' => null,
            'paid_amount' => $totalAmount,
            'payment_status' => 'paid',
            'paid_at' => now(),
        ]);

        $this->logPaymentTransaction(
            $order,
            'manual',
            'success',
            $remainingDue,
            'cash',
            [
                'source' => 'fast_pay',
                'mode' => 'full_payment',
            ]
        );

        return redirect()->route('orders.index')
            ->with('success', 'Fast payment completed (full due recorded).');
    }

    // DEFENSE: §5.7 — only after this flag is true does KitchenController show the ticket
    public function approveCustomerOrder(Order $order)
    {
        if ($order->order_source !== 'customer') {
            return redirect()->route('orders.index')->withErrors(['approval' => 'Only customer orders require approval.']);
        }

        if ($order->is_customer_approved) {
            return redirect()->route('orders.index')->with('success', 'Order already approved.');
        }

        // Mark the order as approved and update status so it appears in kitchen queue
        $order->update(['is_customer_approved' => true, 'status' => 'approved']);

        return redirect()->route('orders.index')->with('success', 'Customer order approved and sent to kitchen.');
    }

    // Delete an order and free table if no active orders remain.
    public function destroy(Order $order)
    {
        $tableId = $order->table_id;
        $order->delete();
        $active = Order::where('table_id', $tableId)->whereNotIn('status', ['completed', 'cancelled'])->count();
        if ($active === 0) {
            Table::where('id', $tableId)->update(['status' => 'available']);
        }
        return redirect()->route('orders.index')->with('success', 'Order deleted successfully!');
    }

    // Record a manual payment or partial payment for an order.
    public function recordPayment(Request $request, Order $order)
    {
        $currentPaid = (float) ($order->paid_amount ?? 0);
        $totalAmount = (float) $order->total_amount;
        $remainingDue = max($totalAmount - $currentPaid, 0);

        if ($remainingDue <= 0) {
            return redirect()->route('orders.edit', $order)->with('success', 'This order is already fully paid.');
        }

        $request->validate([
            'payment_method' => 'required|in:cash,bkash,rocket,card',
            'paid_amount' => 'required|numeric|min:0.01|max:' . $remainingDue,
            'payment_reference' => 'nullable|string|max:100',
        ]);

        if (in_array($request->payment_method, ['bkash', 'rocket', 'card']) && blank($request->payment_reference)) {
            return back()->withErrors([
                'payment_reference' => 'Reference is required for bKash, Rocket, and card payments.',
            ]);
        }

        $paymentAmount = (float) $request->paid_amount;
        $newPaidAmount = min($currentPaid + $paymentAmount, $totalAmount);
        $paymentStatus = $newPaidAmount >= $totalAmount ? 'paid' : 'partial';

        $order->update([
            'payment_method' => $request->payment_method,
            'payment_reference' => $request->payment_reference,
            'paid_amount' => $newPaidAmount,
            'payment_status' => $paymentStatus,
            'paid_at' => now(),
        ]);

        $this->logPaymentTransaction(
            $order,
            'manual',
            'success',
            $paymentAmount,
            $request->payment_method,
            [
                'source' => 'record_payment',
                'payment_reference' => $request->payment_reference,
                'paid_amount_before' => $currentPaid,
                'paid_amount_after' => $newPaidAmount,
                'payment_status' => $paymentStatus,
            ]
        );

        if ($request->boolean('print_receipt')) {
            return redirect()->route('orders.receipt', [$order, 'autoprint' => 1, 'return' => 'orders'])
                ->with('success', 'Payment recorded successfully.');
        }

        return redirect()->route('orders.index')
            ->with('success', 'Payment recorded successfully.');
    }

    // Shortcut to record payment and print receipt immediately.
    public function pay(Request $request, Order $order)
    {
        return $this->recordPayment($request->merge(['print_receipt' => true]), $order);
    }

    // SSLCommerz methods removed - May 14, 2026
    // Removed: startOnlinePayment(), sslCommerzSuccess(), sslCommerzFail(), sslCommerzCancel(), sslCommerzIpn()
    // Use manual payment recording only via recordPayment() and fastPay() methods

    // Render printable receipt for an order.
    public function receipt(Order $order)
    {
        $order->load(['table', 'user', 'items.menu']);
        return view('admin.orders.receipt', compact('order'));
    }

    // Cancel an order (clear reservations) if allowed.
    public function cancel(Order $order)
    {
        if (in_array($order->status, ['completed', 'cancelled'])) {
            return redirect()->route('orders.index')->withErrors(['cancel' => 'Cannot cancel completed or already cancelled orders.']);
        }

        if ($order->inventory_deducted_at) {
            return redirect()->route('orders.index')->withErrors(['cancel' => 'Cannot cancel this order because inventory has already been deducted.']);
        }

        // Clear reservation (if any) and mark cancelled
        $order->update([
            'status' => 'cancelled',
            'reserved_requirements' => null,
            'reserved_at' => null,
        ]);

        // If no other active orders on the table, free the table
        if ($order->table_id) {
            $active = Order::where('table_id', $order->table_id)->where('id', '!=', $order->id)
                ->whereNotIn('status', ['completed', 'cancelled'])->count();
            if ($active === 0) {
                Table::where('id', $order->table_id)->update(['status' => 'available']);
            }
        }

        return redirect()->route('orders.index')->with('success', 'Order cancelled successfully.');
    }

    // invoice() method removed - May 14, 2026 - use receipt instead
    // DomPDF library removed as part of system simplification

    // Deduct inventory quantities for a fulfilled order.
    private function deductInventoryForOrder(Order $order): void
    {
        if ($order->inventory_deducted_at) {
            return;
        }

        $deductions = [];

        foreach ($order->items as $item) {
            $menu = Menu::with('menuIngredients.inventory')->find($item->menu_id);
            if (!$menu || $menu->menuIngredients->isEmpty()) {
                continue;
            }

            foreach ($menu->menuIngredients as $ingredient) {
                $neededQty = (float) $ingredient->quantity_per_dish * (float) $item->quantity;
                $deductions[$ingredient->inventory_id] = ($deductions[$ingredient->inventory_id] ?? 0) + $neededQty;
            }
        }

        foreach ($deductions as $inventoryId => $qty) {
            $inventory = Inventory::find($inventoryId);
            if (!$inventory) {
                continue;
            }

            $newQty = max((float) $inventory->quantity - $qty, 0);
            $inventory->update(['quantity' => $newQty]);
        }

        $order->update(['inventory_deducted_at' => now()]);
        Menu::syncAvailabilityFromInventory();
    }

    // Check if required inventory is sufficient for provided menu items.
    private function hasSufficientInventoryForItems(array $items): array
    {
        $requiredByInventory = [];
        $inventoryNames = [];

        foreach ($items as $item) {
            $menu = Menu::with('menuIngredients.inventory')->find($item['menu_id']);
            if (!$menu) {
                continue;
            }

            if ($menu->menuIngredients->isEmpty()) {
                return [
                    false,
                    'Recipe is missing for menu item "' . $menu->name . '". Add required inventory items in Menu settings first.',
                ];
            }

            foreach ($menu->menuIngredients as $ingredient) {
                $neededQty = (float) $ingredient->quantity_per_dish * (float) $item['quantity'];
                $requiredByInventory[$ingredient->inventory_id] = ($requiredByInventory[$ingredient->inventory_id] ?? 0) + $neededQty;
                $inventoryNames[$ingredient->inventory_id] = $ingredient->inventory->item_name ?? 'Unknown Item';
            }
        }

        foreach ($requiredByInventory as $inventoryId => $requiredQty) {
            $inventory = Inventory::find($inventoryId);
            if (!$inventory) {
                continue;
            }

            if ((float) $inventory->quantity < $requiredQty) {
                return [
                    false,
                    'Not enough stock for ' . ($inventoryNames[$inventoryId] ?? 'an item') . '. Required: ' . $requiredQty . ', Available: ' . (float) $inventory->quantity,
                ];
            }
        }

        return [true, null];
    }

    // verifySslCommerzPayment() removed - May 14, 2026 - SSLCommerz payment gateway removed

    // Deprecated: payment gateway card-type mapping (gateway removed May 14, 2026).
    // private function resolvePaymentMethodFromCardType(string $cardType): string
    // {
    //     $normalized = strtolower($cardType);
    //
    //     if (str_contains($normalized, 'bkash')) {
    //         return 'bkash';
    //     }
    //
    //     if (str_contains($normalized, 'rocket')) {
    //         return 'rocket';
    //     }
    //
    //     return 'card';
    // }

    // Persist a payment transaction record for an order.
    private function logPaymentTransaction(
        Order $order,
        string $gateway,
        string $status,
        float $amount,
        ?string $paymentMethod = null,
        array $gatewayResponse = [],
        ?string $transactionId = null
    ): PaymentTransaction {
        return PaymentTransaction::create([
            'order_id' => $order->id,
            'gateway' => $gateway,
            'transaction_id' => $transactionId ?? strtoupper($gateway) . '-' . $order->id . '-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(6)),
            'amount' => $amount,
            'status' => $status,
            'payment_method' => $paymentMethod,
            'gateway_response' => $gatewayResponse,
            'paid_at' => in_array($status, ['success', 'paid']) ? now() : null,
        ]);
    }
}
