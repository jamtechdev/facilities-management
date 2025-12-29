@props([
    'pageClass' => 'login-page',
    'showSidebar' => true,
    'sidebarTitle' => 'Welcome',
    'sidebarSubtitle' => 'Sign in to access your account',
    'sidebarImage' => 'office-cleaning.jpg',
    'sidebarFeatures' => []
])

<div class="{{ $pageClass }}">
    <div class="container-fluid p-0 h-100">
        <div class="row g-0 h-100 login-card">
            @if($showSidebar)
                <x-auth-sidebar
                    :title="$sidebarTitle"
                    :subtitle="$sidebarSubtitle"
                    :image="$sidebarImage"
                    :features="$sidebarFeatures"
                />
            @endif

            <div class="col-lg-6 col-md-6 p-5 d-flex flex-column justify-content-center h-100 register-right">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
