@extends('layouts.guest')

@section('title', 'Reset Password')

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
                    <h2 class="fw-bold mb-3 display-5 animate-fade-in-delay">Set New Password</h2>
                    <p class="lead mb-5 fs-5 animate-fade-in-delay-2">Create a strong password to secure your account</p>
                </div>
            </div>

            <!-- Right Side - Reset Password Form -->
            <div class="col-lg-6 col-md-6 p-5 d-flex flex-column justify-content-center h-100">
                <div class="login-form-card">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold mb-2 login-form-title">Reset Password</h2>
                        <p class="text-muted">Enter your new password</p>
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

                    <form method="POST" action="{{ route('password.reset') }}" class="mt-4">
                        @csrf

                        <x-form-input
                            type="password"
                            name="password"
                            label="New Password"
                            icon="bi-lock"
                            placeholder="Enter new password"
                            :required="true"
                            :autofocus="true"
                            autocomplete="new-password"
                            :showToggle="true"
                        />

                        <x-form-input
                            type="password"
                            name="password_confirmation"
                            label="Confirm New Password"
                            icon="bi-lock-fill"
                            placeholder="Confirm new password"
                            :required="true"
                            autocomplete="new-password"
                            :showToggle="true"
                        />

                        <div class="d-grid mb-4">
                            <button type="submit" class="btn btn-lg text-white login-btn">
                                <span><i class="bi bi-key me-2"></i>Reset Password</span>
                            </button>
                        </div>

                        <div class="text-center">
                            <a href="{{ route('login') }}" class="login-link">
                                <i class="bi bi-arrow-left me-1"></i>Back to Login
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
