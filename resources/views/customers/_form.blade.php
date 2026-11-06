<div class="mb-3">
    <label class="form-label">Name *</label>
    <input type="text" name="name"
           class="form-control"
           value="{{ old('name', $customer->name ?? '') }}" required>
</div>

<div class="mb-3">
    <label class="form-label">Email</label>
    <input type="email" name="email"
           class="form-control"
           value="{{ old('email', $customer->email ?? '') }}">
</div>

<div class="mb-3">
    <label class="form-label">Phone</label>
    <input type="text" name="phone"
           class="form-control"
           value="{{ old('phone', $customer->phone ?? '') }}">
</div>

<div class="mb-3">
    <label class="form-label">GSTIN</label>
    <input type="text" name="gstin"
           class="form-control"
           value="{{ old('gstin', $customer->gstin ?? '') }}">
</div>

<div class="mb-3">
    <label class="form-label">Address</label>
    <textarea name="address" class="form-control" rows="3">{{ old('address', $customer->address ?? '') }}</textarea>
</div>

<button class="btn btn-primary">Save Customer</button>

<a href="{{ route('customers.index') }}" class="btn btn-secondary">
    Cancel
</a>