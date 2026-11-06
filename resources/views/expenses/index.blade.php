@extends('layouts.app')

@section('title', 'Expenses')

@section('content')

    <div class="d-flex justify-content-between mb-3">
        <h3>Expenses</h3>

        <a href="{{ route('expenses.create') }}" class="btn btn-primary">
            Add Expense
        </a>
    </div>

    <form method="GET" class="mb-3">
        <div class="row g-2">
            <div class="col-md-7">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Search expense title, notes, or budget..."
                        value="{{ request('search') }}">
                    <button class="btn btn-dark" type="submit">Search</button>
                </div>
            </div>
            <div class="col-md-5">
                <select name="budget_id" class="form-select" onchange="this.form.submit()">
                    <option value="">All Budgets</option>
                    @foreach($budgets as $b)
                        <option value="{{ $b->id }}" @selected(request('budget_id') == $b->id)>
                            {{ $b->name }} ({{ $b->category }} - {{ $b->period_label }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Budget Category</th>
                        <th>Expense Date</th>
                        <th>Amount</th>
                        <th>Notes</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($expenses as $expense)
                        <tr>
                            <td>{{ $expenses->firstItem() + $loop->index }}</td>
                            <td>
                                <a href="{{ route('expenses.show', $expense) }}" class="text-decoration-none fw-semibold">
                                    {{ $expense->title }}
                                </a>
                            </td>
                            <td>
                                @if($expense->budget)
                                    <a href="{{ route('budgets.show', $expense->budget) }}" class="badge bg-secondary text-decoration-none">
                                        {{ $expense->budget->name }} ({{ $expense->budget->category }})
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $expense->expense_date ? $expense->expense_date->format('d-m-Y') : '-' }}</td>
                            <td class="text-danger fw-bold">₹{{ number_format($expense->amount, 2) }}</td>
                            <td>{{ $expense->notes ?? '-' }}</td>

                            <td class="d-flex gap-1">
                                <a href="{{ route('expenses.show', $expense) }}"
                                    class="btn btn-sm btn-info">View</a>

                                <a href="{{ route('expenses.edit', $expense) }}"
                                    class="btn btn-sm btn-warning">Edit</a>

                                <form method="POST" action="{{ route('expenses.destroy', $expense) }}"
                                    onsubmit="return confirm('Delete this expense?')">
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
                            <td colspan="7" class="text-center py-4">
                                No expenses found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $expenses->links() }}
    </div>

@endsection

