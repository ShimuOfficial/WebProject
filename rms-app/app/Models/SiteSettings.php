<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

/** DEFENSE: §5.16 singleton branding row (id = 1) */
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
        'hours_json',
        'about_us',
        'about_title',
        'content_json',
        'facebook_url',
        'instagram_url',
        'guest_rating',
    ];

    protected $casts = [
        'hours_json' => 'array',
        'content_json' => 'array',
    ];

    public static function getInstance()
    {
        try {
            $defaults = [
                'website_name' => config('restaurant.name', 'Restaurant Management System'),
                'website_tagline' => config('restaurant.tagline', 'Fresh Food, Warm Service'),
                'hero_badge' => config('restaurant.hero_badge', 'Open Today'),
                'hero_title' => config('restaurant.hero_title', 'Fresh Flavors,'),
                'hero_accent' => config('restaurant.hero_accent', 'Fired Daily.'),
                'hero_subtitle' => config('restaurant.hero_subtitle'),
                'primary_color' => '#FF6B35',
                'secondary_color' => '#004E89',
                'accent_color' => '#F7C59F',
                'phone_number' => config('restaurant.phone'),
                'email_address' => config('restaurant.email'),
                'address' => config('restaurant.address'),
                'about_us' => data_get(config('restaurant.about'), 'text'),
            ];

            if (Schema::hasColumn('site_settings', 'about_title')) {
                $defaults['about_title'] = data_get(config('restaurant.about'), 'title');
            }
            if (Schema::hasColumn('site_settings', 'hours_json')) {
                $defaults['hours_json'] = config('restaurant.hours');
            }
            if (Schema::hasColumn('site_settings', 'content_json')) {
                $defaults['content_json'] = config('restaurant.content');
            }
            if (Schema::hasColumn('site_settings', 'guest_rating')) {
                $defaults['guest_rating'] = config('restaurant.guest_rating', '4.8');
            }

            return self::firstOrCreate(['id' => 1], $defaults);
        } catch (\Exception $e) {
            return null;
        }
    }

    public static function get($key, $default = null)
    {
        try {
            $settings = self::getInstance();
            return $settings ? ($settings->getAttribute($key) ?? $default) : $default;
        } catch (\Exception $e) {
            return $default;
        }
    }

    public static function set($key, $value)
    {
        $settings = self::getInstance();
        $settings->setAttribute($key, $value);
        $settings->save();
        return $settings;
    }

    public function content(string $key = null, $default = null)
    {
        $stored = is_array($this->content_json) ? $this->content_json : [];
        $merged = array_replace_recursive(config('restaurant.content', []), $stored);

        return $key === null ? $merged : data_get($merged, $key, $default);
    }

    public function hoursList(): array
    {
        $hours = is_array($this->hours_json) ? $this->hours_json : [];

        return ! empty($hours) ? $hours : config('restaurant.hours', []);
    }

    public function socialLinks(): array
    {
        return array_values(array_filter([
            ['label' => 'Facebook', 'url' => $this->facebook_url],
            ['label' => 'Instagram', 'url' => $this->instagram_url],
        ], fn ($link) => filled($link['url'] ?? null)));
    }

    public static function publicAsset(string $relative): string
    {
        return asset(ltrim($relative, '/'));
    }

    public function storedImageUrl(?string $path, string $fallbackKey): string
    {
        if (filled($path)) {
            return asset('storage/' . ltrim($path, '/'));
        }

        return self::publicAsset(config('restaurant.images.' . $fallbackKey, 'images/brand/hero.jpg'));
    }

    public static function presentation(?self $settings = null): array
    {
        $settings ??= self::getInstance();
        $content = $settings?->content() ?? config('restaurant.content', []);
        $phone = $settings?->phone_number ?: config('restaurant.phone', '+880 1700-000000');
        $icon = self::publicAsset(config('restaurant.images.icon', 'images/brand/restaurant-icon.svg'));
        $logo = filled($settings?->logo)
            ? asset('storage/' . ltrim($settings->logo, '/'))
            : $icon;

        return [
            'name' => $settings?->website_name ?: config('restaurant.name', 'Your Restaurant'),
            'tagline' => $settings?->website_tagline ?: config('restaurant.tagline', 'Fresh food, warm service'),
            'logo_url' => $logo,
            'icon_url' => $icon,
            'favicon_url' => $icon,
            'primary_color' => $settings?->primary_color ?: '#FF6B35',
            'secondary_color' => $settings?->secondary_color ?: '#004E89',
            'accent_color' => $settings?->accent_color ?: '#F7C59F',
            'phone' => $phone,
            'phone_href' => preg_replace('/\s+/', '', $phone),
            'email' => $settings?->email_address ?: config('restaurant.email'),
            'address' => $settings?->address ?: config('restaurant.address'),
            'hours' => $settings?->hoursList() ?? config('restaurant.hours', []),
            'opening_hours' => $settings?->opening_hours,
            'guest_rating' => $settings?->guest_rating ?: config('restaurant.guest_rating', '4.8'),
            'facebook_url' => $settings?->facebook_url,
            'instagram_url' => $settings?->instagram_url,
            'social' => $settings?->socialLinks() ?? [],
            'hero_image_url' => $settings
                ? $settings->storedImageUrl($settings->hero_background_image, 'hero')
                : self::publicAsset(config('restaurant.images.hero')),
            'about_image_url' => $settings
                ? $settings->storedImageUrl($settings->about_image, 'about')
                : self::publicAsset(config('restaurant.images.about')),
            'login_image_url' => self::publicAsset(config('restaurant.images.login')),
            'staff_image_url' => self::publicAsset(config('restaurant.images.staff')),
            'contact_image_url' => self::publicAsset(config('restaurant.images.contact')),
            'dish_fallback_url' => self::publicAsset(config('restaurant.images.dish_fallback')),
            'content' => $content,
        ];
    }

    public static function hasCmsColumns(): bool
    {
        try {
            return Schema::hasColumn('site_settings', 'content_json');
        } catch (\Throwable $e) {
            return false;
        }
    }
}
