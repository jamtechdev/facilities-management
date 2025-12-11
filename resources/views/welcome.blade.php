@extends('layouts.guest')

@section('title', 'Welcome')

@push('styles')
    @vite(['resources/css/welcome.css'])
@endpush

@section('content')
<!-- Hero Section -->
<section class="hero-section position-relative overflow-hidden" style="background-image: url('{{ asset('office-cleaning.jpg') }}');">
    <div class="container h-100">
        <div class="row h-100 align-items-center py-5">
            <div class="col-lg-8 mx-auto text-center text-white">
                <h1 class="display-2 fw-bold mb-4 animate-fade-in hero-title">
                    Commercial Cleaning Services<br>In London
                </h1>
                <div class="d-flex gap-3 flex-wrap justify-content-center animate-fade-in-delay-2 mt-5">
                    <a href="#features" class="btn btn-light btn-lg px-5 py-3 shadow-lg rounded-pill fw-semibold hero-cta-btn">
                        EXPLORE ALL OUR SERVICES <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="stats-section py-5">
    <div class="container">
        <div class="row g-4 text-center">
            <div class="col-md-3 col-6">
                <div class="stat-item py-4">
                    <h3 class="stat-number mb-2">100+</h3>
                    <p class="stat-label mb-0">Active Users</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-item py-4">
                    <h3 class="stat-number mb-2">24/7</h3>
                    <p class="stat-label mb-0">Support</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-item py-4">
                    <h3 class="stat-number mb-2">99.9%</h3>
                    <p class="stat-label mb-0">Uptime</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-item py-4">
                    <h3 class="stat-number mb-2">50+</h3>
                    <p class="stat-label mb-0">Features</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section id="features" class="features-section py-5">
    <div class="container">
        <div class="text-center mb-5">
            <span class="badge rounded-pill feature-badge mb-3">Key Features</span>
            <h2 class="display-4 fw-bold mb-3 section-title">Everything You Need</h2>
            <p class="lead text-muted section-subtitle">Powerful tools to manage your facilities efficiently and effectively</p>
        </div>
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <x-feature-card
                    icon="bi-people"
                    title="User Management"
                    description="Complete control over users, roles, and permissions with an intuitive interface designed for efficiency."
                    link="{{ route('login') }}"
                />
            </div>
            <div class="col-lg-4 col-md-6">
                <x-feature-card
                    icon="bi-shield-check"
                    title="Security & Permissions"
                    description="Enterprise-grade security with granular role-based access control to protect your sensitive data."
                    link="{{ route('login') }}"
                />
            </div>
            <div class="col-lg-4 col-md-6">
                <x-feature-card
                    icon="bi-speedometer2"
                    title="Real-Time Analytics"
                    description="Get instant insights with comprehensive dashboards and reports to make data-driven decisions."
                    link="{{ route('login') }}"
                />
            </div>
        </div>
    </div>
</section>

<!-- Why Choose Us Section -->
<section class="why-choose-section py-5">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6 order-lg-2">
                <div class="position-relative">
                    <img src="{{ asset('office-cleaning.jpg') }}" alt="Professional Team" class="img-fluid rounded-4 shadow-lg">
                    <div class="position-absolute top-0 start-0 m-4">
                        <span class="badge px-4 py-2 rounded-pill feature-badge">
                            <i class="bi bi-star-fill me-1"></i>Trusted Solution
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 order-lg-1">
                <span class="badge rounded-pill why-choose-badge mb-3">Why Choose Us</span>
                <h2 class="display-4 fw-bold mb-4 section-title">Built for Excellence</h2>
                <p class="lead text-muted mb-5">Experience the difference with our comprehensive ERP solution designed to streamline your facilities management operations.</p>
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="d-flex align-items-start">
                            <div class="flex-shrink-0">
                                <div class="why-choose-icon">
                                    <i class="bi bi-check-circle"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="fw-bold mb-2 why-choose-title">Expert Team</h5>
                                <p class="text-muted mb-0 small">Built by industry professionals with decades of combined experience.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-start">
                            <div class="flex-shrink-0">
                                <div class="why-choose-icon">
                                    <i class="bi bi-shield-check"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="fw-bold mb-2 why-choose-title">Secure Platform</h5>
                                <p class="text-muted mb-0 small">Enterprise-grade security with regular updates and monitoring.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-start">
                            <div class="flex-shrink-0">
                                <div class="why-choose-icon">
                                    <i class="bi bi-lightning-charge"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="fw-bold mb-2 why-choose-title">Fast Performance</h5>
                                <p class="text-muted mb-0 small">Lightning-fast response times for optimal user experience.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-start">
                            <div class="flex-shrink-0">
                                <div class="why-choose-icon">
                                    <i class="bi bi-star"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="fw-bold mb-2 why-choose-title">Proven Track Record</h5>
                                <p class="text-muted mb-0 small">Trusted by businesses worldwide for reliable solutions.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-5">
                    <a href="{{ route('login') }}" class="btn btn-lg px-5 py-3 text-white shadow-lg rounded-pill cta-btn">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Get Started Now
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Additional Features Grid -->
<section class="additional-features-section py-5">
    <div class="container">
        <div class="text-center mb-5">
            <span class="badge rounded-pill feature-badge mb-3">Solutions</span>
            <h2 class="display-4 fw-bold mb-3 section-title">Complete Management Suite</h2>
            <p class="lead text-muted section-subtitle">All the tools you need in one powerful platform</p>
        </div>
        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="text-center p-4">
                    <div class="mb-4">
                        <div class="additional-feature-icon">
                            <i class="bi bi-clipboard-data"></i>
                        </div>
                    </div>
                    <h5 class="fw-bold mb-3 section-title">Smart Dashboard</h5>
                    <p class="text-muted small">Real-time overview with customizable widgets and insights</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="text-center p-4">
                    <div class="mb-4">
                        <div class="additional-feature-icon">
                            <i class="bi bi-person-badge"></i>
                        </div>
                    </div>
                    <h5 class="fw-bold mb-3 section-title">User Control</h5>
                    <p class="text-muted small">Comprehensive user management with role assignments</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="text-center p-4">
                    <div class="mb-4">
                        <div class="additional-feature-icon">
                            <i class="bi bi-key"></i>
                        </div>
                    </div>
                    <h5 class="fw-bold mb-3 section-title">Access Control</h5>
                    <p class="text-muted small">Granular permissions and security settings</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="text-center p-4">
                    <div class="mb-4">
                        <div class="additional-feature-icon">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                    </div>
                    <h5 class="fw-bold mb-3 section-title">Advanced Analytics</h5>
                    <p class="text-muted small">Track performance with detailed reports and metrics</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action Section -->
<section class="cta-section py-5 text-white text-center position-relative overflow-hidden">
    <div class="container position-relative py-5">
        <h2 class="display-3 fw-bold mb-4">Ready to Transform Your Operations?</h2>
        <p class="lead mb-5 fs-4 cta-subtitle">Join thousands of businesses already using our ERP portal to streamline their facilities management</p>
        <div class="d-flex gap-3 justify-content-center flex-wrap">
            <a href="{{ route('login') }}" class="btn btn-light btn-lg px-5 py-3 shadow-lg rounded-pill fw-semibold">
                <i class="bi bi-box-arrow-in-right me-2"></i>Login Now
            </a>
            <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg px-5 py-3 rounded-pill fw-semibold border-2">
                <i class="bi bi-person-plus me-2"></i>Create Account
            </a>
        </div>
    </div>
</section>
@endsection
