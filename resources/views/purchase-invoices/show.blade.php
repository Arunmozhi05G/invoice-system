@extends('layouts.app')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Purchase Invoice Details</h3>
        <div class="d-flex gap-2">
            <a href="{{ route('purchase-invoices.edit', $invoice) }}"
                class="btn btn-warning">Edit</a>
            <a href="{{ route('purchase-invoices.index') }}"
                class="btn btn-secondary">Back</a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <strong>Invoice Number:</strong>
                    <p>{{ $invoice->invoice_no }}</p>
                </div>
                <div class="col-md-6">
                    <strong>Supplier:</strong>
                    <p>{{ $invoice->supplier->name ?? '-' }}</p>
                </div>
                <div class="col-md-6">
                    <strong>Supplier Bill Number:</strong>
                    <p>{{ $invoice->bill_no ?? '-' }}</p>
                </div>
                <div class="col-md-6">
                    <strong>Invoice Date:</strong>
                    <p>{{ $invoice->invoice_date->format('d M Y') }}</p>
                </div>
                <div class="col-md-6">
                    <strong>Due Date:</strong>
                    <p>{{ $invoice->due_date->format('d M Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header fw-semibold">Invoice Items</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Unit Price</th>
                            <th>Discount</th>
                            <th>Tax %</th>
                            <th>Tax Amount</th>
                            <th>Line Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->items as $item)
                            <tr>
                                <td>{{ $item->description ?? $item->product->name ?? '-' }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>₹{{ number_format($item->unit_price, 2) }}</td>
                                <td>₹{{ number_format($item->discount, 2) }}</td>
                                <td>{{ $item->tax_rate }}%</td>
                                <td>₹{{ number_format($item->tax_amount, 2) }}</td>
                                <td>₹{{ number_format($item->line_total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="row justify-content-end">
                <div class="col-md-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal</span>
                        <strong>₹{{ number_format($invoice->subtotal, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Discount</span>
                        <strong>₹{{ number_format($invoice->discount, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Tax</span>
                        <strong>₹{{ number_format($invoice->tax_total, 2) }}</strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <strong>Grand Total</strong>
                        <strong>₹{{ number_format($invoice->grand_total, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mt-2">
                        <span>Paid Amount</span>
                        <strong>₹{{ number_format($invoice->paid_amount, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mt-2">
                        <span>Balance</span>
                        <strong>₹{{ number_format($invoice->grand_total - $invoice->paid_amount, 2) }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($invoice->notes)
        <div class="card mb-4">
            <div class="card-header fw-semibold">Notes</div>
            <div class="card-body">{{ $invoice->notes }}</div>
        </div>
    @endif

</div>
@endsection