@extends('layouts.app')

@section('title', 'Reports & Analytics')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3>Reports & Analytics</h3>
            <p class="text-muted mb-0">Financial Summary & Budget vs Tracking</p>
        </div>
        <button onclick="window.print()" class="btn btn-outline-dark btn-sm">
            Print Report
        </button>
    </div>

    {{-- Date Filter Form --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('reports.index') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">From Date</label>
                    <input type="date" name="from_date" class="form-control" value="{{ $fromDate }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">To Date</label>
                    <input type="date" name="to_date" class="form-control" value="{{ $toDate }}">
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-dark w-100">Apply Filter</button>
                    <a href="{{ route('reports.index') }}" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Financial Overview Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm h-100 border-start border-primary border-4">
                <div class="card-body">
                    <span class="text-muted small fw-semibold">TOTAL SALES</span>
                    <h4 class="mt-2 text-primary fw-bold">₹{{ number_format($totalSales, 2) }}</h4>
                    <div class="small text-muted mt-2">
                        <span>Received: ₹{{ number_format($salesCollected, 2) }}</span><br>
                        <span class="text-danger">Pending: ₹{{ number_format($salesPending, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm h-100 border-start border-info border-4">
                <div class="card-body">
                    <span class="text-muted small fw-semibold">TOTAL PURCHASES</span>
                    <h4 class="mt-2 text-info fw-bold">₹{{ number_format($totalPurchases, 2) }}</h4>
                    <div class="small text-muted mt-2">
                        <span>Paid: ₹{{ number_format($purchasesPaid, 2) }}</span><br>
                        <span class="text-danger">Due: ₹{{ number_format($purchasesPending, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm h-100 border-start border-warning border-4">
                <div class="card-body">
                    <span class="text-muted small fw-semibold">OPERATING EXPENSES</span>
                    <h4 class="mt-2 text-warning fw-bold">₹{{ number_format($totalExpenses, 2) }}</h4>
                    <div class="small text-muted mt-2">
                        <span>Tracked against Budgets</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-dark text-white fw-semibold">
            Budget vs Tracking
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Budget Name</th>
                        <th>Category</th>
                        <th>Period</th>
                        <th>Budget</th>
                        <th>Spent</th>
                        <th>Remaining</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($budgets as $budget)
                        @php
                            $spent = $budget->total_spent;
                            $diff = $budget->amount - $spent;
                            $isOver = $spent > $budget->amount;
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <a href="{{ route('budgets.show', $budget) }}" class="fw-semibold text-decoration-none">
                                    {{ $budget->name }}
                                </a>
                            </td>
                            <td>{{ $budget->category }}</td>
                            <td>
                                <span class="badge bg-secondary">{{ $budget->period_label }}</span>
                            </td>
                            <td>₹{{ number_format($budget->amount, 2) }}</td>
                            <td class="text-danger fw-semibold">₹{{ number_format($spent, 2) }}</td>
                            <td>
                                @if($isOver)
                                    <span class="text-danger fw-bold">-₹{{ number_format(abs($diff), 2) }}</span>
                                @else
                                    <span class="text-success fw-bold">₹{{ number_format($diff, 2) }}</span>
                                @endif
                            </td>
                            <td>
                                @if($isOver)
                                    <span class="badge bg-danger">Over Budget</span>
                                @else
                                    <span class="badge bg-success">Under Budget</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">No budgets recorded.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection

