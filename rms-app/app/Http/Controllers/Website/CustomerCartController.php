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

    // Add an item to the cart (or increase quantity).
    public function add(Request $request)
    {
        $validated = $request->validate([
            'menu_id' => 'required|exists:menus,id',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $menuId = (int) $validated['menu_id'];
        $quantity = (int) ($validated['quantity'] ?? 1);
        $menu = Menu::with('menuIngredients.inventory')->findOrFail($menuId);

        if (!$menu->isOrderable()) {
            return $this->stockError($request, 'This dish is currently out of stock.');
        }

        $cart = $this->getCartItems($request);
        $nextQty = ($cart[$menuId] ?? 0) + $quantity;

        if ($nextQty > $menu->available_servings) {
            return $this->stockError($request, 'Only ' . $menu->available_servings . ' serving(s) left for ' . $menu->name . '.');
        }

        $cart[$menuId] = $nextQty;
        $this->storeCartItems($request, $cart);

        if ($request->expectsJson()) {
            return response()->json($this->buildCartPayload($request));
        }

        return redirect()->back()->with('success', 'Added to cart.');
    }

    // Update quantity for a menu item in cart.
    public function update(Request $request, Menu $menu)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $menu->loadMissing('menuIngredients.inventory');
        $nextQty = (int) $validated['quantity'];

        if ($nextQty > $menu->available_servings) {
            return $this->stockError($request, 'Only ' . $menu->available_servings . ' serving(s) left for ' . $menu->name . '.');
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

    // Return raw cart items array from session.
    private function getCartItems(Request $request): array
    {
        return $request->session()->get('cart.items', []);
    }

    // Persist cart items to session.
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
