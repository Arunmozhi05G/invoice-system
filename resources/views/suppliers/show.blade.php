@extends('layouts.app')

@section('title', 'Supplier Details')

@section('content')

<div class="d-flex justify-content-between mb-3">
    <h3>Supplier Details</h3>
    <div>
        <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-warning">Edit</a>
        <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Back</a>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <h4>{{ $supplier->name }}</h4>
        <hr>

        <p><strong>Email:</strong> {{ $supplier->email ?? '-' }}</p>
        <p><strong>Phone:</strong> {{ $supplier->phone ?? '-' }}</p>
        <p><strong>GSTIN:</strong> {{ $supplier->gstin ?? '-' }}</p>
        <p><strong>Address:</strong> {{ $supplier->address ?? '-' }}</p>
    </div>
</div>

@endsection
