<?php

namespace App\Providers;

use App\Models\Menu;
use App\Models\SiteSettings;
use App\Models\User;
use App\Models\Order;
use App\Models\Inventory;
use App\Models\MenuIngredient;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

/**
 * DEFENSE: boot — share $site / cart badge / notification dot with Blade
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();
        $this->composeSharedViewData();

        try {
            if (! $this->app->environment('local') || ! Schema::hasTable('users')) {
                return;
            }
        } catch (\Throwable $e) {
            // Database not available yet (tests/CI) - skip seed-on-boot.
            return;
        }

        User::updateOrCreate(
            ['email' => 'admin@restaurant.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '555-0100',
                'is_active' => true,
            ]
        )->syncLegacyRole();
    }

    private function composeSharedViewData(): void
    {
        View::composer(['website.*', 'admin.auth.login', 'layouts.partials.sidebar', 'admin.partials.sidebar'], function ($view) {
            $settings = SiteSettings::getInstance();
            $siteName = $settings?->website_name ?: 'Restaurant Management System';
            $tagline = $settings?->website_tagline ?: 'Fresh Food, Warm Service';
            $phone = $settings?->phone_number ?: config('restaurant.phone', '+880 1700-000000');
            $email = $settings?->email_address ?: config('restaurant.email', 'info@restaurantos.com');
            $address = $settings?->address ?: config('restaurant.address', '12 Lakeview Road, Dhaka');

            $view->with('site', [
                'name' => $siteName,
                'tagline' => $tagline,
                'logo_url' => $settings?->logo ? asset('storage/' . ltrim($settings->logo, '/')) : null,
                'primary_color' => $settings?->primary_color ?: '#FF6B35',
                'secondary_color' => $settings?->secondary_color ?: '#004E89',
                'accent_color' => $settings?->accent_color ?: '#F7C59F',
                'phone' => $phone,
                'phone_href' => preg_replace('/\s+/', '', $phone),
                'email' => $email,
                'address' => $address,
                'hours' => config('restaurant.hours', []),
            ]);

            $heroSettings = $settings ?: (object) [
                'hero_badge' => 'Open Today',
                'hero_title' => 'Fresh Flavors,',
                'hero_accent' => 'Fired Daily.',
                'hero_subtitle' => 'Bold dishes crafted from seasonal ingredients. Order online, track your meal, and enjoy a warm dining experience every visit.',
                'hero_background_image' => null,
            ];

            $view->with('heroSettings', $heroSettings);
            $view->with('aboutPage', [
                'title' => data_get(config('restaurant.about'), 'title', 'Seasonal dishes, warm hospitality.'),
                'text' => $settings?->about_us ?: data_get(config('restaurant.about'), 'text', 'We focus on fresh ingredients, calm service, and a menu that changes with the market.'),
                'points' => data_get(config('restaurant.about'), 'points', []),
                'image_url' => $settings?->about_image ? asset('storage/' . ltrim($settings->about_image, '/')) : null,
            ]);

            $view->with('websiteAssets', [
                'vite_ready' => Vite::isRunningHot() || file_exists(public_path('build/manifest.json')),
                'inline_css' => (Vite::isRunningHot() || file_exists(public_path('build/manifest.json')))
                    ? null
                    : @file_get_contents(resource_path('css/app.css')),
            ]);

            $view->with('cartSummary', $this->buildCartSummary());
            // Compute whether there are new notifications since user's last seen timestamp
            $lastSeen = session('notifications_last_seen_at', 0);
            $hasNew = false;
            try {
                $pendingLatest = Order::whereIn('status', ['pending', 'preparing'])->max('updated_at');
                if ($pendingLatest && strtotime($pendingLatest) > $lastSeen) {
                    $hasNew = true;
                }

                if (! $hasNew) {
                    $readyLatest = Order::where('status', 'ready')->max('updated_at');
                    if ($readyLatest && strtotime($readyLatest) > $lastSeen) {
                        $hasNew = true;
                    }
                }

                if (! $hasNew && Schema::hasColumn('orders', 'payment_status')) {
                    $unpaidLatest = Order::whereIn('payment_status', ['unpaid', 'partial'])->max('updated_at');
                    if ($unpaidLatest && strtotime($unpaidLatest) > $lastSeen) {
                        $hasNew = true;
                    }
                }

                if (! $hasNew && Schema::hasTable('inventories')) {
                    $lowStockLatest = Inventory::whereColumn('quantity', '<=', 'min_quantity')->max('updated_at');
                    if ($lowStockLatest && strtotime($lowStockLatest) > $lastSeen) {
                        $hasNew = true;
                    }
                }

                if (! $hasNew && Schema::hasTable('inventories')) {
                    $outStockLatest = Inventory::where('quantity', '<=', 0)->max('updated_at');
                    if ($outStockLatest && strtotime($outStockLatest) > $lastSeen) {
                        $hasNew = true;
                    }
                }
            } catch (\Throwable $e) {
                // best-effort: if DB inaccessible, assume no new notifications
                $hasNew = false;
            }

            $view->with('hasNewNotifications', $hasNew);
            $view->with('currentUserRole', auth()->user()->role ?? '');
        });
    }

    private function buildCartSummary(): array
    {
        if (! auth()->check() || auth()->user()->role !== 'customer') {
            return ['count' => 0, 'total' => 0.0];
        }

        $cartItems = session('cart.items', []);
        $cartMenuIds = array_keys($cartItems);

        if (empty($cartMenuIds)) {
            return ['count' => 0, 'total' => 0.0];
        }

        $cartMenus = Menu::whereIn('id', $cartMenuIds)->get();
        $cartCount = 0;
        $cartTotal = 0.0;

        foreach ($cartMenus as $menu) {
            $qty = (int) ($cartItems[$menu->id] ?? 0);
            $cartCount += $qty;
            $cartTotal += ((float) $menu->price) * $qty;
        }

        return ['count' => $cartCount, 'total' => $cartTotal];
    }
}
