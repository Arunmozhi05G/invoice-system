@extends('layouts.app')

@section('title', 'Budget Details')

@section('content')

    @php
        $spent = $budget->total_spent;
        $pct = $budget->spent_percentage;
        $isOver = $spent > $budget->amount;
        $barClass = $isOver ? 'bg-danger' : ($pct >= 80 ? 'bg-warning' : 'bg-success');
    @endphp

    <div class="d-flex justify-content-between mb-3">
        <h3>Budget Details</h3>
        <div>
            <a href="{{ route('budgets.edit', $budget) }}" class="btn btn-warning">Edit</a>
            <a href="{{ route('budgets.index') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h4>{{ $budget->name }}</h4>
                    <p class="mb-2"><strong>Category:</strong> {{ $budget->category }}</p>
                    <p class="mb-2"><strong>Period Type:</strong> {{ ucfirst($budget->period_type->value) }}</p>
                    <p class="mb-2"><strong>Period:</strong> {{ $budget->period_label }}</p>
                </div>

                <div class="col-md-6">
                    <p class="mb-2"><strong>Budget Amount:</strong> <span class="fs-5 fw-bold">₹{{ number_format($budget->amount, 2) }}</span></p>
                    <p class="mb-2"><strong>Total Spent:</strong> <span class="fs-5 text-danger fw-bold">₹{{ number_format($spent, 2) }}</span></p>
                    <p class="mb-2"><strong>Remaining:</strong> 
                        @if($isOver)
                            <span class="fs-5 text-danger fw-bold">Over by ₹{{ number_format($spent - $budget->amount, 2) }}</span>
                        @else
                            <span class="fs-5 text-success fw-bold">₹{{ number_format($budget->remaining, 2) }}</span>
                        @endif
                    </p>

                    <div class="mt-3">
                        <label class="form-label small fw-semibold">Budget Utilization ({{ $pct }}%)</label>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar {{ $barClass }} text-dark fw-bold" style="width: {{ $pct }}%;">
                                {{ $pct }}%
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <span class="fw-semibold">Tracked Expenses</span>
            <a href="{{ route('expenses.create', ['budget_id' => $budget->id]) }}" class="btn btn-sm btn-light">
                + Add Expense
            </a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Title</th>
                        <th>Notes</th>
                        <th>Amount</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($budget->expenses as $expense)
                        <tr>
                            <td>{{ $expense->expense_date ? $expense->expense_date->format('d-m-Y') : '-' }}</td>
                            <td class="fw-semibold">{{ $expense->title }}</td>
                            <td>{{ $expense->notes ?? '-' }}</td>
                            <td class="text-danger fw-semibold">₹{{ number_format($expense->amount, 2) }}</td>
                            <td>
                                <a href="{{ route('expenses.show', $expense) }}" class="btn btn-sm btn-info">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-3 text-muted">No expenses recorded against this budget yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection
