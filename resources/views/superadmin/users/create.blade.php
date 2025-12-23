@extends('layouts.app')

@section('title', 'Create User')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col">
                <h1 class="h3 mb-0">Create New User</h1>
                <p class="text-muted">Add a new user to the system</p>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-10">
                <div class="form-card">
                    <div class="form-card-header">
                        <h5><i class="bi bi-person-plus me-2"></i>User Information</h5>
                    </div>
                    <div class="form-card-body">
                        <form method="POST" action="{{ route('admin.users.store') }}">
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
                                <div class="col-md-6">
                                    <label for="role" class="form-label">
                                        <i class="bi bi-shield-check me-1"></i>Role <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="role" name="role" required>
                                        <option value="">Select Role</option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-2"></i>Create User
                                </button>
                                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
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
