<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Register - Contact Manager</title>
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
        .register-container {
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
        .register-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #2D6CDF, #1A4FA0);
        }
        .register-title {
            font-weight: 700;
            color: #1A202C;
            font-size: 1.75rem;
            margin-bottom: 0.5rem;
            letter-spacing: -0.5px;
        }
        .register-subtitle {
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
        .password-strength {
            height: 4px;
            background: #E2E8F0;
            border-radius: 2px;
            margin-top: 0.5rem;
            overflow: hidden;
        }
        .password-strength-bar {
            height: 100%;
            width: 0;
            transition: all 0.3s ease;
        }
        .password-strength-bar.weak { width: 33.33%; background: #EF4444; }
        .password-strength-bar.medium { width: 66.66%; background: #F59E0B; }
        .password-strength-bar.strong { width: 100%; background: #10B981; }
        .form-text {
            font-size: 0.8rem;
            color: #64748B;
            margin-top: 0.5rem;
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
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 2rem;
        }
        .nav-links a {
            color: #2D6CDF;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            transition: color 0.2s ease;
            white-space: nowrap;
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
            .register-container {
                padding: 1.5rem;
                margin: 1rem;
            }
            .register-title {
                font-size: 1.5rem;
            }
            .nav-links {
                flex-direction: column;
                gap: 1rem;
            }
            .nav-links a {
                display: block;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container d-flex align-items-center justify-content-center min-vh-100">
        <div class="register-container">
            <div class="text-center mb-4">
                <div class="register-title">Create Account</div>
                <div class="register-subtitle">Join us to manage your contacts efficiently</div>
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
            <form method="POST" action="{{ route('register.submit') }}" id="registerForm">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Enter your full name" required autofocus>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input type="tel" class="form-control" id="phone" name="phone" placeholder="Enter your phone number" required>
                    <small class="form-text">Please enter a valid Philippine mobile number starting with 09 or +63</small>
                    <div id="phoneError" class="invalid-feedback" style="display: none;"></div>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="password-field">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Create a password" required>
                        <button type="button" class="password-toggle" onclick="togglePassword('password')">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    <div class="password-strength">
                        <div class="password-strength-bar" id="passwordStrength"></div>
                    </div>
                    <small class="form-text">Password must be at least 8 characters long</small>
                </div>
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                    <div class="password-field">
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Confirm your password" required>
                        <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation')">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="termsCheckbox" required>
                    <label class="form-check-label" for="termsCheckbox">
                        I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">Terms and Conditions</a>
                    </label>
                </div>
                <button type="submit" class="btn btn-primary" id="submitButton" disabled>Create Account</button>
            </form>
            <div class="nav-links">
                <a href="{{ route('login') }}">Already have an account?</a>
                <a href="/">Back to Home</a>
            </div>
        </div>
    </div>

    <!-- Terms and Conditions Modal -->
    <div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="termsModalLabel">Terms and Conditions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h6>1. Acceptance of Terms</h6>
                    <p>By accessing and using this Contact Manager application, you agree to be bound by these Terms and Conditions.</p>

                    <h6>2. User Responsibilities</h6>
                    <p>You are responsible for maintaining the confidentiality of your account and password. You agree to accept responsibility for all activities that occur under your account.</p>

                    <h6>3. Data Privacy</h6>
                    <p>We are committed to protecting your privacy. Your personal information and contact data will be handled in accordance with our Privacy Policy.</p>

                    <h6>4. Prohibited Activities</h6>
                    <p>You agree not to use the service for any illegal purposes or in violation of any local, state, national, or international laws.</p>

                    <h6>5. Service Modifications</h6>
                    <p>We reserve the right to modify or discontinue the service at any time without notice.</p>

                    <h6>6. Limitation of Liability</h6>
                    <p>We shall not be liable for any indirect, incidental, special, consequential, or punitive damages resulting from your use of the service.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="acceptTerms">Accept Terms</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script>
        function togglePassword(fieldId) {
            const passwordInput = document.getElementById(fieldId);
            const toggleButton = document.querySelector(`#${fieldId} + .password-toggle i`);
            
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

        function formatPhoneNumber(input) {
            let value = input.value.replace(/\D/g, '');
            
            // If the user types +63, keep it
            if (input.value.startsWith('+63')) {
                if (value.length > 12) {
                    value = value.slice(0, 12);
                }
                input.value = '+63' + value.substring(3);
                return;
            }
            
            // Otherwise, format as 09
            if (value.startsWith('63')) {
                value = '0' + value.substring(2);
            }
            if (value.length > 11) {
                value = value.slice(0, 11);
            }
            input.value = value;
        }

        function checkPasswordStrength(password) {
            const strengthBar = document.getElementById('passwordStrength');
            let strength = 0;
            
            if (password.length >= 8) strength += 1;
            if (password.match(/[a-z]+/)) strength += 1;
            if (password.match(/[A-Z]+/)) strength += 1;
            if (password.match(/[0-9]+/)) strength += 1;
            if (password.match(/[^a-zA-Z0-9]+/)) strength += 1;

            strengthBar.className = 'password-strength-bar';
            if (strength <= 2) {
                strengthBar.classList.add('weak');
            } else if (strength <= 4) {
                strengthBar.classList.add('medium');
            } else {
                strengthBar.classList.add('strong');
            }
        }

        $(document).ready(function() {
            // Set up CSRF token for all AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Add debounce function
            function debounce(func, wait) {
                let timeout;
                return function executedFunction(...args) {
                    const later = () => {
                        clearTimeout(timeout);
                        func(...args);
                    };
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                };
            }

            // Check phone number availability
            const checkPhoneAvailability = debounce(function(phone) {
                if (phone.length >= 11) { // Only check if phone number is complete
                    $.ajax({
                        url: '/check-phone',
                        method: 'POST',
                        data: { phone: phone },
                        success: function(response) {
                            if (!response.available) {
                                $('#phone').addClass('is-invalid');
                                $('#phoneError').text('This phone number is already registered. Please use a different number.').show();
                                $('#submitButton').prop('disabled', true);
                            } else {
                                $('#phone').removeClass('is-invalid');
                                $('#phoneError').hide();
                                if ($('#termsCheckbox').is(':checked')) {
                                    $('#submitButton').prop('disabled', false);
                                }
                            }
                        }
                    });
                }
            }, 500);

            // Add phone input event listener
            $('#phone').on('input', function() {
                formatPhoneNumber(this);
                checkPhoneAvailability(this.value);
            });

            // Handle terms acceptance
            $('#acceptTerms').click(function() {
                $('#termsCheckbox').prop('checked', true);
                $('#termsModal').modal('hide');
                updateSubmitButton();
            });

            // Update submit button state based on terms checkbox
            $('#termsCheckbox').change(function() {
                updateSubmitButton();
            });

            function updateSubmitButton() {
                $('#submitButton').prop('disabled', !$('#termsCheckbox').is(':checked'));
            }

            // Handle form submission
            $('#registerForm').on('submit', function(e) {
                e.preventDefault();
                
                if (!$('#termsCheckbox').is(':checked')) {
                    alert('Please accept the Terms and Conditions to proceed.');
                    return;
                }
                
                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        window.location.href = '/dashboard';
                    },
                    error: function(xhr) {
                        let errors = xhr.responseJSON.errors;
                        let errorHtml = '<div class="alert alert-danger"><ul>';
                        for (let field in errors) {
                            errors[field].forEach(function(error) {
                                errorHtml += `<li>${error}</li>`;
                            });
                        }
                        errorHtml += '</ul></div>';
                        $('.alert').remove();
                        $('#registerForm').prepend(errorHtml);
                    }
                });
            });
        });
    </script>
</body>
</html>
