<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSettings;
use Illuminate\Http\Request;

/**
 * DEFENSE: §5.16 website name, colors, hero, about, logo
 */
class SiteSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    /**
     * Show the site settings form
     */
    // Display form with current site settings.
    public function index()
    {
        $settings = SiteSettings::getInstance();
        return view('admin.site-settings.index', compact('settings'));
    }

    /**
     * Update site settings
     */
    // Validate and persist updated settings including image uploads.
    public function update(Request $request)
    {
        $validated = $request->validate([
            'website_name' => 'required|string|max:255',
            'website_tagline' => 'nullable|string|max:255',
            'hero_badge' => 'required|string|max:255',
            'hero_title' => 'required|string|max:255',
            'hero_accent' => 'required|string|max:255',
            'hero_subtitle' => 'required|string',
            'primary_color' => 'required|string|regex:/^#[0-9A-F]{6}$/i',
            'secondary_color' => 'required|string|regex:/^#[0-9A-F]{6}$/i',
            'accent_color' => 'required|string|regex:/^#[0-9A-F]{6}$/i',
            'phone_number' => 'nullable|string|max:20',
            'email_address' => 'nullable|email',
            'address' => 'nullable|string|max:500',
            'opening_hours' => 'nullable|string|max:500',
            'about_us' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'hero_background_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'about_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $settings = SiteSettings::getInstance();

        // Handle single logo upload (used for website, admin panel, and login panel)
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('images/logos', 'public');
            $validated['logo'] = $logoPath;
        }

        // Handle hero background image upload
        if ($request->hasFile('hero_background_image')) {
            $heroBgPath = $request->file('hero_background_image')->store('images/hero', 'public');
            $validated['hero_background_image'] = $heroBgPath;
        }

        if ($request->hasFile('about_image')) {
            $aboutImagePath = $request->file('about_image')->store('images/about', 'public');
            $validated['about_image'] = $aboutImagePath;
        }

        $settings->update($validated);

        return redirect()->back()->with('success', 'Site settings updated successfully!');
    }
}
