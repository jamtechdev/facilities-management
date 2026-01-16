@extends('layouts.app')

@section('title', 'Create User')

@push('styles')
    @vite(['resources/css/profile.css'])
@endpush

@section('content')
    <div class="container-fluid">
        <!-- User Header -->
        <div class="profile-header">
            <div class="profile-header-content">
                <div class="profile-avatar">
                    <i class="bi bi-person-plus"></i>
                </div>
                <div class="profile-info flex-grow-1">
                    <h1>Create New User</h1>
                    <p>Add a new user to the system</p>
                </div>
                <div class="profile-header-actions">
                    <a href="{{ \App\Helpers\RouteHelper::url('users.index') }}" class="btn btn-outline-light">
                        <i class="bi bi-arrow-left me-2"></i>Back to Users
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-10">
                <div class="form-card">
                    <div class="form-card-header">
                        <h5><i class="bi bi-person-plus me-2"></i>User Information</h5>
                    </div>
                    <div class="form-card-body">
                        <form id="createUserForm" method="POST" action="{{ \App\Helpers\RouteHelper::url('users.store') }}">
                            @csrf
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">
                                        <i class="bi bi-person me-1"></i>Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        placeholder="Enter full name" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label">
                                        <i class="bi bi-envelope me-1"></i>Email <span class="text-danger">*</span>
                                    </label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        placeholder="example@email.com" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="password" class="form-label">
                                        <i class="bi bi-lock me-1"></i>Password <span class="text-danger">*</span>
                                    </label>
                                    <input type="password" class="form-control" id="password" name="password"
                                        placeholder="Enter password" required>
                                </div>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="bi bi-check-circle me-2"></i>Create User
                                </button>
                                <a href="{{ \App\Helpers\RouteHelper::url('users.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle me-2"></i>Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @vite(['resources/js/forms.js'])
@endpush
