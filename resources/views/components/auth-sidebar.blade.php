@props([
    'title' => 'Welcome',
    'subtitle' => 'Sign in to access your account',
    'image' => 'office-cleaning.jpg',
    'features' => []
])

<div class="col-lg-6 col-md-6 d-flex align-items-center justify-content-center p-5 login-left h-100" style="background-image: url('{{ asset($image) }}');">
    <div class="text-white text-center">
        <x-logo height="80" class="mb-4 animate-fade-in" />
        <h2 class="fw-bold mb-3 display-5 animate-fade-in-delay">{{ $title }}</h2>
        <p class="lead mb-5 fs-5 animate-fade-in-delay-2">{{ $subtitle }}</p>

        @if(count($features) > 0)
            <div class="d-flex justify-content-center gap-4 animate-fade-in-delay-3">
                @foreach($features as $feature)
                    <div class="text-center">
                        <div class="login-feature-icon mb-3">
                            <i class="bi {{ $feature['icon'] ?? 'bi-check-circle' }} fs-3"></i>
                        </div>
                        <small class="fw-semibold">{{ $feature['label'] ?? '' }}</small>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
