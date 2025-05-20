<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Manager - Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/animate.css@4.1.1/animate.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            scroll-behavior: smooth;
        }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
            min-height: 100vh;
        }
        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
            padding: 1rem 0;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            transition: background 0.3s ease, box-shadow 0.3s ease;
        }
        .navbar.scrolled {
            background: #fff;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        }
        .navbar-brand {
            font-weight: 700;
            color: #1A202C;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .navbar-brand i {
            color: #2D6CDF;
        }
        .nav-link {
            color: #64748B;
            font-weight: 500;
            transition: all 0.2s ease;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            position: relative;
        }
        .nav-link:hover {
            color: #2D6CDF;
            background-color: rgba(45, 108, 223, 0.1);
        }
        .btn-primary {
            background: #2D6CDF;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .btn-primary:hover {
            background: #1A4FA0;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(45, 108, 223, 0.2);
        }
        .btn-light:hover {
            background-color: #fff !important;
            color: #1A4FA0 !important;
            box-shadow: 0 4px 12px rgba(255, 255, 255, 0.3);
        }
        .hero-section {
            padding: 8rem 0 6rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%232D6CDF' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            opacity: 0.5;
        }
        .hero-title {
            font-weight: 700;
            color: #1A202C;
            font-size: 3.5rem;
            margin-bottom: 1.5rem;
            line-height: 1.2;
        }
        .hero-subtitle {
            color: #64748B;
            font-size: 1.25rem;
            margin-bottom: 2.5rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            line-height: 1.6;
        }
        .features-section {
            padding: 6rem 0;
            background: #ffffff;
        }
        .feature-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 2.5rem 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
            text-align: center;
        }
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        .feature-icon {
            font-size: 2.5rem;
            color: #2D6CDF;
            margin-bottom: 1.5rem;
            transition: transform 0.3s ease;
        }
        .feature-card:hover .feature-icon {
            transform: rotate(6deg) scale(1.2);
        }
        .feature-title {
            font-weight: 600;
            color: #1A202C;
            font-size: 1.25rem;
            margin-bottom: 1rem;
        }
        .feature-text {
            color: #64748B;
            font-size: 1rem;
            line-height: 1.6;
        }
        .cta-section {
            padding: 6rem 0;
            text-align: center;
            background: linear-gradient(135deg, #2D6CDF 0%, #1A4FA0 100%);
            color: white;
            position: relative;
            overflow: hidden;
        }
        .cta-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
        .cta-title {
            font-weight: 700;
            color: white;
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
            position: relative;
        }
        .cta-text {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1.1rem;
            margin-bottom: 2rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            position: relative;
        }
        .footer {
            background: #1A202C;
            color: #fff;
            padding: 4rem 0 2rem;
        }
        .footer-title {
            font-weight: 700;
            font-size: 1.25rem;
            margin-bottom: 1.5rem;
        }
        .footer-link {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: color 0.2s ease;
            display: block;
            margin-bottom: 0.75rem;
        }
        .footer-link:hover {
            color: #fff;
        }
        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            margin-top: 3rem;
            padding-top: 2rem;
            text-align: center;
            color: rgba(255, 255, 255, 0.5);
        }
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            .hero-subtitle {
                font-size: 1.1rem;
            }
            .feature-card {
                margin-bottom: 1.5rem;
            }
            .navbar {
                padding: 0.75rem 0;
            }
            .hero-section {
                padding: 6rem 0 4rem;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="bi bi-person-lines-fill"></i>
                Contact Manager
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register') }}">Register</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="hero-section">
        <div class="container animate__animated animate__fadeInUp">
            <h1 class="hero-title">Manage Your Contacts with Ease</h1>
            <p class="hero-subtitle">A powerful contact management system designed for professionals. Organize, categorize, and access your contacts anytime, anywhere.</p>
            <a href="{{ route('register') }}" class="btn btn-primary btn-lg">Get Started</a>
        </div>
    </section>

    <section class="features-section">
        <div class="container">
            <div class="row">
                <div class="col-md-4 animate__animated animate__fadeInUp animate__delay-1s">
                    <div class="feature-card">
                        <i class="bi bi-diagram-3 feature-icon"></i>
                        <h3 class="feature-title">Smart Organization</h3>
                        <p class="feature-text">Categorize your contacts and keep them organized with our intuitive category system.</p>
                    </div>
                </div>
                <div class="col-md-4 animate__animated animate__fadeInUp animate__delay-2s">
                    <div class="feature-card">
                        <i class="bi bi-phone feature-icon"></i>
                        <h3 class="feature-title">Easy Access</h3>
                        <p class="feature-text">Access your contacts from any device with our responsive web interface.</p>
                    </div>
                </div>
                <div class="col-md-4 animate__animated animate__fadeInUp animate__delay-3s">
                    <div class="feature-card">
                        <i class="bi bi-shield-check feature-icon"></i>
                        <h3 class="feature-title">Secure Storage</h3>
                        <p class="feature-text">Your contacts are securely stored and protected with industry-standard security measures.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="cta-section">
        <div class="container animate__animated animate__fadeInUp">
            <h2 class="cta-title">Ready to Get Started?</h2>
            <p class="cta-text">Join thousands of professionals who trust Contact Manager for their contact management needs.</p>
            <a href="{{ route('register') }}" class="btn btn-light btn-lg">Create Free Account</a>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h3 class="footer-title">Contact Manager</h3>
                    <p style="color: rgba(255, 255, 255, 0.7);">Your trusted solution for contact management. Simple, secure, and efficient.</p>
                </div>
                <div class="col-md-4">
                    <h3 class="footer-title">Quick Links</h3>
                    <a href="{{ route('login') }}" class="footer-link">Login</a>
                    <a href="{{ route('register') }}" class="footer-link">Register</a>
                </div>
                <div class="col-md-4">
                    <h3 class="footer-title">Contact Us</h3>
                    <a href="mailto:support@contactmanager.com" class="footer-link">support@contactmanager.com</a>
                    <a href="tel:+1234567890" class="footer-link">+1 (234) 567-890</a>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; {{ date('Y') }} Contact Manager. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        window.addEventListener('scroll', function () {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    </script>
</body>
</html>
