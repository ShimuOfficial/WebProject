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
        View::composer(['website.*', 'admin.*', 'layouts.*'], function ($view) {
            $settings = SiteSettings::getInstance();
            $site = SiteSettings::presentation($settings);

            $view->with('site', $site);

            $heroSettings = $settings ?: (object) [
                'hero_badge' => config('restaurant.hero_badge'),
                'hero_title' => config('restaurant.hero_title'),
                'hero_accent' => config('restaurant.hero_accent'),
                'hero_subtitle' => config('restaurant.hero_subtitle'),
                'hero_background_image' => null,
            ];

            $view->with('heroSettings', $heroSettings);
            $view->with('aboutPage', [
                'title' => $settings?->about_title ?: data_get(config('restaurant.about'), 'title'),
                'text' => $settings?->about_us ?: data_get(config('restaurant.about'), 'text'),
                'points' => data_get($site['content'], 'about_points', data_get(config('restaurant.about'), 'points', [])),
                'paragraphs' => data_get($site['content'], 'about_paragraphs', []),
                'image_url' => $site['about_image_url'],
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
