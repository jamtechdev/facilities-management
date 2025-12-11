@props([
    'type' => 'text',
    'name' => '',
    'label' => '',
    'icon' => '',
    'value' => '',
    'placeholder' => '',
    'required' => false,
    'autocomplete' => null,
    'autofocus' => false,
    'error' => null,
    'showToggle' => false,
    'inputGroupClass' => 'login-input-group',
    'useInputGroup' => true
])

@php
    $hasIcon = !empty($icon);
    $needsInputGroup = $useInputGroup && ($hasIcon || ($type === 'password' && $showToggle));
    $inputClass = $needsInputGroup ? 'form-control border-start-0' : 'form-control';
@endphp

<div class="mb-{{ $type === 'password' && !$hasIcon ? '3' : '4' }}">
    @if($label)
        <label for="{{ $name }}" class="form-label {{ $hasIcon ? 'login-form-label' : '' }}">
            @if($icon)
                <i class="bi {{ $icon }} me-2"></i>
            @endif
            {{ $label }}
        </label>
    @endif
    @if($needsInputGroup)
        <div class="input-group {{ $inputGroupClass }}">
            @if($icon)
                <span class="input-group-text bg-light border-end-0">
                    <i class="bi {{ $icon }} text-muted"></i>
                </span>
            @endif
            <input 
                type="{{ $type }}" 
                class="{{ $inputClass }} @error($name) is-invalid @enderror" 
                id="{{ $name }}" 
                name="{{ $name }}" 
                value="{{ old($name, $value) }}" 
                @if($required) required @endif
                @if($autofocus) autofocus @endif
                @if($autocomplete) autocomplete="{{ $autocomplete }}" @endif
                placeholder="{{ $placeholder }}">
            @if($type === 'password' && $showToggle)
                <button class="btn btn-outline-secondary border-start-0" type="button" id="togglePassword">
                    <i class="bi bi-eye" id="eyeIcon"></i>
                </button>
            @endif
            @error($name)
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            @if($error && !$errors->has($name))
                <div class="invalid-feedback">{{ $error }}</div>
            @endif
        </div>
    @else
        <input 
            type="{{ $type }}" 
            class="{{ $inputClass }} @error($name) is-invalid @enderror" 
            id="{{ $name }}" 
            name="{{ $name }}" 
            value="{{ old($name, $value) }}" 
            @if($required) required @endif
            @if($autofocus) autofocus @endif
            @if($autocomplete) autocomplete="{{ $autocomplete }}" @endif
            placeholder="{{ $placeholder }}">
        @error($name)
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        @if($error && !$errors->has($name))
            <div class="invalid-feedback">{{ $error }}</div>
        @endif
    @endif
    @if($type === 'password' && !$hasIcon && !$showToggle)
        <small class="form-text text-muted">Password must be at least 8 characters long.</small>
    @endif
</div>

