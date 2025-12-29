@extends('errors::layout')

@section('title', 'Page Expired')
@section('code', '419')
@section('icon', 'bi-clock-history')

@section('message')
    Your session has expired due to inactivity. Please refresh the page and try again.
@endsection

@section('actions')
    <a href="javascript:location.reload()" class="btn-error btn-error-primary">
        <i class="bi bi-arrow-clockwise"></i>
        <span>Refresh Page</span>
    </a>
    @auth
        @php
            $user = auth()->user();
            $dashboardRoute = 'admin.dashboard';
            if ($user->can('view admin dashboard')) {
                if ($user->can('view roles')) {
                    $dashboardRoute = 'superadmin.dashboard';
                } else {
                    $dashboardRoute = 'admin.dashboard';
                }
            } elseif ($user->can('view staff dashboard')) {
                $dashboardRoute = 'staff.dashboard';
            } elseif ($user->can('view client dashboard')) {
                $dashboardRoute = 'client.dashboard';
            } elseif ($user->can('view lead dashboard')) {
                $dashboardRoute = 'lead.dashboard';
            }
        @endphp
        <a href="{{ route($dashboardRoute) }}" class="btn-error btn-error-secondary">
            <i class="bi bi-house-door"></i>
            <span>Go to Dashboard</span>
        </a>
    @else
        <a href="{{ route('login') }}" class="btn-error btn-error-secondary">
            <i class="bi bi-box-arrow-in-right"></i>
            <span>Login</span>
        </a>
    @endauth
@endsection
