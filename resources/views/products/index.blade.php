@extends('layouts.app')

@section('title', 'Products')

@section('content')

    <div class="d-flex justify-content-between mb-3">
        <h3>Products</h3>

        <a href="{{ route('products.create') }}" class="btn btn-primary">
            Add Product
        </a>
    </div>

    <form method="GET" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search product..."
                value="{{ request('search') }}">

            <button class="btn btn-dark" type="submit">Search</button>
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Unit</th>
                        <th>Price</th>
                        <th>GST (%)</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td>{{ $products->firstItem() + $loop->index }}</td>
                            <td>{{ $product->name }}</td>
                            <td>{{ ucfirst($product->type->value) }}</td>
                            <td>{{ $product->unit ?? '-' }}</td>
                            <td>₹{{ number_format($product->price, 2) }}</td>
                            <td>{{ $product->tax_rate }}%</td>

                            <td class="d-flex gap-1">
                                <a href="{{ route('products.show', $product) }}"
                                    class="btn btn-sm btn-info">View</a>

                                <a href="{{ route('products.edit', $product) }}"
                                    class="btn btn-sm btn-warning">Edit</a>

                                <form method="POST" action="{{ route('products.destroy', $product) }}"
                                    onsubmit="return confirm('Delete this product?')">
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
                            <td colspan="7" class="text-center py-4">
                                No products found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $products->links() }}
    </div>

@endsection
