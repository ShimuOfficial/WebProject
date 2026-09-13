{{-- DEFENSE: §5.14 sales reports --}}
@extends('layouts.app')
@section('title', 'Reports')
@section('subtitle', 'Sales analytics and performance metrics')

@section('content')

    <!-- Date Range Filter -->
    <div class="card mb-4 fade-in">
        <div class="card-body">
            <form class="row g-3 align-items-end" method="GET">
                <div class="col-md-3">
                    <label class="form-label fw-semibold mb-1" style="font-size:12px">Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold mb-1" style="font-size:12px">End Date</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                </div>
                <div class="col-md-6 d-flex gap-2 flex-wrap">
                    <button class="btn btn-primary"><i class="fas fa-filter me-1"></i>Apply</button>
                    <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">Reset</a>
                    <button type="button" class="btn btn-outline-dark" onclick="window.print()"><i
                            class="fas fa-print me-1"></i>Print</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Stats -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-sm-6 fade-in">
            <div class="stat-card primary">
                <div class="stat-icon"><i class="fas fa-money-bill-wave text-success"></i></div>
                <div class="stat-value" id="totalRevenueValue">৳{{ number_format($totalRevenue, 2) }}</div>
                <div class="stat-label">Total Revenue</div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 fade-in fade-in-delay-1">
            <div class="stat-card success">
                <div class="stat-icon"><i class="fas fa-receipt"></i></div>
                <div class="stat-value" id="totalOrdersValue">{{ $totalOrders }}</div>
                <div class="stat-label">Total Orders</div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 fade-in fade-in-delay-2">
            <div class="stat-card info">
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                <div class="stat-value" id="completedOrdersValue">{{ $completedOrders }}</div>
                <div class="stat-label">Completed</div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 fade-in fade-in-delay-3">
            <div class="stat-card danger">
                <div class="stat-icon"><i class="fas fa-times-circle"></i></div>
                <div class="stat-value" id="cancelledOrdersValue">{{ $cancelledOrders }}</div>
                <div class="stat-label">Cancelled</div>
            </div>
        </div>
    </div>

    <!-- Most Popular Dish -->
    <div class="card mb-4 fade-in fade-in-delay-1">
        <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <div class="text-muted" style="font-size:12px">Most Popular Dish</div>
                <div class="fw-bold" style="font-size:22px">
                    {{ $mostPopularDish?->name ?? 'No sales data in selected range' }}
                </div>
                <div class="text-muted" style="font-size:13px">
                    Ranked from sold quantity in the selected period
                </div>
            </div>
            <div class="d-flex gap-4 flex-wrap">
                <div class="text-center">
                    <div class="text-muted" style="font-size:12px">Qty Sold</div>
                    <div style="font-size:20px;font-weight:700;color:var(--primary)">
                        {{ $mostPopularDish?->total_quantity ?? 0 }}</div>
                </div>
                <div class="text-center">
                    <div class="text-muted" style="font-size:12px">Revenue</div>
                    <div style="font-size:20px;font-weight:700;color:var(--success)">
                        ৳{{ number_format($mostPopularDish?->total_revenue ?? 0, 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-7 fade-in">
            <div class="card h-100">
                <div class="card-header"><i class="fas fa-chart-line me-2" style="color:var(--primary)"></i>Daily Revenue
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <div class="col-xl-5 fade-in fade-in-delay-1">
            <div class="card h-100">
                <div class="card-header"><i class="fas fa-chart-pie me-2" style="color:var(--secondary)"></i>Revenue by
                    Category</div>
                <div class="card-body">
                    <canvas id="categoryChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-5 fade-in fade-in-delay-2">
            <!-- Top Items Table -->
            <div class="card h-100">
                <div class="card-header"><i class="fas fa-trophy me-2" style="color:var(--accent)"></i>Top Selling Items
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Item</th>
                                    <th>Qty Sold</th>
                                    <th>Revenue</th>
                                </tr>
                            </thead>
                            <tbody id="topItemsBody">
                                @forelse($topItems as $i => $item)
                                    <tr>
                                        <td>
                                            @if ($i < 3)
                                                <span class="badge"
                                                    style="background:{{ ['#f59e0b', '#94a3b8', '#cd7f32'][$i] }};width:28px;height:28px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:12px">{{ $i + 1 }}</span>
                                            @else
                                                <span class="text-muted">{{ $i + 1 }}</span>
                                            @endif
                                        </td>
                                        <td><strong>{{ $item->name }}</strong></td>
                                        <td>{{ $item->total_quantity }}</td>
                                        <td><strong>৳{{ number_format($item->total_revenue, 2) }}</strong></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">No data for this period
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-7 fade-in fade-in-delay-2">
            <!-- Orders (printable) -->
            <div class="card h-100">
                <div class="card-header"><i class="fas fa-receipt me-2" style="color:var(--primary)"></i>Orders</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Order #</th>
                                    <th>Date</th>
                                    <th>Customer/Table</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>

                            </thead>
                            <tbody id="ordersBody">
                                @forelse($orders as $i => $order)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td><strong>{{ $order->order_number }}</strong></td>
                                        <td>{{ \Carbon\Carbon::parse($order->created_at)->format('Y-m-d H:i') }}</td>
                                        <td>{{ $order->table?->table_number ?? ($order->user?->name ?? '-') }}</td>
                                        <td>৳{{ number_format($order->total_amount, 2) }}</td>
                                        <td>{{ $order->display_status }}</td>
                                        <td>
                                            <a href="{{ route('orders.receipt', ['order' => $order, 'return' => 'reports']) }}"
                                                class="btn btn-sm btn-outline-dark">
                                                <i class="fas fa-print me-1"></i> Print
                                            </a>
                                            @if (!in_array($order->status, ['completed', 'cancelled']) && !$order->inventory_deducted_at)
                                                <form method="POST" action="{{ route('orders.cancel', $order) }}"
                                                    style="display:inline-block;margin-left:6px"
                                                    onsubmit="return confirm('Are you sure you want to cancel this order?');">
                                                    @csrf
                                                    <button class="btn btn-sm btn-outline-danger"><i
                                                            class="fas fa-times me-1"></i> Cancel</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">No orders found for this
                                            period</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-between align-items-center">
                    <div>
                        <button id="ordersPrev" class="btn btn-sm btn-outline-secondary me-2" disabled>Prev</button>
                        <button id="ordersNext" class="btn btn-sm btn-outline-secondary">Next</button>
                    </div>
                    <div>
                        <button id="ordersToggleSize" class="btn btn-sm btn-outline-dark">Show more</button>
                        <span id="ordersPagerInfo" class="ms-3 text-muted"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            const revenueCanvas = document.getElementById('revenueChart');
            const categoryCanvas = document.getElementById('categoryChart');

            if (revenueCanvas) {
                const revCtx = revenueCanvas.getContext('2d');
                window.reportRevenueChart = new Chart(revCtx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($dailyRevenue->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('M d'))) !!},
                        datasets: [{
                            label: 'Revenue (৳)',
                            data: {!! json_encode($dailyRevenue->pluck('revenue')) !!},
                            borderColor: '#6366f1',
                            backgroundColor: 'rgba(99,102,241,0.1)',
                            fill: true,
                            tension: 0.35,
                            borderWidth: 2,
                            pointRadius: 3,
                            pointBackgroundColor: '#6366f1'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0,0,0,0.04)'
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }

            if (categoryCanvas) {
                const catCtx = categoryCanvas.getContext('2d');
                window.reportCategoryChart = new Chart(catCtx, {
                    type: 'doughnut',
                    data: {
                        labels: {!! json_encode($categoryRevenue->pluck('category')) !!},
                        datasets: [{
                            data: {!! json_encode($categoryRevenue->pluck('total_revenue')) !!},
                            backgroundColor: ['#6366f1', '#8b5cf6', '#06b6d4', '#10b981', '#f59e0b', '#ef4444',
                                '#ec4899'
                            ],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 16,
                                    usePointStyle: true
                                }
                            }
                        },
                        cutout: '65%'
                    }
                });
            }
        </script>

        <script>
            // Client-side pagination for Orders table in reports view
            document.addEventListener('DOMContentLoaded', function() {
                const pageSizeDefault = 5;
                let pageSize = pageSizeDefault;
                let currentPage = 1;

                const tbody = document.getElementById('ordersBody');
                if (!tbody) return;
                const rows = Array.from(tbody.querySelectorAll('tr'));
                const totalRows = rows.length;

                const prevBtn = document.getElementById('ordersPrev');
                const nextBtn = document.getElementById('ordersNext');
                const toggleBtn = document.getElementById('ordersToggleSize');
                const info = document.getElementById('ordersPagerInfo');

                function render() {
                    const start = (currentPage - 1) * pageSize;
                    const end = start + pageSize;

                    rows.forEach((r, i) => {
                        if (i >= start && i < end) r.style.display = '';
                        else r.style.display = 'none';
                    });

                    const totalPages = Math.max(1, Math.ceil(totalRows / pageSize));
                    prevBtn.disabled = currentPage <= 1;
                    nextBtn.disabled = currentPage >= totalPages;
                    info.textContent = `${Math.min(end, totalRows)} of ${totalRows}`;
                    if (pageSize > pageSizeDefault) toggleBtn.textContent = 'Show less';
                    else toggleBtn.textContent = 'Show more';
                }

                prevBtn.addEventListener('click', () => {
                    if (currentPage > 1) currentPage--;
                    render();
                });

                nextBtn.addEventListener('click', () => {
                    const totalPages = Math.max(1, Math.ceil(totalRows / pageSize));
                    if (currentPage < totalPages) currentPage++;
                    render();
                });

                toggleBtn.addEventListener('click', () => {
                    if (pageSize > pageSizeDefault) {
                        pageSize = pageSizeDefault;
                        currentPage = 1;
                    } else {
                        pageSize = Math.max(50, totalRows); // show all
                        currentPage = 1;
                    }
                    render();
                });

                // initialize
                render();
            });
        </script>
    @endpush
@endsection
