@extends('errors::layout')

@section('title', 'Service Unavailable')
@section('code', '503')
@section('icon', 'bi-gear')

@section('message')
    We're currently performing maintenance. The service will be back online shortly. Thank you for your patience.
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
        <a href="{{ route('welcome') }}" class="btn-error btn-error-secondary">
            <i class="bi bi-house"></i>
            <span>Go to Home</span>
        </a>
    @endauth
@endsection
