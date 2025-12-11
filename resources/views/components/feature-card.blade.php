@props([
    'icon' => '',
    'title' => '',
    'description' => '',
    'link' => null,
    'linkText' => 'Learn More'
])

<div class="card h-100 feature-card p-4">
    <div class="mb-4">
        <div class="feature-icon">
            <i class="bi {{ $icon }}"></i>
        </div>
    </div>
    <h4 class="fw-bold mb-3 section-title">{{ $title }}</h4>
    <p class="text-muted mb-4">{{ $description }}</p>
    @if($link)
        <a href="{{ $link }}" class="feature-link">
            {{ $linkText }} <i class="bi bi-arrow-right ms-2"></i>
        </a>
    @endif
</div>

