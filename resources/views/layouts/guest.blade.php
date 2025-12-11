<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} - @yield('title', 'Welcome')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    @vite(['resources/css/app.css', 'resources/css/guest.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body>
    <div class="min-vh-100 d-flex flex-column">
        <!-- Navigation -->
        <nav class="navbar navbar-expand-lg navbar-dark fixed-top shadow-lg navbar-guest">
            <div class="container">
                <a class="navbar-brand fw-bold d-flex align-items-center" href="{{ route('welcome') }}">
                    <x-logo height="45" class="me-2" />
                </a>
                <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link fw-semibold {{ request()->routeIs('welcome') ? 'active' : '' }}" href="{{ route('welcome') }}">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link fw-semibold {{ request()->routeIs('login') ? 'active' : '' }}" href="{{ route('login') }}">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link fw-semibold {{ request()->routeIs('register') ? 'active' : '' }}" href="{{ route('register') }}">Register</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="flex-grow-1 main-content-guest">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="text-white py-4 mt-auto shadow-lg footer-guest">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-4 mb-3 mb-md-0">
                        <div class="d-flex align-items-center">
                            <x-logo height="35" class="me-2" />
                            <span class="fw-bold">KEYSTONE Facilities Management</span>
                        </div>
                    </div>
                    <div class="col-md-4 text-center mb-3 mb-md-0">
                        <p class="mb-0">&copy; {{ date('Y') }} keystonefm.co.uk. All rights reserved.</p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <div class="d-flex justify-content-md-end justify-content-center gap-3">
                            <a href="#" class="text-white opacity-75 hover-opacity"><i class="bi bi-facebook"></i></a>
                            <a href="#" class="text-white opacity-75 hover-opacity"><i class="bi bi-instagram"></i></a>
                            <a href="#" class="text-white opacity-75 hover-opacity"><i class="bi bi-linkedin"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>
    @stack('scripts')
</body>
</html>
