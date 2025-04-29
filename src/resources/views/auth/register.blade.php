<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Register') }} - {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #4facfe, #00f2fe);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .card {
            max-width: 400px;
            width: 100%;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
        .form-control.is-invalid {
            border-color: #dc3545;
        }
        .form-control:focus {
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
        .input-group-text {
            background-color: #e9ecef;
        }
        .back-button {
            margin-top: 20px;
            display: block;
            text-align: center;
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s ease;
        }
        .back-button:hover {
            color: #0056b3;
        }
        .form-text {
            font-size: 0.875rem;
            color: #6c757d;
        }
        .terms-label {
            font-size: 0.875rem;
            color: #495057;
        }
        .terms-link {
            color: #007bff;
            text-decoration: none;
        }
        .terms-link:hover {
            color: #0056b3;
        }
    </style>
</head>
<body>

<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="card p-4">
        <div class="card-body">
            <h2 class="card-title text-center text-primary mb-4">{{ __('Register') }}</h2>
            <form id="registerForm" method="POST" action="{{ route('register.submit') }}" novalidate>
                @csrf

                <!-- Name -->
                <div class="mb-3">
                    <label for="name" class="form-label">{{ __('Name') }}</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input type="text" class="form-control" id="name" name="name" placeholder="{{ __('Name') }}" required pattern="^[a-zA-Z0-9 ]+$">
                    </div>
                    <div class="invalid-feedback">{{ __('Please enter a valid name (letters and numbers only).') }}</div>
                </div>

                <!-- Email -->
                <div class="mb-3">
                    <label for="email" class="form-label">{{ __('Email') }}</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input type="email" class="form-control" id="email" name="email" placeholder="{{ __('Email') }}" required>
                    </div>
                    <div class="invalid-feedback">{{ __('Please enter a valid @gmail.com email address.') }}</div>
                </div>

                <!-- Phone Number -->
                <div class="mb-3">
                    <label for="phone" class="form-label">{{ __('Phone Number') }}</label>
                    <div class="input-group">
                        <span class="input-group-text">+63</span>
                        <input type="text" class="form-control" id="phone" name="phone" placeholder="{{ __('9-digit number (e.g. 912345678)') }}" required maxlength="9">
                    </div>
                    <div class="form-text">{{ __('Enter your number.') }}</div>
                    <div class="invalid-feedback">{{ __('Please enter exactly 9 digits after +63.') }}</div>
                </div>

                <!-- Password -->
                <div class="mb-3">
                    <label for="password" class="form-label">{{ __('Password') }}</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" placeholder="{{ __('Password') }}" required>
                        <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    <div class="invalid-feedback">{{ __('Password is required.') }}</div>
                </div>

                <!-- Confirm Password -->
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">{{ __('Confirm Password') }}</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="{{ __('Confirm Password') }}" required>
                    </div>
                    <div class="invalid-feedback">{{ __('Please confirm your password.') }}</div>
                </div>

                <!-- Terms and Conditions -->
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
                    <label class="form-check-label terms-label" for="terms">
                        {{ __('I agree to the') }} <a href="/terms" class="terms-link" target="_blank">{{ __('Terms and Conditions') }}</a>.
                    </label>
                    <div class="invalid-feedback">{{ __('You must agree to the Terms and Conditions to proceed.') }}</div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary w-100" id="submitButton" disabled>{{ __('Register') }}</button>

                <!-- Back to Home Button -->
                <a href="/" class="back-button">← {{ __('Back to Home') }}</a>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('registerForm');
    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');
    const phoneInput = document.getElementById('phone');
    const passwordInput = document.getElementById('password');
    const passwordConfirmationInput = document.getElementById('password_confirmation');
    const togglePassword = document.getElementById('togglePassword');
    const submitButton = document.getElementById('submitButton');
    const termsCheckBox = document.getElementById('terms');

    // Show/Hide Password
    togglePassword.addEventListener('click', () => {
        const type = passwordInput.type === 'password' ? 'text' : 'password';
        passwordInput.type = type;
        togglePassword.innerHTML = type === 'password' ? '<i class="bi bi-eye"></i>' : '<i class="bi bi-eye-slash"></i>';
    });

    // Enable/Disable Submit Button based on form validation
    const validateForm = () => {
        const nameValid = /^[a-zA-Z0-9 ]+$/.test(nameInput.value.trim());
        const emailValid = emailInput.value.trim().endsWith('@gmail.com');
        const phoneValid = phoneInput.value.trim().length === 9;
        const passwordValid = passwordInput.value.trim() !== '';
        const passwordMatch = passwordInput.value === passwordConfirmationInput.value;
        const termsChecked = termsCheckBox.checked;

        submitButton.disabled = !(nameValid && emailValid && phoneValid && passwordValid && passwordMatch && termsChecked);
    };

    // Real-time Validation
    form.addEventListener('input', validateForm);

    // Phone number field handling
    phoneInput.addEventListener('input', (e) => {
        let phoneValue = e.target.value.replace(/[^0-9]/g, ''); // Only allow digits
        if (phoneValue.length > 9) phoneValue = phoneValue.substring(0, 9); // Limit to 9 digits
        phoneInput.value = phoneValue;
    });

    // Form submission logic
    form.addEventListener('submit', (e) => {
        if (submitButton.disabled) {
            e.preventDefault();
        }
    });
});
</script>
</body>
</html>