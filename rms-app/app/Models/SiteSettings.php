<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSettings extends Model
{
    protected $table = 'site_settings';

    protected $fillable = [
        'website_name',
        'website_tagline',
        'logo',
        'hero_badge',
        'hero_title',
        'hero_accent',
        'hero_subtitle',
        'hero_background_image',
        'about_image',
        'primary_color',
        'secondary_color',
        'accent_color',
        'phone_number',
        'email_address',
        'address',
        'opening_hours',
        'about_us',
    ];

    /**
     * Get or create the single settings record
     */
    public static function getInstance()
    {
        try {
            return self::firstOrCreate(
                ['id' => 1],
                [
                    'website_name' => 'Restaurant Management System',
                    'hero_badge' => 'Open Today',
                    'hero_title' => 'Fresh Flavors,',
                    'hero_accent' => 'Fired Daily.',
                    'hero_subtitle' => 'Bold dishes crafted from seasonal ingredients. Order online, track your meal, and enjoy a warm dining experience every visit.',
                    'primary_color' => '#FF6B35',
                    'secondary_color' => '#004E89',
                    'accent_color' => '#F7C59F',
                ]
            );
        } catch (\Exception $e) {
            // Table might not exist yet (during migration)
            return null;
        }
    }

    /**
     * Get a setting value
     */
    public static function get($key, $default = null)
    {
        try {
            $settings = self::getInstance();
            return $settings ? ($settings->getAttribute($key) ?? $default) : $default;
        } catch (\Exception $e) {
            return $default;
        }
    }

    /**
     * Set a setting value
     */
    public static function set($key, $value)
    {
        $settings = self::getInstance();
        $settings->setAttribute($key, $value);
        $settings->save();
        return $settings;
    }
}
