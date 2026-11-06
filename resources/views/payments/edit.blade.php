@extends('layouts.app')

@section('title', 'Edit Payment')

@section('content')

<h3 class="mb-3">Edit Payment</h3>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('payments.update', $payment) }}">
            @csrf
            @method('PUT')

            @include('payments._form')
        </form>
    </div>
</div>

@endsection

