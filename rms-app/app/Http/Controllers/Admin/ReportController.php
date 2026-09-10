<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * ReportController
 *
 * Generate reports and JSON endpoints for revenue, top items and orders.
 */
class ReportController extends Controller
{
    // Show reports index with initial data.
    public function index(Request $request)
    {
        return view('admin.reports.index', $this->buildReportData($request));
    }

    // Return JSON payload of report metrics for AJAX clients.
    public function data(Request $request)
    {
        $reportData = $this->buildReportData($request);

        return response()->json([
            'startDate' => $reportData['startDate'],
            'endDate' => $reportData['endDate'],
            'stats' => [
                'totalRevenue' => number_format($reportData['totalRevenue'], 2, '.', ''),
                'totalOrders' => $reportData['totalOrders'],
                'completedOrders' => $reportData['completedOrders'],
                'cancelledOrders' => $reportData['cancelledOrders'],
            ],
            'mostPopularDish' => [
                'name' => $reportData['mostPopularDish']?->name,
                'total_quantity' => $reportData['mostPopularDish']?->total_quantity ?? 0,
                'total_revenue' => number_format((float) ($reportData['mostPopularDish']?->total_revenue ?? 0), 2, '.', ''),
            ],
            'dailyRevenue' => $reportData['dailyRevenue']->map(fn($item) => [
                'date' => $item->date,
                'revenue' => (float) $item->revenue,
            ])->values(),
            'topItems' => $reportData['topItems']->map(fn($item) => [
                'name' => $item->name,
                'total_quantity' => (int) $item->total_quantity,
                'total_revenue' => (float) $item->total_revenue,
            ])->values(),
            'categoryRevenue' => $reportData['categoryRevenue']->map(fn($item) => [
                'category' => $item->category,
                'total_revenue' => (float) $item->total_revenue,
            ])->values(),
            'orders' => $reportData['orders']->map(fn($order) => [
                'order_number' => $order->order_number,
                'created_at' => $order->created_at?->format('Y-m-d H:i'),
                'customer' => $order->table?->name ?? ($order->user?->name ?? '-'),
                'total_amount' => number_format((float) $order->total_amount, 2, '.', ''),
                'status' => $this->statusLabel($order),
                'receipt_url' => route('orders.receipt', ['order' => $order, 'return' => 'orders']),
            ])->values(),
            'updatedAt' => now()->format('h:i:s A'),
        ]);
    }

    // Build the report dataset for requested date range.
    private function buildReportData(Request $request): array
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());
        $rangeStart = Carbon::parse($startDate)->startOfDay();
        $rangeEnd = Carbon::parse($endDate)->endOfDay();

        $orderQuery = Order::query()->whereBetween('created_at', [$rangeStart, $rangeEnd]);
        $paidOrderQuery = (clone $orderQuery)->where('status', '!=', 'cancelled');

        $totalRevenue = (clone $paidOrderQuery)->sum('total_amount');
        $totalOrders = (clone $orderQuery)->count();
        $completedOrders = (clone $orderQuery)->where('status', 'completed')->count();
        $cancelledOrders = (clone $orderQuery)->where('status', 'cancelled')->count();

        $dailyRevenue = (clone $paidOrderQuery)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_amount) as revenue'), DB::raw('COUNT(*) as orders'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $topItems = OrderItem::whereBetween('order_items.created_at', [$rangeStart, $rangeEnd])
            ->join('menus', 'order_items.menu_id', '=', 'menus.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', '!=', 'cancelled')
            ->select('menus.name', DB::raw('SUM(order_items.quantity) as total_quantity'), DB::raw('SUM(order_items.subtotal) as total_revenue'))
            ->groupBy('menus.name')
            ->orderByDesc('total_quantity')
            ->limit(8)
            ->get();

        $mostPopularDish = $topItems->first();

        $categoryRevenue = OrderItem::whereBetween('order_items.created_at', [$rangeStart, $rangeEnd])
            ->join('menus', 'order_items.menu_id', '=', 'menus.id')
            ->select('menus.category', DB::raw('SUM(order_items.subtotal) as total_revenue'))
            ->groupBy('menus.category')
            ->orderByDesc('total_revenue')
            ->get();

        $orders = (clone $orderQuery)
            ->with(['table', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit(12)
            ->get();

        return compact(
            'startDate',
            'endDate',
            'totalRevenue',
            'totalOrders',
            'completedOrders',
            'cancelledOrders',
            'dailyRevenue',
            'topItems',
            'categoryRevenue',
            'mostPopularDish',
            'orders'
        );
    }

    // Human-friendly status label for orders in reports.
    private function statusLabel(Order $order): string
    {
        // Keep a consistent label for status; do not convert customer 'ready' to 'Delivered'.
        return ucfirst((string) $order->status);
    }
}
