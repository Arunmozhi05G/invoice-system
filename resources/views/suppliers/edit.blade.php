@extends('layouts.app')

@section('title', 'Edit Supplier')

@section('content')

<h3 class="mb-3">Edit Supplier</h3>

<div class="card shadow-sm">
    <div class="card-body">

        <form method="POST" action="{{ route('suppliers.update', $supplier) }}">
            @csrf
            @method('PUT')

            @include('suppliers._form')

        </form>

    </div>
</div>

@endsection