@extends('layouts.guest')

@section('title', 'Verify OTP')

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
                    <h2 class="fw-bold mb-3 display-5 animate-fade-in-delay">Verify OTP</h2>
                    <p class="lead mb-5 fs-5 animate-fade-in-delay-2">Enter the 6-digit OTP sent to your email address</p>
                </div>
            </div>

            <!-- Right Side - Verify OTP Form -->
            <div class="col-lg-6 col-md-6 p-5 d-flex flex-column justify-content-center h-100">
                <div class="login-form-card">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold mb-2 login-form-title">Enter OTP</h2>
                        <p class="text-muted">Check your email for the OTP code</p>
                        @if(session('email'))
                            <p class="text-muted small">Sent to: <strong>{{ session('email') }}</strong></p>
                        @endif
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

                    <form method="POST" action="{{ route('password.verify-otp') }}" class="mt-4">
                        @csrf

                        <div class="mb-4">
                            <label for="otp" class="form-label fw-semibold">OTP Code</label>
                            <div class="otp-input-group">
                                <input
                                    type="text"
                                    name="otp"
                                    id="otp"
                                    class="form-control form-control-lg text-center otp-input"
                                    placeholder="000000"
                                    maxlength="6"
                                    pattern="[0-9]{6}"
                                    required
                                    autofocus
                                    autocomplete="off"
                                />
                            </div>
                            <small class="text-muted d-block mt-2">Enter the 6-digit code from your email</small>
                        </div>

                        <div class="d-grid mb-4">
                            <button type="submit" class="btn btn-lg text-white login-btn">
                                <span><i class="bi bi-shield-check me-2"></i>Verify OTP</span>
                            </button>
                        </div>

                        <div class="text-center">
                            <p class="text-muted mb-2">Didn't receive the OTP?</p>
                            <form method="POST" action="{{ route('password.send-otp') }}" class="d-inline">
                                @csrf
                                <input type="hidden" name="email" value="{{ session('email') }}">
                                <button type="submit" class="btn btn-link login-link fw-bold p-0">
                                    Resend OTP
                                </button>
                            </form>
                        </div>

                        <div class="text-center mt-3">
                            <a href="{{ route('password.forgot') }}" class="login-link">
                                <i class="bi bi-arrow-left me-1"></i>Back to Forgot Password
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
