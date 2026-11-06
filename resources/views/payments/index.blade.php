@extends('layouts.app')

@section('title', 'Payments')

@section('content')

    <div class="d-flex justify-content-between mb-3">
        <h3>Payments</h3>

        <a href="{{ route('payments.create') }}" class="btn btn-primary">
            Add Payment
        </a>
    </div>

    <form method="GET" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search reference or method..."
                value="{{ request('search') }}">

            <button class="btn btn-dark" type="submit">Search</button>
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Invoice No</th>
                        <th>Invoice Type</th>
                        <th>Party</th>
                        <th>Amount</th>
                        <th>Payment Date</th>
                        <th>Method</th>
                        <th>Reference</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($payments as $payment)
                        @php
                            $isSales = $payment->payable_type === 'App\Models\SalesInvoice';
                            $invoice = $payment->payable;
                            $partyName = $isSales ? ($invoice->customer->name ?? '-') : ($invoice->supplier->name ?? '-');
                        @endphp
                        <tr>
                            <td>{{ $payments->firstItem() + $loop->index }}</td>
                            <td>
                                @if($invoice)
                                    <a href="{{ $isSales ? route('sales-invoices.show', $invoice) : route('purchase-invoices.show', $invoice) }}" class="text-decoration-none fw-semibold">
                                        {{ $invoice->invoice_no }}
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($isSales)
                                    <span class="badge bg-success">Sales</span>
                                @else
                                    <span class="badge bg-info text-dark">Purchase</span>
                                @endif
                            </td>
                            <td>{{ $partyName }}</td>
                            <td>₹{{ number_format($payment->amount, 2) }}</td>
                            <td>{{ $payment->paid_on ? $payment->paid_on->format('d-m-Y') : '-' }}</td>
                            <td>
                                <span class="badge bg-secondary">
                                    {{ ucfirst($payment->method->value ?? $payment->method) }}
                                </span>
                            </td>
                            <td>{{ $payment->reference ?? '-' }}</td>

                            <td class="d-flex gap-1">
                                <a href="{{ route('payments.show', $payment) }}"
                                    class="btn btn-sm btn-info">View</a>

                                <a href="{{ route('payments.edit', $payment) }}"
                                    class="btn btn-sm btn-warning">Edit</a>

                                <form method="POST" action="{{ route('payments.destroy', $payment) }}"
                                    onsubmit="return confirm('Delete this payment?')">
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
                                No payments found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $payments->links() }}
    </div>

@endsection

