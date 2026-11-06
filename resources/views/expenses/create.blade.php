@extends('layouts.app')

@section('title', 'Record Expense')

@section('content')

<h3 class="mb-3">Record Expense</h3>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('expenses.store') }}">
            @csrf

            @include('expenses._form')
        </form>
    </div>
</div>

@endsection

