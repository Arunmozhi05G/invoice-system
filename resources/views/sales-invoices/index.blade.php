@extends('layouts.app')

@section('title', 'Sales Invoices')

@section('content')

    <div class="d-flex justify-content-between mb-3">
        <h3>Sales Invoices</h3>

        <a href="{{ route('sales-invoices.create') }}" class="btn btn-primary">
            Add Sales Invoice
        </a>
    </div>

    <form method="GET" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search invoice or customer..."
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
                        <th>Customer</th>
                        <th>Invoice Date</th>
                        <th>Due Date</th>
                        <th>Total</th>
                        <th>Paid</th>
                        <th>Balance</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($salesInvoices as $invoice)
                        <tr>
                            <td>{{ $salesInvoices->firstItem() + $loop->index }}</td>
                            <td>{{ $invoice->invoice_no }}</td>
                            <td>{{ $invoice->customer->name ?? '-' }}</td>
                            <td>{{ $invoice->invoice_date->format('d-m-Y') }}</td>
                            <td>{{ $invoice->due_date->format('d-m-Y') }}</td>
                            <td>₹{{ number_format($invoice->grand_total, 2) }}</td>
                            <td>₹{{ number_format($invoice->paid_amount, 2) }}</td>
                            <td>₹{{ number_format(max(0, $invoice->grand_total - $invoice->paid_amount), 2) }}</td>

                            <td class="d-flex gap-1">
                                <a href="{{ route('sales-invoices.show', $invoice) }}"
                                    class="btn btn-sm btn-info">View</a>

                                <a href="{{ route('sales-invoices.edit', $invoice) }}"
                                    class="btn btn-sm btn-warning">Edit</a>

                                <form method="POST" action="{{ route('sales-invoices.destroy', $invoice) }}"
                                    onsubmit="return confirm('Delete this invoice?')">
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
                                No sales invoices found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $salesInvoices->links() }}
    </div>

@endsection
