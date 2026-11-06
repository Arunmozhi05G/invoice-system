@extends('layouts.app')

@section('title', 'Purchase Invoices')

@section('content')

    <div class="d-flex justify-content-between mb-3">
        <h3>Purchase Invoices</h3>

        <a href="{{ route('purchase-invoices.create') }}" class="btn btn-primary">
            Add Purchase Invoice
        </a>
    </div>

    <form method="GET" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search invoice, bill or supplier..."
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
                        <th>Supplier</th>
                        <th>Bill No</th>
                        <th>Invoice Date</th>
                        <th>Due Date</th>
                        <th>Total</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($invoices as $invoice)
                        <tr>
                            <td>{{ $invoices->firstItem() + $loop->index }}</td>
                            <td>{{ $invoice->invoice_no }}</td>
                            <td>{{ $invoice->supplier->name ?? '-' }}</td>
                            <td>{{ $invoice->bill_no ?? '-' }}</td>
                            <td>{{ $invoice->invoice_date->format('d-m-Y') }}</td>
                            <td>{{ $invoice->due_date->format('d-m-Y') }}</td>
                            <td>₹{{ number_format($invoice->grand_total, 2) }}</td>

                            <td class="d-flex gap-1">
                                <a href="{{ route('purchase-invoices.show', $invoice) }}"
                                    class="btn btn-sm btn-info">View</a>

                                <a href="{{ route('purchase-invoices.edit', $invoice) }}"
                                    class="btn btn-sm btn-warning">Edit</a>

                                <form method="POST" action="{{ route('purchase-invoices.destroy', $invoice) }}"
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
                            <td colspan="8" class="text-center py-4">
                                No purchase invoices found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $invoices->links() }}
    </div>

@endsection
