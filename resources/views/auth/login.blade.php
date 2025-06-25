<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Pembayaran SPP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            background: linear-gradient(to right, #00c6ff, #0072ff);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
        }

        .login-container {
            max-width: 420px;
            width: 100%;
            padding: 40px;
            border-radius: 12px;
            background-color: #ffffff;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .login-container h2 {
            color: #00aaff;
            font-weight: 600;
            margin-bottom: 30px;
            text-align: center;
        }

        .btn-primary {
            background-color: #00aaff;
            border: none;
        }

        .btn-primary:hover {
            background-color: #0077cc;
        }

        .form-check-label {
            color: #555;
        }

        .text-muted a {
            color: #00aaff;
            text-decoration: none;
        }

        .text-muted a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <div class="login-container">
        <h2>Sistem Pembayaran SPP</h2>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                    name="email" value="{{ old('email') }}" required autofocus>
                @error('email')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password:</label>
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                    name="password" required>
                @error('password')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                <label class="form-check-label" for="remember">Ingat saya</label>
            </div>

            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>

        <!-- Link ke halaman register (opsional) -->
        {{-- <p class="text-center text-muted mt-3">Belum punya akun? <a href="{{ route('register') }}">Daftar di sini</a></p> --}}
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
</body>

</html>
