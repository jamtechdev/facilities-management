@extends('layouts.app')

@section('title', 'User Details')

@push('styles')
    @vite(['resources/css/profile.css'])
@endpush

@section('content')
    <div class="container-fluid">
        <!-- User Header -->
        <div class="profile-header">
            <div class="profile-header-content">
                <div class="profile-avatar">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div class="profile-info flex-grow-1">
                    <h1>{{ $user->name }}</h1>
                    <p>{{ $user->email }}</p>
                </div>
                <div class="profile-header-actions">
                    @can('edit users')
                        @if($user->id !== auth()->id())
                            <a href="{{ \App\Helpers\RouteHelper::url('users.edit', $user->id) }}" class="btn btn-light me-2">
                                <i class="bi bi-pencil me-2"></i>Edit
                            </a>
                        @endif
                    @endcan
                    <a href="{{ \App\Helpers\RouteHelper::url('users.index') }}" class="btn btn-outline-light">
                        <i class="bi bi-arrow-left me-2"></i>Back
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">User Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <strong>Name:</strong>
                            </div>
                            <div class="col-md-8">
                                {{ $user->name }}
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <strong>Email:</strong>
                            </div>
                            <div class="col-md-8">
                                {{ $user->email }}
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <strong>Roles:</strong>
                            </div>
                            <div class="col-md-8">
                                @foreach($user->roles as $role)
                                    <span class="badge bg-{{ $role->name === 'SuperAdmin' ? 'danger' : 'primary' }} me-1">
                                        {{ $role->name }}
                                    </span>
                                @endforeach
                                @if($user->roles->isEmpty())
                                    <span class="text-muted">No roles assigned</span>
                                @endif
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <strong>Created At:</strong>
                            </div>
                            <div class="col-md-8">
                                {{ $user->created_at->format('M d, Y h:i A') }}
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <strong>Updated At:</strong>
                            </div>
                            <div class="col-md-8">
                                {{ $user->updated_at->format('M d, Y h:i A') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
