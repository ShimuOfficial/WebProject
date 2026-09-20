<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Reservation;

/**
 * DEFENSE: §5.1 public website pages
 * HomeController — home, about, menu, contact.
 */
class HomeController extends Controller
{
    // Render the website homepage with menu data.
    public function index()
    {
        return view('website.index', $this->buildWebsiteData());
    }

    // Render the about page.
    public function about()
    {
        return view('website.pages.about', $this->buildWebsiteData());
    }

    // Render the public menu page (sorted alphabetically).
    public function menu()
    {
        $data = $this->buildWebsiteData();
        $data['featuredItems'] = Menu::with(['menuIngredients.inventory'])
            ->orderBy('name', 'asc')
            ->get();
        $data['orderMenuItems'] = $data['featuredItems']->groupBy('category');

        return view('website.pages.menu', $data);
    }

    // Render the contact page.
    public function contact()
    {
        $upcomingReservations = collect();
        if (auth()->check() && auth()->user()->role === 'customer') {
            $upcomingReservations = Reservation::query()
                ->with('table')
                ->where('user_id', auth()->id())
                ->whereIn('status', ['pending', 'confirmed'])
                ->whereDate('reservation_date', '>=', now()->toDateString())
                ->orderBy('reservation_date')
                ->orderBy('time_slot')
                ->get();
        }

        return view('website.pages.contact', [
            'reservationSlots' => Reservation::TIME_SLOTS,
            'upcomingReservations' => $upcomingReservations,
        ]);
    }

    // Gather and prepare menu data used across website pages.
    private function buildWebsiteData(): array
    {
        $menus = Menu::with(['menuIngredients.inventory'])
            ->available()
            ->orderBy('category', 'asc')
            ->orderBy('name', 'asc')
            ->get();
        $featuredItems = $menus;
        $menuByCategory = $menus->groupBy('category');
        $orderMenuItems = $menus->groupBy('category');
        $menuMeta = $menus->mapWithKeys(function ($menu) {
            return [$menu->id => [
                'name' => $menu->name,
                'price' => (float) $menu->price,
                'ingredients' => $menu->menuIngredients->map(fn($ingredient) => [
                    'inventory_id' => $ingredient->inventory_id,
                ])->values(),
            ]];
        });

        return compact('featuredItems', 'menuByCategory', 'orderMenuItems', 'menuMeta');
    }
}
