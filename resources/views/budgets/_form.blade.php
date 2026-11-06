<div class="row g-3 mb-3">
    <div class="col-md-6">
        <label class="form-label">Budget Name *</label>
        <input type="text" name="name" class="form-control" placeholder="e.g. Office Supplies 2026"
            value="{{ old('name', $budget->name ?? '') }}" required>
    </div>

    <div class="col-md-6">
        <label class="form-label">Category *</label>
        <input type="text" name="category" list="categoryOptions" class="form-control" placeholder="e.g. Office Supplies, Marketing, IT, Travel"
            value="{{ old('category', $budget->category ?? '') }}" required>
        <datalist id="categoryOptions">
            <option value="Office Supplies">
            <option value="Marketing & Ads">
            <option value="IT & Software">
            <option value="Travel & Lodging">
            <option value="Utilities & Bills">
            <option value="Salaries & Operations">
            <option value="Raw Materials">
        </datalist>
    </div>

    <div class="col-md-4">
        <label class="form-label">Period Type *</label>
        <select name="period_type" id="periodTypeSelect" class="form-select" required>
            <option value="">Select Period Type</option>
            @foreach($periods as $period)
                @php
                    $val = is_object($period) ? $period->value : $period;
                @endphp
                <option value="{{ $val }}"
                    @selected(old('period_type', isset($budget) ? ($budget->period_type->value ?? $budget->period_type) : 'monthly') === $val)>
                    {{ ucfirst($val) }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-4">
        <label class="form-label">Year *</label>
        <input type="number" name="year" min="2000" max="2100" class="form-control"
            value="{{ old('year', $budget->year ?? date('Y')) }}" required>
    </div>

    <div class="col-md-4" id="monthContainer">
        <label class="form-label">Month *</label>
        <select name="month" id="monthSelect" class="form-select">
            <option value="">Select Month</option>
            @for($m = 1; $m <= 12; $m++)
                @php
                    $mName = date('F', mktime(0, 0, 0, $m, 1));
                @endphp
                <option value="{{ $m }}" @selected(old('month', $budget->month ?? (int)date('n')) == $m)>
                    {{ $mName }}
                </option>
            @endfor
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label">Budget Amount (₹) *</label>
        <input type="number" step="0.01" min="0.01" name="amount" class="form-control" placeholder="0.00"
            value="{{ old('amount', $budget->amount ?? '') }}" required>
    </div>
</div>

<button type="submit" class="btn btn-primary">Save Budget</button>
<a href="{{ route('budgets.index') }}" class="btn btn-secondary">Cancel</a>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const periodTypeSelect = document.getElementById('periodTypeSelect');
    const monthContainer = document.getElementById('monthContainer');
    const monthSelect = document.getElementById('monthSelect');

    function toggleMonth() {
        if (periodTypeSelect.value === 'monthly') {
            monthContainer.style.display = '';
            monthSelect.required = true;
        } else {
            monthContainer.style.display = 'none';
            monthSelect.required = false;
        }
    }

    periodTypeSelect.addEventListener('change', toggleMonth);
    toggleMonth();
});
</script>

