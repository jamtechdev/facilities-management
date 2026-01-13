<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} - @yield('title', 'Dashboard')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    {{-- Load jQuery first from npm, then other scripts - jQuery will be globally available on all pages --}}
    @vite([
        'resources/js/jquery-global.js',
        'resources/css/app.css',
        'resources/css/common-styles.css',
        'resources/css/utilities.css',
        'resources/css/layout.css',
        'resources/css/forms.css',
        'resources/css/datatables.css',
        'resources/css/preloader.css',
        'resources/js/app.js',
        'resources/js/layout.js',
        'resources/js/forms.js',
        'resources/js/flash-messages.js',
        'resources/js/image-modal.js',
        'resources/js/datatables-init.js',
        'resources/js/datatables-handlers.js',
        'resources/js/global-loader.js',
        'resources/js/preloader.js'
    ])
    @stack('styles')

    <!-- Firebase SDK -->
    <script src="https://www.gstatic.com/firebasejs/10.7.1/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/10.7.1/firebase-messaging-compat.js"></script>

    <!-- Firebase Config -->
    <script>
        window.firebaseConfig = {
            apiKey: "{{ config('services.firebase.api_key', '') }}",
            authDomain: "{{ config('services.firebase.auth_domain', '') }}",
            projectId: "{{ config('services.firebase.project_id', '') }}",
            storageBucket: "{{ config('services.firebase.storage_bucket', '') }}",
            messagingSenderId: "{{ config('services.firebase.messaging_sender_id', '') }}",
            appId: "{{ config('services.firebase.app_id', '') }}",
            vapidKey: "{{ config('services.firebase.vapid_key', '') }}"
        };
    </script>
</head>
<body class="authenticated"
    @if(session('success')) data-flash-success="{{ session('success') }}" @endif
    @if(session('error')) data-flash-error="{{ session('error') }}" @endif
    @if($errors->any()) data-flash-errors='@json($errors->all())' @endif>
    @include('layouts.partials.preloader')
    <div class="min-vh-100 d-flex">
        <!-- Modern Sidebar -->
        <aside class="sidebar-modern">
            <!-- Sidebar Header -->
            <div class="sidebar-header">
                <a href="{{ \App\Helpers\RouteHelper::url('dashboard') }}" class="sidebar-logo">
                    <div class="sidebar-logo-icon">
                        <i class="bi bi-building"></i>
                    </div>
                    <span>KEYSTONE</span>
                </a>
            </div>

            @include('layouts.partials.sidebar')
            {{-- Legacy sidebar markup retained for reference

            <!-- Sidebar Navigation -->
            <nav class="sidebar-nav">
                @if(auth()->user()->can('view admin dashboard'))
                    <!-- Main Section -->
                    <div class="nav-section">
                        <div class="nav-section-title">Main</div>
                        @can('view admin dashboard')
                        <div class="nav-item-modern">
                            <a class="nav-link-modern {{ \App\Helpers\RouteHelper::routeIsAny('dashboard') ? 'active' : '' }}" href="{{ \App\Helpers\RouteHelper::url('dashboard') }}">
                                <span class="nav-icon"><i class="bi bi-speedometer2"></i></span>
                                <span class="nav-text">Dashboard</span>
                            </a>
                        </div>
                        @endcan
                    </div>

                    <!-- Business Section -->
                    <div class="nav-section">
                        <div class="nav-section-title">Business</div>
                        @can('view leads')
                        <div class="nav-item-modern">
                            <a class="nav-link-modern {{ \App\Helpers\RouteHelper::routeIsAny('leads.*') ? 'active' : '' }}" href="{{ \App\Helpers\RouteHelper::url('leads.index') }}">
                                <span class="nav-icon"><i class="bi bi-person-lines-fill"></i></span>
                                <span class="nav-text">Leads</span>
                            </a>
                        </div>
                        @endcan

                        @can('view clients')
                        <div class="nav-item-modern">
                            <a class="nav-link-modern {{ \App\Helpers\RouteHelper::routeIsAny('clients.*') ? 'active' : '' }}" href="{{ \App\Helpers\RouteHelper::url('clients.index') }}">
                                <span class="nav-icon"><i class="bi bi-building"></i></span>
                                <span class="nav-text">Clients</span>
                            </a>
                        </div>
                        @endcan

                        @can('view staff')
                        <div class="nav-item-modern">
                            <a class="nav-link-modern {{ \App\Helpers\RouteHelper::routeIsAny('staff.*') ? 'active' : '' }}" href="{{ \App\Helpers\RouteHelper::url('staff.index') }}">
                                <span class="nav-icon"><i class="bi bi-people"></i></span>
                                <span class="nav-text">Staff</span>
                            </a>
                        </div>
                        @endcan
                    </div>

                    <!-- Financial Section -->
                    <div class="nav-section">
                        <div class="nav-section-title">Financial</div>
                        @can('view invoices')
                        <div class="nav-item-modern">
                            <a class="nav-link-modern {{ \App\Helpers\RouteHelper::routeIsAny('invoices.*') ? 'active' : '' }}" href="{{ \App\Helpers\RouteHelper::url('invoices.index') }}">
                                <span class="nav-icon"><i class="bi bi-receipt"></i></span>
                                <span class="nav-text">Invoices</span>
                            </a>
                        </div>
                        @endcan

                        @can('view payouts')
                        <div class="nav-item-modern">
                            <a class="nav-link-modern {{ \App\Helpers\RouteHelper::routeIsAny('payouts.*') ? 'active' : '' }}" href="{{ \App\Helpers\RouteHelper::url('payouts.index') }}">
                                <span class="nav-icon"><i class="bi bi-cash-coin"></i></span>
                                <span class="nav-text">Payouts</span>
                            </a>
                        </div>
                        @endcan
                    </div>

                    <!-- Operations Section -->
                    <div class="nav-section">
                        <div class="nav-section-title">Operations</div>
                        @can('view inventory')
                        <div class="nav-item-modern">
                            <a class="nav-link-modern {{ \App\Helpers\RouteHelper::routeIsAny('inventory.*') ? 'active' : '' }}" href="{{ \App\Helpers\RouteHelper::url('inventory.index') }}">
                                <span class="nav-icon"><i class="bi bi-box-seam"></i></span>
                                <span class="nav-text">Inventory</span>
                            </a>
                        </div>
                        @endcan
                    </div>

                    <!-- System Section -->
                    <div class="nav-section">
                        <div class="nav-section-title">System</div>
                        @can('view roles')
                        <div class="nav-item-modern">
                            <a class="nav-link-modern {{ \App\Helpers\RouteHelper::routeIsAny('roles.*') || \App\Helpers\RouteHelper::routeIsAny('permissions.*') ? 'active' : '' }}" href="{{ \App\Helpers\RouteHelper::url('roles.index') }}">
                                <span class="nav-icon"><i class="bi bi-shield-check"></i></span>
                                <span class="nav-text">Roles & Permissions</span>
                            </a>
                        </div>
                        @endcan

                        @can('view users')
                        <div class="nav-item-modern">
                            <a class="nav-link-modern {{ \App\Helpers\RouteHelper::routeIsAny('users.*') ? 'active' : '' }}" href="{{ \App\Helpers\RouteHelper::url('users.index') }}">
                                <span class="nav-icon"><i class="bi bi-person-gear"></i></span>
                                <span class="nav-text">Users</span>
                            </a>
                        </div>
                        @endcan
                    </div>
                @elseif(auth()->user()->can('view staff dashboard'))
                    <!-- Staff Section -->
                    <div class="nav-section">
                        <div class="nav-section-title">Main</div>
                        @can('view staff dashboard')
                        <div class="nav-item-modern">
                            <a class="nav-link-modern {{ \App\Helpers\RouteHelper::routeIs('dashboard') ? 'active' : '' }}" href="{{ \App\Helpers\RouteHelper::url('dashboard') }}">
                                <span class="nav-icon"><i class="bi bi-speedometer2"></i></span>
                                <span class="nav-text">Dashboard</span>
                            </a>
                        </div>
                        @endcan

                        @can('view timesheets')
                        <div class="nav-item-modern">
                            <a class="nav-link-modern {{ \App\Helpers\RouteHelper::routeIs('timesheet*') ? 'active' : '' }}" href="{{ \App\Helpers\RouteHelper::url('timesheet') }}">
                                <span class="nav-icon"><i class="bi bi-clock-history"></i></span>
                                <span class="nav-text">Timesheet</span>
                            </a>
                        </div>
                        @endcan

                        <div class="nav-item-modern">
                            <a class="nav-link-modern {{ \App\Helpers\RouteHelper::routeIs('profile*') ? 'active' : '' }}" href="{{ \App\Helpers\RouteHelper::url('profile') }}">
                                <span class="nav-icon"><i class="bi bi-person"></i></span>
                                <span class="nav-text">Profile</span>
                            </a>
                        </div>

                        <div class="nav-item-modern">
                            <a class="nav-link-modern {{ \App\Helpers\RouteHelper::routeIs('activity') ? 'active' : '' }}" href="{{ \App\Helpers\RouteHelper::url('activity') }}">
                                <span class="nav-icon"><i class="bi bi-activity"></i></span>
                                <span class="nav-text">Activity Log</span>
                            </a>
                        </div>
                    </div>
                @endif
            </nav>

            <!-- Sidebar Footer -->
            <div class="sidebar-footer">
                <div class="user-info">
                    <div class="user-avatar">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="user-details">
                        <div class="user-name">{{ auth()->user()->name }}</div>
                        <div class="user-role">
                            @php
                                $authUser = auth()->user();
                                if($authUser->can('view roles')) {
                                    $roleName = 'Super Admin';
                                } elseif($authUser->can('view admin dashboard')) {
                                    $roleName = 'Admin';
                                } elseif($authUser->can('view staff dashboard')) {
                                    $roleName = 'Staff';
                                } elseif($authUser->can('view client dashboard')) {
                                    $roleName = 'Client';
                                } else {
                                    $roleName = 'User';
                                }
                            @endphp
                            {{ $roleName }}
                        </div>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-light btn-sm w-100">
                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                    </button>
                </form>
            </div>
        --}}
        </aside>

        <!-- Main Content Area -->
        <div class="flex-grow-1 main-content-wrapper">
            @include('layouts.partials.topbar')

            <!-- Page Content -->
            <main class="flex-grow-1 p-4 main-content-area">
                @yield('content')
            </main>
        </div>
    </div>

    {{-- jQuery is loaded globally from npm via jquery-global.js in head section above --}}
    {{-- jQuery ($ and jQuery) is now available on all pages --}}
    {{-- Flash messages are handled by flash-messages.js --}}
    @vite(['resources/js/firebase-notifications.js'])
    @stack('scripts')
</body>
</html>
