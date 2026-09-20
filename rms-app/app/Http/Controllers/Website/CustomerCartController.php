<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * DEFENSE: §5.4 session cart — session('cart.items')[menu_id] = qty
 * Board: "Cart table ache?" → No. Session only.
 */
class CustomerCartController extends Controller
{
    // Show cart page with items and totals.
    public function show(Request $request)
    {
        $cartItems = $this->getCartItems($request);
        $menus = $this->getMenusForCart($cartItems);
        $summary = $this->summarizeCart($menus, $cartItems);

        return view('website.customer.cart', [
            'menus' => $menus,
            'cartRows' => $summary['items'],
            'cartTotal' => $summary['total'],
            'cartCount' => $summary['count'],
        ]);
    }

    // DEFENSE Q6/Q7: Add to session cart with max = min(20, stock).
    // Board: "Cart table ache?" → NO. See getCartItems()/storeCartItems() below (session only).
    public function add(Request $request)
    {
        $validated = $request->validate([
            'menu_id' => 'required|exists:menus,id',
            // Soft max 20 from config; stock may lower it further via maxOrderableQuantity().
            'quantity' => 'nullable|integer|min:1|max:' . (int) config('restaurant.max_item_quantity', 20),
        ]);

        $menuId = (int) $validated['menu_id'];
        $quantity = (int) ($validated['quantity'] ?? 1);
        $menu = Menu::with('menuIngredients.inventory')->findOrFail($menuId);

        if (!$menu->isOrderable()) {
            return $this->stockError($request, 'This dish is currently out of stock.');
        }

        $cart = $this->getCartItems($request);
        $nextQty = ($cart[$menuId] ?? 0) + $quantity;
        // DEFENSE Q7: hard business cap
        $maxQty = $menu->maxOrderableQuantity();

        if ($nextQty > $maxQty) {
            $reason = $maxQty < (int) config('restaurant.max_item_quantity', 20)
                ? 'Only ' . $maxQty . ' serving(s) left for ' . $menu->name . '.'
                : 'You can order at most ' . config('restaurant.max_item_quantity', 20) . ' of ' . $menu->name . ' per order.';

            return $this->stockError($request, $reason);
        }

        $cart[$menuId] = $nextQty;
        $this->storeCartItems($request, $cart);

        if ($request->expectsJson()) {
            $payload = $this->buildCartPayload($request);
            $payload['message'] = 'Added to cart.';
            $payload['added'] = [
                'menu_id' => $menuId,
                'quantity' => $quantity,
                'line_quantity' => $nextQty,
                'remaining' => max(0, $menu->available_servings - $nextQty),
            ];

            return response()->json($payload);
        }

        return redirect()->back()->with('success', 'Added to cart.');
    }

    // Update quantity for a menu item in cart.
    public function update(Request $request, Menu $menu)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1|max:' . (int) config('restaurant.max_item_quantity', 20),
        ]);

        $menu->loadMissing('menuIngredients.inventory');
        $nextQty = (int) $validated['quantity'];
        $maxQty = $menu->maxOrderableQuantity();

        if ($nextQty > $maxQty) {
            $reason = $maxQty < (int) config('restaurant.max_item_quantity', 20)
                ? 'Only ' . $maxQty . ' serving(s) left for ' . $menu->name . '.'
                : 'Maximum ' . config('restaurant.max_item_quantity', 20) . ' per item per order.';

            return $this->stockError($request, $reason);
        }

        $cart = $this->getCartItems($request);
        $cart[$menu->id] = $nextQty;
        $this->storeCartItems($request, $cart);

        if ($request->expectsJson()) {
            return response()->json($this->buildCartPayload($request));
        }

        return redirect()->back()->with('success', 'Cart updated.');
    }

    // Remove a menu item from the cart.
    public function remove(Request $request, Menu $menu)
    {
        $cart = $this->getCartItems($request);
        unset($cart[$menu->id]);
        $this->storeCartItems($request, $cart);

        if ($request->expectsJson()) {
            return response()->json($this->buildCartPayload($request));
        }

        return redirect()->back()->with('success', 'Item removed from cart.');
    }

    // Empty the cart session.
    public function clear(Request $request)
    {
        $request->session()->forget('cart.items');

        if ($request->expectsJson()) {
            return response()->json($this->buildCartPayload($request));
        }

        return redirect()->back()->with('success', 'Cart cleared.');
    }

    // Build JSON payload representing current cart state.
    private function buildCartPayload(Request $request): array
    {
        $cartItems = $this->getCartItems($request);
        $menus = $this->getMenusForCart($cartItems);
        $summary = $this->summarizeCart($menus, $cartItems);

        return [
            'count' => $summary['count'],
            'total' => $summary['total'],
            'items' => $summary['items'],
        ];
    }

    /**
     * DEFENSE Q6: Session cart — NO `cart` database table.
     * Key: session('cart.items') = [menu_id => quantity, ...]
     * Lost when session expires / browser clears cookies.
     */
    private function getCartItems(Request $request): array
    {
        return $request->session()->get('cart.items', []);
    }

    /** DEFENSE Q6: Persist cart to Laravel session store. */
    private function storeCartItems(Request $request, array $cartItems): void
    {
        $request->session()->put('cart.items', $cartItems);
    }

    // Load Menu models for the given cart item ids.
    private function getMenusForCart(array $cartItems): Collection
    {
        $menuIds = array_keys($cartItems);

        return $menuIds
            ? Menu::with('menuIngredients.inventory')->whereIn('id', $menuIds)->get()->keyBy('id')
            : collect();
    }

    // Summarize cart into count, total and item rows.
    private function summarizeCart(Collection $menus, array $cartItems): array
    {
        $items = [];
        $total = 0.0;
        $count = 0;

        foreach ($menus as $menu) {
            $qty = (int) ($cartItems[$menu->id] ?? 0);

            if ($qty < 1) {
                continue;
            }

            $subtotal = (float) $menu->price * $qty;

            $items[] = [
                'id' => $menu->id,
                'menu' => $menu,
                'name' => $menu->name,
                'price' => (float) $menu->price,
                'quantity' => $qty,
                'subtotal' => $subtotal,
            ];

            $total += $subtotal;
            $count += $qty;
        }

        return [
            'count' => $count,
            'total' => round($total, 2),
            'items' => $items,
        ];
    }

    private function stockError(Request $request, string $message)
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => $message], 422);
        }

        return redirect()->back()->withErrors(['stock' => $message]);
    }
}
