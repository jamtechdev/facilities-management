@extends('errors::layout')

@section('title', 'Page Not Found')
@section('code', '404')
@section('icon', 'bi-search')

@section('message')
    The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.
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
    <a href="javascript:history.back()" class="btn-error btn-error-secondary">
        <i class="bi bi-arrow-left"></i>
        <span>Go Back</span>
    </a>
@endsection
