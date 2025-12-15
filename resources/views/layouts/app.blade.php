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
                @if(auth()->user()->hasAnyRole(['Admin', 'SuperAdmin']))
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
                                if(auth()->user()->hasRole('SuperAdmin')) {
                                    $roleName = 'Super Admin';
                                } elseif(auth()->user()->hasRole('Admin')) {
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
            {{-- Legacy topbar markup retained for reference
            <nav class="navbar-top sticky-top">
                <div class="navbar-top-container">
                    <!-- Left Section -->
                    <div class="navbar-top-left">
                        <button class="btn-sidebar-toggle d-md-none" type="button" id="sidebarToggle">
                            <i class="bi bi-list"></i>
                        </button>
                        <div class="navbar-breadcrumb">
                            <span class="breadcrumb-item">@yield('title', 'Dashboard')</span>
                        </div>
                    </div>

                    <!-- Right Section -->
                    <div class="navbar-top-right">
                        <!-- Notifications -->
                        <div class="navbar-icon-wrapper" data-bs-toggle="tooltip" title="Notifications">
                            <button class="navbar-icon-btn" type="button" id="notificationsBtn" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-bell"></i>
                                @php
                                    $notificationCount = \App\Models\FollowUpTask::where('is_completed', false)
                                        ->where('due_date', '<=', \Carbon\Carbon::now()->addDays(7))
                                        ->count();
                                @endphp
                                @if($notificationCount > 0)
                                    <span class="navbar-badge">{{ $notificationCount > 99 ? '99+' : $notificationCount }}</span>
                                @endif
                            </button>
                            <div class="dropdown-menu dropdown-menu-end notification-dropdown" style="min-width: 350px; max-width: 400px; max-height: 500px; overflow-y: auto;">
                                <div class="dropdown-header d-flex justify-content-between align-items-center">
                                    <strong>Notifications</strong>
                                    <small class="text-muted">Follow-up Reminders</small>
                                </div>
                                <div class="dropdown-divider"></div>
                                <div id="notificationsList">
                                    @php
                                        $notifications = \App\Models\FollowUpTask::where('is_completed', false)
                                            ->where('due_date', '<=', \Carbon\Carbon::now()->addDays(7))
                                            ->with(['lead.assignedStaff'])
                                            ->orderBy('due_date', 'asc')
                                            ->take(10)
                                            ->get();
                                    @endphp
                                    @if($notifications->count() > 0)
                                        @foreach($notifications as $notification)
                                            @php
                                                $isOverdue = $notification->due_date->isPast();
                                                $daysUntil = $notification->due_date->diffInDays(\Carbon\Carbon::now(), false);
                                            @endphp
                                            <a href="{{ route('admin.leads.show', $notification->lead) }}" class="dropdown-item notification-item {{ $isOverdue ? 'notification-overdue' : '' }}">
                                                <div class="d-flex align-items-start">
                                                    <div class="flex-shrink-0">
                                                        <i class="bi bi-calendar-event text-{{ $isOverdue ? 'danger' : 'warning' }}"></i>
                                                    </div>
                                                    <div class="flex-grow-1 ms-2">
                                                        <div class="fw-bold">{{ $notification->lead->name }}</div>
                                                        <small class="text-muted">{{ Str::limit($notification->suggestion, 50) }}</small>
                                                        <div class="mt-1">
                                                            <small class="text-muted">
                                                                Day {{ $notification->reminder_day }} - {{ $notification->due_date->format('M d, Y') }}
                                                                @if($isOverdue)
                                                                    <span class="badge bg-danger ms-1">{{ abs($daysUntil) }}d overdue</span>
                                                                @elseif($daysUntil <= 3)
                                                                    <span class="badge bg-warning ms-1">{{ $daysUntil }}d left</span>
                                                                @endif
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                            <div class="dropdown-divider"></div>
                                        @endforeach
                                    @else
                                        <div class="dropdown-item text-center py-3">
                                            <i class="bi bi-check-circle text-success" style="font-size: 32px;"></i>
                                            <p class="text-muted mt-2 mb-0">No notifications</p>
                                        </div>
                                    @endif
                                </div>
                                @if($notifications->count() > 10)
                                    <div class="dropdown-footer text-center py-2">
                                        <a href="{{ route('admin.dashboard') }}" class="text-decoration-none small">View all notifications</a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Messages -->
                        <div class="navbar-icon-wrapper" data-bs-toggle="tooltip" title="Messages">
                            <button class="navbar-icon-btn" type="button" id="messagesBtn">
                                <i class="bi bi-envelope"></i>
                                <span class="navbar-badge">5</span>
                            </button>
                        </div>

                        <!-- User Dropdown -->
                        <div class="navbar-user-dropdown">
                            <button class="navbar-user-btn" type="button" id="userDropdownBtn" data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="navbar-user-avatar">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                                <div class="navbar-user-info">
                                    <div class="navbar-user-name">{{ auth()->user()->name }}</div>
                                    <div class="navbar-user-role">
                                        @php
                                            if(auth()->user()->hasRole('SuperAdmin')) {
                                                $roleName = 'Super Admin';
                                                $roleColor = 'danger';
                                            } elseif(auth()->user()->hasRole('Admin')) {
                                                $roleName = 'Admin';
                                                $roleColor = 'primary';
                                            } elseif(auth()->user()->hasRole('Staff')) {
                                                $roleName = 'Staff';
                                                $roleColor = 'info';
                                            } else {
                                                $roleName = 'User';
                                                $roleColor = 'secondary';
                                            }
                                        @endphp
                                        <span class="badge badge-{{ $roleColor }}">{{ $roleName }}</span>
                                    </div>
                                </div>
                                <i class="bi bi-chevron-down navbar-user-chevron"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end navbar-dropdown-menu" aria-labelledby="userDropdownBtn">
                                <li>
                                    <a class="dropdown-item" href="{{ route('staff.profile') }}">
                                        <i class="bi bi-person me-2"></i>Profile
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#">
                                        <i class="bi bi-gear me-2"></i>Settings
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-box-arrow-right me-2"></i>Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>
            --}}

            <!-- Page Content -->
            <main class="flex-grow-1 p-4 main-content-area">
                @if(session('success'))
                    <x-alert type="success" :message="session('success')" />
                @endif

                @if(session('error'))
                    <x-alert type="danger" :message="session('error')" />
                @endif

                @if($errors->any())
                    <x-alert type="danger" :message="$errors->all()" />
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
