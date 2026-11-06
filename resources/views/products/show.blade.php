@extends('layouts.app')

@section('title', 'Product Details')

@section('content')

<div class="d-flex justify-content-between mb-3">
    <h3>Product Details</h3>
    <div>
        <a href="{{ route('products.edit', $product) }}" class="btn btn-warning">Edit</a>
        <a href="{{ route('products.index') }}" class="btn btn-secondary">Back</a>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <h4>{{ $product->name }}</h4>
        <hr>

        <p><strong>Type:</strong> {{ ucfirst($product->type->value) }}</p>
        <p><strong>Unit:</strong> {{ $product->unit ?? '-' }}</p>
        <p><strong>Price:</strong> ₹{{ number_format($product->price, 2) }}</p>
        <p><strong>GST:</strong> {{ $product->tax_rate }}%</p>
    </div>
</div>

@endsection
