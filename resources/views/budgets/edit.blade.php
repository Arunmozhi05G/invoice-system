@extends('layouts.app')

@section('title', 'Edit Budget')

@section('content')

<h3 class="mb-3">Edit Budget</h3>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('budgets.update', $budget) }}">
            @csrf
            @method('PUT')

            @include('budgets._form')
        </form>
    </div>
</div>

@endsection

