<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Website\HomeController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Website\CustomerAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Website\CustomerOrderController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\TableController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\KitchenController;
use App\Http\Controllers\Admin\SiteSettingsController;
use App\Http\Controllers\Website\CustomerCartController;
use App\Http\Controllers\Website\ReservationController;
use App\Http\Controllers\Admin\ReservationController as AdminReservationController;
use App\Http\Controllers\SslCommerzPaymentController;

Route::get('/', [HomeController::class, 'index'])->name('website.home');
Route::get('/about', [HomeController::class, 'about'])->name('website.about');
Route::get('/our-menu', [HomeController::class, 'menu'])->name('website.menu');


// Auth Routes
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::post('/reservations', [ReservationController::class, 'store'])->name('reservations.store');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Customer Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/customer/register', [CustomerAuthController::class, 'showRegister'])->name('customer.register');
    Route::post('/customer/register', [CustomerAuthController::class, 'register'])->name('customer.register.store');
    Route::get('/customer/login', [CustomerAuthController::class, 'showLogin'])->name('customer.login');
    Route::post('/customer/login', [CustomerAuthController::class, 'login'])->name('customer.login.store');
});

// bKash callback route removed.

Route::middleware(['auth', 'role:customer'])->group(function () {
    Route::get('/customer/account', [CustomerAuthController::class, 'account'])->name('customer.account');
    Route::put('/customer/account', [CustomerAuthController::class, 'updateProfile'])->name('customer.account.update');
    Route::get('/customer/cart', [CustomerCartController::class, 'show'])->name('customer.cart');
    Route::post('/customer/cart/add', [CustomerCartController::class, 'add'])->name('customer.cart.add');
    Route::patch('/customer/cart/{menu}', [CustomerCartController::class, 'update'])->name('customer.cart.update');
    Route::delete('/customer/cart/{menu}', [CustomerCartController::class, 'remove'])->name('customer.cart.remove');
    Route::delete('/customer/cart', [CustomerCartController::class, 'clear'])->name('customer.cart.clear');
    Route::post('/customer/orders', [CustomerOrderController::class, 'store'])->name('customer.orders.store');
    // Online payment route disabled for demo (gateway removed).
    // Route::post('/customer/orders/{order}/pay-online', [CustomerOrderController::class, 'payOnline'])->name('customer.orders.pay-online');
    Route::get('/customer/orders', [CustomerOrderController::class, 'index'])->name('customer.orders');
    Route::post('/customer/orders/{order}/cancel', [CustomerOrderController::class, 'cancel'])->name('customer.orders.cancel');
});

// Protected Routes
Route::middleware(['auth', 'role:manager,chef,cashier'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/data', [DashboardController::class, 'data'])->name('dashboard.data');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::resource('orders', OrderController::class)->except(['show'])->middleware('role:manager,cashier');
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->middleware('role:manager,cashier')->name('orders.cancel');
    Route::patch('/orders/{order}/approve-customer', [OrderController::class, 'approveCustomerOrder'])
        ->middleware('role:manager,admin')
        ->name('orders.approve-customer');
    Route::patch('/orders/{order}/payment', [OrderController::class, 'recordPayment'])->middleware('role:manager,cashier')->name('orders.payment');
    Route::post('/orders/{order}/payment/fast', [OrderController::class, 'fastPay'])->middleware('role:manager,cashier')->name('orders.payment.fast');
    // Online payment removed - May 14, 2026 - use manual payment only
    Route::post('/orders/{order}/pay', [OrderController::class, 'pay'])->middleware('role:manager,cashier')->name('orders.pay');
    Route::get('/orders/{order}/receipt', [OrderController::class, 'receipt'])->middleware('role:manager,cashier')->name('orders.receipt');
    // Invoice PDF removed - May 14, 2026 (use receipt instead)

    // Menu: Chef can view + toggle availability only
    Route::get('/menu', [MenuController::class, 'index'])->middleware('role:manager,chef')->name('menu.index');
    Route::patch('/menu/{menu}/availability', [MenuController::class, 'toggleAvailability'])->middleware('role:chef,manager')->name('menu.availability');
    Route::resource('menu', MenuController::class)->except(['index'])->middleware('role:manager');

    // Kitchen Display
    Route::get('/kitchen', [KitchenController::class, 'index'])->middleware('role:chef')->name('kitchen.index');
    Route::patch('/kitchen/{order}', [KitchenController::class, 'updateStatus'])->middleware('role:chef')->name('kitchen.update');

    Route::resource('tables', TableController::class)->except(['create', 'show', 'edit'])->middleware('role:manager');
    Route::patch('/tables/{table}/status', [TableController::class, 'updateStatus'])->middleware('role:manager')->name('tables.status');
    Route::get('/reservations', [AdminReservationController::class, 'index'])->middleware('role:manager,admin')->name('reservations.index');
    Route::patch('/reservations/{reservation}', [AdminReservationController::class, 'update'])->middleware('role:manager,admin')->name('reservations.update');

    // Inventory: Chef can view only
    Route::get('/inventory', [InventoryController::class, 'index'])->middleware('role:manager,admin,chef')->name('inventory.index');

    Route::middleware('role:manager,admin')->group(function () {
        Route::resource('inventory', InventoryController::class)->except(['create', 'show', 'edit', 'index']);
        Route::resource('customers', CustomerController::class)->only(['index', 'destroy']);
        Route::patch('/customers/{customer}/status', [CustomerController::class, 'toggleStatus'])
            ->name('customers.status');
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/data', [ReportController::class, 'data'])->name('reports.data');
    });

    Route::middleware('role:admin')->group(function () {
        Route::resource('staff', StaffController::class)->except(['create', 'show', 'edit']);

        // Site Settings
        Route::get('/admin/site-settings', [SiteSettingsController::class, 'index'])->name('admin.site-settings.index');
        Route::put('/admin/site-settings', [SiteSettingsController::class, 'update'])->name('admin.site-settings.update');
    });
    
});

    // bKash routes removed — online tokenized checkout is disabled in this deployment.

// bKash demo routes removed.

    // SSLCommerz demo routes (sandbox/testing only)
    Route::get('/sslcommerz/example1', [SslCommerzPaymentController::class, 'exampleEasyCheckout'])->name('sslcommerz.example1');
    Route::get('/sslcommerz/example2', [SslCommerzPaymentController::class, 'exampleHostedCheckout'])->name('sslcommerz.example2');
    Route::post('/sslcommerz/pay', [SslCommerzPaymentController::class, 'index'])->name('sslcommerz.pay');
    Route::post('/sslcommerz/pay-via-ajax', [SslCommerzPaymentController::class, 'payViaAjax'])->name('sslcommerz.pay-via-ajax');
    Route::post('/sslcommerz/success', [SslCommerzPaymentController::class, 'success'])->name('sslcommerz.success');
    Route::post('/sslcommerz/fail', [SslCommerzPaymentController::class, 'fail'])->name('sslcommerz.fail');
    Route::post('/sslcommerz/cancel', [SslCommerzPaymentController::class, 'cancel'])->name('sslcommerz.cancel');
    Route::post('/sslcommerz/ipn', [SslCommerzPaymentController::class, 'ipn'])->name('sslcommerz.ipn');

    // Simple test success page for local development
    Route::get('/success', [SslCommerzPaymentController::class, 'showTestSuccess'])
        ->name('sslcommerz.test.success');

    // Accept POST from gateway to /success (some gateway redirects POST to root /success)
    Route::post('/success', [SslCommerzPaymentController::class, 'success']);

    // Simulate gateway success POST for local testing (form on /success posts here)
    Route::post('/sslcommerz/simulate-success', [SslCommerzPaymentController::class, 'simulateSuccess'])
        ->name('sslcommerz.simulate_success');

