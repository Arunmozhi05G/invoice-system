@extends('layouts.app')

@section('title', 'Create Supplier')

@section('content')

<h3 class="mb-3">Create Supplier</h3>

<div class="card shadow-sm">
    <div class="card-body">

        <form method="POST" action="{{ route('suppliers.store') }}">
            @csrf

            @include('suppliers._form')
        </form>

    </div>
</div>

@endsection