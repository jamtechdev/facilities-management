@extends('layouts.guest')

@section('title', 'Login')

@push('styles')
    @vite(['resources/css/auth.css'])
@endpush

@push('scripts')
    @vite(['resources/js/auth.js'])
@endpush

@section('content')
<div class="login-page">
    <div class="container-fluid p-0 h-100">
        <div class="row g-0 h-100 login-card">
            <!-- Left Side - Image/Info -->
            <div class="col-lg-6 col-md-6 d-flex align-items-center justify-content-center p-5 login-left h-100" style="background-image: url('{{ asset('office-cleaning.jpg') }}');">
                <div class="text-white text-center">
                    <x-logo height="80" class="mb-4 animate-fade-in" />
                    <h2 class="fw-bold mb-3 display-5 animate-fade-in-delay">Welcome Back!</h2>
                    <p class="lead mb-5 fs-5 animate-fade-in-delay-2">Sign in to access your ERP portal and manage your facilities efficiently</p>
                    <div class="d-flex justify-content-center gap-4 animate-fade-in-delay-3">
                        <div class="text-center">
                            <div class="login-feature-icon mb-3">
                                <i class="bi bi-shield-check fs-3"></i>
                            </div>
                            <small class="fw-semibold">Secure</small>
                        </div>
                        <div class="text-center">
                            <div class="login-feature-icon mb-3">
                                <i class="bi bi-speedometer2 fs-3"></i>
                            </div>
                            <small class="fw-semibold">Fast</small>
                        </div>
                        <div class="text-center">
                            <div class="login-feature-icon mb-3">
                                <i class="bi bi-people fs-3"></i>
                            </div>
                            <small class="fw-semibold">Reliable</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side - Login Form -->
            <div class="col-lg-6 col-md-6 p-5 d-flex flex-column justify-content-center h-100">
                <div class="login-form-card">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold mb-2 login-form-title">Login to Portal</h2>
                        <p class="text-muted">Enter your credentials to continue</p>
                    </div>

                    @if($errors->any())
                        <x-alert type="danger" :message="$errors->first()" />
                    @endif

                    <form method="POST" action="{{ route('login') }}" class="mt-4">
                    @csrf

                    <x-form-input
                        type="email"
                        name="email"
                        label="Email Address"
                        icon="bi-envelope"
                        value="{{ old('email') }}"
                        placeholder="Enter your email"
                        :required="true"
                        :autofocus="true"
                        autocomplete="email"
                    />

                    <x-form-input
                        type="password"
                        name="password"
                        label="Password"
                        icon="bi-lock"
                        placeholder="Enter your password"
                        :required="true"
                        autocomplete="current-password"
                        :showToggle="true"
                    />

                    <div class="mb-4 d-flex justify-content-between align-items-center">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label text-muted" for="remember">
                                Remember me
                            </label>
                        </div>
                        <a href="{{ route('password.forgot') }}" class="login-link">Forgot Password?</a>
                    </div>

                    <div class="d-grid mb-4">
                        <button type="submit" class="btn btn-lg text-white login-btn">
                            <span><i class="bi bi-box-arrow-in-right me-2"></i>Login</span>
                        </button>
                    </div>

                    <div class="text-center">
                        <p class="text-muted mb-0">
                            Don't have an account?
                            <a href="{{ route('register') }}" class="login-link fw-bold">
                                Register here
                            </a>
                        </p>
                    </div>
                </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
