@extends('layouts.app')
@section('title', 'Edit Menu Item')
@section('subtitle', 'Update ' . $menu->name)

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card fade-in">
                <div class="card-header"><i class="fas fa-edit me-2"></i>Edit Menu Item</div>
                <div class="card-body">
                    <form action="{{ route('menu.update', $menu) }}" method="POST" enctype="multipart/form-data">
                        @csrf @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Item Name *</label>
                                <input type="text" name="name" class="form-control"
                                    value="{{ old('name', $menu->name) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Category *</label>
                                <input type="text" name="category" class="form-control"
                                    value="{{ old('category', $menu->category) }}" list="categories" required>
                                <datalist id="categories">
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat }}">
                                    @endforeach
                                    <option value="Appetizers">
                                    <option value="Main Course">
                                    <option value="Desserts">
                                    <option value="Beverages">
                                    <option value="Salads">
                                    <option value="Soups">
                                </datalist>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Price (BDT) *</label>
                                <input type="number" name="price" class="form-control" step="0.01" min="0.01"
                                    value="{{ old('price', $menu->price) }}" required
                                    oninvalid="this.setCustomValidity('Please enter a price greater than 0')"
                                    oninput="this.setCustomValidity('')">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Image</label>
                                <div class="admin-image-preview mb-2">
                                    <img src="{{ $menu->image_url }}" alt="{{ $menu->name }}">
                                </div>
                                <input type="file" name="image" class="form-control" accept="image/*">
                                <small class="text-muted">Current photo is shown above. Upload to replace.</small>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Description</label>
                                <textarea name="description" class="form-control" rows="3">{{ old('description', $menu->description) }}</textarea>
                            </div>
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input type="checkbox" name="is_available" class="form-check-input" id="avail"
                                        value="1" {{ $menu->is_available ? 'checked' : '' }}>
                                    <label class="form-check-label" for="avail">Available for ordering</label>
                                </div>
                            </div>

                            <div class="col-12">
                                <hr>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label fw-semibold mb-0">Required Inventory Per 1 Dish</label>
                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                        onclick="addIngredientRow()">
                                        <i class="fas fa-plus me-1"></i>Add Ingredient
                                    </button>
                                </div>
                                <div id="ingredientRows" class="d-flex flex-column gap-2"></div>
                                <div id="estimationBox" class="mt-3 p-3 rounded-3"
                                    style="background:#f8fafc;border:1px solid var(--border)">
                                    <div class="fw-semibold mb-2">Estimation for 1 Dish</div>
                                    <div id="estimationList" class="text-muted" style="font-size:14px">No ingredients added
                                        yet.</div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Update
                                Item</button>
                            <a href="{{ route('menu.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            const inventoryOptions = @json($inventories->map(fn($i) => ['id' => $i->id, 'name' => $i->item_name, 'unit' => $i->unit])->values());
            const existingIngredients = @json(old(
                    'ingredients',
                    $menu->menuIngredients->map(fn($mi) => ['inventory_id' => $mi->inventory_id, 'quantity_per_dish' => (float) $mi->quantity_per_dish])->values()->toArray()));

            function reindexRows() {
                document.querySelectorAll('#ingredientRows .row').forEach((row, idx) => {
                    row.querySelector('.ingredient-select').name = `ingredients[${idx}][inventory_id]`;
                    row.querySelector('.ingredient-qty').name = `ingredients[${idx}][quantity_per_dish]`;
                });
            }

            function ingredientOptionHtml() {
                let html = '<option value="">Select inventory item</option>';
                inventoryOptions.forEach((opt) => {
                    html += `<option value="${opt.id}" data-unit="${opt.unit}">${opt.name} (${opt.unit})</option>`;
                });
                return html;
            }

            function addIngredientRow(data = {}) {
                const container = document.getElementById('ingredientRows');
                const idx = container.children.length;
                const row = document.createElement('div');
                row.className = 'row g-2 align-items-center';
                row.innerHTML = `
        <div class="col-md-6">
            <select name="ingredients[${idx}][inventory_id]" class="form-select ingredient-select" onchange="renderEstimation()">
                ${ingredientOptionHtml()}
            </select>
        </div>
        <div class="col-md-4">
            <input type="number" min="0.0001" step="0.0001" name="ingredients[${idx}][quantity_per_dish]" class="form-control ingredient-qty" placeholder="Qty per dish" oninput="renderEstimation()">
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-outline-danger w-100" onclick="this.closest('.row').remove(); reindexRows(); renderEstimation();">
                <i class="fas fa-trash"></i>
            </button>
        </div>`;

                container.appendChild(row);

                if (data.inventory_id) {
                    row.querySelector('.ingredient-select').value = String(data.inventory_id);
                }
                if (data.quantity_per_dish) {
                    row.querySelector('.ingredient-qty').value = data.quantity_per_dish;
                }

                renderEstimation();
            }

            function renderEstimation() {
                const rows = document.querySelectorAll('#ingredientRows .row');
                const list = document.getElementById('estimationList');
                const lines = [];

                rows.forEach((row) => {
                    const select = row.querySelector('.ingredient-select');
                    const qty = parseFloat(row.querySelector('.ingredient-qty').value || 0);
                    if (!select.value || qty <= 0) return;

                    const opt = select.options[select.selectedIndex];
                    const name = opt.textContent;
                    lines.push(`${name}: ${qty.toFixed(4).replace(/0+$/, '').replace(/\\.$/, '')}`);
                });

                list.innerHTML = lines.length ? lines.map((line) => `<div>${line}</div>`).join('') :
                    'No ingredients added yet.';
            }

            if (existingIngredients.length) {
                existingIngredients.forEach((ingredient) => addIngredientRow(ingredient));
            } else {
                addIngredientRow();
            }
        </script>
    @endpush
@endsection
