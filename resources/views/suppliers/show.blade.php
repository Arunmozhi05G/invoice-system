@extends('layouts.app')

@section('title', 'Supplier Details')

@section('content')

<div class="card shadow-sm">
    <div class="card-body">

        <h3>{{ $supplier->name }}</h3>

        <hr>

        <p><strong>Email:</strong> {{ $supplier->email ?? '-' }}</p>
        <p><strong>Phone:</strong> {{ $supplier->phone ?? '-' }}</p>
        <p><strong>GSTIN:</strong> {{ $supplier->gstin ?? '-' }}</p>
        <p><strong>Address:</strong> {{ $supplier->address ?? '-' }}</p>

        <a href="{{ route('suppliers.edit', $supplier) }}"
           class="btn btn-warning">Edit</a>

        <a href="{{ route('suppliers.index') }}"
           class="btn btn-secondary">Back</a>

    </div>
</div>

@endsection