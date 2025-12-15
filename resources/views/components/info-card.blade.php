@props([
    'label',
    'value' => null,
    'badge' => null,
    'badgeColor' => 'primary',
    'link' => null
])

<div class="info-card">
    <div class="info-label">{{ $label }}</div>
    <div class="info-value">
        @if($link)
            <a href="{{ $link }}" class="text-decoration-none">{{ $value ?? $slot }}</a>
        @elseif($badge)
            <span class="badge bg-{{ $badgeColor }}">{{ $badge }}</span>
        @else
            {{ $value ?? $slot }}
        @endif
    </div>
</div>

