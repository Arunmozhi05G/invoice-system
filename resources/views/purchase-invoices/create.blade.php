@extends('layouts.app')

@section('title', 'Create Purchase Invoice')

@section('content')

<h3 class="mb-3">Create Purchase Invoice</h3>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('purchase-invoices.store') }}">
            @csrf

            @include('purchase-invoices._form')
        </form>
    </div>
</div>

@endsection
