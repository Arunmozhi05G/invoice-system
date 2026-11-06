@extends('layouts.app')

@section('title', 'Create Product')

@section('content')

<h3 class="mb-3">Create Product / Service</h3>

<div class="card shadow-sm">
    <div class="card-body">

        <form method="POST" action="{{ route('products.store') }}">
            @csrf

            @include('products._form')

        </form>

    </div>
</div>

@endsection