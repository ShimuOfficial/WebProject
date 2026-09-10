<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Menu;
use App\Models\Table;
use App\Models\Inventory;
use App\Models\User;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * DashboardController
 *
 * Prepare data for admin dashboard and chef KDS endpoints.
 */
class DashboardController extends Controller
{
    // Render contact page used on the public website.
    public function contact()
    {
        $pageTitle = 'Contact Us';
        return view('website.pages.contact', compact('pageTitle'));
    }

    // Render the admin dashboard view.
    public function index()
    {
        return view('admin.dashboard.index', $this->buildDashboardData());
    }

    // Return JSON dashboard data (used by AJAX/updates).
    public function data(Request $request)
    {
        $data = $this->buildDashboardData();
        $role = $data['role'] ?? '';

        if ($role === 'chef') {
            return response()->json([
                'role' => $role,
                'pendingCount' => $data['pendingCount'] ?? 0,
                'preparingCount' => $data['preparingCount'] ?? 0,
                'readyCount' => $data['readyCount'] ?? 0,
                'kitchenQueue' => ($data['kitchenQueue'] ?? collect())->map(function ($order) {
                    return [
                        'order_number' => $order->order_number,
                        'table' => $order->table?->table_number ?? 'N/A',
                        'staff' => $order->user?->name ?? 'N/A',
                        'status_label' => $this->statusLabel($order),
                        'status_class' => $order->status,
                        'time_label' => $order->created_at->diffForHumans(),
                    ];
                })->values(),
            ]);
        }

        return response()->json([
            'role' => $role,
            'todayOrders' => $data['todayOrders'] ?? 0,
            'todayRevenue' => (float) ($data['todayRevenue'] ?? 0),
            'totalRevenue' => (float) ($data['totalRevenue'] ?? 0),
            'availableTables' => $data['availableTables'] ?? 0,
            'totalTables' => $data['totalTables'] ?? 0,
            'occupiedTables' => $data['occupiedTables'] ?? 0,
            'totalMenuItems' => $data['totalMenuItems'] ?? 0,
            'lowStockItems' => $data['lowStockItems'] ?? 0,
            'totalStaff' => $data['totalStaff'] ?? 0,
            'mostPopularDish' => [
                'name' => $data['mostPopularDish']?->name,
                'total_quantity' => $data['mostPopularDish']?->total_quantity ?? 0,
            ],
            'pendingOrders' => ($data['pendingOrders'] ?? collect())->take(6)->map(function ($order) {
                return [
                    'order_number' => $order->order_number,
                    'table' => $order->table?->table_number ?? 'N/A',
                    'staff' => $order->user?->name ?? 'N/A',
                    'status_label' => $this->statusLabel($order),
                    'status_class' => $order->status,
                    'total_amount' => (float) $order->total_amount,
                ];
            })->values(),
            'recentOrders' => ($data['recentOrders'] ?? collect())->map(function ($order) {
                return [
                    'order_number' => $order->order_number,
                    'table' => $order->table?->table_number ?? 'N/A',
                    'staff' => $order->user?->name ?? 'N/A',
                    'items_label' => ($order->items_count ?? $order->items->count()) . ' items',
                    'status_label' => $this->statusLabel($order),
                    'status_class' => $order->status,
                    'total_amount' => (float) $order->total_amount,
                    'time_label' => $order->created_at->diffForHumans(),
                ];
            })->values(),
        ]);
    }

    // Build common dashboard data for views and APIs.
    private function buildDashboardData(): array
    {
        $role = auth()->user()->role ?? '';
        $isChef = $role === 'chef';

        if ($isChef) {
            $pendingCount = Order::where('status', 'pending')->count();
            $preparingCount = Order::where('status', 'preparing')->count();
            $readyCount = Order::where('status', 'ready')->count();
            // Kitchen should receive orders that have been approved by admin and those being prepared
            $kitchenQueue = Order::whereIn('status', ['approved', 'preparing'])
                ->with(['table', 'user'])
                ->orderBy('created_at')
                ->take(12)
                ->get();

            return compact('role', 'isChef', 'pendingCount', 'preparingCount', 'readyCount', 'kitchenQueue');
        }

        $todayOrders = Order::whereDate('created_at', today())->count();
        $todayRevenue = Order::whereDate('created_at', today())->where('status', '!=', 'cancelled')->sum('total_amount');
        $totalRevenue = Order::where('status', '!=', 'cancelled')->sum('total_amount');
        $availableTables = Table::where('status', 'available')->count();
        $totalTables = Table::count();
        $occupiedTables = Table::where('status', 'occupied')->count();
        $totalMenuItems = Menu::count();
        $lowStockItems = Inventory::whereColumn('quantity', '<=', 'min_quantity')->count();
        $totalStaff = User::where('role', '!=', 'admin')->count();
        $mostPopularDish = OrderItem::join('menus', 'order_items.menu_id', '=', 'menus.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', '!=', 'cancelled')
            ->select('menus.name', DB::raw('SUM(order_items.quantity) as total_quantity'))
            ->groupBy('menus.name')
            ->orderByDesc('total_quantity')
            ->first();
        $recentOrders = Order::with(['table', 'user', 'items'])->latest()->take(10)->get();
        // For admin dashboard show pending, approved and preparing orders in the pending list
        $pendingOrders = Order::whereIn('status', ['pending', 'approved', 'preparing'])->with(['table', 'user'])->latest()->get();

        return compact(
            'role',
            'isChef',
            'todayOrders',
            'todayRevenue',
            'totalRevenue',
            'availableTables',
            'totalTables',
            'occupiedTables',
            'totalMenuItems',
            'lowStockItems',
            'totalStaff',
            'mostPopularDish',
            'recentOrders',
            'pendingOrders'
        );
    }

    // Human-readable label for an order status.
    private function statusLabel(Order $order): string
    {
        // Always return the status label derived from the order's status.
        // Do not special-case online orders marked 'ready' as 'Delivered'.
        return ucfirst((string) $order->status);
    }
}
