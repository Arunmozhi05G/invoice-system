<div class="mb-3">
    <label class="form-label">Product Name *</label>
    <input type="text" name="name" class="form-control"
        value="{{ old('name', $product->name ?? '') }}" required>
</div>

<div class="mb-3">
    <label class="form-label">Type *</label>
    <select name="type" class="form-select" required>
        <option value="">Select Type</option>
        @foreach($types as $type)
            <option value="{{ $type->value }}"
                @selected(old('type', isset($product) ? $product->type->value : '') === $type->value)>
                {{ ucfirst($type->value) }}
            </option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label class="form-label">Unit</label>
    <input type="text" name="unit" class="form-control"
        placeholder="e.g. pcs, hrs, kg"
        value="{{ old('unit', $product->unit ?? '') }}">
</div>

<div class="mb-3">
    <label class="form-label">Price *</label>
    <input type="number" name="price" class="form-control"
        step="0.01" min="0"
        value="{{ old('price', $product->price ?? '0.00') }}" required>
</div>

<div class="mb-3">
    <label class="form-label">GST Rate (%) *</label>
    <input type="number" name="tax_rate" class="form-control"
        step="0.01" min="0" max="100"
        value="{{ old('tax_rate', $product->tax_rate ?? '18.00') }}" required>
</div>

<button type="submit" class="btn btn-primary">Save Product</button>
<a href="{{ route('products.index') }}" class="btn btn-secondary">Cancel</a>
