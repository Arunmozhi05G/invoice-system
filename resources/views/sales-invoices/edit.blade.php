@extends('layouts.app')

@section('title', 'Edit Sales Invoice')

@section('content')

<h3 class="mb-3">Edit Sales Invoice</h3>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('sales-invoices.update', $salesInvoice) }}">
            @csrf
            @method('PUT')

            @include('sales-invoices._form', ['invoice' => $salesInvoice])
        </form>
    </div>
</div>

@endsection
