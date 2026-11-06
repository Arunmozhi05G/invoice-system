@extends('layouts.app')

@section('title', 'Create Sales Invoice')

@section('content')

<h3 class="mb-3">Create Sales Invoice</h3>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('sales-invoices.store') }}">
            @csrf

            @include('sales-invoices._form')
        </form>
    </div>
</div>

@endsection
