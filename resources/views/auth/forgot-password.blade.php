@extends('layouts.guest')

@section('title', 'Forgot Password')

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
                    <h2 class="fw-bold mb-3 display-5 animate-fade-in-delay">Reset Your Password</h2>
                    <p class="lead mb-5 fs-5 animate-fade-in-delay-2">Enter your email address and we'll send you an OTP to reset your password</p>
                </div>
            </div>

            <!-- Right Side - Forgot Password Form -->
            <div class="col-lg-6 col-md-6 p-5 d-flex flex-column justify-content-center h-100">
                <div class="login-form-card">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold mb-2 login-form-title">Forgot Password</h2>
                        <p class="text-muted">Enter your email to receive OTP</p>
                    </div>

                    @if(session('success'))
                        <x-alert type="success" :message="session('success')" />
                    @endif

                    @if(session('error'))
                        <x-alert type="danger" :message="session('error')" />
                    @endif

                    @if($errors->any())
                        <x-alert type="danger" :message="$errors->first()" />
                    @endif

                    <form method="POST" action="{{ route('password.send-otp') }}" class="mt-4">
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

                        <div class="d-grid mb-4">
                            <button type="submit" class="btn btn-lg text-white login-btn">
                                <span><i class="bi bi-send me-2"></i>Send OTP</span>
                            </button>
                        </div>

                        <div class="text-center">
                            <p class="text-muted mb-0">
                                Remember your password?
                                <a href="{{ route('login') }}" class="login-link fw-bold">
                                    Login here
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
