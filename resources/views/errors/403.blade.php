@extends('errors::layout')

@section('title', 'Access Forbidden')
@section('code', '403')
@section('icon', 'bi-shield-exclamation')

@section('message')
    {{ $exception->getMessage() ?: 'You do not have permission to access this page. Please contact your administrator if you need this access.' }}
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
        <a href="{{ route('login') }}" class="btn-error btn-error-primary">
            <i class="bi bi-box-arrow-in-right"></i>
            <span>Login</span>
        </a>
    @endauth
    <a href="javascript:history.back()" class="btn-error btn-error-secondary">
        <i class="bi bi-arrow-left"></i>
        <span>Go Back</span>
    </a>
@endsection
