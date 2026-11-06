@extends('layouts.app')

@section('title', 'Purchase Invoice Details')

@section('content')

    <div class="d-flex justify-content-between mb-3">
        <h3>Purchase Invoice Details</h3>
        <div class="d-flex gap-2">
            @if($purchaseInvoice->grand_total - $purchaseInvoice->paid_amount > 0)
                <a href="{{ route('payments.create', ['invoice_type' => 'purchase', 'invoice_id' => $purchaseInvoice->id]) }}" class="btn btn-success">
                    + Add Payment
                </a>
            @endif
            <a href="{{ route('purchase-invoices.edit', $purchaseInvoice) }}" class="btn btn-warning">Edit</a>
            <a href="{{ route('purchase-invoices.index') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>{{ $purchaseInvoice->invoice_no }}</h5>
                    <p class="mb-1"><strong>Supplier:</strong> {{ $purchaseInvoice->supplier->name ?? '-' }}</p>
                    <p class="mb-1"><strong>Bill No:</strong> {{ $purchaseInvoice->bill_no ?? '-' }}</p>
                </div>
                <div class="col-md-6">
                    <p class="mb-1"><strong>Invoice Date:</strong> {{ $purchaseInvoice->invoice_date->format('d-m-Y') }}</p>
                    <p class="mb-1"><strong>Due Date:</strong> {{ $purchaseInvoice->due_date->format('d-m-Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-dark text-white fw-semibold">Invoice Items</div>
        <div class="table-responsive">
            <table class="table table-bordered mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Product</th>
                        <th>Description</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Discount</th>
                        <th>Tax %</th>
                        <th>Tax Amount</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchaseInvoice->items as $item)
                        <tr>
                            <td>{{ $item->product->name ?? '-' }}</td>
                            <td>{{ $item->description ?? '-' }}</td>
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
    </div>

    <div class="row justify-content-end mb-4">
        <div class="col-md-4">
            <table class="table table-bordered">
                <tr>
                    <th>Subtotal</th>
                    <td class="text-end">₹{{ number_format($purchaseInvoice->subtotal, 2) }}</td>
                </tr>
                <tr>
                    <th>Discount</th>
                    <td class="text-end">₹{{ number_format($purchaseInvoice->discount, 2) }}</td>
                </tr>
                <tr>
                    <th>Tax</th>
                    <td class="text-end">₹{{ number_format($purchaseInvoice->tax_total, 2) }}</td>
                </tr>
                <tr class="table-light">
                    <th>Grand Total</th>
                    <th class="text-end">₹{{ number_format($purchaseInvoice->grand_total, 2) }}</th>
                </tr>
                <tr>
                    <th>Paid Amount</th>
                    <td class="text-end">₹{{ number_format($purchaseInvoice->paid_amount, 2) }}</td>
                </tr>
                <tr>
                    <th>Balance</th>
                    <th class="text-end">
                        ₹{{ number_format(max(0, $purchaseInvoice->grand_total - $purchaseInvoice->paid_amount), 2) }}
                    </th>
                </tr>
            </table>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-dark text-white fw-semibold">Payment History</div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Method</th>
                        <th>Reference</th>
                        <th>Amount</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchaseInvoice->payments as $payment)
                        <tr>
                            <td>{{ $payment->paid_on ? $payment->paid_on->format('d-m-Y') : '-' }}</td>
                            <td><span class="badge bg-secondary">{{ ucfirst($payment->method->value ?? $payment->method) }}</span></td>
                            <td>{{ $payment->reference ?? '-' }}</td>
                            <td>₹{{ number_format($payment->amount, 2) }}</td>
                            <td>
                                <a href="{{ route('payments.show', $payment) }}" class="btn btn-sm btn-info">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-3 text-muted">No payments recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($purchaseInvoice->notes)
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <strong>Notes:</strong>
                <p class="mb-0">{{ $purchaseInvoice->notes }}</p>
            </div>
        </div>
    @endif

@endsection
