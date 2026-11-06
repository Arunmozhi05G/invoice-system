<div class="mb-3">
    <label class="form-label">Name *</label>
    <input type="text" name="name" class="form-control"
        value="{{ old('name', $supplier->name ?? '') }}" required>
</div>

<div class="mb-3">
    <label class="form-label">Email</label>
    <input type="email" name="email" class="form-control"
        value="{{ old('email', $supplier->email ?? '') }}">
</div>

<div class="mb-3">
    <label class="form-label">Phone</label>
    <input type="text" name="phone" class="form-control"
        value="{{ old('phone', $supplier->phone ?? '') }}">
</div>

<div class="mb-3">
    <label class="form-label">GSTIN</label>
    <input type="text" name="gstin" class="form-control"
        value="{{ old('gstin', $supplier->gstin ?? '') }}">
</div>

<div class="mb-3">
    <label class="form-label">Address</label>
    <textarea name="address" class="form-control" rows="3">{{ old('address', $supplier->address ?? '') }}</textarea>
</div>

<button class="btn btn-primary">Save Supplier</button>
<a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Cancel</a>