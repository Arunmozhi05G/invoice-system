@extends('layouts.app')

@section('title', 'Sales Invoice Details')

@section('content')

    <div class="d-flex justify-content-between mb-3">
        <h3>Sales Invoice Details</h3>
        <div class="d-flex gap-2">
            @if($salesInvoice->grand_total - $salesInvoice->paid_amount > 0)
                <a href="{{ route('payments.create', ['invoice_type' => 'sales', 'invoice_id' => $salesInvoice->id]) }}" class="btn btn-success">
                    + Add Payment
                </a>
            @endif
            @if(!$salesInvoice->payments()->exists())
                <a href="{{ route('sales-invoices.edit', $salesInvoice) }}" class="btn btn-warning">Edit</a>
            @endif
            <a href="{{ route('sales-invoices.index') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>{{ $salesInvoice->invoice_no }}</h5>
                    <p class="mb-1"><strong>Customer:</strong> {{ $salesInvoice->customer->name ?? '-' }}</p>
                    <p class="mb-1"><strong>Email:</strong> {{ $salesInvoice->customer->email ?? '-' }}</p>
                    <p class="mb-1"><strong>Phone:</strong> {{ $salesInvoice->customer->phone ?? '-' }}</p>
                </div>
                <div class="col-md-6">
                    <p class="mb-1"><strong>Invoice Date:</strong> {{ $salesInvoice->invoice_date->format('d-m-Y') }}</p>
                    <p class="mb-1"><strong>Due Date:</strong> {{ $salesInvoice->due_date->format('d-m-Y') }}</p>
                    <p class="mb-1"><strong>Status:</strong>
                        @if($salesInvoice->paid_amount >= $salesInvoice->grand_total)
                            <span class="badge bg-success">Paid</span>
                        @elseif($salesInvoice->due_date->isPast())
                            <span class="badge bg-danger">Overdue</span>
                        @else
                            <span class="badge bg-warning text-dark">Pending</span>
                        @endif
                    </p>
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
                    @foreach($salesInvoice->items as $item)
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
                    <td class="text-end">₹{{ number_format($salesInvoice->subtotal, 2) }}</td>
                </tr>
                <tr>
                    <th>Discount</th>
                    <td class="text-end">₹{{ number_format($salesInvoice->discount, 2) }}</td>
                </tr>
                <tr>
                    <th>Tax</th>
                    <td class="text-end">₹{{ number_format($salesInvoice->tax_total, 2) }}</td>
                </tr>
                <tr class="table-light">
                    <th>Grand Total</th>
                    <th class="text-end">₹{{ number_format($salesInvoice->grand_total, 2) }}</th>
                </tr>
                <tr>
                    <th>Paid Amount</th>
                    <td class="text-end">₹{{ number_format($salesInvoice->paid_amount, 2) }}</td>
                </tr>
                <tr>
                    <th>Balance</th>
                    <th class="text-end">
                        ₹{{ number_format(max(0, $salesInvoice->grand_total - $salesInvoice->paid_amount), 2) }}
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
                    @forelse($salesInvoice->payments as $payment)
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

    @if($salesInvoice->notes)
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <strong>Notes:</strong>
                <p class="mb-0">{{ $salesInvoice->notes }}</p>
            </div>
        </div>
    @endif

@endsection
