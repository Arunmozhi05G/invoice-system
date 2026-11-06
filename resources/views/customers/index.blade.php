@extends('layouts.app')

@section('title', 'Customers')

@section('content')

    <div class="d-flex justify-content-between mb-3">
        <h3>Customers</h3>

        <a href="{{ route('customers.create') }}" class="btn btn-primary">
            Add Customer
        </a>
    </div>

    <form method="GET" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search customer..."
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
                    @forelse($customers as $customer)
                        <tr>
                            <td>{{ $customers->firstItem() + $loop->index }}</td>
                            <td>{{ $customer->name }}</td>
                            <td>{{ $customer->email ?? '-' }}</td>
                            <td>{{ $customer->phone ?? '-' }}</td>
                            <td>{{ $customer->gstin ?? '-' }}</td>

                            <td class="d-flex gap-1">
                                <a href="{{ route('customers.show', $customer) }}" class="btn btn-sm btn-info">View</a>

                                <a href="{{ route('customers.edit', $customer) }}" class="btn btn-sm btn-warning">Edit</a>

                                <form method="POST" action="{{ route('customers.destroy', $customer) }}"
                                    onsubmit="return confirm('Delete this customer?')">

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
                                No customers found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>  

    <div class="mt-3">
        {{ $customers->links() }}
    </div>

@endsection
