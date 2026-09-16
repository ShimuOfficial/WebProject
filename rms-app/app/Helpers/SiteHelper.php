<?php

namespace App\Helpers;

use App\Models\SiteSettings;

/** DEFENSE: Blade helpers for logo / site name (reads SiteSettings) */
class SiteHelper
{
    public static function settings()
    {
        return SiteSettings::getInstance();
    }

    public static function get($key, $default = null)
    {
        return SiteSettings::get($key, $default);
    }

    public static function presentation(): array
    {
        return SiteSettings::presentation();
    }

    public static function logoUrl()
    {
        return self::presentation()['logo_url'] ?? null;
    }

    public static function iconUrl()
    {
        return self::presentation()['icon_url'] ?? asset('images/brand/restaurant-icon.svg');
    }

    public static function siteName()
    {
        return self::presentation()['name'] ?? 'Restaurant Management System';
    }

    public static function websiteName()
    {
        return self::siteName();
    }

    public static function tagline()
    {
        return self::presentation()['tagline'] ?? 'Fresh Food, Warm Service';
    }

    public static function phoneNumber()
    {
        return self::presentation()['phone'] ?? config('restaurant.phone', '+880 1700-000000');
    }

    public static function phoneHref()
    {
        return preg_replace('/\s+/', '', self::phoneNumber());
    }

    public static function emailAddress()
    {
        return self::presentation()['email'] ?? config('restaurant.email');
    }

    public static function address()
    {
        return self::presentation()['address'] ?? config('restaurant.address');
    }

    public static function primaryColor()
    {
        return self::presentation()['primary_color'] ?? '#FF6B35';
    }

    public static function secondaryColor()
    {
        return self::presentation()['secondary_color'] ?? '#004E89';
    }

    public static function accentColor()
    {
        return self::presentation()['accent_color'] ?? '#F7C59F';
    }
}
