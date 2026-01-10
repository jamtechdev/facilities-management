@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col">
                <h1 class="h3 mb-0">Edit User</h1>
                <p class="text-muted">Update user information</p>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-10">
                <div class="form-card">
                    <div class="form-card-header">
                        <h5><i class="bi bi-pencil-square me-2"></i>Edit User Information</h5>
                    </div>
                    <div class="form-card-body">
                        <form id="editUserForm" method="POST" action="{{ \App\Helpers\RouteHelper::url('users.update', $user->id) }}">
                            @csrf
                            @method('PUT')

                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">
                                        <i class="bi bi-person me-1"></i>Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="name" name="name"
                                           value="{{ old('name', $user->name) }}" placeholder="Enter full name" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label">
                                        <i class="bi bi-envelope me-1"></i>Email <span class="text-danger">*</span>
                                    </label>
                                    <input type="email" class="form-control" id="email" name="email"
                                           value="{{ old('email', $user->email) }}" placeholder="example@email.com" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="password" class="form-label">
                                        <i class="bi bi-lock me-1"></i>Password <span class="text-muted">(leave blank to keep current)</span>
                                    </label>
                                    <input type="password" class="form-control" id="password" name="password"
                                           placeholder="Enter new password">
                                </div>
                            </div>

                            <div class="form-actions mt-4">
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="bi bi-check-circle me-2"></i>Update User
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
