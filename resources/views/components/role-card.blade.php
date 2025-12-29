@props([
    'id' => '',
    'name' => 'role',
    'value' => '',
    'icon' => 'bi-person',
    'title' => '',
    'subtitle' => '',
    'features' => [],
    'checked' => false
])

<div class="col-12 col-md-6">
    <input
        type="radio"
        class="btn-check"
        name="{{ $name }}"
        id="{{ $id }}"
        value="{{ $value }}"
        {{ $checked ? 'checked' : '' }}
        required
    >
    <label class="btn btn-outline-primary w-100 py-4 role-select-card" for="{{ $id }}">
        <div class="role-icon-wrapper">
            <i class="bi {{ $icon }}"></i>
        </div>
        <h4 class="fw-bold mt-3 mb-2">{{ $title }}</h4>
        <p class="text-muted mb-0 small">{{ $subtitle }}</p>

        @if(count($features) > 0)
            <div class="role-features mt-3">
                @foreach($features as $feature)
                    <small class="text-muted d-block">
                        <i class="bi bi-check-circle me-1"></i>{{ $feature }}
                    </small>
                @endforeach
            </div>
        @endif
    </label>
</div>
