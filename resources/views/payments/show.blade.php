@extends('layouts.app')

@section('title', 'Payment Details')

@section('content')

    @php
        $isSales = $payment->payable_type === 'App\Models\SalesInvoice';
        $invoice = $payment->payable;
        $partyLabel = $isSales ? 'Customer' : 'Supplier';
        $partyName = $isSales ? ($invoice->customer->name ?? '-') : ($invoice->supplier->name ?? '-');
    @endphp

    <div class="d-flex justify-content-between mb-3">
        <h3>Payment Details</h3>
        <div>
            <a href="{{ route('payments.edit', $payment) }}" class="btn btn-warning">Edit</a>
            <a href="{{ route('payments.index') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-2"><strong>Amount Paid:</strong> <span class="fs-5 text-success fw-bold">₹{{ number_format($payment->amount, 2) }}</span></p>
                    <p class="mb-2"><strong>Payment Date:</strong> {{ $payment->paid_on ? $payment->paid_on->format('d-m-Y') : '-' }}</p>
                    <p class="mb-2"><strong>Payment Method:</strong> {{ ucfirst($payment->method->value ?? $payment->method) }}</p>
                    <p class="mb-2"><strong>Reference / Note:</strong> {{ $payment->reference ?? '-' }}</p>
                </div>

                <div class="col-md-6">
                    @if($invoice)
                        <p class="mb-2"><strong>Invoice Number:</strong> 
                            <a href="{{ $isSales ? route('sales-invoices.show', $invoice) : route('purchase-invoices.show', $invoice) }}" class="fw-semibold">
                                {{ $invoice->invoice_no }} ({{ $isSales ? 'Sales' : 'Purchase' }})
                            </a>
                        </p>
                        <p class="mb-2"><strong>{{ $partyLabel }}:</strong> {{ $partyName }}</p>
                        <p class="mb-2"><strong>Invoice Grand Total:</strong> ₹{{ number_format($invoice->grand_total, 2) }}</p>
                        <p class="mb-2"><strong>Total Paid to Date:</strong> ₹{{ number_format($invoice->paid_amount, 2) }}</p>
                        <p class="mb-2"><strong>Remaining Balance:</strong> ₹{{ number_format(max(0, $invoice->grand_total - $invoice->paid_amount), 2) }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection

