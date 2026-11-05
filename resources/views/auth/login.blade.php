<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Login</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">

        <div class="col-md-5 col-lg-4">

            <div class="card shadow border-0">

                <div class="card-body p-4">

                    <h3 class="text-center mb-4">Admin Login</h3>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login.submit') }}">

                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Email Address</label>

                            <input
                                type="email"
                                name="email"
                                class="form-control"
                                value="{{ old('email') }}"
                                placeholder="Enter your email"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password</label>

                            <input
                                type="password"
                                name="password"
                                class="form-control"
                                placeholder="Enter your password"
                                required
                            >
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            Login
                        </button>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>