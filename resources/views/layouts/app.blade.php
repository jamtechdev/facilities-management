<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} - @yield('title', 'Dashboard')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    @vite([
        'resources/css/app.css',
        'resources/css/utilities.css',
        'resources/css/layout.css',
        'resources/css/dashboard.css',
        'resources/css/forms.css',
        'resources/css/datatables.css',

        'resources/js/app.js',
        'resources/js/layout.js',
        'resources/js/forms.js',
        'resources/js/datatables-init.js',
        'resources/js/datatables-handlers.js'
    ])
    @stack('styles')
</head>
<body>
    <div class="min-vh-100 d-flex">
        <!-- Modern Sidebar -->
        <aside class="sidebar-modern">
            <!-- Sidebar Header -->
            <div class="sidebar-header">
                <a href="{{ route('admin.dashboard') }}" class="sidebar-logo">
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
                @if(auth()->user()->hasAnyRole(['SuperAdmin', 'Admin']))
                    <!-- Main Section -->
                    <div class="nav-section">
                        <div class="nav-section-title">Main</div>
                        @can('view admin dashboard')
                        <div class="nav-item-modern">
                            <a class="nav-link-modern {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
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
                            <a class="nav-link-modern {{ request()->routeIs('admin.leads.*') ? 'active' : '' }}" href="{{ route('admin.leads.index') }}">
                                <span class="nav-icon"><i class="bi bi-person-lines-fill"></i></span>
                                <span class="nav-text">Leads</span>
                            </a>
                        </div>
                        @endcan

                        @can('view clients')
                        <div class="nav-item-modern">
                            <a class="nav-link-modern {{ request()->routeIs('admin.clients.*') ? 'active' : '' }}" href="{{ route('admin.clients.index') }}">
                                <span class="nav-icon"><i class="bi bi-building"></i></span>
                                <span class="nav-text">Clients</span>
                            </a>
                        </div>
                        @endcan

                        @can('view staff')
                        <div class="nav-item-modern">
                            <a class="nav-link-modern {{ request()->routeIs('admin.staff.*') ? 'active' : '' }}" href="{{ route('admin.staff.index') }}">
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
                            <a class="nav-link-modern {{ request()->routeIs('admin.invoices.*') ? 'active' : '' }}" href="{{ route('admin.invoices.index') }}">
                                <span class="nav-icon"><i class="bi bi-receipt"></i></span>
                                <span class="nav-text">Invoices</span>
                            </a>
                        </div>
                        @endcan

                        @can('view payouts')
                        <div class="nav-item-modern">
                            <a class="nav-link-modern {{ request()->routeIs('admin.payouts.*') ? 'active' : '' }}" href="{{ route('admin.payouts.index') }}">
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
                            <a class="nav-link-modern {{ request()->routeIs('admin.inventory.*') ? 'active' : '' }}" href="{{ route('admin.inventory.index') }}">
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
                            <a class="nav-link-modern {{ request()->routeIs('admin.roles.*') || request()->routeIs('admin.permissions.*') ? 'active' : '' }}" href="{{ route('admin.roles.index') }}">
                                <span class="nav-icon"><i class="bi bi-shield-check"></i></span>
                                <span class="nav-text">Roles & Permissions</span>
                            </a>
                        </div>
                        @endcan

                        @can('view users')
                        <div class="nav-item-modern">
                            <a class="nav-link-modern {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                                <span class="nav-icon"><i class="bi bi-person-gear"></i></span>
                                <span class="nav-text">Users</span>
                            </a>
                        </div>
                        @endcan
                    </div>
                @elseif(auth()->user()->hasRole('Staff'))
                    <!-- Staff Section -->
                    <div class="nav-section">
                        <div class="nav-section-title">Main</div>
                        @can('view staff dashboard')
                        <div class="nav-item-modern">
                            <a class="nav-link-modern {{ request()->routeIs('staff.dashboard') ? 'active' : '' }}" href="{{ route('staff.dashboard') }}">
                                <span class="nav-icon"><i class="bi bi-speedometer2"></i></span>
                                <span class="nav-text">Dashboard</span>
                            </a>
                        </div>
                        @endcan

                        @can('view timesheets')
                        <div class="nav-item-modern">
                            <a class="nav-link-modern {{ request()->routeIs('staff.timesheet*') ? 'active' : '' }}" href="{{ route('staff.timesheet') }}">
                                <span class="nav-icon"><i class="bi bi-clock-history"></i></span>
                                <span class="nav-text">Timesheet</span>
                            </a>
                        </div>
                        @endcan

                        <div class="nav-item-modern">
                            <a class="nav-link-modern {{ request()->routeIs('staff.profile*') ? 'active' : '' }}" href="{{ route('staff.profile') }}">
                                <span class="nav-icon"><i class="bi bi-person"></i></span>
                                <span class="nav-text">Profile</span>
                            </a>
                        </div>

                        <div class="nav-item-modern">
                            <a class="nav-link-modern {{ request()->routeIs('staff.activity') ? 'active' : '' }}" href="{{ route('staff.activity') }}">
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
                                if(auth()->user()->hasRole('Admin')) {
                                    $roleName = 'Super Admin';
                                } elseif(auth()->user()->hasRole('SuperAdmin')) {
                                    $roleName = 'Admin';
                                } elseif(auth()->user()->hasRole('Staff')) {
                                    $roleName = 'Staff';
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')

    <script>
        // Show flash messages using Toastr
        @if(session('success'))
            if (typeof showToast !== 'undefined') {
                showToast('success', '{{ session('success') }}');
            }
        @endif

        @if(session('error'))
            if (typeof showToast !== 'undefined') {
                showToast('error', '{{ session('error') }}');
            }
        @endif

        @if($errors->any())
            if (typeof showToast !== 'undefined') {
                @foreach($errors->all() as $error)
                    showToast('error', '{{ $error }}');
                @endforeach
            }
        @endif
    </script>
</body>
</html>
