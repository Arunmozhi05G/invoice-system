@extends('layouts.app')

@section('title', 'Edit Expense')

@section('content')

<h3 class="mb-3">Edit Expense</h3>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('expenses.update', $expense) }}">
            @csrf
            @method('PUT')

            @include('expenses._form')
        </form>
    </div>
</div>

@endsection

