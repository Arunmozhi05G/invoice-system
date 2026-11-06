@extends('layouts.app')

@section('title', 'Customer Details')

@section('content')

<div class="card shadow-sm">
    <div class="card-body">

        <h3>{{ $customer->name }}</h3>

        <hr>

        <p><strong>Email:</strong> {{ $customer->email ?? '-' }}</p>
        <p><strong>Phone:</strong> {{ $customer->phone ?? '-' }}</p>
        <p><strong>GSTIN:</strong> {{ $customer->gstin ?? '-' }}</p>
        <p><strong>Address:</strong> {{ $customer->address ?? '-' }}</p>

        <a href="{{ route('customers.edit', $customer) }}"
           class="btn btn-warning">Edit</a>

        <a href="{{ route('customers.index') }}"
           class="btn btn-secondary">Back</a>

    </div>
</div>

@endsection