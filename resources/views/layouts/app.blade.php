<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} - @yield('title', 'Dashboard')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    @vite(['resources/css/app.css', 'resources/css/dashboard.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body>
    <div class="min-vh-100 d-flex">
        <!-- Sidebar -->
        <aside class="bg-dark text-white sidebar-custom" style="width: 250px; min-height: 100vh;">
            <div class="p-3">
                <h4 class="text-white mb-4">KEYSTONE</h4>
                <nav class="nav flex-column">
                    @if(auth()->user()->hasRole('Admin'))
                        <a class="nav-link text-white {{ request()->routeIs('admin.dashboard') ? 'bg-primary rounded' : '' }}" href="{{ route('admin.dashboard') }}">
                            <i class="bi bi-speedometer2 me-2"></i> Dashboard
                        </a>
                        <a class="nav-link text-white {{ request()->routeIs('admin.leads.*') ? 'bg-primary rounded' : '' }}" href="{{ route('admin.leads.index') }}">
                            <i class="bi bi-person-lines-fill me-2"></i> Leads
                        </a>
                        <a class="nav-link text-white {{ request()->routeIs('admin.clients.*') ? 'bg-primary rounded' : '' }}" href="{{ route('admin.clients.index') }}">
                            <i class="bi bi-building me-2"></i> Clients
                        </a>
                        <a class="nav-link text-white {{ request()->routeIs('admin.staff.*') ? 'bg-primary rounded' : '' }}" href="{{ route('admin.staff.index') }}">
                            <i class="bi bi-people me-2"></i> Staff
                        </a>
                        <a class="nav-link text-white {{ request()->routeIs('admin.users.*') ? 'bg-primary rounded' : '' }}" href="{{ route('admin.users.index') }}">
                            <i class="bi bi-person-gear me-2"></i> Users
                        </a>
                    @elseif(auth()->user()->hasRole('Staff'))
                        <a class="nav-link text-white {{ request()->routeIs('staff.dashboard') ? 'bg-primary rounded' : '' }}" href="{{ route('staff.dashboard') }}">
                            <i class="bi bi-speedometer2 me-2"></i> Dashboard
                        </a>
                        <a class="nav-link text-white {{ request()->routeIs('staff.timesheets.*') ? 'bg-primary rounded' : '' }}" href="{{ route('staff.timesheets.index') }}">
                            <i class="bi bi-clock-history me-2"></i> Timesheets
                        </a>
                    @elseif(auth()->user()->hasRole('Client'))
                        <a class="nav-link text-white {{ request()->routeIs('client.dashboard') ? 'bg-primary rounded' : '' }}" href="{{ route('client.dashboard') }}">
                            <i class="bi bi-speedometer2 me-2"></i> Dashboard
                        </a>
                    @elseif(auth()->user()->hasRole('Lead'))
                        <a class="nav-link text-white {{ request()->routeIs('lead.dashboard') ? 'bg-primary rounded' : '' }}" href="{{ route('lead.dashboard') }}">
                            <i class="bi bi-speedometer2 me-2"></i> Dashboard
                        </a>
                    @endif
                </nav>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-grow-1 d-flex flex-column">
            <!-- Top Navbar -->
            <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
                <div class="container-fluid">
                    <button class="btn btn-link d-md-none" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="ms-auto d-flex align-items-center">
                        <span class="me-3">Welcome, <strong>{{ auth()->user()->name }}</strong></span>
                        @php
                            $roleColor = 'secondary';
                            $roleName = 'User';
                            if(auth()->user()->hasRole('Admin')) {
                                $roleColor = 'danger';
                                $roleName = 'Admin';
                            } elseif(auth()->user()->hasRole('Staff')) {
                                $roleColor = 'info';
                                $roleName = 'Staff';
                            } elseif(auth()->user()->hasRole('Client')) {
                                $roleColor = 'success';
                                $roleName = 'Client';
                            } elseif(auth()->user()->hasRole('Lead')) {
                                $roleColor = 'warning';
                                $roleName = 'Lead';
                            }
                        @endphp
                        <span class="badge bg-{{ $roleColor }} me-3">
                            {{ $roleName }}
                        </span>
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger btn-sm">Logout</button>
                        </form>
                    </div>
                </div>
            </nav>

            <!-- Page Content -->
            <main class="flex-grow-1 p-4">
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

