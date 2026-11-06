@extends('layouts.app')

@section('title', 'Create Budget')

@section('content')

<h3 class="mb-3">Create Budget</h3>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('budgets.store') }}">
            @csrf

            @include('budgets._form')
        </form>
    </div>
</div>

@endsection

