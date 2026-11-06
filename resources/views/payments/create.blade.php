@extends('layouts.app')

@section('title', 'Record Payment')

@section('content')

<h3 class="mb-3">Record Payment</h3>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('payments.store') }}">
            @csrf

            @include('payments._form')
        </form>
    </div>
</div>

@endsection

