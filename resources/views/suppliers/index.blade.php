@extends('layouts.app')

@section('title', 'Suppliers')

@section('content')

    <div class="d-flex justify-content-between mb-3">
        <h3>Suppliers</h3>

        <a href="{{ route('suppliers.create') }}" class="btn btn-primary">
            Add Supplier
        </a>
    </div>

    <form method="GET" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search supplier..."
                value="{{ request('search') }}">

            <button class="btn btn-dark">Search</button>
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>GSTIN</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($suppliers as $supplier)
                        <tr>
                            <td>{{ $suppliers->firstItem() + $loop->index }}</td>
                            <td>{{ $supplier->name }}</td>
                            <td>{{ $supplier->email ?? '-' }}</td>
                            <td>{{ $supplier->phone ?? '-' }}</td>
                            <td>{{ $supplier->gstin ?? '-' }}</td>

                            <td class="d-flex gap-1">
                                <a href="{{ route('suppliers.show', $supplier) }}" class="btn btn-sm btn-info">View</a>

                                <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-sm btn-warning">Edit</a>

                                <form method="POST" action="{{ route('suppliers.destroy', $supplier) }}"
                                    onsubmit="return confirm('Delete this supplier?')">

                                    @csrf
                                    @method('DELETE')

                                    <button class="btn btn-sm btn-danger">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                No suppliers found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        </div>
    </div>

    <div class="mt-3">
        {{ $suppliers->links() }}
    </div>

@endsection
