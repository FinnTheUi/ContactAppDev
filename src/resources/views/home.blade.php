<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
    <style>
        /* General styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #4facfe, #00f2fe);
            color: #333;
        }

        h1 {
            margin-top: 100px;
            font-size: 3rem;
            text-align: center;
            animation: fadeIn 1.5s ease-in-out;
        }

        p {
            font-size: 1.2rem;
            text-align: center;
            margin: 20px auto;
            max-width: 600px;
            animation: fadeIn 2s ease-in-out;
        }

        /* Top-right buttons */
        .top-right-buttons {
            position: absolute;
            top: 20px;
            right: 20px;
        }

        .top-right-buttons a {
            text-decoration: none;
            color: #fff;
            background: #007bff;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: bold;
            margin: 0 5px;
            transition: transform 0.3s ease, background 0.3s ease;
        }

        .top-right-buttons a:hover {
            background: #0056b3;
            transform: scale(1.1);
        }

        /* Info section */
        .info-section {
            margin-top: 50px;
            padding: 20px;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 10px;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
            animation: slideIn 1.5s ease-in-out;
        }

        /* Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        @keyframes slideIn {
            from {
                transform: translateY(50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
    </style>
</head>
<body>
    <!-- Top-right buttons -->
    <div class="top-right-buttons">
        <a href="{{ route('login') }}">{{ __('Login') }}</a>
        <a href="{{ route('register') }}">{{ __('Register') }}</a>
    </div>

    <!-- Welcome message -->
    <h1>{{ __('Welcome to :appName', ['appName' => config('app.name')]) }}</h1>

    <!-- Info section -->
    <div class="info-section">
        <p>
            {{ __(':appName is your ultimate contact management solution. Whether you\'re an individual or a business, our platform helps you organize, manage, and access your contacts effortlessly. With a user-friendly interface and powerful tools, :appName ensures your contact information is always at your fingertips.', ['appName' => config('app.name')]) }}
        </p>
    </div>
</body>
</html>