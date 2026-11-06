@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Edit Purchase Invoice</h3>
        <a href="{{ route('purchase-invoices.index') }}" class="btn btn-secondary">
            Back
        </a>
    </div>

    @include('purchase-invoices._form', ['invoice' => $purchaseInvoice])
</div>
@endsection