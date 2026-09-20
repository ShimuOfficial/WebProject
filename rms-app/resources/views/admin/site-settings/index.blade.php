@extends('layouts.app')

@section('title', 'Site Settings')

@php
    $hours = old('hours_label')
        ? collect(old('hours_label'))->map(fn ($label, $i) => ['label' => $label, 'time' => old('hours_time.' . $i)])
        : collect($hours ?? []);
    while ($hours->count() < 2) {
        $hours->push(['label' => '', 'time' => '']);
    }
    $features = collect($content['features'] ?? []);
    $process = collect($content['process'] ?? []);
    $tiles = collect($content['hero_tiles'] ?? []);
    $points = collect($content['about_points'] ?? []);
    $refund = collect($content['refund_items'] ?? []);
    while ($features->count() < 4) { $features->push(['title' => '', 'text' => '']); }
    while ($process->count() < 4) { $process->push(['step' => str_pad($process->count() + 1, 2, '0', STR_PAD_LEFT), 'title' => '', 'text' => '']); }
    while ($tiles->count() < 3) { $tiles->push(['title' => '', 'text' => '']); }
    while ($points->count() < 3) { $points->push(['title' => '', 'text' => '']); }
    while ($refund->count() < 4) { $refund->push(['title' => '', 'text' => '']); }
@endphp

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
                <h1 class="h4 mb-1">Site Settings</h1>
                <p class="text-muted mb-0">All public copy, hours, photos, and branding for client handover. Change these values — the website updates everywhere.</p>
            </div>
            <div>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">Back</a>
            </div>
        </div>

        <form action="{{ route('admin.site-settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="mb-1">Restaurant identity</h5>
                    <p class="text-muted small mb-3">Name, contact details, and logo used on the website, logins, and staff console.</p>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Restaurant name</label>
                            <input type="text" name="website_name" value="{{ old('website_name', $settings->website_name) }}" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tagline</label>
                            <input type="text" name="website_tagline" value="{{ old('website_tagline', $settings->website_tagline) }}" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Contact email</label>
                            <input type="email" name="email_address" value="{{ old('email_address', $settings->email_address) }}" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone number</label>
                            <input type="text" name="phone_number" value="{{ old('phone_number', $settings->phone_number) }}" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Address</label>
                            <input type="text" name="address" value="{{ old('address', $settings->address) }}" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Facebook URL</label>
                            <input type="text" name="facebook_url" value="{{ old('facebook_url', $settings->facebook_url) }}" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Instagram URL</label>
                            <input type="text" name="instagram_url" value="{{ old('instagram_url', $settings->instagram_url) }}" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Guest rating</label>
                            <input type="text" name="guest_rating" value="{{ old('guest_rating', $settings->guest_rating) }}" class="form-control" placeholder="4.8">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Logo / restaurant icon</label>
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <img id="logoPreview" src="{{ $site['logo_url'] }}" alt="Logo" style="width:56px;height:56px;object-fit:contain;border-radius:12px;background:#1a1510;padding:6px">
                                <div class="flex-fill">
                                    <input class="form-control" type="file" name="logo" accept="image/*" id="logoInput">
                                    <div class="form-text">Default is the restaurant icon. Upload a PNG or SVG to replace it.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="mb-1">Opening hours</h5>
                    @foreach ($hours as $index => $hour)
                        <div class="row g-3 mb-2">
                            <div class="col-md-5">
                                <input type="text" name="hours_label[]" class="form-control" placeholder="Sat – Thu" value="{{ $hour['label'] ?? '' }}">
                            </div>
                            <div class="col-md-7">
                                <input type="text" name="hours_time[]" class="form-control" placeholder="11:00 AM – 11:00 PM" value="{{ $hour['time'] ?? '' }}">
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="mb-1">Homepage hero</h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Badge</label>
                            <input type="text" name="hero_badge" value="{{ old('hero_badge', $settings->hero_badge) }}" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Title</label>
                            <input type="text" name="hero_title" value="{{ old('hero_title', $settings->hero_title) }}" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Accent</label>
                            <input type="text" name="hero_accent" value="{{ old('hero_accent', $settings->hero_accent) }}" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Subtitle</label>
                            <textarea name="hero_subtitle" class="form-control" rows="3">{{ old('hero_subtitle', $settings->hero_subtitle) }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Hero photo</label>
                            <img src="{{ $site['hero_image_url'] }}" alt="Hero" class="d-block mb-2" style="width:220px;height:120px;object-fit:cover;border-radius:8px">
                            <input class="form-control" type="file" name="hero_background_image" accept="image/*">
                        </div>
                        @foreach ($tiles as $index => $tile)
                            <div class="col-md-4">
                                <label class="form-label">Hero tile {{ $index + 1 }} title</label>
                                <input type="text" name="tile_title[]" class="form-control" value="{{ $tile['title'] ?? '' }}">
                                <textarea name="tile_text[]" class="form-control mt-2" rows="3">{{ $tile['text'] ?? '' }}</textarea>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="mb-1">About the restaurant</h5>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Section label</label>
                            <input type="text" name="about_label" class="form-control" value="{{ old('about_label', $content['about_label'] ?? '') }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">About title</label>
                            <input type="text" name="about_title" class="form-control" value="{{ old('about_title', $settings->about_title) }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Lead paragraph</label>
                            <textarea name="about_us" class="form-control" rows="4">{{ old('about_us', $settings->about_us) }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Extra story paragraphs</label>
                            <textarea name="about_paragraphs" class="form-control" rows="5" placeholder="Separate paragraphs with a blank line">{{ old('about_paragraphs', implode("\n\n", $content['about_paragraphs'] ?? [])) }}</textarea>
                        </div>
                        @foreach ($points as $index => $point)
                            <div class="col-md-4">
                                <label class="form-label">Focus {{ $index + 1 }}</label>
                                <input type="text" name="about_point_title[]" class="form-control" value="{{ $point['title'] ?? '' }}">
                                <textarea name="about_point_text[]" class="form-control mt-2" rows="3">{{ $point['text'] ?? '' }}</textarea>
                            </div>
                        @endforeach
                        <div class="col-12">
                            <label class="form-label">About photo</label>
                            <img src="{{ $site['about_image_url'] }}" alt="About" class="d-block mb-2" style="width:220px;height:120px;object-fit:cover;border-radius:8px">
                            <input class="form-control" type="file" name="about_image" accept="image/*">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="mb-1">Restaurant system articles</h5>
                    <p class="text-muted small">Homepage features, guest journey, menu labels, login copy, and refund policy.</p>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Features label</label>
                            <input type="text" name="features_label" class="form-control" value="{{ $content['features_label'] ?? '' }}">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Features title</label>
                            <input type="text" name="features_title" class="form-control" value="{{ $content['features_title'] ?? '' }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Features intro</label>
                            <textarea name="features_intro" class="form-control" rows="2">{{ $content['features_intro'] ?? '' }}</textarea>
                        </div>
                        @foreach ($features as $index => $feature)
                            <div class="col-md-6">
                                <label class="form-label">Feature {{ $index + 1 }}</label>
                                <input type="text" name="feature_title[]" class="form-control" value="{{ $feature['title'] ?? '' }}">
                                <textarea name="feature_text[]" class="form-control mt-2" rows="3">{{ $feature['text'] ?? '' }}</textarea>
                            </div>
                        @endforeach
                        <div class="col-md-4">
                            <label class="form-label">Process label</label>
                            <input type="text" name="process_label" class="form-control" value="{{ $content['process_label'] ?? '' }}">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Process title</label>
                            <input type="text" name="process_heading" class="form-control" value="{{ $content['process_title'] ?? '' }}">
                        </div>
                        @foreach ($process as $index => $step)
                            <div class="col-md-6">
                                <label class="form-label">Step {{ $step['step'] ?? $index + 1 }}</label>
                                <input type="hidden" name="process_step[]" value="{{ $step['step'] ?? str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}">
                                <input type="text" name="process_title[]" class="form-control" value="{{ $step['title'] ?? '' }}">
                                <textarea name="process_text[]" class="form-control mt-2" rows="3">{{ $step['text'] ?? '' }}</textarea>
                            </div>
                        @endforeach
                        <div class="col-md-6">
                            <label class="form-label">Featured label</label>
                            <input type="text" name="featured_label" class="form-control" value="{{ $content['featured_label'] ?? '' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Featured title</label>
                            <input type="text" name="featured_title" class="form-control" value="{{ $content['featured_title'] ?? '' }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Menu label</label>
                            <input type="text" name="menu_label" class="form-control" value="{{ $content['menu_label'] ?? '' }}">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Menu title</label>
                            <input type="text" name="menu_title" class="form-control" value="{{ $content['menu_title'] ?? '' }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Menu intro</label>
                            <textarea name="menu_intro" class="form-control" rows="2">{{ $content['menu_intro'] ?? '' }}</textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Contact label</label>
                            <input type="text" name="contact_label" class="form-control" value="{{ $content['contact_label'] ?? '' }}">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Contact title</label>
                            <input type="text" name="contact_title" class="form-control" value="{{ $content['contact_title'] ?? '' }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Contact intro</label>
                            <textarea name="contact_intro" class="form-control" rows="2">{{ $content['contact_intro'] ?? '' }}</textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Reserve label</label>
                            <input type="text" name="reserve_label" class="form-control" value="{{ $content['reserve_label'] ?? '' }}">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Reserve title</label>
                            <input type="text" name="reserve_title" class="form-control" value="{{ $content['reserve_title'] ?? '' }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Reserve intro</label>
                            <textarea name="reserve_intro" class="form-control" rows="2">{{ $content['reserve_intro'] ?? '' }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Footer blurb</label>
                            <textarea name="footer_blurb" class="form-control" rows="2">{{ $content['footer_blurb'] ?? '' }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Footer note</label>
                            <input type="text" name="footer_note" class="form-control" value="{{ $content['footer_note'] ?? '' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Call to action title</label>
                            <input type="text" name="cta_title" class="form-control" value="{{ $content['cta_title'] ?? '' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Login title</label>
                            <input type="text" name="login_title" class="form-control" value="{{ $content['login_title'] ?? '' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Login subtitle</label>
                            <input type="text" name="login_subtitle" class="form-control" value="{{ $content['login_subtitle'] ?? '' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Register title</label>
                            <input type="text" name="register_title" class="form-control" value="{{ $content['register_title'] ?? '' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Register subtitle</label>
                            <input type="text" name="register_subtitle" class="form-control" value="{{ $content['register_subtitle'] ?? '' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Staff login title</label>
                            <input type="text" name="staff_login_title" class="form-control" value="{{ $content['staff_login_title'] ?? '' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Staff login subtitle</label>
                            <input type="text" name="staff_login_subtitle" class="form-control" value="{{ $content['staff_login_subtitle'] ?? '' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Dashboard kicker</label>
                            <input type="text" name="dashboard_kicker" class="form-control" value="{{ $content['dashboard_kicker'] ?? '' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Refund heading</label>
                            <input type="text" name="refund_heading" class="form-control" value="{{ $content['refund_title'] ?? '' }}">
                        </div>
                        @foreach ($refund as $item)
                            <div class="col-md-6">
                                <input type="text" name="refund_title[]" class="form-control" value="{{ $item['title'] ?? '' }}">
                                <textarea name="refund_text[]" class="form-control mt-2" rows="2">{{ $item['text'] ?? '' }}</textarea>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="mb-1">Brand colors</h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Primary</label>
                            <input type="color" name="primary_color" value="{{ old('primary_color', $settings->primary_color) }}" class="form-control form-control-color p-1">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Secondary</label>
                            <input type="color" name="secondary_color" value="{{ old('secondary_color', $settings->secondary_color) }}" class="form-control form-control-color p-1">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Accent</label>
                            <input type="color" name="accent_color" value="{{ old('accent_color', $settings->accent_color) }}" class="form-control form-control-color p-1">
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mb-5">
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Save settings</button>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            document.getElementById('logoInput')?.addEventListener('change', function (e) {
                const file = e.target.files && e.target.files[0];
                if (!file) return;
                const img = document.getElementById('logoPreview');
                if (img) img.src = URL.createObjectURL(file);
            });
        </script>
    @endpush
@endsection
