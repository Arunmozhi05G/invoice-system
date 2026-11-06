@extends('layouts.app')

@section('title', 'Edit Product')

@section('content')

<h3 class="mb-3">Edit Product</h3>

<div class="card shadow-sm">
    <div class="card-body">

        <form method="POST" action="{{ route('products.update', $product) }}">
            @csrf
            @method('PUT')

            @include('products._form')

        </form>

    </div>
</div>

@endsection