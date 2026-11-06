@extends('layouts.app')

@section('title', 'Edit Customer')

@section('content')

<h3 class="mb-3">Edit Customer</h3>

<div class="card shadow-sm">
    <div class="card-body">

        <form method="POST" action="{{ route('customers.update', $customer) }}">
            @csrf
            @method('PUT')

            @include('customers._form')

        </form>

    </div>
</div>

@endsection