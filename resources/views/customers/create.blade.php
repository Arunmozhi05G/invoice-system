@extends('layouts.app')

@section('title', 'Create Customer')

@section('content')

<h3 class="mb-3">Create Customer</h3>

<div class="card shadow-sm">
    <div class="card-body">

        <form method="POST" action="{{ route('customers.store') }}">
            @csrf

            @include('customers._form')
        </form>

    </div>
</div>

@endsection