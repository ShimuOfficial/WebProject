@extends('layouts.app')

@section('title', 'Site Settings')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
                <h1 class="h4 mb-1">Site Settings</h1>
                <p class="text-muted mb-0">Update the website name, branding, and homepage content.</p>
            </div>
            <div>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">Back</a>
            </div>
        </div>

        <div class="card">
            <form action="{{ route('admin.site-settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="mb-4">
                        <h5 class="mb-1">Basic Information</h5>
                        <p class="text-muted small mb-2">Main website details shown across the system.</p>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Website Name</label>
                                <input type="text" name="website_name" value="{{ old('website_name', $settings->website_name) }}"
                                    class="form-control" required>
                            </div>
	                            <div class="col-md-6">
	                                <label class="form-label">Contact Email</label>
	                                <input type="email" name="email_address" value="{{ old('email_address', $settings->email_address) }}"
	                                    class="form-control">
	                            </div>
	                            <div class="col-md-6">
	                                <label class="form-label">Phone Number</label>
	                                <input type="text" name="phone_number" value="{{ old('phone_number', $settings->phone_number) }}"
	                                    class="form-control" placeholder="+880 1700-000000">
	                            </div>
	                            <div class="col-md-6">
	                                <label class="form-label">Address</label>
	                                <input type="text" name="address" value="{{ old('address', $settings->address) }}"
	                                    class="form-control" placeholder="Restaurant address">
	                            </div>
	                            <div class="col-12">
	                                <label class="form-label">Tagline</label>
                                <input type="text" name="website_tagline" value="{{ old('website_tagline', $settings->website_tagline) }}"
                                    class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Hero Title</label>
                                <input type="text" name="hero_title" value="{{ old('hero_title', $settings->hero_title) }}"
                                    class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Hero Subtitle</label>
                                <input type="text" name="hero_subtitle" value="{{ old('hero_subtitle', $settings->hero_subtitle) }}"
                                    class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Hero Badge</label>
                                <input type="text" name="hero_badge" value="{{ old('hero_badge', $settings->hero_badge) }}"
                                    class="form-control" placeholder="e.g. Popular, New">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Hero Accent</label>
                                <input type="text" name="hero_accent" value="{{ old('hero_accent', $settings->hero_accent) }}"
                                    class="form-control" placeholder="Short accent text">
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="mb-4">
                        <h5 class="mb-1">Branding</h5>
                        <p class="text-muted small mb-2">Choose the main colors and upload logo files.</p>

                        <div class="row g-3 align-items-center">
                            <div class="col-md-3">
                                <label class="form-label">Primary Color</label>
                                <input type="color" name="primary_color" value="{{ old('primary_color', $settings->primary_color) }}"
                                    class="form-control form-control-color p-1">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Secondary Color</label>
                                <input type="color" name="secondary_color" value="{{ old('secondary_color', $settings->secondary_color) }}"
                                    class="form-control form-control-color p-1">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Accent Color</label>
                                <input type="color" name="accent_color" value="{{ old('accent_color', $settings->accent_color) }}"
                                    class="form-control form-control-color p-1">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Logo</label>
                                <div class="d-flex align-items-center gap-3 mb-2">
                                    <div style="width:84px;height:56px;border:1px solid var(--border);background:#fff;padding:6px;display:flex;align-items:center;justify-content:center;overflow:hidden;border-radius:6px">
                                        @if ($settings->logo)
                                            <img id="logoPreview" src="{{ asset('storage/' . $settings->logo) }}" alt="Logo" style="max-height:44px;max-width:100%">
                                        @else
                                            <img id="logoPreview" src="https://via.placeholder.com/84x56?text=Logo" alt="Logo" style="max-height:44px;max-width:100%">
                                        @endif
                                    </div>
                                    <div class="flex-fill">
                                        <input class="form-control" type="file" name="logo" accept="image/*" id="logoInput">
                                        <div class="form-text">Recommended: PNG or SVG, transparent background.</div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Hero Background</label>
                                <input class="form-control" type="file" name="hero_background_image" accept="image/*">
                                <div class="form-text">Optional background image for homepage hero.</div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">About Page Details</label>
                                <textarea name="about_us" class="form-control" rows="4"
                                    placeholder="Write the restaurant story or about details">{{ old('about_us', $settings->about_us) }}</textarea>
                                <div class="form-text">This text appears on the public About page.</div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">About Image</label>
                                @if ($settings->about_image)
                                    <div class="mb-2">
                                        <img src="{{ asset('storage/' . $settings->about_image) }}" alt="About image"
                                            style="width:180px;height:110px;object-fit:cover;border-radius:6px;border:1px solid var(--border)">
                                    </div>
                                @endif
                                <input class="form-control" type="file" name="about_image" accept="image/*">
                                <div class="form-text">Optional restaurant, kitchen, interior, or team image for the About page.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer d-flex justify-content-end gap-2">
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save Settings</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            document.getElementById('logoInput')?.addEventListener('change', function (e) {
                const file = e.target.files && e.target.files[0];
                if (!file) return;
                const url = URL.createObjectURL(file);
                const img = document.getElementById('logoPreview');
                if (img) img.src = url;
            });
        </script>
    @endpush

@endsection
