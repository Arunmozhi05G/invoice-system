@extends('layouts.app')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1">Purchase Invoices</h3>
            <p class="text-muted mb-0">Manage your supplier invoices</p>
        </div>
        <a href="{{ route('purchase-invoices.create') }}"
            class="btn btn-primary">
            + Create Invoice
        </a>
    </div>

    <div class="card">
        <div class="card-body">

            <form method="GET" class="row g-2 mb-3">
                <div class="col-md-5">
                    <input type="text" name="search" class="form-control"
                        placeholder="Search invoice, bill or supplier"
                        value="{{ request('search') }}">
                </div>
                <div class="col-auto">
                    <button class="btn btn-dark">Search</button>
                    <a href="{{ route('purchase-invoices.index') }}"
                        class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Invoice No</th>
                            <th>Supplier</th>
                            <th>Bill No</th>
                            <th>Invoice Date</th>
                            <th>Due Date</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $invoice)
                            <tr>
                                <td>{{ $invoice->invoice_no }}</td>
                                <td>{{ $invoice->supplier->name ?? '-' }}</td>
                                <td>{{ $invoice->bill_no ?? '-' }}</td>
                                <td>{{ $invoice->invoice_date->format('d M Y') }}</td>
                                <td>{{ $invoice->due_date->format('d M Y') }}</td>
                                <td>₹{{ number_format($invoice->grand_total, 2) }}</td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('purchase-invoices.show', $invoice) }}"
                                            class="btn btn-info btn-sm">View</a>
                                        <a href="{{ route('purchase-invoices.edit', $invoice) }}"
                                            class="btn btn-warning btn-sm">Edit</a>
                                        <form action="{{ route('purchase-invoices.destroy', $invoice) }}"
                                            method="POST"
                                            onsubmit="return confirm('Delete this invoice?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    No purchase invoices found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <small class="text-muted">
                    Showing {{ $invoices->firstItem() ?? 0 }}
                    to {{ $invoices->lastItem() ?? 0 }}
                    of {{ $invoices->total() }} invoices
                </small>
                {{ $invoices->links() }}
            </div>
        </div>
    </div>
</div>
@endsection