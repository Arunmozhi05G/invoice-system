<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'Invoice Management')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('customers.index') }}">
                Invoice Management
            </a>

            <div class="navbar-nav flex-row gap-3">
                <a class="nav-link" href="{{ route('customers.index') }}">Customers</a>
                <a class="nav-link" href="{{ route('suppliers.index') }}">Suppliers</a>
                <a class="nav-link" href="{{ route('products.index') }}">Products</a>
                <a class="nav-link" href="{{ route('purchase-invoices.index') }}">Purchase Invoice</a>
            </div>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="btn btn-sm btn-danger">Logout</button>
            </form>
        </div>
    </nav>

    <main class="container py-4">

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')

    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
