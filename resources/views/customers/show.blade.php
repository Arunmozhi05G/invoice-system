@extends('layouts.app')

@section('title', 'Customer Details')

@section('content')

<div class="d-flex justify-content-between mb-3">
    <h3>Customer Details</h3>
    <div>
        <a href="{{ route('customers.edit', $customer) }}" class="btn btn-warning">Edit</a>
        <a href="{{ route('customers.index') }}" class="btn btn-secondary">Back</a>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <h4>{{ $customer->name }}</h4>
        <hr>

        <p><strong>Email:</strong> {{ $customer->email ?? '-' }}</p>
        <p><strong>Phone:</strong> {{ $customer->phone ?? '-' }}</p>
        <p><strong>GSTIN:</strong> {{ $customer->gstin ?? '-' }}</p>
        <p><strong>Address:</strong> {{ $customer->address ?? '-' }}</p>
    </div>
</div>

@endsection
