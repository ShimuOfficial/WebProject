<?php

return [
    'name' => env('RESTAURANT_NAME', 'RestaurantOS'),
    'tagline' => env('RESTAURANT_TAGLINE', 'Fresh Food, Warm Service'),
    'hero_badge' => env('RESTAURANT_HERO_BADGE', 'Open Today - 11AM - 11PM'),
    'hero_title' => env('RESTAURANT_HERO_TITLE', 'Fresh Flavors,'),
    'hero_accent' => env('RESTAURANT_HERO_ACCENT', 'Fired Daily.'),
    'hero_subtitle' => env('RESTAURANT_HERO_SUBTITLE', 'Bold dishes crafted from seasonal ingredients. Order online, track your meal, and enjoy a warm dining experience every visit.'),
    'phone' => env('RESTAURANT_PHONE', '+880 1700-000000'),
    'email' => env('RESTAURANT_EMAIL', 'info@restaurantos.com'),
    'address' => env('RESTAURANT_ADDRESS', '12 Lakeview Road, Dhaka'),
    'hours' => [
        ['label' => 'Mon - Fri', 'time' => '11AM - 11PM'],
        ['label' => 'Sat - Sun', 'time' => '10AM - 12AM'],
    ],
    'about' => [
        'title' => 'Seasonal dishes, warm hospitality.',
        'text' => 'We focus on fresh ingredients, calm service, and a menu that changes with the market. Every plate is prepared to feel familiar, yet exciting.',
        'points' => [
            ['title' => 'Local sourcing', 'text' => 'Seasonal produce and trusted suppliers.'],
            ['title' => 'Open kitchen', 'text' => 'Transparent cooking and fast prep.'],
            ['title' => 'Flexible dining', 'text' => 'Dine-in, pickup, or delivery.'],
        ],
    ],
    'social' => [
        ['label' => 'Facebook', 'url' => 'https://facebook.com'],
        ['label' => 'Instagram', 'url' => 'https://instagram.com'],
    ],
];
