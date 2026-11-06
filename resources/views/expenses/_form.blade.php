<div class="row g-3 mb-3">
    <div class="col-md-6">
        <label class="form-label">Expense Title *</label>
        <input type="text" name="title" class="form-control" placeholder="e.g. Google Workspace Subscription"
            value="{{ old('title', $expense->title ?? '') }}" required>
    </div>

    <div class="col-md-6">
        <label class="form-label">Budget *</label>
        <select name="budget_id" class="form-select" required>
            <option value="">Select Budget</option>
            @foreach($budgets as $b)
                <option value="{{ $b->id }}"
                    @selected(old('budget_id', isset($expense) ? $expense->budget_id : request('budget_id')) == $b->id)>
                    {{ $b->name }} ({{ $b->category }} - {{ $b->period_label }}) - Remaining: ₹{{ number_format(max(0, $b->remaining), 2) }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label">Amount (₹) *</label>
        <input type="number" step="0.01" min="0.01" name="amount" class="form-control" placeholder="0.00"
            value="{{ old('amount', $expense->amount ?? '') }}" required>
    </div>

    <div class="col-md-6">
        <label class="form-label">Expense Date *</label>
        <input type="date" name="expense_date" class="form-control"
            value="{{ old('expense_date', isset($expense) && $expense->expense_date ? $expense->expense_date->format('Y-m-d') : date('Y-m-d')) }}"
            required>
    </div>

    <div class="col-12">
        <label class="form-label">Notes</label>
        <textarea name="notes" class="form-control" rows="3" placeholder="Optional expense description or receipt details...">{{ old('notes', $expense->notes ?? '') }}</textarea>
    </div>
</div>

<button type="submit" class="btn btn-primary">Save Expense</button>
<a href="{{ route('expenses.index') }}" class="btn btn-secondary">Cancel</a>

