@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3>Dashboard</h3>
        </div>
        <span class="badge bg-dark py-2 px-3">
            Logged in as: {{ auth()->user()->name }}
        </span>
    </div>

    {{-- Financial Summary Cards --}}
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
                    <span class="text-muted small fw-semibold">TOTAL EXPENSES</span>
                    <h4 class="mt-2 text-warning fw-bold">₹{{ number_format($totalExpenses, 2) }}</h4>
                    <div class="small text-muted mt-2">
                        <span>Budgets:</span><br>
                        <span>₹{{ number_format($totalBudgets, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Invoice Payment Status Tracker --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body py-3">
            <div class="row text-center align-items-center">
                <div class="col-md-3 text-md-start">
                    <h6 class="mb-0 fw-bold">Sales Invoice Status</h6>
                </div>
                <div class="col-md-3">
                    <span class="badge bg-success fs-6 px-3 py-2">Paid: {{ $paidInvoicesCount }}</span>
                </div>
                <div class="col-md-3">
                    <span class="badge bg-warning text-dark fs-6 px-3 py-2">Pending: {{ $pendingInvoicesCount }}</span>
                </div>
                <div class="col-md-3">
                    <span class="badge bg-danger fs-6 px-3 py-2">Overdue: {{ $overdueInvoicesCount }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Public API Integration: Live Currency Exchange Rates --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <h6 class="mb-0 fw-bold">Live Currency Exchange Rates</h6>
                <small class="text-muted">Public API Integration &bull; Foreign Currencies to INR (₹) &bull; Updated:
                    {{ $exchangeRates['last_updated'] }}</small>
            </div>
        </div>
        <div class="card-body">
            <div class="row g-2 text-center mb-3">
                @foreach ($exchangeRates['rates'] as $cur => $rate)
                    <div class="col-6 col-md-2">
                        <div class="p-2 border rounded bg-light">
                            <span class="text-muted small fw-semibold d-block">1 {{ $cur }}</span>
                            <span class="fs-6 fw-bold text-dark">₹{{ number_format($rate, 2) }}</span>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Quick Currency Calculator --}}
            <div class="p-3 bg-light rounded border">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">

                    <!-- Left Corner -->
                    <div>
                        <span class="fw-semibold small text-muted d-block mb-1">
                            Quick Converter:
                        </span>
                        <div class="input-group input-group-sm" style="width: 220px;">
                            <input type="number" id="convert_amount" class="form-control" value="100" min="1"
                                step="any">

                            <select id="convert_currency" class="form-select" style="max-width: 90px;">
                                @foreach ($exchangeRates['rates'] as $cur => $rate)
                                    <option value="{{ $rate }}">{{ $cur }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Right Corner -->
                    <div class="text-end">
                        <span class="fw-semibold small text-muted d-block mb-1">
                            Converted Value (INR):
                        </span>
                        <span id="convert_result" class="fs-5 fw-bold text-success">
                            ₹0.00
                        </span>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- Recent Activities --}}
    <div class="row g-3 mb-4">
        {{-- Recent Sales Invoices --}}
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <span class="fw-semibold">Recent Sales Invoices</span>
                    <a href="{{ route('sales-invoices.index') }}" class="btn btn-sm btn-light">View All</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Invoice</th>
                                <th>Customer</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentSales as $sale)
                                @php
                                    $isPaid = $sale->paid_amount >= $sale->grand_total;
                                    $isOverdue = !$isPaid && $sale->due_date->isPast();
                                @endphp
                                <tr>
                                    <td>
                                        <a href="{{ route('sales-invoices.show', $sale) }}"
                                            class="fw-semibold text-decoration-none">
                                            {{ $sale->invoice_no }}
                                        </a>
                                    </td>
                                    <td>{{ $sale->customer->name ?? '-' }}</td>
                                    <td>₹{{ number_format($sale->grand_total, 2) }}</td>
                                    <td>
                                        @if ($isPaid)
                                            <span class="badge bg-success">Paid</span>
                                        @elseif($isOverdue)
                                            <span class="badge bg-danger">Overdue</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-3 text-muted">No sales invoices yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Recent Expenses --}}
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <span class="fw-semibold">Recent Expenses</span>
                    <a href="{{ route('expenses.index') }}" class="btn btn-sm btn-light">View All</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Title</th>
                                <th>Budget</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentExpenses as $exp)
                                <tr>
                                    <td>{{ $exp->expense_date ? $exp->expense_date->format('d-m-Y') : '-' }}</td>
                                    <td>
                                        <a href="{{ route('expenses.show', $exp) }}"
                                            class="fw-semibold text-decoration-none">
                                            {{ $exp->title }}
                                        </a>
                                    </td>
                                    <td>{{ $exp->budget->name ?? '-' }}</td>
                                    <td class="text-danger fw-semibold">₹{{ number_format($exp->amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-3 text-muted">No expenses recorded yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Module Navigation Buttons --}}
    <div class="card shadow-sm">
        <div class="card-body">
            <h6 class="fw-bold mb-3">Quick Navigation</h6>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('customers.index') }}" class="btn btn-outline-dark btn-sm">Customers</a>
                <a href="{{ route('suppliers.index') }}" class="btn btn-outline-dark btn-sm">Suppliers</a>
                <a href="{{ route('products.index') }}" class="btn btn-outline-dark btn-sm">Products</a>
                <a href="{{ route('purchase-invoices.index') }}" class="btn btn-outline-dark btn-sm">Purchase
                    Invoices</a>
                <a href="{{ route('sales-invoices.index') }}" class="btn btn-outline-dark btn-sm">Sales Invoices</a>
                <a href="{{ route('payments.index') }}" class="btn btn-outline-dark btn-sm">Payments</a>
                <a href="{{ route('budgets.index') }}" class="btn btn-outline-dark btn-sm">Budgets</a>
                <a href="{{ route('expenses.index') }}" class="btn btn-outline-dark btn-sm">Expenses</a>
                <a href="{{ route('reports.index') }}" class="btn btn-dark btn-sm">Reports & Analytics</a>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const amountInput = document.getElementById('convert_amount');
            const currencySelect = document.getElementById('convert_currency');
            const resultSpan = document.getElementById('convert_result');

            function updateConversion() {
                if (!amountInput || !currencySelect || !resultSpan) return;
                const amount = parseFloat(amountInput.value) || 0;
                const rate = parseFloat(currencySelect.value) || 0;
                const inr = amount * rate;
                resultSpan.textContent = '₹' + inr.toLocaleString('en-IN', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }

            if (amountInput && currencySelect) {
                amountInput.addEventListener('input', updateConversion);
                currencySelect.addEventListener('change', updateConversion);
                updateConversion();
            }
        });
    </script>
@endpush
