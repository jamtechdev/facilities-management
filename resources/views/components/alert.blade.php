@props([
    'type' => 'info',
    'message' => '',
    'dismissible' => true
])

@php
    $alertClass = match($type) {
        'success' => 'alert-success',
        'error', 'danger' => 'alert-danger',
        'warning' => 'alert-warning',
        'info' => 'alert-info',
        default => 'alert-info',
    };
    
    $icon = match($type) {
        'success' => 'bi-check-circle',
        'error', 'danger' => 'bi-exclamation-triangle',
        'warning' => 'bi-exclamation-triangle',
        'info' => 'bi-info-circle',
        default => 'bi-info-circle',
    };
@endphp

<div class="alert {{ $alertClass }} alert-dismissible fade show" role="alert">
    <i class="bi {{ $icon }} me-2"></i>
    @if(is_array($message) || $message instanceof Illuminate\Support\Collection)
        <ul class="mb-0">
            @foreach($message as $msg)
                <li>{{ $msg }}</li>
            @endforeach
        </ul>
    @else
        {{ $message }}
    @endif
    @if($dismissible)
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    @endif
</div>

