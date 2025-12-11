@extends('layouts.guest')

@section('title', 'Register')

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
                    <h2 class="fw-bold mb-3 display-5 animate-fade-in-delay">Join Our Platform!</h2>
                    <p class="lead mb-5 fs-5 animate-fade-in-delay-2">Create your account and start managing your facilities with our comprehensive ERP solution</p>
                    <div class="d-flex justify-content-center gap-4 animate-fade-in-delay-3">
                        <div class="text-center">
                            <div class="login-feature-icon mb-3">
                                <i class="bi bi-building fs-3"></i>
                            </div>
                            <small class="fw-semibold">Facilities</small>
                        </div>
                        <div class="text-center">
                            <div class="login-feature-icon mb-3">
                                <i class="bi bi-graph-up fs-3"></i>
                            </div>
                            <small class="fw-semibold">Analytics</small>
                        </div>
                        <div class="text-center">
                            <div class="login-feature-icon mb-3">
                                <i class="bi bi-gear fs-3"></i>
                            </div>
                            <small class="fw-semibold">Management</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side - Register Form -->
            <div class="col-lg-6 col-md-6 p-5 d-flex flex-column justify-content-center h-100">
                <div class="login-form-card">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold mb-2 login-form-title">Create Account</h2>
                        <p class="text-muted">Join us and start managing your facilities efficiently</p>
                    </div>

                    @if($errors->any())
                        <x-alert type="danger" :message="$errors->first()" />
                    @endif

                    <form method="POST" action="{{ route('register') }}" class="mt-4">
                        @csrf

                        <x-form-input 
                            type="text"
                            name="name"
                            label="Full Name"
                            icon="bi-person"
                            value="{{ old('name') }}"
                            placeholder="Enter your full name"
                            :required="true"
                            :autofocus="true"
                            autocomplete="name"
                        />

                        <x-form-input 
                            type="email"
                            name="email"
                            label="Email Address"
                            icon="bi-envelope"
                            value="{{ old('email') }}"
                            placeholder="Enter your email"
                            :required="true"
                            autocomplete="email"
                        />

                        <x-form-input 
                            type="password"
                            name="password"
                            label="Password"
                            icon="bi-lock"
                            placeholder="Create a password"
                            :required="true"
                            autocomplete="new-password"
                            :showToggle="true"
                        />

                        <x-form-input 
                            type="password"
                            name="password_confirmation"
                            label="Confirm Password"
                            icon="bi-lock-fill"
                            placeholder="Confirm your password"
                            :required="true"
                            autocomplete="new-password"
                            :showToggle="true"
                        />

                        <div class="d-grid mb-4">
                            <button type="submit" class="btn btn-lg text-white login-btn">
                                <span><i class="bi bi-person-plus me-2"></i>Create Account</span>
                            </button>
                        </div>

                        <div class="text-center">
                            <p class="text-muted mb-0">
                                Already have an account? 
                                <a href="{{ route('login') }}" class="login-link fw-bold">
                                    Login here
                                </a>
                            </p>
                        </div>
                    </form>

                    <div class="mt-4 pt-4 border-top text-center">
                        <p class="text-muted small mb-2">Or continue with</p>
                        <div class="d-flex justify-content-center">
                            <button class="btn btn-outline-secondary social-login-btn">
                                <i class="bi bi-google me-2"></i>Register with Google
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

