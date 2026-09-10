<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Menu;
use App\Models\Table;
use App\Models\Inventory;
use App\Models\User;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function contact()
    {
        $pageTitle = 'Contact Us';
        return view('website.contact', compact('pageTitle'));
    }

    public function index()
    {
        return view('dashboard.index', $this->buildDashboardData());
    }

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

    private function buildDashboardData(): array
    {
        $role = auth()->user()->role ?? '';

        if ($role === 'chef') {
            $pendingCount = Order::where('status', 'pending')->count();
            $preparingCount = Order::where('status', 'preparing')->count();
            $readyCount = Order::where('status', 'ready')->count();
            $kitchenQueue = Order::whereIn('status', ['pending', 'preparing'])
                ->with(['table', 'user'])
                ->orderBy('created_at')
                ->take(12)
                ->get();

            return compact('role', 'pendingCount', 'preparingCount', 'readyCount', 'kitchenQueue');
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
        $pendingOrders = Order::whereIn('status', ['pending', 'preparing'])->with(['table', 'user'])->latest()->get();

        return compact(
            'role',
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

    private function statusLabel(Order $order): string
    {
        // Keep display label consistent with the actual status.
        return ucfirst((string) $order->status);
    }
}
