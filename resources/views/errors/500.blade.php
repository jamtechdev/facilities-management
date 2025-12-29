@extends('errors::layout')

@section('title', 'Server Error')
@section('code', '500')
@section('icon', 'bi-exclamation-triangle')

@section('message')
    We're experiencing some technical difficulties. Our team has been notified and is working to fix the issue. Please try again later.
@endsection

@section('actions')
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
        <a href="{{ route($dashboardRoute) }}" class="btn-error btn-error-primary">
            <i class="bi bi-house-door"></i>
            <span>Go to Dashboard</span>
        </a>
    @else
        <a href="{{ route('welcome') }}" class="btn-error btn-error-primary">
            <i class="bi bi-house"></i>
            <span>Go to Home</span>
        </a>
    @endauth
    <a href="javascript:location.reload()" class="btn-error btn-error-secondary">
        <i class="bi bi-arrow-clockwise"></i>
        <span>Reload Page</span>
    </a>
@endsection
