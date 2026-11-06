@extends('layouts.app')

@section('title', 'Product Details')

@section('content')

<div class="card shadow-sm">
    <div class="card-body">

        <h3>{{ $product->name }}</h3>

        <hr>

        <p><strong>Type:</strong> {{ ucfirst($product->type->value) }}</p>
        <p><strong>Unit:</strong> {{ $product->unit ?? '-' }}</p>
        <p><strong>Price:</strong> ₹{{ $product->price }}</p>
        <p><strong>GST:</strong> {{ $product->tax_rate }}%</p>

        <a href="{{ route('products.edit', $product) }}"
           class="btn btn-warning">Edit</a>

        <a href="{{ route('products.index') }}"
           class="btn btn-secondary">Back</a>

    </div>
</div>

@endsection