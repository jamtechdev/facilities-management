@props([
    'icon' => '',
    'iconColor' => 'primary',
    'label' => '',
    'value' => '',
    'badge' => null
])

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="d-flex align-items-center">
            <div class="bg-{{ $iconColor }} bg-opacity-10 rounded p-3 me-3">
                <i class="bi {{ $icon }} text-{{ $iconColor }}" style="font-size: 2rem;"></i>
            </div>
            <div>
                <h6 class="text-muted mb-0">{{ $label }}</h6>
                @if($badge)
                    <h6 class="mb-0">
                        <span class="badge bg-{{ $badge['color'] ?? 'primary' }}">{{ $badge['text'] ?? $value }}</span>
                    </h6>
                @else
                    <h6 class="mb-0">{{ $value }}</h6>
                @endif
            </div>
        </div>
    </div>
</div>

