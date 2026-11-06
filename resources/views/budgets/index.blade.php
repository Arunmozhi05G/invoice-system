@extends('layouts.app')

@section('title', 'Budgets')

@section('content')

    <div class="d-flex justify-content-between mb-3">
        <h3>Budgets</h3>

        <a href="{{ route('budgets.create') }}" class="btn btn-primary">
            Add Budget
        </a>
    </div>

    <form method="GET" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search budget name, category or year..."
                value="{{ request('search') }}">

            <button class="btn btn-dark" type="submit">Search</button>
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Period</th>
                        <th>Budget Amount</th>
                        <th>Spent</th>
                        <th>Remaining</th>
                        <th style="min-width: 140px;">Usage</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($budgets as $budget)
                        @php
                            $spent = $budget->total_spent;
                            $pct = $budget->spent_percentage;
                            $isOver = $spent > $budget->amount;
                            $barClass = $isOver ? 'bg-danger' : ($pct >= 80 ? 'bg-warning' : 'bg-success');
                        @endphp
                        <tr>
                            <td>{{ $budgets->firstItem() + $loop->index }}</td>
                            <td>
                                <a href="{{ route('budgets.show', $budget) }}" class="text-decoration-none fw-semibold">
                                    {{ $budget->name }}
                                </a>
                            </td>
                            <td>{{ $budget->category }}</td>
                            <td>
                                <span class="badge {{ $budget->period_type->value === 'monthly' ? 'bg-primary' : 'bg-secondary' }}">
                                    {{ $budget->period_label }}
                                </span>
                            </td>
                            <td>₹{{ number_format($budget->amount, 2) }}</td>
                            <td>₹{{ number_format($spent, 2) }}</td>
                            <td>
                                @if($isOver)
                                    <span class="text-danger fw-bold">Over by ₹{{ number_format($spent - $budget->amount, 2) }}</span>
                                @else
                                    <span class="text-success fw-bold">₹{{ number_format($budget->remaining, 2) }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="progress" style="height: 18px;" role="progressbar">
                                    <div class="progress-bar {{ $barClass }} text-dark fw-bold" style="width: {{ $pct }}%; font-size: 11px;">
                                        {{ $pct }}%
                                    </div>
                                </div>
                            </td>

                            <td class="d-flex gap-1">
                                <a href="{{ route('budgets.show', $budget) }}"
                                    class="btn btn-sm btn-info">View</a>

                                <a href="{{ route('budgets.edit', $budget) }}"
                                    class="btn btn-sm btn-warning">Edit</a>

                                <form method="POST" action="{{ route('budgets.destroy', $budget) }}"
                                    onsubmit="return confirm('Delete this budget?')">
                                    @csrf
                                    @method('DELETE')

                                    <button class="btn btn-sm btn-danger">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                No budgets found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $budgets->links() }}
    </div>

@endsection

