<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Waste2Product - Transform your waste into resources')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-color: #10b981;
            --secondary-color: #059669;
            --dark-color: #1f2937;
            --light-color: #f3f4f6;
        }

        * {
            font-family: 'Poppins', sans-serif;
        }

        body {
            overflow-x: hidden;
        }

        /* Navbar Styles */
        .navbar-custom {
            background: linear-gradient(135deg, #16a085 0%, #10b981 100%);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
            padding: 0.5rem 0;
        }

        .navbar-custom .navbar-brand {
            font-size: 1.5rem;
            font-weight: 700;
            color: white !important;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
        }

        .navbar-custom .navbar-brand:hover {
            opacity: 0.9;
        }

        .navbar-custom .navbar-brand img {
            height: 40px;
            width: auto;
        }

        .navbar-custom .nav-link {
            color: rgba(255, 255, 255, 0.95) !important;
            font-weight: 500;
            font-size: 0.95rem;
            margin: 0 8px;
            padding: 10px 0 !important;
            transition: all 0.3s ease;
            position: relative;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .navbar-custom .nav-link i {
            font-size: 1.1rem;
        }

        .navbar-custom .nav-link:hover {
            color: white !important;
            opacity: 0.8;
        }

        .navbar-custom .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: white;
            transition: width 0.3s ease;
        }

        .navbar-custom .nav-link:hover::after,
        .navbar-custom .nav-link.active::after {
            width: 100%;
        }

        .navbar-toggler {
            border: none;
            padding: 5px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 5px;
        }

        .navbar-toggler:focus {
            box-shadow: none;
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(255, 255, 255, 1)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        .btn-custom-primary {
            background: white;
            color: #10b981;
            padding: 10px 30px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.9rem;
            border: 2px solid white;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-custom-primary:hover {
            background: rgba(255, 255, 255, 0.9);
            color: #059669;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .btn-custom-outline {
            border: 2px solid white;
            background: transparent;
            color: white;
            padding: 10px 30px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-custom-outline:hover {
            background: white;
            color: #10b981;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        /* Footer Styles */
        .footer {
            background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
            color: white;
            padding: 60px 0 20px;
            margin-top: 80px;
        }

        .footer h5 {
            font-weight: 700;
            margin-bottom: 25px;
            color: #10b981;
            font-size: 1.1rem;
        }

        .footer a {
            color: rgba(255, 255, 255, 0.75);
            text-decoration: none;
            transition: all 0.3s ease;
            display: block;
            margin-bottom: 12px;
            font-size: 0.95rem;
        }

        .footer a:hover {
            color: #10b981;
            padding-left: 8px;
        }

        .footer .social-links a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 45px;
            height: 45px;
            background: rgba(16, 185, 129, 0.15);
            border-radius: 50%;
            color: #10b981;
            margin-right: 12px;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .footer .social-links a:hover {
            background: #10b981;
            color: white;
            transform: translateY(-5px);
        }

        .footer .text-muted {
            color: rgba(255, 255, 255, 0.5) !important;
        }

        .footer .form-control {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            padding: 12px 15px;
            border-radius: 25px;
        }

        .footer .form-control:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: #10b981;
            color: white;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }

        .footer .form-control::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .footer .btn-success {
            background: #10b981;
            border: none;
            padding: 12px 20px;
            border-radius: 25px;
            transition: all 0.3s ease;
        }

        .footer .btn-success:hover {
            background: #059669;
            transform: scale(1.05);
        }

        /* Scroll to top button */
        #scrollToTop {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border: none;
            border-radius: 50%;
            font-size: 20px;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
            transition: all 0.3s ease;
            opacity: 0;
            visibility: hidden;
            z-index: 999;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #scrollToTop.show {
            opacity: 1;
            visibility: visible;
        }

        #scrollToTop:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            transform: translateY(-5px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.5);
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in-up {
            animation: fadeInUp 0.6s ease-out;
        }
    </style>

    @yield('styles')
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('front.home') }}">
                <img src="{{ asset('images/waste2product_logo.png') }}" alt="Waste2Product">
                <span>Waste2Product</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    style="background: white; border: none;">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('front.home') }}">
                            <i class="fas fa-home me-1"></i>Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('front.events') }}">
                            <i class="fas fa-calendar-alt me-1"></i>Events
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('front.marketplace') }}">
                            <i class="fas fa-shopping-bag me-1"></i>Marketplace
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('front.about') }}">
                            <i class="fas fa-info-circle me-1"></i>About
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('front.how-it-works') }}">
                            <i class="fas fa-question-circle me-1"></i>How it works
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('front.contact') }}">
                            <i class="fas fa-envelope me-1"></i>Contact
                        </a>
                    </li>

                    @guest
                        <li class="nav-item ms-3">
                            <a href="{{ route('login') }}" class="btn btn-custom-outline btn-sm">
                                <i class="fas fa-sign-in-alt me-1"></i>Login
                            </a>
                        </li>
                        <li class="nav-item ms-2">
                            <a href="{{ route('register') }}" class="btn btn-custom-primary btn-sm">
                                <i class="fas fa-user-plus me-1"></i>Sign up
                            </a>
                        </li>
                    @else
                        <li class="nav-item ms-3">
                            <a href="{{ route('dashboard') }}" class="btn btn-custom-primary btn-sm">
                                <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                            </a>
                            </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5><i class="fas fa-leaf me-2"></i>Waste2Product</h5>
                    <p class="text-muted">
                        Transform your waste into valuable resources.
                        Join our community for a more sustainable future.
                    </p>
                    <div class="social-links mt-3">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>

                <div class="col-lg-2 col-md-6 mb-4">
                    <h5>Navigation</h5>
                    <a href="{{ route('front.home') }}">Home</a>
                    <a href="{{ route('front.events') }}">Events</a>
                    <a href="{{ route('front.marketplace') }}">Marketplace</a>
                    <a href="{{ route('front.about') }}">About</a>
                </div>

                <div class="col-lg-2 col-md-6 mb-4">
                    <h5>Information</h5>
                    <a href="{{ route('front.how-it-works') }}">How it works</a>
                    <a href="{{ route('front.contact') }}">Contact</a>
                    @guest
                        <a href="{{ route('login') }}">Connexion</a>
                        <a href="{{ route('register') }}">Inscription</a>
                    @else
                        <a href="{{ route('dashboard') }}">Dashboard</a>
                    @endguest
                </div>

                <div class="col-lg-4 mb-4">
                    <h5>Newsletter</h5>
                    <p class="text-muted">Inscrivez-vous pour recevoir nos actualités</p>
                    <form class="d-flex gap-2">
                        <input type="email" class="form-control" placeholder="Votre email">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
            </div>

            <hr style="border-color: rgba(255, 255, 255, 0.1); margin: 40px 0 20px;">

            <div class="row">
                <div class="col-md-6 text-center text-md-start">
                    <p class="text-muted mb-0">
                        &copy; {{ date('Y') }} Waste2Product. Tous droits réservés.
                    </p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <a href="#" class="text-muted me-3">Conditions d'utilisation</a>
                    <a href="#" class="text-muted">Politique de confidentialité</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scroll to top button -->
    <button id="scrollToTop" title="Retour en haut">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Scroll to top functionality -->
    <script>
        // Show/hide scroll to top button
        window.addEventListener('scroll', function() {
            const scrollToTop = document.getElementById('scrollToTop');
            if (window.pageYOffset > 300) {
                scrollToTop.classList.add('show');
            } else {
                scrollToTop.classList.remove('show');
            }
        });

        // Scroll to top
        document.getElementById('scrollToTop').addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    </script>

    @yield('scripts')
</body>
</html>
