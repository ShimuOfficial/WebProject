@extends('layouts.app')
@section('title', 'Menu')
@section('subtitle', 'Manage your restaurant menu items')

@section('content')
    <div class="admin-toolbar">
        <form class="admin-toolbar-form" method="GET">
            <div class="input-group admin-search">
                <span class="input-group-text"><i class="fas fa-search text-muted"></i></span>
                <input type="text" name="search" class="form-control" placeholder="Search menu..."
                    value="{{ request('search') }}">
            </div>
            <select name="category" class="form-select admin-filter" onchange="this.form.submit()">
                <option value="">All Categories</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>
                        {{ $cat }}</option>
                @endforeach
            </select>
            <button class="btn btn-outline-primary btn-sm">Filter</button>
        </form>
        @if ($canManageMenu)
            <a href="{{ route('menu.create') }}" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Add Item</a>
        @endif
    </div>

    <div class="row g-3 g-lg-4">
        @forelse($menuItems as $item)
            <div class="col-12 col-sm-6 col-lg-4 col-xl-3 fade-in">
                <div class="card h-100 admin-media-card">
                    <div class="admin-media-card__image">
                        <img src="{{ $item->image_url }}" alt="{{ $item->name }}"
                            onerror="this.src='{{ $site['dish_fallback_url'] ?? asset('images/dishes/plain-rice.jpg') }}'">
                    </div>
                    <div class="card-body admin-media-card__body">
                        <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                            <span class="badge text-wrap" style="background:rgba(196,92,38,.12);color:var(--primary);font-size:11px">{{ $item->category }}</span>
                            @if ($item->is_available)
                                <span class="badge bg-success" style="font-size:10px">Available</span>
                            @else
                                <span class="badge bg-secondary" style="font-size:10px">Unavailable</span>
                            @endif
                        </div>
                        <h6 class="fw-bold mb-1">{{ $item->name }}</h6>
                        <p class="text-muted mb-2 admin-media-card__desc">{{ Str::limit($item->description, 60) }}</p>
                        <div class="text-muted mb-2" style="font-size:12px">Recipe: {{ $item->menu_ingredients_count }}
                            ingredient(s) · {{ $item->available_servings }} serving(s) left</div>
                        <div class="admin-media-card__foot">
                            <span class="fw-bold admin-media-card__price">৳{{ number_format($item->price, 2) }}</span>
                            <div class="admin-media-card__actions">
                                @if ($canToggleAvailability)
                                    <form action="{{ route('menu.availability', $item) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <button
                                            class="btn btn-sm {{ $item->is_available ? 'btn-outline-secondary' : 'btn-outline-success' }}"
                                            title="{{ $item->is_available ? 'Mark Unavailable' : 'Mark Available' }}">
                                            <i class="fas {{ $item->is_available ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                                        </button>
                                    </form>
                                @endif
                                @if ($canManageMenu)
                                    <a href="{{ route('menu.edit', $item) }}" class="btn btn-sm btn-outline-primary"><i
                                            class="fas fa-edit"></i></a>
                                    <form action="{{ route('menu.destroy', $item) }}" method="POST"
                                        onsubmit="return confirm('Delete this item?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class="fas fa-book-open fa-3x mb-3 text-muted"></i>
                <p class="text-muted">No menu items yet</p>
                @if ($canManageMenu)
                    <a href="{{ route('menu.create') }}" class="btn btn-primary">Add First Item</a>
                @endif
            </div>
        @endforelse
    </div>
    <div class="mt-4">{{ $menuItems->withQueryString()->links() }}</div>
@endsection
