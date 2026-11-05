<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <title>Admin Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

    <nav class="navbar navbar-dark bg-dark">
        <div class="container">

            <span class="navbar-brand">Admin Dashboard</span>

            <form action="{{ route('logout') }}" method="POST">
                @csrf

                <button type="submit" class="btn btn-danger">
                    Logout
                </button>
            </form>

        </div>
    </nav>

    <div class="container mt-5">

        <div class="card shadow-sm">
            <div class="card-body">

                <h3>Welcome, {{ auth()->user()->name }}!</h3>

                <p>You have successfully logged in.</p>

            </div>
        </div>

    </div>

</body>
</html>