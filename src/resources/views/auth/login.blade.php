<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Contact Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
            min-height: 100vh;
            position: relative;
        }
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%232D6CDF' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            pointer-events: none;
        }
        .login-container {
            background: #ffffff;
            padding: 2.5rem;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
            max-width: 420px;
            width: 100%;
            margin: 0 auto;
            position: relative;
            overflow: hidden;
            animation: fadeIn 0.5s ease;
        }
        .login-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #2D6CDF, #1A4FA0);
        }
        .login-title {
            font-weight: 700;
            color: #1A202C;
            font-size: 1.75rem;
            margin-bottom: 0.5rem;
            letter-spacing: -0.5px;
        }
        .login-subtitle {
            color: #64748B;
            font-size: 0.95rem;
            margin-bottom: 2rem;
            line-height: 1.5;
        }
        .form-label {
            font-weight: 500;
            color: #334155;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }
        .form-control {
            border: 1.5px solid #E2E8F0;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            transition: all 0.2s ease;
            background-color: #F8FAFC;
        }
        .form-control:focus {
            border-color: #2D6CDF;
            box-shadow: 0 0 0 3px rgba(45, 108, 223, 0.1);
            background-color: #ffffff;
        }
        .form-control::placeholder {
            color: #94A3B8;
        }
        .password-field {
            position: relative;
        }
        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94A3B8;
            cursor: pointer;
            transition: color 0.2s ease;
            background: none;
            border: none;
            padding: 0;
        }
        .password-toggle:hover {
            color: #2D6CDF;
        }
        .form-check {
            margin-top: 1rem;
        }
        .form-check-input {
            border: 1.5px solid #E2E8F0;
            width: 1.1em;
            height: 1.1em;
            margin-top: 0.15em;
        }
        .form-check-input:checked {
            background-color: #2D6CDF;
            border-color: #2D6CDF;
        }
        .form-check-label {
            color: #64748B;
            font-size: 0.9rem;
        }
        .btn-primary {
            background: #2D6CDF;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.95rem;
            padding: 0.75rem 1.5rem;
            transition: all 0.2s ease;
            width: 100%;
            margin-top: 1rem;
        }
        .btn-primary:hover {
            background: #1A4FA0;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(45, 108, 223, 0.2);
        }
        .btn-primary:active {
            transform: translateY(0);
        }
        .nav-links {
            margin-top: 2rem;
            text-align: center;
            padding-top: 1.5rem;
            border-top: 1px solid #E2E8F0;
        }
        .nav-links a {
            color: #2D6CDF;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            transition: color 0.2s ease;
            margin: 0 1rem;
        }
        .nav-links a:hover {
            color: #1A4FA0;
        }
        .nav-links a:last-child {
            color: #64748B;
        }
        .alert {
            border: none;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            background-color: #FEE2E2;
            color: #DC2626;
            font-size: 0.9rem;
            animation: shake 0.5s ease;
        }
        .alert ul {
            margin: 0;
            padding-left: 1.2rem;
        }
        .alert li {
            margin: 0.25rem 0;
        }
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        @media (max-width: 500px) {
            .login-container {
                padding: 1.5rem;
                margin: 1rem;
            }
            .login-title {
                font-size: 1.5rem;
            }
            .nav-links a {
                display: block;
                margin: 0.5rem 0;
            }
        }
    </style>
</head>
<body>
    <div class="container d-flex align-items-center justify-content-center min-vh-100">
        <div class="login-container">
            <div class="text-center mb-4">
                <div class="login-title">Welcome Back</div>
                <div class="login-subtitle">Sign in to access your contact manager</div>
            </div>
            @if ($errors->any())
                <div class="alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required autofocus>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="password-field">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                        <button type="button" class="password-toggle" onclick="togglePassword()">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label" for="remember">
                        Remember me
                    </label>
                </div>
                <button type="submit" class="btn btn-primary">Sign In</button>
            </form>
            <div class="nav-links">
                <a href="{{ route('register') }}">Create Account</a>
                <a href="/">Back to Home</a>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleButton = document.querySelector('.password-toggle i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleButton.classList.remove('bi-eye');
                toggleButton.classList.add('bi-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleButton.classList.remove('bi-eye-slash');
                toggleButton.classList.add('bi-eye');
            }
        }
    </script>
</body>
</html>
