@props([
    'title' => 'Create Account',
    'subtitle' => 'Choose your account type to get started',
    'roles' => [],
    'selectedRole' => null,
    'error' => null,
    'showLoginLink' => true,
    'loginRoute' => 'login',
    'loginText' => 'Already have an account?',
    'loginLinkText' => 'Login here'
])

<div class="register-role-selection" id="roleSelection">
    <div class="text-center mb-5">
        <h2 class="fw-bold mb-2 login-form-title">{{ $title }}</h2>
        <p class="text-muted">{{ $subtitle }}</p>
    </div>

    @if($error)
        <x-alert type="danger" :message="$error" />
    @endif

    <div class="role-selection-container">
        <div class="row g-4">
            @foreach($roles as $role)
                <x-role-card
                    :id="'role_' . $role['value']"
                    name="role"
                    :value="$role['value']"
                    :icon="$role['icon'] ?? 'bi-person'"
                    :title="$role['title'] ?? ''"
                    :subtitle="$role['subtitle'] ?? ''"
                    :features="$role['features'] ?? []"
                    :checked="($selectedRole === $role['value'])"
                />
            @endforeach
        </div>

        @error('role')
            <div class="text-danger small mt-3 text-center">{{ $message }}</div>
        @enderror
    </div>

    @if($showLoginLink)
        <div class="text-center mt-4">
            <p class="text-muted mb-0">
                {{ $loginText }}
                <a href="{{ route($loginRoute) }}" class="login-link fw-bold">
                    {{ $loginLinkText }}
                </a>
            </p>
        </div>
    @endif
</div>
