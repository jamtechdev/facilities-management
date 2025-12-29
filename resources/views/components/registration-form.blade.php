@props([
    'action' => route('register'),
    'method' => 'POST',
    'title' => 'Complete Registration',
    'subtitle' => 'Fill in your details to create your account',
    'showBackButton' => true,
    'backButtonId' => 'backToRole',
    'formId' => 'registerForm',
    'submitText' => 'Create Account',
    'submitIcon' => 'bi-person-plus',
    'fields' => [],
    'clientFields' => []
])

<div class="register-form-container" id="registerFormContainer" style="display: none;">
    <div class="login-form-card">
        <div class="d-flex align-items-center mb-4">
            @if($showBackButton)
                <button type="button" class="btn btn-link p-0 me-3 back-to-role-btn" id="{{ $backButtonId }}">
                    <i class="bi bi-arrow-left fs-4"></i>
                </button>
            @endif
            <div class="flex-grow-1">
                <h2 class="fw-bold mb-1 login-form-title">{{ $title }}</h2>
                <p class="text-muted mb-0 small" id="selectedRoleText">{{ $subtitle }}</p>
            </div>
        </div>

        <form method="{{ $method }}" action="{{ $action }}" id="{{ $formId }}">
            @csrf
            <input type="hidden" name="role" id="selectedRole" value="">

            <!-- Client-specific fields (hidden by default) -->
            @if(count($clientFields) > 0)
                <div id="clientFields" style="display: none;">
                    @foreach($clientFields as $field)
                        <x-form-input
                            :type="$field['type'] ?? 'text'"
                            :name="$field['name'] ?? ''"
                            :label="$field['label'] ?? ''"
                            :icon="$field['icon'] ?? ''"
                            :value="old($field['name'] ?? '', $field['value'] ?? '')"
                            :placeholder="$field['placeholder'] ?? ''"
                            :required="$field['required'] ?? false"
                            :showToggle="$field['showToggle'] ?? false"
                            :autocomplete="$field['autocomplete'] ?? null"
                        />
                    @endforeach
                </div>
            @endif

            @foreach($fields as $field)
                <x-form-input
                    :type="$field['type'] ?? 'text'"
                    :name="$field['name'] ?? ''"
                    :label="$field['label'] ?? ''"
                    :icon="$field['icon'] ?? ''"
                    :value="old($field['name'] ?? '', $field['value'] ?? '')"
                    :placeholder="$field['placeholder'] ?? ''"
                    :required="$field['required'] ?? false"
                    :autofocus="$field['autofocus'] ?? false"
                    :autocomplete="$field['autocomplete'] ?? null"
                    :showToggle="$field['showToggle'] ?? false"
                />
            @endforeach

            <div class="d-grid mb-4">
                <button type="submit" class="btn btn-lg text-white login-btn">
                    <span><i class="bi {{ $submitIcon }} me-2"></i>{{ $submitText }}</span>
                </button>
            </div>
        </form>

        <div class="text-center mt-4">
            <p class="text-muted mb-0">
                Already have an account?
                <a href="{{ route('login') }}" class="login-link fw-bold">Login here</a>
            </p>
        </div>
    </div>
</div>
