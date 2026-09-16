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

    public function index()
    {
        $settings = SiteSettings::getInstance();
        $content = $settings->content();
        $hours = $settings->hoursList();

        return view('admin.site-settings.index', compact('settings', 'content', 'hours'));
    }

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
            'about_title' => 'nullable|string|max:255',
            'about_us' => 'nullable|string',
            'guest_rating' => 'nullable|string|max:10',
            'facebook_url' => 'nullable|string|max:255',
            'instagram_url' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'hero_background_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'about_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'hours_label' => 'nullable|array',
            'hours_time' => 'nullable|array',
        ]);

        $settings = SiteSettings::getInstance();

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('images/logos', 'public');
        }

        if ($request->hasFile('hero_background_image')) {
            $validated['hero_background_image'] = $request->file('hero_background_image')->store('images/hero', 'public');
        }

        if ($request->hasFile('about_image')) {
            $validated['about_image'] = $request->file('about_image')->store('images/about', 'public');
        }

        $hours = [];
        foreach ($request->input('hours_label', []) as $index => $label) {
            $time = $request->input('hours_time.' . $index);
            if (filled($label) || filled($time)) {
                $hours[] = [
                    'label' => trim((string) $label),
                    'time' => trim((string) $time),
                ];
            }
        }
        $validated['hours_json'] = $hours;
        $validated['opening_hours'] = collect($hours)
            ->map(fn ($row) => trim(($row['label'] ?? '') . ' ' . ($row['time'] ?? '')))
            ->filter()
            ->implode(' · ');

        $validated['content_json'] = $this->contentFromRequest($request, $settings->content());

        unset($validated['hours_label'], $validated['hours_time']);

        $settings->update($validated);

        return redirect()->back()->with('success', 'Site settings updated successfully!');
    }

    private function contentFromRequest(Request $request, array $current): array
    {
        $pairs = function (string $titleKey, string $textKey): array {
            $titles = request()->input($titleKey, []);
            $texts = request()->input($textKey, []);
            $rows = [];
            foreach ($titles as $index => $title) {
                $text = $texts[$index] ?? '';
                if (filled($title) || filled($text)) {
                    $rows[] = ['title' => trim((string) $title), 'text' => trim((string) $text)];
                }
            }
            return $rows;
        };

        $process = [];
        foreach ($request->input('process_title', []) as $index => $title) {
            $process[] = [
                'step' => $request->input('process_step.' . $index, str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)),
                'title' => trim((string) $title),
                'text' => trim((string) $request->input('process_text.' . $index)),
            ];
        }

        $paragraphs = preg_split('/\R{2,}/', (string) $request->input('about_paragraphs', '')) ?: [];
        $refundItems = $pairs('refund_title', 'refund_text');

        return array_replace_recursive($current, [
            'footer_blurb' => $request->input('footer_blurb', $current['footer_blurb'] ?? ''),
            'footer_note' => $request->input('footer_note', $current['footer_note'] ?? ''),
            'cta_title' => $request->input('cta_title', $current['cta_title'] ?? ''),
            'features_label' => $request->input('features_label', $current['features_label'] ?? ''),
            'features_title' => $request->input('features_title', $current['features_title'] ?? ''),
            'features_intro' => $request->input('features_intro', $current['features_intro'] ?? ''),
            'features' => $pairs('feature_title', 'feature_text') ?: ($current['features'] ?? []),
            'process_label' => $request->input('process_label', $current['process_label'] ?? ''),
            'process_title' => $request->input('process_heading', $current['process_title'] ?? ''),
            'process' => $process ?: ($current['process'] ?? []),
            'hero_tiles' => $pairs('tile_title', 'tile_text') ?: ($current['hero_tiles'] ?? []),
            'featured_label' => $request->input('featured_label', $current['featured_label'] ?? ''),
            'featured_title' => $request->input('featured_title', $current['featured_title'] ?? ''),
            'menu_label' => $request->input('menu_label', $current['menu_label'] ?? ''),
            'menu_title' => $request->input('menu_title', $current['menu_title'] ?? ''),
            'menu_intro' => $request->input('menu_intro', $current['menu_intro'] ?? ''),
            'about_label' => $request->input('about_label', $current['about_label'] ?? ''),
            'about_paragraphs' => array_values(array_filter(array_map('trim', $paragraphs))),
            'about_points' => $pairs('about_point_title', 'about_point_text') ?: data_get(config('restaurant.about'), 'points', []),
            'contact_label' => $request->input('contact_label', $current['contact_label'] ?? ''),
            'contact_title' => $request->input('contact_title', $current['contact_title'] ?? ''),
            'contact_intro' => $request->input('contact_intro', $current['contact_intro'] ?? ''),
            'reserve_label' => $request->input('reserve_label', $current['reserve_label'] ?? ''),
            'reserve_title' => $request->input('reserve_title', $current['reserve_title'] ?? ''),
            'reserve_intro' => $request->input('reserve_intro', $current['reserve_intro'] ?? ''),
            'login_title' => $request->input('login_title', $current['login_title'] ?? ''),
            'login_subtitle' => $request->input('login_subtitle', $current['login_subtitle'] ?? ''),
            'register_title' => $request->input('register_title', $current['register_title'] ?? ''),
            'register_subtitle' => $request->input('register_subtitle', $current['register_subtitle'] ?? ''),
            'staff_login_title' => $request->input('staff_login_title', $current['staff_login_title'] ?? ''),
            'staff_login_subtitle' => $request->input('staff_login_subtitle', $current['staff_login_subtitle'] ?? ''),
            'dashboard_kicker' => $request->input('dashboard_kicker', $current['dashboard_kicker'] ?? ''),
            'refund_title' => $request->input('refund_heading', $current['refund_title'] ?? ''),
            'refund_items' => $refundItems ?: ($current['refund_items'] ?? []),
        ]);
    }
}
