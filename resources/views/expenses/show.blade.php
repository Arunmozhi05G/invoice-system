@extends('layouts.app')

@section('title', 'Expense Details')

@section('content')

    <div class="d-flex justify-content-between mb-3">
        <h3>Expense Details</h3>
        <div>
            <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-warning">Edit</a>
            <a href="{{ route('expenses.index') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h4>{{ $expense->title }}</h4>
                    <p class="mb-2"><strong>Amount:</strong> <span class="fs-4 text-danger fw-bold">₹{{ number_format($expense->amount, 2) }}</span></p>
                    <p class="mb-2"><strong>Expense Date:</strong> {{ $expense->expense_date ? $expense->expense_date->format('d-m-Y') : '-' }}</p>
                    <p class="mb-2"><strong>Notes:</strong> {{ $expense->notes ?? '-' }}</p>
                </div>

                <div class="col-md-6">
                    @if($expense->budget)
                        <h5 class="text-muted mb-3">Linked Budget Information</h5>
                        <p class="mb-2"><strong>Budget Name:</strong> 
                            <a href="{{ route('budgets.show', $expense->budget) }}" class="fw-semibold">
                                {{ $expense->budget->name }}
                            </a>
                        </p>
                        <p class="mb-2"><strong>Category:</strong> {{ $expense->budget->category }}</p>
                        <p class="mb-2"><strong>Period:</strong> {{ $expense->budget->period_label }}</p>
                        <p class="mb-2"><strong>Budget:</strong> ₹{{ number_format($expense->budget->amount, 2) }}</p>
                        <p class="mb-2"><strong>Total Budget Spent:</strong> ₹{{ number_format($expense->budget->total_spent, 2) }}</p>
                        <p class="mb-2"><strong>Remaining Budget:</strong> 
                            @if($expense->budget->total_spent > $expense->budget->amount)
                                <span class="text-danger fw-bold">Over by ₹{{ number_format($expense->budget->total_spent - $expense->budget->amount, 2) }}</span>
                            @else
                                <span class="text-success fw-bold">₹{{ number_format($expense->budget->remaining, 2) }}</span>
                            @endif
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection

