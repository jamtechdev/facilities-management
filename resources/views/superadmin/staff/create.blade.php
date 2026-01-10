@extends('layouts.app')

@section('title', 'Create New Staff')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Create New Staff</h1>
                    <p class="text-muted">Add a new staff member to your system</p>
                </div>
                <a href="{{ \App\Helpers\RouteHelper::url('staff.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back to Staff
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="form-card">
                <div class="form-card-header">
                    <h5><i class="bi bi-person-plus me-2"></i>Staff Information</h5>
                </div>
                <div class="form-card-body">
                    <form id="createStaffForm" method="POST" action="{{ \App\Helpers\RouteHelper::url('staff.store') }}">
                        @csrf

                        <div class="row g-4">
                            <div class="col-md-6">
                                <label for="name" class="form-label">
                                    <i class="bi bi-person me-1"></i>Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Enter full name" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label">
                                    <i class="bi bi-envelope me-1"></i>Email <span class="text-danger">*</span>
                                </label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="example@email.com" autocomplete="off" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="password" class="form-label">
                                    <i class="bi bi-lock me-1"></i>Password <span class="text-danger">*</span>
                                </label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" autocomplete="new-password" placeholder="Enter password" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Minimum 8 characters</small>
                            </div>

                            <div class="col-md-6">
                                <label for="mobile" class="form-label">Mobile</label>
                                <input type="text" class="form-control @error('mobile') is-invalid @enderror" id="mobile" name="mobile" value="{{ old('mobile') }}">
                                @error('mobile')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="hourly_rate" class="form-label">Hourly Rate (£)</label>
                                <input type="number" step="0.01" class="form-control @error('hourly_rate') is-invalid @enderror" id="hourly_rate" name="hourly_rate" value="{{ old('hourly_rate') }}">
                                @error('hourly_rate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="address" class="form-label">Address</label>
                                <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="2">{{ old('address') }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="assigned_weekly_hours" class="form-label">Assigned Weekly Hours</label>
                                <input type="number" step="0.5" class="form-control @error('assigned_weekly_hours') is-invalid @enderror" id="assigned_weekly_hours" name="assigned_weekly_hours" value="{{ old('assigned_weekly_hours') }}">
                                @error('assigned_weekly_hours')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="assigned_monthly_hours" class="form-label">Assigned Monthly Hours</label>
                                <input type="number" step="0.5" class="form-control @error('assigned_monthly_hours') is-invalid @enderror" id="assigned_monthly_hours" name="assigned_monthly_hours" value="{{ old('assigned_monthly_hours') }}">
                                @error('assigned_monthly_hours')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="client_id" class="form-label">
                                    <i class="bi bi-building me-1"></i>Assign to Client (Optional)
                                </label>
                                <select class="form-select @error('client_id') is-invalid @enderror" id="client_id" name="client_id">
                                    <option value="">Select Client</option>
                                    @foreach($clients ?? [] as $client)
                                        <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                            {{ $client->company_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('client_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">You can assign staff to a client later</small>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Active Staff
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="bi bi-check-circle me-2"></i>Create Staff
                            </button>
                            <a href="{{ \App\Helpers\RouteHelper::url('staff.index') }}" class="btn btn-outline-secondary">
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
