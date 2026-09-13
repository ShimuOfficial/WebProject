<?php

namespace App\Helpers;

use App\Models\SiteSettings;

/** DEFENSE: Blade helpers for logo / site name (reads SiteSettings) */
class SiteHelper
{
    /**
     * Get site settings
     */
    public static function settings()
    {
        return SiteSettings::getInstance();
    }

    /**
     * Get a specific site setting
     */
    public static function get($key, $default = null)
    {
        return SiteSettings::get($key, $default);
    }

    /**
     * Get logo URL (used for website, admin panel, and login panel)
     */
    public static function logoUrl()
    {
        $logo = SiteSettings::get('logo');
        return $logo ? asset('storage/' . $logo) : null;
    }

    /**
     * Get website name
     */
    public static function siteName()
    {
        return SiteSettings::get('website_name', 'Restaurant Management System');
    }

    /**
     * Backward-compatible alias for older references.
     */
    public static function websiteName()
    {
        return self::siteName();
    }

    public static function tagline()
    {
        return SiteSettings::get('website_tagline', 'Fresh Food, Warm Service');
    }

    public static function phoneNumber()
    {
        return SiteSettings::get('phone_number', config('restaurant.phone', '+880 1700-000000'));
    }

    public static function phoneHref()
    {
        return preg_replace('/\s+/', '', self::phoneNumber());
    }

    public static function emailAddress()
    {
        return SiteSettings::get('email_address', config('restaurant.email', 'info@restaurantos.com'));
    }

    public static function address()
    {
        return SiteSettings::get('address', config('restaurant.address', '12 Lakeview Road, Dhaka'));
    }

    /**
     * Get primary color
     */
    public static function primaryColor()
    {
        return SiteSettings::get('primary_color', '#FF6B35');
    }

    /**
     * Get secondary color
     */
    public static function secondaryColor()
    {
        return SiteSettings::get('secondary_color', '#004E89');
    }

    /**
     * Get accent color
     */
    public static function accentColor()
    {
        return SiteSettings::get('accent_color', '#F7C59F');
    }
}
